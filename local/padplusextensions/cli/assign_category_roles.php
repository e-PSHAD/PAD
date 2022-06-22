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
 * CLI script to assign users to category roles.
 *
 * @package     theme_padplus
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir.'/csvlib.class.php');

if (moodle_needs_upgrading()) {
    cli_error("Moodle upgrade pending, export execution suspended.");
}

// Increase time and memory limit.
core_php_time_limit::raise();
raise_memory_limit(MEMORY_EXTRA);

// Emulate normal session - we use admin account by default, set language to the site language.
cron_setup_user();
$USER->lang = $CFG->lang;

// Extract CLI params.
list($clioptions, $args) = cli_get_params(array());
$filename = $args[0];

// Load CSV content.
$iid = \csv_import_reader::get_new_iid('assigncategoryroles');
$cir = new \csv_import_reader($iid, 'assigncategoryroles');
$cir->load_csv_content(file_get_contents($filename), 'UTF-8', 'comma');
$csvloaderror = $cir->get_error();
if (!is_null($csvloaderror)) {
    cli_error(get_string('csvloaderror', 'error', $csvloaderror), 1);
}

// Read header.
$columns = $cir->get_columns();
$nbusers = count($columns) - 2;
print("-- Header --\n");
print(implode(' | ', $columns));
print("\nMax users per row: {$nbusers}\n");

// Process rows.
print("-- Rows --\n");
$cir->init();
while ($line = $cir->next()) {
    list($categoryname, $rolename) = $line;
    $usernames = array_slice($line, 2, $nbusers);

    if (!$category = $DB->get_record('course_categories', ['name' => $categoryname])) {
        die("ERROR - category {$categoryname} not found\n");
    }

    if (!$role = $DB->get_record('role', ['shortname' => $rolename])) {
        die("ERROR - role {$rolename} not found\n");
    }

    $context = context_coursecat::instance($category->id);
    print("- Assigning role '{$role->shortname}' ({$role->id}) in category '{$category->name}' ({$category->id})\n");

    // Loop over users with given category context and role.
    foreach ($usernames as $username) {
        if (!empty($username)) { // Ignore empty username.
            if (!$user = $DB->get_record('user', ['username' => $username])) {
                die("ERROR - user {$username} not found\n");
            }

            // Function role_assign is idempotent w.r.t existing role assignment.
            $id = role_assign($role->id, $user->id, $context->id);
            print("User {$user->username} ({$user->id}) found -> assigned {$id}\n");
        }
    }
}
$cir->close();
$cir->cleanup(true);
