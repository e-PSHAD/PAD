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

namespace block_padplusvideocall\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/mod/bigbluebuttonbn/locallib.php');
require_once($CFG->dirroot . '/blocks/padplusvideocall/lib.php');

use context;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;

/**
 * PADPLUS TODO
 */

class bbbpad_external extends external_api {
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function initialize_videocall_parameters() {
        return new external_function_parameters(array(
            'contextid' => new external_value(PARAM_INT, 'Id of the authorized context for videocall'),
            'viewersid' => new external_multiple_structure(
                new external_value(PARAM_INT, 'Id of the user to invite in videocall')
            )
        ));
    }

    /**
     * The function itself
     * @return string welcome message
     */
    public static function initialize_videocall($contextid, $viewersid) {
        global $DB, $USER, $SITE;

        $params = self::validate_parameters(self::initialize_videocall_parameters(),
                array(
                    'contextid' => $contextid,
                    'viewersid' => $viewersid
                ));

        $context = context::instance_by_id($params['contextid']);
        self::validate_context($context);
        require_capability('block/padplusvideocall:createvideocall', $context);

        $viewers = $DB->get_records_list('user', 'id', $params['viewersid']);

        $meetingid = pad_guid_v4();
        $modpw = bigbluebuttonbn_random_password(12);
        $viewerpw = bigbluebuttonbn_random_password(12, $modpw);

        $moderatorname = fullname($USER);
        $videocallname = "$SITE->shortname appel vidéo : $moderatorname";
        if (count($viewers) === 1) {
            // Retrieve first and only viewer from associative array.
            $viewer = $viewers[array_key_first($viewers)];
            $videocallname .= ' - ' . fullname($viewer);
        }

        $viewerparams = http_build_query(array(
            'action' => 'join',
            'meetingid' => $meetingid,
            'viewerpw' => $viewerpw
        ), '', '&');
        $modparams = $viewerparams.'&'.http_build_query(array(
            'modpw' => $modpw,
            'name' => $videocallname
        ), '', '&');

        $moderatorurl = get_videocall_full_url($modparams);
        $viewerurl = get_videocall_full_url($viewerparams);

        foreach ($viewers as $viewer) {
            send_videocall_notification($USER, $viewer, $viewerurl);
        }

        return array(
            'moderatorurl' => $moderatorurl,
            'viewerurl' => $viewerurl
        );
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function initialize_videocall_returns() {
        return new external_single_structure(array(
            'moderatorurl' => new external_value(PARAM_URL, 'BigBlueButton videocall URL for moderator'),
            'viewerurl' => new external_value(PARAM_URL, 'BigBlueButton videocall URL for viewer')
        ));
    }
}
