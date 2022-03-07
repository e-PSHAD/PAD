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
 * Extend Moodle global navigation with custom items:
 * - item can be inserted in flat navigation (see PAD+ sidebar customisation in columns2.php)
 * - item can appear in breadcrumb
 */
function local_padplusextensions_extend_navigation(global_navigation $nav) {
    $syscontext = context_system::instance();

    // Check for admin/instance manager.
    if (has_capability('moodle/category:manage', $syscontext)) {
        $allcategoriesnode = navigation_node::create(
            get_string('allcategories-menu', 'theme_padplus'),
            new moodle_url('/course/index.php'),
            navigation_node::TYPE_ROOTNODE,
            null,
            'allcategories',
            new pix_icon('i/course', '')
        );
        $allcategoriesnode->showinflatnavigation = true;
        $nav->add_node($allcategoriesnode);
    }
}

/**
 * Tell whether the given category is one of or belongs to workshops as set in theme settings.
 *
 * @param core_course_category  $category   category to test
 * @param theme_config          $theme      PAD+ theme for settings
 * @return bool                 true if category is a workshop one, false otherwise
 */
function category_belongs_to_workshop(core_course_category $category, ?theme_config $theme) {
    if (!$theme) {
        return false;
    }
    list($root, $topcategoryid) = explode('/', $category->path);
    $workshopids = explode(',', $theme->settings->sidebarworkshopids);
    return in_array($topcategoryid, $workshopids);
}

/**
 * Tell whether the given category is or belongs to the catalog category as set in theme settings.
 *
 * @param core_course_category  $category   category to test
 * @param theme_config          $theme      PAD+ theme for settings
 * @return bool                 true if category is or belongs to the catalog, false otherwise
 */
function category_belongs_to_catalog(core_course_category $category, ?theme_config $theme) {
    if (!$theme) {
        return false;
    }
    list($root, $topcategoryid) = explode('/', $category->path);
    return $topcategoryid === $theme->settings->sidebarcatalogid;
}
