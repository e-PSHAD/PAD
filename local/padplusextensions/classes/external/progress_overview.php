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

namespace local_padplusextensions\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/padplusextensions/progresslib.php');

/**
 * Provides the local_padplusextensions_progress_overview external function.
 */
class progress_overview extends \external_api {

    /**
     * Describes the external function parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'userid' => new \external_value(PARAM_TEXT, 'User id for whom to get progress overview'),
            'contextid' => new \external_value(PARAM_TEXT, 'Id of the authorization context', VALUE_OPTIONAL),
        ]);
    }

    /**
     * Retrieve overall user progress, grouped by platform and module categories.
     *
     * @param int   $userid The user id.
     * @param int   $contextid The authorization context id.
     * @return array
     */
    public static function execute(int $userid, int $contextid = null): array {
        global $USER;

        $params = \external_api::validate_parameters(self::execute_parameters(), [
            'userid' => $userid,
            'contextid' => $contextid,
        ]);
        $userid = $params['userid'];

        if ($USER->id == $userid) {
            $context = \context_user::instance($userid);
            self::validate_context($context);
        } else {
            $context = \context::instance_by_id($params['contextid']);
            self::validate_context($context);
            require_capability('moodle/course:create', $context);
        }

        $progress = get_user_overall_progress($userid);
        $totalbystatus = compute_total_courses_by_status($userid);

        return array_merge([
            'userid' => $userid,
            'username' => fullname(\core_user::get_user($userid), \core_user\fields::get_name_fields()),
            'progress' => $progress,
            ], $totalbystatus);
    }

    /**
     * Describes the external function result value.
     *
     * @return external_description
     */
    public static function execute_returns(): \external_description {
        return new \external_single_structure([
            'userid' => new \external_value(PARAM_TEXT, 'User id for progress overview'),
            'username' => new \external_value(PARAM_TEXT, 'User fullname'),
            'progress' => new \external_multiple_structure(
                new \external_single_structure([
                    'id' => new \external_value(PARAM_TEXT, 'Id of category'),
                    'name' => new \external_value(PARAM_TEXT, 'Name of category'),
                    'modules' => new \external_multiple_structure(
                        new \external_single_structure([
                            'id' => new \external_value(PARAM_TEXT, 'Id of category'),
                            'name' => new \external_value(PARAM_TEXT, 'Name of category'),
                            'courses' => new \external_multiple_structure(
                                new \external_single_structure([
                                    'id' => new \external_value(PARAM_TEXT, 'Id of course'),
                                    'fullname' => new \external_value(PARAM_TEXT, 'Fullname of course'),
                                    'viewurl' => new \external_value(PARAM_URL, 'URL of course homepage'),
                                    'progress' => new \external_single_structure([
                                        'done' => new \external_value(
                                            PARAM_BOOL,
                                            'Mark course as done'),
                                        'inprogress' => new \external_value(
                                            PARAM_BOOL,
                                            'Mark course as in progress'),
                                        'percent' => new \external_value(
                                            PARAM_INT,
                                            'Percentage of course completion'),
                                        'todo' => new \external_value(
                                            PARAM_BOOL,
                                            'Mark course as to do'),
                                        'status' => new \external_value(
                                            PARAM_TEXT,
                                            'Status of course completion todo|inprogress|done'),
                                    ]),
                                ]), 'List of user courses')
                        ]), 'List of bottom categories (modules)')
                ]), 'List of upper categories (platforms)'
            ),
            'totalByStatus' => new \external_single_structure([
                'done' => new \external_single_structure([
                    'count' => new \external_value(PARAM_INT, 'Number of done courses'),
                    'hasmany' => new \external_value(PARAM_BOOL, 'Has many done courses?'),
                ]),
                'inprogress' => new \external_single_structure([
                    'count' => new \external_value(PARAM_INT, 'Number of courses in progress'),
                    'hasmany' => new \external_value(PARAM_BOOL, 'Has many courses in progress?'),
                ]),
                'todo' => new \external_single_structure([
                    'count' => new \external_value(PARAM_INT, 'Number of courses to do'),
                    'hasmany' => new \external_value(PARAM_BOOL, 'Has many courses to do?'),
                ]),
                'total' => new \external_single_structure([
                    'count' => new \external_value(PARAM_INT, 'Total number of courses'),
                    'hasmany' => new \external_value(PARAM_BOOL, 'Has many courses?'),
                ]),
            ])
        ]);
    }
}
