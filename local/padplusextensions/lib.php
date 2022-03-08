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

/**
 * Tell whether current user has access to catalog by looking through its top categories.
 *
 * @param theme_config  $theme  PAD+ theme for settings
 * @return bool         true if user has access to catalog, false otherwise
 */
function current_user_has_access_to_catalog(theme_config $theme) {
    $usercategories = \core_course_category::top()->get_children();
    foreach ($usercategories as $cat) {
        if (category_belongs_to_catalog($cat, $theme)) {
            return true;
        }
    }
    return false;
}

/**
 * Search the given $courses for any that match the given $classification up to the specified
 * $limit.
 *
 * This function will return the subset of courses that belong to the catalog category as well as the
 * number of courses it had to process to build that subset.
 *
 * It is recommended that for larger sets of courses this function is given a Generator that loads
 * the courses from the database in chunks.
 *
 * @see course/lib.php Heavily inspired by similar course_filter_courses_* functions.
 * @param array|Traversable $courses List of courses to process
 * @param int $limit Limit the number of results to this amount
 * @param theme_config $theme Theme which contains settings for catalog.
 * @return array First value is the filtered courses, second value is the number of courses processed
 */
function course_filter_courses_in_catalog(
    $courses,
    int $limit = 0,
    $theme = null
) : array {

    $filteredcourses = [];
    $numberofcoursesprocessed = 0;
    $filtermatches = 0;

    foreach ($courses as $course) {
        $numberofcoursesprocessed++;

        $coursecategory = \core_course_category::get($course->category, MUST_EXIST, true);
        if (category_belongs_to_catalog($coursecategory, $theme)) {
            $filteredcourses[] = $course;
            $filtermatches++;
        }

        if ($limit && $filtermatches >= $limit) {
            // We've found the number of requested courses. No need to continue searching.
            break;
        }
    }

    // Return the number of filtered courses as well as the number of courses that were searched
    // in order to find the matching courses. This allows the calling code to do some kind of
    // pagination.
    return [$filteredcourses, $numberofcoursesprocessed];
}
