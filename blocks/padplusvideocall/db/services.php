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

$functions = array(
    // Look up user by matching name or other identity fields.
    //
    // This is directly inspired by core_user_search_identity service,
    // with a different capability requirement.
    'block_padplusvideocall_search_identity' => array(
        'classname'     => 'block_padplusvideocall\external\bbbpad_external',
        'methodname'    => 'search_identity',
        'description'   => 'Search for users matching the given criteria in their name or other identity fields.',
        'type'          => 'read',
        'ajax'          => true,
        'loginrequired' => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        'capabilities'  => 'block/padplusvideocall:invitevideocall',
    ),
    // Generate meeting links for sharing.
    'block_padplusvideocall_generate_meeting_link' => array(
        'classname'     => 'block_padplusvideocall\external\bbbpad_external',
        'methodname'    => 'generate_meeting_link',
        'description'   => 'Generate a new meeting link to share.',
        'type'          => 'read',
        'ajax'          => true,
        'loginrequired' => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        'capabilities'  => 'block/padplusvideocall:invitevideocall',
    ),
);
