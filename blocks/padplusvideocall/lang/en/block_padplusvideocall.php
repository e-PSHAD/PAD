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

$string['pluginname'] = 'PAD+ video conference';
$string['padplusvideocall'] = 'Video conference';

// Capabilities.
$string['padplusvideocall:addinstance'] = 'Add a new video conference block';
$string['padplusvideocall:myaddinstance'] = 'Add a new video conference block to the My Moodle page';
$string['padplusvideocall:createvideocall'] = 'Create video conferences';

// Notifications.
$string['messageprovider:videocall_notification'] = 'Video conference notification';
$string['notification_subject'] = '{$a} invites you to a video call';
$string['notification_hello'] = 'Hello {$a},';
$string['notification_bodyhtml'] = '<strong>{$a}</strong> invites you to join a video conference.<br />Click on button below to join the conference in a new window.';
$string['notification_bodyraw'] = '{$a} invites you to join a video conference. Use the following link: ';
$string['notification_action'] = 'Join';
$string['notification_contexturlname'] = 'Video call';
// Notification: link Reminder.
$string['messageprovider:videocall_reminder'] = 'Video conference link reminder';
$string['reminder_subject'] = 'Link for videoconference';
$string['reminder_bodyhtml'] = 'This is the connection link to your videoconference.<br />You can add it to a calendar event and share it with other participants.<br />As a reminder, you must connect before others to start the meeting.';
$string['reminder_bodyraw'] = 'This is the connection link to your videoconference. You can add it to a calendar event and share it with other participants. As a reminder, you must connect before others to start the meeting.';
$string['reminder_action'] = 'Click on this link to join the videoconference';

// UI.
$string['addparticipants'] = 'Invite participants';
$string['addparticipants_placeholder'] = 'Enter a name';
$string['direct_mode_description'] = 'You can invite any people with a platform account.';
$string['launch'] = 'Launch meeting';
$string['callfromprofile'] = 'Video call';
$string['cancel_link'] = 'Back';
$string['copied_link'] = 'Copied link';
$string['copy_link'] = 'Copy videoconference link';
$string['request_link'] = 'Create a videoconference link';
$string['joinvideocall_leftmeeting'] = 'This tab (or window) should be closed manually.';
$string['joinvideocall_nomeeting'] = 'The meeting is over.';
$string['bigbluebutton_welcome'] = 'Welcome!';
$string['bigbluebutton_moderatormessage'] = 'You can invite a PAD+ participant by sending him this link {$a}';
$string['select_videocall_mode'] = 'I want to create a meeting';
$string['select_videocall_direct'] = 'Now';
$string['select_videocall_link'] = 'To plan it by sharing an invitation link';
$string['shared_link'] = 'Meeting link to share';
$string['meeting_link'] = 'Your link to keep and share';
$string['shared_link_description'] = 'The video conference will not be active until you are present.';
$string['shared_link_subdescription'] = 'You will also receive the shared link by email.';

// Log events.
$string['eventvideocallcreated'] = 'Video call created';
$string['eventvideocalljoined'] = 'Video call joined';
$string['eventvideocallleft'] = 'Video call left';
