<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme functions.
 *
 * @package    theme_padplus
 */


// We will add callbacks here as we add features to our theme.

function theme_padplus_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');

    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_padplus', 'preset', 0, '/', $filename))) {
        // This preset file was fetched from the file area for theme_padplus and not theme_boost (see the line above).
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    $whitelabel = '';
    $whitelabelfile = $CFG->dirroot . '/theme/padplus/scss/marque-blanche.scss';
    if (file_exists($whitelabelfile)) {
        $whitelabel = file_get_contents($whitelabelfile);
    }

    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
    $pre = file_get_contents($CFG->dirroot . '/theme/padplus/scss/pre.scss');
    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
    $post = file_get_contents($CFG->dirroot . '/theme/padplus/scss/post.scss');

    // Combine them together.
    return $whitelabel . "\n" . $pre . "\n" . $scss . "\n" . $post;
}

// Helper function to fill target nav from source nav by selecting given menu keys.
function fill_nav_from_menu_keys(flat_navigation $source, flat_navigation $target, array $menukeys) {
    foreach ($menukeys as $menukey) {
        $menuitem = $source->get($menukey);
        if (is_object($menuitem)) {
            $target->add($menuitem);
        }
    }
}

// Helper function to fill target nav from source nav by selecting menu items by type (e.g. navigation_node::TYPE_COURSE).
function fill_nav_from_menu_type(flat_navigation $source, flat_navigation $target, int $type) {
    $menuitems = $source->type($type);
    foreach ($menuitems as $menuitem) {
        $target->add($menuitem);
    }
}
