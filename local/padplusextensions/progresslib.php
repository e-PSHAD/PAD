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

require_once($CFG->libdir . '/enrollib.php');
require_once($CFG->dirroot . '/grade/lib.php');
require_once($CFG->dirroot . '/grade/report/overview/lib.php');

use core_completion\progress;

/**
 * Compute course totals by todo|inprogress|done status for progress block.
 * See also JS module myprogress#computeTotalCoursesByStatus.
 *
 * @param string $userid
 * @return array data context for local_padplusextensions/myprogress_short_summary template
 */
function compute_total_courses_by_status($userid) {
    $visiblecourses = enrol_get_users_courses($userid);

    $result = array_reduce($visiblecourses, function($totals, $course) use ($userid) {
        $courseprogress = get_course_progress_status($course, $userid);
        $newtotal = $totals[$courseprogress['status']]['count'] + 1;
        $totals[$courseprogress['status']]['count'] = $newtotal;
        $totals[$courseprogress['status']]['hasmany'] = $newtotal > 1;
        return $totals;
    }, array(
        'done' =>
            ['count' => 0, 'hasmany' => false],
        'inprogress' =>
            ['count' => 0, 'hasmany' => false],
        'todo' =>
            ['count' => 0, 'hasmany' => false]
    ));
    $total = count($visiblecourses);
    $result['total'] = array(
        'count' => count($visiblecourses),
        'hasmany' => $total > 1
    );

    return array('totalByStatus' => $result);
}

/**
 * Retrieve all unique students enrolled in given courses.
 *
 * @param array $courses the list of courses to retrieve students from.
 * @return array the sorted set of all users enrolled in given courses
 */
function get_students_in_courses($courses) {
    $uniqueusers = array();

    foreach ($courses as $course) {
        $coursecontext = context_course::instance($course->id);
        // Only students have capability 'moodle/grade:view' in course context.
        $users = get_enrolled_users($coursecontext, 'moodle/grade:view');
        $uniqueusers += $users;
    }

    usort($uniqueusers, fn($a, $b) => strcasecmp($a->lastname, $b->lastname));
    return $uniqueusers;
}

/**
 * Retrieve all unique students enrolled in the same courses as the user.
 * The user is intended to be a teacher, although no such verification is made.
 *
 * @param string $userid
 * @return array a sorted set of all users enrolled the same courses as the user
 */
function get_all_students_in_user_courses($userid) {
    $courses = enrol_get_all_users_courses($userid);
    return get_students_in_courses($courses);
}


/**
 * Compute category contexts for the given course, to be used to regroup courses by categories.
 * The nominal case regroups courses by top group (platform) then bottom group (module) categories.
 *
 * Context path is the readily available data for course/category hierarchy.
 * It always begins with the system context (id 1), then the site category context in PAD+ organization.
 * Then begin category contexts managed by regular manager and teacher.
 * The last context refers to the course itself.
 *
 * - Context path for 3 levels hierarchy (platform - module - course)
 * This is the minimal hierarchy for nominal display
 * "/1/25/26/28/109"
 *   ▲  ▲  ▲  ▲  ▲
 *   ┃  ┃  ┃  ┃  ┗ course *** progress unit (with activity progress)
 *   ┃  ┃  ┃  ┗ module *** bottom level group for progress
 *   ┃  ┃  ┗ platform *** top level group for progress
 *   ┃  ┗ site (only accessible to admin/system managers)
 *   ┗ system level context
 *
 * - Context path for 4+ levels hierarchy (platform - block - module - course)
 * "/1/25/26/27/28/109"
 *   ▲  ▲  ▲  ▲  ▲  ▲
 *   ┃  ┃  ┃  ┃  ┃  ┗ course *** progress unit (with activity progress)
 *   ┃  ┃  ┃  ┃  ┗ module *** bottom level group for progress
 *   ┃  ┃  ┃  ┗ block -----> all intermediate levels are ignored and not displayed
 *   ┃  ┃  ┗ platform *** top level group for progress
 *   ┃  ┗ site (only accessible to admin/system manager)
 *   ┗ system level context
 *
 * - Edge case: course in platform category (2 levels hierarchy)
 * "/1/25/26/658" --> known platform, undefined module
 * - Edge case: course in site category
 * "/1/25/658" --> use site category as substitute for platform, undefined module
 * Note: course can not be created in root category, belongs at least to a site category
 *
 * @param core_course $course
 * @return tuple a pair containing top context (platform) and parent context (module) for course
 */
