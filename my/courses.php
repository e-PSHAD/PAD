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
 * Lists the course categories
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once("../config.php");
require_once($CFG->dirroot . '/local/padplusextensions/lib.php');

require_login(null, false);

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_url('/my/courses.php');
$PAGE->set_pagetype('my-courses');
$PAGE->set_pagelayout('base');
$mysequencesstr = get_string('mycourses-page', 'theme_padplus');
$PAGE->set_title("$SITE->shortname: $mysequencesstr");
$PAGE->set_heading($mysequencesstr);

$categorycontext = get_top_category_context_with_capability('moodle/course:create');
if ($categorycontext) {
    $url = new moodle_url('/course/edit.php', array('category' => $categorycontext->instanceid));
    $addnewcourse = html_writer::link($url, get_string('addnewcourse'), array('class' => 'btn btn-primary btn-add-course'));
    $PAGE->set_button($addnewcourse);
}

echo $OUTPUT->header();
echo $OUTPUT->skip_link_target();

$mycoursesrenderer = $PAGE->get_renderer('local_padplusextensions', 'mycourses');
$content = $mycoursesrenderer->render_main();
echo $content;

// Trigger my courses page has been viewed event.
$eventparams = array('context' => $context);
$event = \local_padplusextensions\event\mycourses_viewed::create($eventparams);
$event->trigger();

echo $OUTPUT->footer();
