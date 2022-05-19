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
$string['createvideocall_nocapability'] = 'You do not have the capability to create video conferences.';

// Notifications.
$string['messageprovider:videocall_notification'] = 'Video conference notification';
$string['notification_subject'] = '{$a} invites you to a video call';
$string['notification_hello'] = 'Hello {$a},';
$string['notification_bodyhtml'] = '<strong>{$a}</strong> invites you to join a video conference.<br />Click on button below to join the conference in a new window.';
$string['notification_bodyraw'] = '{$a} invites you to join a video conference. Use the following link: ';
$string['notification_action'] = 'Join';
$string['notification_contexturlname'] = 'Video call';
// Notification: link Reminder.
$string['messageprovider:videocall_reminder'] = 'Video conference links reminder';
$string['reminder_moderator_subject'] = 'Moderator link reminder for video call - DO NOT SHARE';
$string['reminder_moderator_bodyhtml'] = 'You can save this link to later create your video call. <strong>Do not share it with other participants!</strong>';
$string['reminder_moderator_bodyraw'] = 'You can save this link to later create your video call. DO NOT SHARE IT with other participants!';
$string['reminder_moderator_action'] = 'Click on this link to create the video call now';
$string['reminder_viewer_subject'] = 'Viewer link reminder for video call';
$string['reminder_viewer_bodyhtml'] = 'Send this link to other participants so that they can access the video call after creation.';
$string['reminder_viewer_bodyraw'] = 'Send this link to other participants so that they can access the video call after creation.';
$string['reminder_viewer_action'] = 'Click on this link to join the video call now';

// UI.
$string['addparticipants'] = 'Invite participants';
$string['addparticipants_placeholder'] = 'Enter a name';
$string['addroomname'] = 'Create a meeting room name';
$string['addroomname_placeholder'] = 'Ex: monthly interview with...';
$string['blockintro'] = 'Launch a video conference in a new window. Both fields are optional. You can invite external people from within the conference.';
$string['launch'] = 'Launch meeting';
$string['callfromprofile'] = 'Video call';
$string['cancel'] = 'Cancel';
$string['copied_link'] = 'Copied link';
$string['create'] = 'Create a videoconference link';
$string['joinvideocall_leftmeeting'] = 'This tab (or window) should be closed manually.';
$string['joinvideocall_nomeeting'] = 'The meeting is over.';
$string['bigbluebutton_welcome'] = 'Welcome!';
$string['bigbluebutton_moderatormessage'] = 'You can invite a PAD+ participant by sending him this link {$a}';
$string['select_videocall_mode'] = 'I want to create a meeting';
$string['select_videocall_unplanned'] = 'Now';
$string['select_videocall_planned'] = 'To plan it by sharing an invitation link';
$string['shared_link'] = 'Meeting link to share';
$string['shared_link_description'] = 'The video conference will not be active until you are present.';
$string['shared_link_moderator'] = 'Your link as moderator';
$string['shared_link_subdescription'] = 'You will also receive the sharing link by email.';
$string['shared_link_viewer'] = 'The link to share with guests';
$string['update'] = 'Create new link';

// Log events.
$string['eventvideocallcreated'] = 'Video call created';
$string['eventvideocalljoined'] = 'Video call joined';
$string['eventvideocallleft'] = 'Video call left';
