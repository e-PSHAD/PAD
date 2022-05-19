<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace block_padplusvideocall\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/padplusvideocall/lib.php');

/**
 * Provides PAD+ web services for video calls.
 */
class bbbpad_external extends \external_api {

    /**
     * Describes search_identity function parameters.
     *
     * @return external_function_parameters
     */
    public static function search_identity_parameters(): \external_function_parameters {

        return new \external_function_parameters([
            'contextid' => new \external_value(PARAM_INT, 'Id of the authorization context for videocall invitation'),
            'query' => new \external_value(PARAM_TEXT, 'The search query', VALUE_REQUIRED),
        ]);
    }

    /**
     * Finds users with the identity matching the given query.
     *
     * @see user/classes/external/search_identity for original code - this only changes the capability check.
     *
     * @param string $query The search request.
     * @return array
     */
    public static function search_identity(string $contextid, string $query): array {
        global $DB, $CFG;

        $params = \external_api::validate_parameters(self::search_identity_parameters(), [
            'contextid' => $contextid,
            'query' => $query,
        ]);
        $query = $params['query'];

        /*** PADPLUS Retrieve given [category] context and check invitevideocall capability. */
        $context = \context::instance_by_id($params['contextid']);
        self::validate_context($context);
        require_capability('block/padplusvideocall:invitevideocall', $context);
        /*** PADPLUS END */

        $hasviewfullnames = has_capability('moodle/site:viewfullnames', $context);

        $fields = \core_user\fields::for_name()->with_identity($context, false);
        $extrafields = $fields->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]);

        list($searchsql, $searchparams) = users_search_sql($query, '', true, $extrafields);
        list($sortsql, $sortparams) = users_order_by_sql('', $query, $context);
        $params = array_merge($searchparams, $sortparams);

        $rs = $DB->get_recordset_select('user', $searchsql, $params, $sortsql,
            'id' . $fields->get_sql()->selects, 0, $CFG->maxusersperpage + 1);

        $count = 0;
        $list = [];

        foreach ($rs as $record) {
            $user = (object)[
                'id' => $record->id,
                'fullname' => fullname($record, $hasviewfullnames),
                'extrafields' => [],
            ];

            foreach ($extrafields as $extrafield) {
                // Sanitize the extra fields to prevent potential XSS exploit.
                $user->extrafields[] = (object)[
                    'name' => $extrafield,
                    'value' => s($record->$extrafield)
                ];
            }

            $count++;

            if ($count <= $CFG->maxusersperpage) {
                $list[$record->id] = $user;
            }
        }

        $rs->close();

        return [
            'list' => $list,
            'maxusersperpage' => $CFG->maxusersperpage,
            'overflow' => ($count > $CFG->maxusersperpage),
        ];
    }

    /**
     * Describes search_identity function result value.
     *
     * @return external_description
     */
    public static function search_identity_returns(): \external_description {

        return new \external_single_structure([
            'list' => new \external_multiple_structure(
                new \external_single_structure([
                    'id' => new \external_value(\core_user::get_property_type('id'), 'ID of the user'),
                    // The output of the {@see fullname()} can contain formatting HTML such as <ruby> tags.
                    // So we need PARAM_RAW here and the caller is supposed to render it appropriately.
                    'fullname' => new \external_value(PARAM_RAW, 'The fullname of the user'),
                    'extrafields' => new \external_multiple_structure(
                        new \external_single_structure([
                            'name' => new \external_value(PARAM_TEXT, 'Name of the extrafield.'),
                            'value' => new \external_value(PARAM_TEXT, 'Value of the extrafield.'),
                        ]), 'List of extra fields', VALUE_OPTIONAL)
                ])
            ),
            'maxusersperpage' => new \external_value(PARAM_INT, 'Configured maximum users per page.'),
            'overflow' => new \external_value(PARAM_BOOL, 'Were there more records than maxusersperpage found?'),
        ]);
    }

    /**
     * Describes generate_meeting_links function parameters.
     *
     * @return external_function_parameters
     */
    public static function generate_meeting_links_parameters(): \external_function_parameters {

        return new \external_function_parameters([
            'contextid' => new \external_value(PARAM_INT, 'Id of the authorization context for videocall meeting'),
        ]);
    }

    /**
     * Generate moderator and viewer links for a new meeting.
     *
     * @param string $query The search request.
     * @return array
     */
    public static function generate_meeting_links(string $contextid): array {
        global $USER;

        $params = \external_api::validate_parameters(
            self::generate_meeting_links_parameters(),
            ['contextid' => $contextid]
        );

        $context = \context::instance_by_id($params['contextid']);
        self::validate_context($context);
        require_capability('block/padplusvideocall:invitevideocall', $context);

        $meetingdata = generate_meeting_data();
        $meetingid = $meetingdata['meetingid'];
        $modpw = $meetingdata['modpw'];
        $viewerpw = $meetingdata['viewerpw'];
        $moderatorurl = get_videocall_create_later_url($meetingid, $modpw, $viewerpw, $context->id);
        $viewerurl = get_videocall_join_url($meetingid, $viewerpw);

        send_moderator_link_reminder($USER, $moderatorurl);
        send_viewer_link_reminder($USER, $viewerurl);

        return array(
            'moderatorurl' => $moderatorurl,
            'viewerurl' => $viewerurl
        );
    }

    /**
     * Describes generate_meeting_links function result value.
     *
     * @return external_description
     */
    public static function generate_meeting_links_returns(): \external_description {
        return new \external_single_structure([
            'moderatorurl' => new \external_value(PARAM_TEXT, 'Meeting creation link for moderator.'),
            'viewerurl' => new \external_value(PARAM_TEXT, 'Meeting join link for moderator.'),
        ]);
    }
}
