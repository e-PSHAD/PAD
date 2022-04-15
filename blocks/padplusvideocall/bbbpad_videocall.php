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
 * Handle actions for BigBlueButton videocalls.
 *
 * This controller handles API actions in URL for the current session user.
 * Depending on the given action parameter:
 * - create and/or join a meeting as moderator or attendee
 * - handle meeting logout (typically close the window if it was open programmatically)
 * - run playback for previous meetings recording
 *
 * In order to create a meeting, both following conditions should be met:
 * - current user had the right permission (typically a professional, identifed as a contributor)
 * - URL should convey all parameters to create the meeting (meeting id, passwords, etc.)
 *
 * Viewer URL only have mandatory parameters to join the meeting as a viewer (meeting id and viewer passord).
 *
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/bigbluebuttonbn/locallib.php');
require_once(__DIR__ . '/lib.php');

require_login(null, false);

$pageheading = get_string('padplusvideocall', 'block_padplusvideocall');

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(BBBPAD_VIDEOCALL_PATH);
$PAGE->set_title("$SITE->shortname: $pageheading");
$PAGE->set_cacheable(false);
$PAGE->set_heading($pageheading);
$PAGE->blocks->show_only_fake_blocks();

$action = required_param('action', PARAM_TEXT);

switch (strtolower($action)) {
    case 'logout':
        $message = optional_param('message', '', PARAM_TEXT);

        bbbpad_log_event('left', $context);

        // Close the tab or window where BBB was opened.
        bbbpad_close_window($message);
        break;

    case 'createjoin':
        $contextid = required_param('contextid', PARAM_INT);
        $viewersid = required_param('viewersid', PARAM_SEQUENCE);
        $meetingname = optional_param('name', '', PARAM_TEXT);

        // Check create capability for moderator.
        $context = context::instance_by_id($contextid);
        if (! has_capability('block/padplusvideocall:createvideocall', $context)) {
            $message = get_string('createvideocall_nocapability', 'block_padplusvideocall');
            $logouturl = get_videocall_logout_url($message);
            header('Location: ' . $logouturl);
            break;
        }

        $viewersid = explode(',', $viewersid);
        $viewers = $DB->get_records_list('user', 'id', $viewersid);

        if (strlen($meetingname) === 0) {
            $moderatorname = fullname($USER);
            $meetingname = "$pageheading $moderatorname";
            if (count($viewers) === 1) {
                // Retrieve first and only viewer from associative array.
                $viewer = $viewers[array_key_first($viewers)];
                $meetingname .= ' - ' . fullname($viewer);
            }
        }

        $meetingid = pad_guid_v4();
        $modpw = bigbluebuttonbn_random_password(12);
        $viewerpw = bigbluebuttonbn_random_password(12, $modpw);
        $viewerurl = get_videocall_join_url($meetingid, $viewerpw);
        $logouturl = get_videocall_logout_url();

        // Create meeting.
        // This is idempotent per BBB API specification, so can be called multiples times without side effects.
        $response = bigbluebuttonbn_get_create_meeting_array(
            bbbpad_create_meeting_data($meetingid, $meetingname, $viewerpw, $modpw, $viewerurl, $logouturl),
            bbbpad_create_meeting_metadata()
        );
        if (empty($response)) {
            throw new moodle_exception('view_error_unable_join_teacher', 'mod_bigbluebuttonbn');
            break;
        }
        if ($response['returncode'] == 'FAILED') {
            if (!$printerrorkey) {
                throw new moodle_exception($response['message'], 'mod_bigbluebuttonbn');
                break;
            }
            $printerrorkey = bigbluebuttonbn_get_error_key($response['messageKey'], 'view_error_create');
            throw new moodle_exception($printerrorkey, 'mod_bigbluebuttonbn');
            break;
        }
        if ($response['hasBeenForciblyEnded'] == 'true') {
            throw new moodle_exception(get_string('index_error_forciblyended', 'mod_bigbluebuttonbn'));
            break;
        }

        bbbpad_log_event('created', $context);

        foreach ($viewers as $viewer) {
            send_videocall_notification($USER, $viewer, $viewerurl);
        }

        // The meeting is now running, just join the session.
        bbbpad_join_meeting($meetingid, $USER, $modpw, $logouturl, $context);
        break;


    case 'join':
        $meetingid = required_param('meetingid', PARAM_TEXT);
        $viewerpw = required_param('viewerpw', PARAM_TEXT);

        // Join session if and only if it is in progress.
        // Always request cache update to get meeting info, otherwise the viewer might be rejected because
        // cache might tell meeting is not running and won't update for 1 minute, delaying viewer admission.
        if (bigbluebuttonbn_is_meeting_running($meetingid, true)) {
            $logouturl = get_videocall_logout_url();
            bbbpad_join_meeting($meetingid, $USER, $viewerpw, $logouturl);
            break;
        }

        // Otherwise stop when meeting is not running. It may already be obsolete and we we don't viewer to create
        // meeting by themselves.
        $message = get_string('joinvideocall_nomeeting', 'block_padplusvideocall');
        $logouturl = get_videocall_logout_url($message);
        header('Location: ' . $logouturl);
        break;

    default:
        bbbpad_close_window();
}

