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

require_once($CFG->dirroot . '/mod/bigbluebuttonbn/locallib.php');

define('BBBPAD_VIDEOCALL_PATH', '/blocks/padplusvideocall/bbbpad_videocall.php');

/**
 * Build meeting data.
 *
 * @return object
 */
function generate_meeting_data() {
    $meetingid = pad_guid_v4();
    $modpw = bigbluebuttonbn_random_password(12);
    $viewerpw = bigbluebuttonbn_random_password(12, $modpw);
    return array(
        'meetingid' => $meetingid,
        'modpw' => $modpw,
        'viewerpw' => $viewerpw,
    );
}

/**
 * Build PAD+ videocall URL with given params. This is the URL to bbbpad_videocall controller.
 *
 * @param array|object  $params params to be passed to PHP http_build_query.
 * @return string       The full videocall URL with encoded params.
 */
function build_videocall_full_url($params) {
    global $CFG;

    $httpparams = http_build_query($params, '', '&');
    return $CFG->wwwroot . BBBPAD_VIDEOCALL_PATH . '?' . $httpparams;
}

/**
 * Build the PAD+ URL for deferred videocall creation. This URL is generated when a user requests
 * a videocall link in block.
 *
 * @param string|int    $meetingid meeting id to create and join on the BigBlueButton server.
 * @param string|int    $modpw moderator password.
 * @param string|int    $viewerpw viewer password for meeting creation.
 * @param string|int    $contextid the context in which user should have block/padplusvideocall:createvideocall capability.
 * @return string       The URL to initiate a video call through PAD+.
 */
function get_videocall_create_later_url($meetingid, $modpw, $viewerpw, $contextid) {
    $createparams = array(
        'action' => 'createjoin',
        'meetingid' => $meetingid,
        'modpw' => $modpw,
        'viewerpw' => $viewerpw,
        'contextid' => $contextid
    );

    return build_videocall_full_url($createparams);
}

/**
 * Build the PAD+ URL for immediate videocall creation. This URL is called when a user clicks on
 * a 'launch videocall' in block or profile page, for example.
 *
 * @param string|int    $contextid the context in which user should have block/padplusvideocall:createvideocall capability.
 * @param array         $viewersid list of viewers ids to send notification to.
 * @return string       The URL to initiate a video call through PAD+.
 */
function get_videocall_create_now_url($contextid, $viewersid = array()) {
    $createparams = array(
        'action' => 'createjoinnow',
        'contextid' => $contextid
    );
    if (count($viewersid) > 0) {
        $createparams['viewersid'] = implode(',', $viewersid);
    }

    return build_videocall_full_url($createparams);
}

/**
 * Build the PAD+ URL for videocall invitation. This URL is sent to viewers through the notification system.
 *
 * @param string    $meetingid meeting id to join on the BigBlueButton server.
 * @param string    $viewerpw viewer password to join the meeting on the BigBlueButton server.
 * @return string   The URL to join a video call through PAD+ controller.
 */
function get_videocall_join_url($meetingid, $viewerpw) {
    $joinparams = array(
        'action' => 'join',
        'meetingid' => $meetingid,
        'viewerpw' => $viewerpw
    );
    return build_videocall_full_url($joinparams);
}

/**
 * Build the PAD+ URL to redirect the user when leaving BigBlueButton. It can also take an optional message
 * which will override the default message.
 *
 * @param string    $message replace the default message with this one if given.
 * @return string   The URL to redirect the user after leaving BigBlueButton.
 */
function get_videocall_logout_url($message = '') {
    $logoutparams = array(
        'action' => 'logout'
    );
    if (strlen($message) > 0) {
        $logoutparams['message'] = $message;
    }
    return build_videocall_full_url($logoutparams);
}

/**
 * Build a version 4 GUID to be used as meeting id (for example).
 *
 * @see https://www.php.net/manual/en/function.uniqid.php#94959
 * @return string   A V4 GUID.
 */
function pad_guid_v4() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      // 32 bits for "time_low"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff),
      // 16 bits for "time_mid"
      mt_rand(0, 0xffff),
      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand(0, 0x0fff) | 0x4000,
      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand(0, 0x3fff) | 0x8000,
      // 48 bits for "node"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

/**
 * Send videocall notification to given viewer with the invitation URL.
 *
 * @param object    $moderator user which has initiated the video call.
 * @param object    $viewer user to whom to send the notification.
 * @param string    $viewerurl invitation URL for viewer.
 * @return mixed    the integer ID of the new message or false if there was a problem with submitted data
 */
function send_videocall_notification($moderator, $viewer, $viewerurl) {
    $message = new \core\message\message();

    // Send as notification (web, email, depending on defaults/user choice).
    $message->notification = 1;
    $message->component = 'block_padplusvideocall';
    $message->name = 'videocall_notification';
    $message->userfrom = $moderator;
    $message->userto = $viewer;
    $moderatorname = fullname($moderator);

    $message->subject = get_string('notification_subject', 'block_padplusvideocall', $moderatorname);

    // Default HTML format for notification.
    $hello = get_string('notification_hello', 'block_padplusvideocall', $viewer->firstname);
    $bodyhtml = get_string('notification_bodyhtml', 'block_padplusvideocall', $moderatorname);
    $actionurl = html_writer::link(
        $viewerurl,
        get_string('notification_action', 'block_padplusvideocall'),
        array(
            'target' => '_blank',
            'class' => 'btn btn-primary'
        ));
    $message->fullmessageformat = FORMAT_HTML;
    $message->fullmessagehtml = "<p>{$hello}</p><p>{$bodyhtml}</p>{$actionurl}";

    // Backup format for non-HTML reader.
    $bodyraw = get_string('notification_bodyraw', 'block_padplusvideocall', $moderatorname);
    $message->fullmessage = "{$bodyraw}{$viewerurl}";

    // Small message format for Moodle chat (can be HTML too).
    $message->smallmessage = $message->fullmessagehtml;

    // Direct link when clicking on a notification.
    $message->contexturl = $viewerurl;
    $message->contexturlname = get_string('notification_contexturlname', 'block_padplusvideocall');

    return message_send($message);
}

