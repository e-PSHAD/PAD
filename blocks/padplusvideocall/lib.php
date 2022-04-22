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

define('BBBPAD_VIDEOCALL_PATH', '/blocks/padplusvideocall/bbbpad_videocall.php');

function get_videocall_full_url($params) {
    global $CFG;
    return $CFG->wwwroot . BBBPAD_VIDEOCALL_PATH . '?' . $params;
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

    $notifications = \message_popup\api::get_popup_notifications($USER->id, 'DESC', 5);
    foreach ($notifications as $notification) {
        if ($notification->eventtype == 'videocall_notification' && $notification->timeread == null) {
            $notificationvideocall = new \moodle_url('/blocks/padplusvideocall/medias/notificationvideocall.mp3');
            $output .= "<audio autoplay class='notification-videocall'><source src='$notificationvideocall' ></source></audio>";
            break;
        };
    }

    return $output;
}