function get_course_regroup_contexts($course) {
    $platformctx = PADPLUS_UNDEFINED_REGROUP; // Indicate undefined platform.
    $modulectx = PADPLUS_UNDEFINED_REGROUP; // Indicate undefined module.

    $contexts = explode('/', $course->ctxpath);
    // phpcs:ignore
    // Explode "/1/25/26/28/109" --> ["","1","25","26","28","109"] gives N contexts + 1 empty string.
    $nbctx = count($contexts) - 1;
    if ($nbctx >= 5) { // Mininal count for nominal case.
        $platformctx = $contexts[PADPLUS_NOMINAL_TOP_CONTEXT_INDEX];
        $modulectx = $contexts[$nbctx - PADPLUS_BOTTOM_CONTEXT_REVERSED_INDEX];
    } else if ($nbctx === 4) { // Course in platform category, undefined module.
        $platformctx = $contexts[PADPLUS_NOMINAL_TOP_CONTEXT_INDEX];
    } else { // Course in site category used as 'platform', undefined module.
        $platformctx = $contexts[PADPLUS_DEFAULT_TOP_CONTEXT_INDEX];
    }

    return array($platformctx, $modulectx);
}

define('PADPLUS_NOMINAL_TOP_CONTEXT_INDEX', 3);
define('PADPLUS_DEFAULT_TOP_CONTEXT_INDEX', 2);
define('PADPLUS_BOTTOM_CONTEXT_REVERSED_INDEX', 1);
define('PADPLUS_UNDEFINED_REGROUP', 0);

/**
 * Retrieve all category records linked to given contexts, indexed by context ids for easy lookup.
 * Adapted from core_course_category::get_records, which is unfortunately protected.
 *
 * @param array $categorycontextids list of category context ids to look up as category records
 * @param array $fields subset of category attributes to select in records, 'id' and 'name' by default
 * @return array list of categories matching contexts, indexed by context ids
 */
function get_categories_from_context_list($categorycontextids, $fields = array('id', 'name')) {
    global $DB;
    $ctxselect = context_helper::get_preload_record_columns_sql('ctx');

    list($sqlinclause, $inparams) = $DB->get_in_or_equal($categorycontextids, SQL_PARAMS_NAMED);
    $whereclause = "ctx.id " . $sqlinclause;

    // First field 'ctxid' is used as index field in record results.
    $sql = "SELECT $ctxselect, cc.". join(',cc.', $fields). "
            FROM {course_categories} cc
            JOIN {context} ctx ON cc.id = ctx.instanceid AND ctx.contextlevel = :contextcoursecat
            WHERE ". $whereclause;
    return $DB->get_records_sql($sql,
            array('contextcoursecat' => CONTEXT_COURSECAT) + $inparams);
}

/**
 * Group all user courses by top groups then bottom groups.
 * This computes the basic data which enables overall progress monitoring for a student.
 *
 * @param string $userid
 * @return tuple the dataset of context groups and courses, as well as the related map of context and categories
 */
function group_courses_by_category_context($userid) {
    $visiblecourses = enrol_get_users_courses($userid);

    // Context groups data structure.
    $contextgroups = array();
    // Flat set of category contexts for later category retrieval.
    $categorycontexts = array();

    foreach ($visiblecourses as $course) {
        $coursecontexts = get_course_regroup_contexts($course);
        list($platformctx, $modulectx) = $coursecontexts;
        $categorycontexts = array_merge($categorycontexts, $coursecontexts);

        // Add course to module group. PHP magic will lazily initialize subarrays if need be.
        $contextgroups[$platformctx][$modulectx][] = $course;
    }

    // Retrieve category ids/names from contexts.
    $categoriesbycontext = get_categories_from_context_list(
        // Filter unique ids and reject 0 (PADPLUS_UNDEFINED_REGROUP) id.
        array_filter(array_unique($categorycontexts))
    );

    return array($contextgroups, $categoriesbycontext);
}

