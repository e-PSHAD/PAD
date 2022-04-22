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
require_once($CFG->dirroot . '/local/padplusextensions/lib.php');
require_once(__DIR__ . '/lib.php');

require_login(null, false);

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_url(BBBPAD_VIDEOCALL_PATH);
$PAGE->set_title("$SITE->shortname: Appel vidéo");
$PAGE->set_cacheable(false);
$PAGE->set_heading("$SITE->shortname: Appel vidéo");
$PAGE->blocks->show_only_fake_blocks();

$action = required_param('action', PARAM_TEXT);

switch (strtolower($action)) {
    case 'logout':
        $message = optional_param('message', '', PARAM_TEXT);

        bbbpad_log_event('left', $context ? $context : context_system::instance());

        // Close the tab or window where BBB was opened.
        bbbpad_close_window($message);
        break;

    case 'join':
        $meetingid = required_param('meetingid', PARAM_TEXT);
        $meetingname = optional_param('name', 'Appel Vidéo', PARAM_TEXT); // PADPLUS TODO: default name?
        $viewerpw = required_param('viewerpw', PARAM_TEXT);
        $modpw = optional_param('modpw', '', PARAM_TEXT);

        // For modetator: also check capability if modpw is given.
        $context = get_top_category_context_with_capability('block/padplusvideocall:createvideocall');
        $userismoderator = !empty($modpw) && $context;
        $password = $userismoderator ? $modpw : $viewerpw;
        $logouturl = get_videocall_full_url('action=logout');

        // See if the session is in progress.
        // Always request cache update to get meeting info, otherwise the viewer might be rejected because
        // cache might tell meeting is not running and won't update for 1 minute, delaying viewer admission.
        if (bigbluebuttonbn_is_meeting_running($meetingid, true)) {
            // Since the meeting is already running, we just join the session.
            bbbpad_join_meeting($meetingid, $USER, $password, $logouturl, $context);
            break;
        }

        // If user is only a viewer, stop now as we don't have enough information to create the meeting anyway.
        if (!$userismoderator) {
            $params = http_build_query(array(
                'message' => "Cet appel n'est pas actif."
            ));
            header('Location: ' . $logouturl . '&' . $params);
            break;
        }

        // As the meeting doesn't exist, try to create it.
        $response = bigbluebuttonbn_get_create_meeting_array(
            bbbpad_create_meeting_data($meetingid, $meetingname, $viewerpw, $modpw, $logouturl),
            bbbpad_create_meeting_metadata()
        );
        if (empty($response)) {
            throw new moodle_exception('view_error_unable_join_teacher', 'mod_bigbluebuttonbn');
            break;
        }
        if ($response['returncode'] == 'FAILED') {
            // The meeting was not created.
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

        // The meeting is now running, just join the session.
        bbbpad_join_meeting($meetingid, $USER, $password, $logouturl, $context);
        break;

    default:
        bbbpad_close_window();
}

function bbbpad_close_window($message = '') {
    global $OUTPUT, $PAGE;
    echo $OUTPUT->header();
    if ($message != '') {
        echo html_writer::tag('p', $message, array('class' => 'alert alert-warning'));
    }
    echo "Vous êtes sorti de l'appel vidéo. Vous pouvez continuer vos activités sur la plateforme ou fermez cette fenêtre.";
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
 * @param string    $logouturl
 * @return object
 */
function bbbpad_create_meeting_data($meetingid, $meetingname, $viewerpw, $modpw, $logouturl) {
    // PADPLUS TODO: extract string?
    $welcomemessage = 'Bienvenue !';
    $moderatormessage =
        'Vous pouvez inviter votre correspondant en lui transmettant cette adresse ' . $viewerpw;
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

        // PADPLUS TODO: Following options does not work on BBB test server. Need to test on our own BBB server.
        // 'bannerText' => 'message de bannière',
        // 'bannerColor' => '#FF0000',
        // 'guestPolicy' => 'ALWAYS_ACCEPT',
        // 'endWhenNoModerator' => true, // If moderator quits without ending meeting, meeting will stop soon after.
        // 'endWhenNoModeratorDelayInMinutes' => 1,
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
function bbbpad_join_meeting($meetingid, $user, $password, $logouturl, $context) {
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
