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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/completionlib.php');

/**
 * Extend Moodle global navigation with custom items:
 * - item can be inserted in flat navigation (see PAD+ sidebar customisation in columns2.php)
 * - item can appear in breadcrumb
 */
function local_padplusextensions_extend_navigation(global_navigation $nav) {
    global $COURSE;
    $syscontext = context_system::instance();

    $padnodes = array();

    // Check for any authenticated user.
    if (has_capability('moodle/user:changeownpassword', $syscontext)) {
        $mycoursesnode = navigation_node::create(
            get_string('mycourses-page', 'theme_padplus'),
            new moodle_url('/my/courses.php'),
            navigation_node::TYPE_ROOTNODE,
            null,
            'mycoursespage',
            new pix_icon('i/mycourses', '')
        );
        $padnodes[] = $mycoursesnode;

        $professionnalctx = get_top_category_context_for_professional();
        $myprogressnode = navigation_node::create(
            get_string( $professionnalctx ? 'myprogress-professional-title' : 'myprogress-student-title', 'theme_padplus'),
            new moodle_url('/my/progress.php'),
            navigation_node::TYPE_ROOTNODE,
            null,
            'myprogresspage',
            new pix_icon('i/myprogress', '')
        );
        $padnodes[] = $myprogressnode;
    }

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
        $padnodes[] = $allcategoriesnode;
    }

    // Check for access and availability of progress report.
    if (has_capability('report/progress:view', context_course::instance($COURSE->id))) {
        // Activity report is enabled by default, but might be disabled or unavailable for single-activity course.
        $completion = new completion_info($COURSE);
        if ($completion->is_enabled() && $completion->has_activities()) {
            $progressnode = navigation_node::create(
                get_string('pluginname', 'report_progress'),
                new moodle_url('/report/progress/index.php', array('course' => $COURSE->id)),
                navigation_node::TYPE_SETTING,
                null,
                'progressreport'
            );
            // Force active check on URL base only.
            // It seems progress report manipulates URL params in a weird way, which invalidates default check_if_active match.
            $progressnode->check_if_active(URL_MATCH_BASE);
            $padnodes[] = $progressnode;
        }
    }

    // Check for access and availability of completion report.
    if (has_capability('report/completion:view', context_course::instance($COURSE->id))) {
        // Completion report is not available if not configured, we need to check that.
        $completion = new completion_info($COURSE);
        if ($completion->is_enabled() && $completion->has_criteria()) {
            $completionnode = navigation_node::create(
                get_string('pluginname', 'report_completion'),
                new moodle_url('/report/completion/index.php', array('course' => $COURSE->id)),
                navigation_node::TYPE_SETTING,
                null,
                'completionreport'
            );
            $padnodes[] = $completionnode;
        }
    }

    // Add all PAD+ navigation nodes to flat navigation for later insertion in sidemenu (see columns2.php).
    foreach ($padnodes as $node) {
        $node->showinflatnavigation = true;
        $nav->add_node($node);
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
    return categoryid_is_workshop($topcategoryid, $theme);
}

/**
 * Tell whether given course belongs directly to a workshop category as set in theme settings.
 * WARNING! This does not handle course in workshop subcategories.
 *
 * @param               $course   course to test (stdClass or other course representation)
 * @param theme_config  $theme    PAD+ theme for settings
 * @return bool         true if course is a workshop, false otherwise
 */
function course_is_workshop($course, theme_config $theme) {
    return categoryid_is_workshop($course->category, $theme);
}

function categoryid_is_workshop($categoryid, theme_config $theme) {
    $workshopids = explode(',', $theme->settings->sidebarworkshopids);
    return in_array($categoryid, $workshopids);
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
 * Get first context in user top categories which matches the given capability.
 * If user does not have the capability in any top context, return false.
 *
 * @param string    $capability capability to check in a category context
 * @return mixed    id of the category context for which user has the given capability, false otherwise
 */
function get_top_category_context_with_capability($capability) {
    $usercategories = \core_course_category::top()->get_children();
    foreach ($usercategories as $cat) {
        $context = context_coursecat::instance($cat->id);
        if (has_capability($capability, $context)) {
            return $context;
        }
    }
    return false;
}

/**
 * Get first category context which matches a professional role, i.e. a course creator.
 * This function is the closest we have as a predicate to discriminate professionals and students,
 * since students can not create courses.
 *
 * @return mixed id of the category context for a professional user, false for a student
 */
function get_top_category_context_for_professional() {
    return get_top_category_context_with_capability('moodle/course:create');
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