/**
 * Send videocall reminder to oneself with moderator link.
 *
 * @param object    $moderator user which has requested a video call.
 * @param string    $moderatorurl creation URL for moderator.
 * @return mixed    the integer ID of the new message or false if there was a problem with submitted data
 */
function send_moderator_link_reminder($moderator, $moderatorurl) {
    $subject = get_string('reminder_moderator_subject', 'block_padplusvideocall');

    $moderatorname = fullname($moderator);
    $hello = get_string('notification_hello', 'block_padplusvideocall', $moderatorname);

    $bodyhtml = get_string('reminder_moderator_bodyhtml', 'block_padplusvideocall');
    $actionurl = html_writer::link($moderatorurl, get_string('reminder_moderator_action', 'block_padplusvideocall'));
    $htmlmessage = <<<END_HTML
    <p>{$hello}</p>
    <p>{$bodyhtml}</p>
    <p>{$moderatorurl}</p>
    <p>{$actionurl}</p>
    END_HTML;

    $bodyraw = get_string('reminder_moderator_bodyraw', 'block_padplusvideocall');
    $rawmessage = <<<END_RAW
    {$hello}\n
    {$bodyraw}\n
    {$moderatorurl}\n
    END_RAW;

    return send_reminder($moderator, $subject, $htmlmessage, $rawmessage);
}

/**
 * Send videocall reminder to oneself with viewer link.
 *
 * @param object    $moderator user which has requested a video call.
 * @param string    $viewerurl invitation URL for viewer.
 * @return mixed    the integer ID of the new message or false if there was a problem with submitted data
 */
function send_viewer_link_reminder($moderator, $viewerurl) {
    $subject = get_string('reminder_viewer_subject', 'block_padplusvideocall');

    $moderatorname = fullname($moderator);
    $hello = get_string('notification_hello', 'block_padplusvideocall', $moderatorname);

    $bodyhtml = get_string('reminder_viewer_bodyhtml', 'block_padplusvideocall');
    $actionurl = html_writer::link($viewerurl, get_string('reminder_viewer_action', 'block_padplusvideocall'));
    $htmlmessage = <<<END_HTML
    <p>{$hello}</p>
    <p>{$bodyhtml}</p>
    <p>{$viewerurl}</p>
    <p>{$actionurl}</p>
    END_HTML;

    $bodyraw = get_string('reminder_viewer_bodyraw', 'block_padplusvideocall');
    $rawmessage = <<<END_RAW
    {$hello}\n
    {$bodyraw}\n
    {$viewerurl}\n
    END_RAW;

    return send_reminder($moderator, $subject, $htmlmessage, $rawmessage);
}

/**
 * Send reminder message to oneself.
 *
 * @param object $moderator user which has initiated the video call.
 * @param string $subject message subject.
 * @param string $htmlmessage message body in html format.
 * @param string $rawmessage message body in raw format for non-HTML reader.
 * @return mixed the integer ID of the new message or false if there was a problem with submitted data
 */
function send_reminder($moderator, $subject, $htmlmessage, $rawmessage) {
    $message = new \core\message\message();
    $message->notification = 1;
    $message->component = 'block_padplusvideocall';
    $message->name = 'videocall_reminder';
    $message->userfrom = $moderator;
    $message->userto = $moderator;
    $message->subject = $subject;
    $message->fullmessageformat = FORMAT_HTML;
    $message->fullmessagehtml = $htmlmessage;
    $message->fullmessage = $rawmessage;

    return message_send($message);
}

/**
 * Log videocall events for administration reports.
 *
 * @param string    $eventtype either created, joined, or left event type.
 * @param object    $context context in which the event happened.
 */
function bbbpad_log_event($eventtype, $context) {
    $params = array('context' => $context);

    $eventname = "videocall_{$eventtype}";
    $event = call_user_func_array(
        '\block_padplusvideocall\event\\'.$eventname.'::create',
        array($params)
    );
    $event->trigger();
}

/**
 * Extend navbar output (next to notification icon) to trigger enhanced notification (sound+animation).
 * This is triggered only when an unread videocall notification appears in the last 5 notifications.
 */
function block_padplusvideocall_render_navbar_output() {
    global $USER;

    $output = '';

    if (core_user::is_real_user($USER->id)) { // Skip notification for anonymous user on homepage.
        $notifications = \message_popup\api::get_popup_notifications($USER->id, 'DESC', 5);
        foreach ($notifications as $notification) {
            if ($notification->eventtype == 'videocall_notification' && $notification->timeread == null) {
                $notificationvideocall = new \moodle_url('/blocks/padplusvideocall/medias/notificationvideocall.mp3');
                $output .= "<audio autoplay class='notification-videocall'><source src='$notificationvideocall' ></source></audio>";
                break;
            };
        }
    }

    return $output;
}
