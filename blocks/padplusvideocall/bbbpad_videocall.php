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
 *
 * In order to create a meeting, both following conditions should be met:
 * - current user had the right permission (typically a professional, identifed as a contributor)
 * - URL should convey all parameters to create the meeting (meeting id, passwords, etc.)
 *
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/bigbluebuttonbn/locallib.php');
require_once($CFG->dirroot . '/local/padplusextensions/lib.php');
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

    case 'join':
        // Dispatch user workflow:
        // - users with createvideocall capability can create meeting and become moderator;
        // - users without that capability can only join meeting as regular viewers.
        $meetingid = required_param('meetingid', PARAM_TEXT);
        // Technically, $modpw is only required for moderator, but we don't know which workflow will be chosen yet.
        $modpw = required_param('modpw', PARAM_TEXT);
        $viewerpw = required_param('viewerpw', PARAM_TEXT);
        $viewersid = optional_param('viewersid', '', PARAM_SEQUENCE);
        $meetingname = optional_param('name', '', PARAM_TEXT);

        $context = get_top_category_context_with_capability('block/padplusvideocall:createvideocall');
        if ($context) { /* Moderator workflow should always succeed */
            $videocalldata = new VideocallData($meetingid, $modpw, $viewerpw);
            $meetingurl = get_videocall_join_url($videocalldata);
            $logouturl = get_videocall_logout_url();

            // Fetch invitees if any.
            $viewersid = explode(',', $viewersid);
            $viewers = $DB->get_records_list('user', 'id', $viewersid);

            // Set default meeting name if none.
            if (strlen($meetingname) === 0) {
                $moderatorname = fullname($USER);
                $meetingname = "$pageheading $moderatorname";
                if (count($viewers) === 1) {
                    // Retrieve first and only viewer from associative array.
                    $viewer = $viewers[array_key_first($viewers)];
                    $meetingname .= ' - ' . fullname($viewer);
                }
            }

            // Create meeting if need be. This is idempotent if multiple users request the same meeting creation.
            bbbpad_create_meeting($videocalldata, $meetingname, $meetingurl, $logouturl, $context);

            // Send invitations to viewers if any.
            foreach ($viewers as $viewer) {
                send_videocall_notification($USER, $viewer, $meetingurl);
            }

            // The meeting should now be running, join the session as moderator.
            bbbpad_join_meeting($videocalldata->meetingid, $USER, $videocalldata->modpw, $logouturl, $context);

        } else { /* Viewer workflow */
            // Join session if and only if it is in progress.
            // Always request cache update to get meeting info, otherwise the viewer might be rejected because
            // cache might tell meeting is not running and won't update for 1 minute, delaying viewer admission.
            if (bigbluebuttonbn_is_meeting_running($meetingid, true)) {
                $logouturl = get_videocall_logout_url();
                bbbpad_join_meeting($meetingid, $USER, $viewerpw, $logouturl);

            } else {
                // Redirect if meeting is not running. It may already be obsolete and we don't want viewer to create
                // meeting by themselves.
                $message = get_string('joinvideocall_nomeeting', 'block_padplusvideocall');
                $logouturl = get_videocall_logout_url($message);
                header('Location: ' . $logouturl);
            }
        }
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
 * Create BigBlueButton meeting.
 * This is idempotent per BBB API specification, so can be called multiples times without side effects.
 *
 * @param VideocallData $videocalldata
 * @param string        $meetingname
 * @param string        $meetingurl
 * @param string        $logouturl
 * @param object        $context for log event
 * @throws moodle_exception in case something bad happens when creating the meeting
 */
function bbbpad_create_meeting($videocalldata, $meetingname, $meetingurl, $logouturl, $context) {
    $response = bigbluebuttonbn_get_create_meeting_array(
        bbbpad_build_meeting_data($videocalldata, $meetingname, $meetingurl, $logouturl),
        bbbpad_build_meeting_metadata()
    );
    if (empty($response)) {
        throw new moodle_exception('view_error_unable_join_teacher', 'mod_bigbluebuttonbn');
    }
    if ($response['returncode'] == 'FAILED') {
        $printerrorkey = bigbluebuttonbn_get_error_key($response['messageKey'], 'view_error_create');
        if (!$printerrorkey) {
            throw new moodle_exception($response['message'], 'mod_bigbluebuttonbn');
        }
        throw new moodle_exception($printerrorkey, 'mod_bigbluebuttonbn');
    }
    if ($response['hasBeenForciblyEnded'] == 'true') {
        throw new moodle_exception(get_string('index_error_forciblyended', 'mod_bigbluebuttonbn'));
    }

    bbbpad_log_event('created', $context);

    return;
}

/**
 * Build BigBlueButton payload for meeting creation.
 *
 * @see                 mod/bigbluebuttonbn/bbb_view.php bigbluebuttonbn_bbb_view_create_meeting_data
 * @param VideocallData $videocalldata
 * @param string        $meetingname
 * @param string        $meetingurl
 * @param string        $logouturl
 * @return object
 */
function bbbpad_build_meeting_data($videocalldata, $meetingname, $meetingurl, $logouturl) {
    $welcomemessage = get_string('bigbluebutton_welcome', 'block_padplusvideocall');
    $moderatormessage = get_string('bigbluebutton_moderatormessage', 'block_padplusvideocall', $meetingurl);
    $data = array(
        'meetingID' => $videocalldata->meetingid,
        'name' => $meetingname,
        'attendeePW' => $videocalldata->viewerpw,
        'moderatorPW' => $videocalldata->modpw,
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
function bbbpad_build_meeting_metadata() {
    return array(
        'bbbpad-type' => 'videocall'
    );
}

/**
 * Join BigBlueButton meeting.
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