/**
 * Return category id and name from given context id and map of categories.
 * If context id matches the undefined group context, the function returns a dummy record
 * with a unique id (and a special prefix) and the given label for undefined group.
 *
 * @param int $ctxid                    context id linking to category
 * @param array $categoriesbycontext    list of categories indexed by context id
 * @param string $unknownlabel          label to use if context is not found among categories
 * @param string $unknownprefix         prefix to use in unique id if context is not found among categories
 * @return object                       category record, or a dummy record for unknown context
 */
function get_category_from_context($ctxid, $categoriesbycontext, $unknownlabel, $unknownprefix = '0_') {
    if ($ctxid === PADPLUS_UNDEFINED_REGROUP) {
        return (object) array(
            'id' => uniqid($unknownprefix),
            'name' => $unknownlabel
        );
    }
    return $categoriesbycontext[$ctxid];
}

/**
 * Return progress data for the given course and user.
 * The progress data contains activity completion in percentage, enum status as todo|inprogress|done,
 * as well as a boolean flag for each status.
 *
 * @param course $course
 * @param string $userid
 * @return array a record with progress data
 */
function get_course_progress_status($course, $userid) {
    $percentprogress = floor(progress::get_course_progress_percentage($course, $userid));
    switch ($percentprogress) {
        case 100:
            $status = 'done';
            break;
        case 0:
            $status = 'todo';
            break;
        default:
            $status = 'inprogress';
    }
    return array(
        'percent' => $percentprogress,
        'status' => $status,
        'done' => $status === 'done',
        'inprogress' => $status === 'inprogress',
        'todo' => $status === 'todo',
    );
}

/**
 * Return user overall course progress, grouped by categories, with status details per course.
 * This returns data ready to be consumed by local_padplusextensions/myprogress_content template.
 *
 * @param string $userid
 * @return array user dataset of category groups and courses, with status details per course.
 */
function get_user_overall_progress($userid) {
    list($contextgroups, $categoriesbycontext) = group_courses_by_category_context($userid);

    return array_map(function($ctxid, $platformgroup) use ($categoriesbycontext, $userid) {
        $platform = get_category_from_context($ctxid, $categoriesbycontext, get_string('undefined-platform', 'theme_padplus'));

        $modules = array_map(function($modctxid, $modulegroup) use ($categoriesbycontext, $userid) {
            $module = get_category_from_context($modctxid, $categoriesbycontext, get_string('undefined-module', 'theme_padplus'));

            $courses = array_map(
                fn($course) => array(
                    'id' => $course->id,
                    'viewurl' => (new moodle_url('/course/view.php', array('id' => $course->id)))->out(),
                    'fullname' => $course->fullname,
                    'progress' => get_course_progress_status($course, $userid)
                ), $modulegroup);

            return array(
                'id' => $module->id,
                'name' => $module->name,
                'courses' => $courses
            );
        }, array_keys($platformgroup), $platformgroup);

        return array(
            'id' => $platform->id,
            'name' => $platform->name,
            'modules' => $modules
        );
    }, array_keys($contextgroups), $contextgroups);
}

/**
 * Retrieve and format for export course total for all user courses.
 *
 * @see class grade_report_overview used in /grade/report/overview/classes/external.php
 * @param string $userid
 * @return array a map of course id to course total
 */
function retrieve_courses_totals($userid) {
    $course = get_course(SITEID);
    $context = context_course::instance($course->id);
    $gpr = new grade_plugin_return(array(
        'type' => 'report',
        'plugin' => 'overview',
        'courseid' => $course->id,
        'userid' => $userid));
    $report = new grade_report_overview($userid, $gpr, $context);
    $coursesgrades = $report->setup_courses_data(true);

    $grades = array();
    foreach ($coursesgrades as $coursegrade) {
        $courseid = $coursegrade['course']->id;
        $finalgrade = grade_format_gradevalue($coursegrade['finalgrade'], $coursegrade['courseitem'], true);
        $grades[$courseid] = $finalgrade;
    }

    return $grades;
}

function get_course_total($courseid, $coursetotals) {
    if (array_key_exists($courseid, $coursetotals)) {
        return $coursetotals[$courseid];
    }
    return '';
}