function bbbpad_close_window($message = '') {
    global $OUTPUT, $PAGE;
    echo $OUTPUT->header();
    if (strlen($message) === 0) {
        // Default message displayed to user when he quits a video call but window can not be closed automatically.
        $message = get_string('joinvideocall_leftmeeting', 'block_padplusvideocall');
    }
    echo html_writer::tag('p', $message);
    $PAGE->requires->js_init_code('window.close()');
    echo $OUTPUT->footer();
}

/**
 * Build BigBlueButton payload for meeting creation.
 *
 * @see             mod/bigbluebuttonbn/bbb_view.php bigbluebuttonbn_bbb_view_create_meeting_data
 * @param string    $meetingid
 * @param string    $meetingname
 * @param string    $viewerpw
 * @param string    $modpw
 * @param string    $viewerurl
 * @param string    $logouturl
 * @return object
 */
function bbbpad_create_meeting_data($meetingid, $meetingname, $viewerpw, $modpw, $viewerurl, $logouturl) {
    $welcomemessage = get_string('bigbluebutton_welcome', 'block_padplusvideocall');
    $moderatormessage = get_string('bigbluebutton_moderatormessage', 'block_padplusvideocall', $viewerurl);
    $data = array(
        'meetingID' => $meetingid,
        'name' => $meetingname,
        'attendeePW' => $viewerpw,
        'moderatorPW' => $modpw,
        'logoutURL' => $logouturl,
        'welcome' => $welcomemessage,
        'moderatorOnlyMessage' => $moderatormessage,
        'meetingLayout' => 'VIDEO_FOCUS', // Put focus on video by default since it is a video call.
        'allowStartStopRecording' => false, // Disable video recording.
    );
    return $data;
}

/**
 * Build BigBlueButton metadata for meeting creation.
 *
 * @see             mod/bigbluebuttonbn/locallib.php bigbluebuttonbn_create_meeting_metadata
 * @return object
 */
function bbbpad_create_meeting_metadata() {
    return array(
        'bbbpad-type' => 'videocall'
    );
}

/**
 * Helper for preparing data used while joining the meeting.
 *
 * @see            mod/bigbluebuttonbn/bbb_view.php bigbluebuttonbn_bbb_view_join_meeting
 * @param string   $meetingid the meeting id on BigBlueButton server
 * @param object   $user the user record
 * @param string   $password the password which matches the user role, either moderator or attendee
 * @param string   $logouturl the url to redirect the user to when quitting
 */
function bbbpad_join_meeting($meetingid, $user, $password, $logouturl, $context = false) {
    $joinurl = bigbluebuttonbn_get_join_url(
        $meetingid,
        fullname($user),
        $password,
        $logouturl,
        null,
        $user->id,
        BIGBLUEBUTTON_CLIENTTYPE_HTML5);

    bbbpad_log_event('joined', $context ? $context : context_system::instance());

    // Execute the redirect.
    header('Location: ' . $joinurl);
}
