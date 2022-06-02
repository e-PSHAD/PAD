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
 * This script exports an Excel spreadsheet with students progress.
 */

require_once('../../../config.php');
require_once($CFG->dirroot . '/lib/excellib.class.php');
require_once($CFG->dirroot . '/local/padplusextensions/progresslib.php');

$contextid = required_param('contextid', PARAM_INT);
$PAGE->set_url('/local/padplusextensions/export/progress_xls.php', array('contextid' => $contextid));

require_login(null, false);

// This page is reserved to teachers (as contributors).
$context = \context::instance_by_id($contextid);
require_capability('moodle/course:create', $context);


/**
 * Write summary of courses by status in student worksheet.
 *
 * @param object $student
 * @param object $worksheet
 * @param int $rowstart the row index where to write the first header
 * @param int $colstart the column index where to write the first head
 * @param object $headerformat
 * @return int the last row index
 */
function write_student_courses_summary($student, $worksheet, $rowstart, $colstart, $headerformat) {
    $row = $rowstart;
    $totalbystatus = compute_total_courses_by_status($student->id)['totalByStatus'];
    foreach ($totalbystatus as $status => $state) {
        $str = get_string("course-$status-plural", 'theme_padplus');
        $worksheet->write_string($row, $colstart, $str, $headerformat);
        $worksheet->write_number($row, $colstart + 1, $state['count']);
        $row++;
    }

    return $row;
}


/**
 * Write details of courses in student worksheet.
 *
 * @param object $student
 * @param object $worksheet
 * @param int $rowstart the row index where to write the first header
 * @param object $headerformat
 * @return int the last row index
 */
function write_student_courses_details($student, $worksheet, $rowstart, $headerformat) {
    $row = $rowstart;

    $headers = ['platform', 'module', 'course', 'status', 'progress', 'total'];
    foreach ($headers as $col => $header) {
        $worksheet->write_string($row, $col, get_string("export-header-$header", 'theme_padplus'), $headerformat);
    }
    $row++;

    $progress = get_user_overall_progress($student->id);
    $coursetotals = retrieve_courses_totals($student->id);

    foreach ($progress as $platform) {
        foreach ($platform['modules'] as $module) {
            foreach ($module['courses'] as $course) {
                $progress = $course['progress'];
                $total = get_course_total($course['id'], $coursetotals);

                $coursedetails = [
                    $platform['name'],
                    $module['name'],
                    $course['fullname'],
                    get_string('export-status-' . $progress['status'], 'theme_padplus'),
                    $progress['percent'],
                    $total
                ];

                foreach ($coursedetails as $col => $item) {
                    $worksheet->write($row, $col, $item);
                }
                $row++;
            }
        }
    }

    return $row;
}

/**
 * Fill in workbook with students progress followed by user.
 *
 * @param int $userid
 * @param object $workwook
 */
function fill_in_students_progress($userid, $workbook) {
    $headerformat = $workbook->add_format(array('bold' => 1, 'size' => 12));

    $uniquestudents = get_all_students_in_user_courses($userid);

    // Create a worksheet per student.
    foreach ($uniquestudents as $student) {
        $sheettitle = fullname($student);
        $worksheet = $workbook->add_worksheet($sheettitle);

        // Extend column width.
        $worksheet->set_column(0, 5, 23);

        // Start with details.
        $rowstart = 0;
        $rownext = write_student_courses_details($student, $worksheet, $rowstart, $headerformat);

        // Append summary just below the Course/Status columns.
        $colstart = 2;
        write_student_courses_summary($student, $worksheet, $rownext + 1, $colstart, $headerformat);
    }

}

$workbook = new \MoodleExcelWorkbook("-");
$userdate = userdate(time(), '%d-%m-%Y');
$filename = clean_filename(get_string('myprogress-professional-title', 'theme_padplus') . " $userdate.xls");
$workbook->send($filename);

fill_in_students_progress($USER->id, $workbook);

// Send HTTP headers and file stream for download.
$workbook->close();
exit;
