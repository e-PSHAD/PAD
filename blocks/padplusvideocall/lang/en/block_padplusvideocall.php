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

$string['pluginname'] = 'PAD+ video conference block';
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

// UI.
$string['addparticipants'] = 'Invite participants';
$string['addparticipants_placeholder'] = 'Enter a name';
$string['addroomname'] = 'Create a meeting room name';
$string['addroomname_placeholder'] = 'Ex: monthly interview with...';
$string['blockintro'] = 'Launch a video conference in a new window. Both fields are optional. You can invite external people from within the conference.';
$string['launch'] = 'Launch meeting';
$string['callfromprofile'] = 'Video call';
$string['joinvideocall_leftmeeting'] = 'This tab (or window) should be closed manually.';
$string['joinvideocall_nomeeting'] = 'The meeting is over.';
$string['bigbluebutton_welcome'] = 'Welcome!';
$string['bigbluebutton_moderatormessage'] = 'You can invite a PAD+ participant by sending him this link {$a}';

// Log events.
$string['eventvideocallcreated'] = 'Video call created';
$string['eventvideocalljoined'] = 'Video call joined';
$string['eventvideocallleft'] = 'Video call left';
