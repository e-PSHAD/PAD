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
 * Display overall progress for student (current user or selected by teacher).
 */

require_once("../config.php");
require_once($CFG->dirroot . '/local/padplusextensions/lib.php');

require_login(null, false);

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_url('/my/progress.php');
$PAGE->set_pagetype('my-progress');
$PAGE->set_pagelayout('base');

$professionnalctx = get_top_category_context_for_professional();
$myprogresstitle = get_string( $professionnalctx ? 'myprogress-professional-title' : 'myprogress-student-title', 'theme_padplus');
$PAGE->set_title("$SITE->shortname: $myprogresstitle");
$PAGE->set_heading($myprogresstitle);

echo $OUTPUT->header();
echo $OUTPUT->skip_link_target();

$myprogressrenderer = $PAGE->get_renderer('local_padplusextensions', 'myprogress');
$content = $myprogressrenderer->render_main();
echo $content;

// Trigger progress page has been viewed event.
$eventparams = array('context' => $context);
$event = \local_padplusextensions\event\myprogress_viewed::create($eventparams);
$event->trigger();

echo $OUTPUT->footer();
