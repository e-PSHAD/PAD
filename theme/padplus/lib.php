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

    // Map theme settings to SCSS variables.
    $settingstoscssmap = [
        'primarycolor' => 'primary',
        'complementarycolor' => 'complementary',
        'sidebarcolor' => 'sidebar-background'
    ];
    $whitelabelscss = '';
    foreach ($settingstoscssmap as $settingkey => $scssvariable) {
        $value = isset($theme->settings->{$settingkey}) ? $theme->settings->{$settingkey} : null;
        if (empty($value)) {
            continue;
        }
        $whitelabelscss .= "\${$scssvariable}: {$value};\n";
    }

    $prepad = file_get_contents($CFG->dirroot . '/theme/padplus/scss/pre.scss');
    // PAD+ theme uses default Boost preset.
    $boostpreset = file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    $postpad = file_get_contents($CFG->dirroot . '/theme/padplus/scss/post.scss');

    return $whitelabelscss . "\n" . $prepad . "\n" . $boostpreset . "\n" . $postpad;
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
