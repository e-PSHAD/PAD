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
 * Language file.
 *
 * @package   theme_padplus
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

// A description shown in the admin theme selector.
$string['choosereadme'] = 'Theme PAD+ is a child theme of Boost.';
// The name of our plugin.
$string['pluginname'] = 'PAD+';
// We need to include a lang string for each block region.
$string['region-side-pre'] = 'Right';

// Privacy.
$string['privacy:metadata'] = 'The PAD+ Theme plugin does not store any personal data.';

// Main strings.
$string['global-search'] = 'Search for courses, people...';
$string['myhome-welcome'] = 'Welcome {$a}';

// Sidebar menu strings.
$string['aria-main-nav'] = 'Main navigation';
$string['categories-menu-nav'] = 'Categories';
$string['allcategories-menu'] = 'All categories';
$string['allcourses-menu'] = 'All courses';
$string['workshop-menu'] = 'Additional workshops';
$string['catalog-menu'] = 'Professional resources';

// Login page strings.
$string['show-password'] = 'Show password';
$string['hide-password'] = 'Hide password';

// My Courses page strings.
$string['mycourses-page'] = 'My sequences';
$string['mycourses-page_help'] = "• My current sequences are those for which you are registered and have started.<br>
                                  • My upcoming sequences are those for which you are registered and whose start date is in the future.<br>
                                  • My past sequences are those that have been completed or whose deadline has passed.<br>
                                  • My favourite sequences are those you have added with the 'star' icon.";
$string['mycourse-page_help_label'] = 'Help with my sequences';
$string['eventmycoursesviewed'] = 'My courses viewed';
$string['mycatalog-courses'] = 'My professional resources';
$string['aria:mycatalog-courses'] = 'Show my professional resources';
$string['catalog-course'] = 'Professional resource';
$string['workshop-course'] = 'Additional workshop';

// My progress page strings.
$string['myprogress-professional-title'] = 'Students curriculum';
$string['goto-myprogress'] = 'Go to my curriculum';
$string['myprogress-professional-intro'] = 'You can track the progress of trainees enrolled in your sequences. They are sorted according to their main category.';
$string['myprogress-professional-intro-sub'] = 'The data displayed takes into account only what is done on the platform.';
$string['myprogress-student-title'] = 'My curriculum';
$string['myprogress-student-intro'] = 'Follow the progress of your sequences. They are sorted according to their main category.';
$string['myprogress-student-intro-note'] = 'The data displayed only takes into account what is done on the platform.';
$string['myprogress-subtitle'] = 'Distribution of sequences';
$string['myprogress-subtitle-description'] = 'The detail view displays the parent category of sequences and their progress.';
$string['myprogress-loading'] = 'Looking for student data...';
$string['eventmyprogressviewed'] = 'My progress viewed';
$string['user-progress'] = '{$a}\'s curriculum';
$string['user-progress-search-placeholder'] = 'Search for students';
$string['user-progress-no-selection'] = 'No student selected';
$string['course-singular'] = 'course';
$string['course-plural'] = 'courses';
$string['course-done'] = 'done course';
$string['course-done-plural'] = 'done courses';
$string['course-inprogress'] = 'in progress course';
$string['course-inprogress-plural'] = 'in progress courses';
$string['course-todo'] = 'todo course';
$string['course-todo-plural'] = 'todo courses';
$string['course-total'] = 'assigned course';
$string['course-total-plural'] = 'assigned courses';
$string['export-progress-intro'] = 'You can also export the tracking of all your students to an Excel file (.xls).';
$string['export-progress'] = 'Export progress for all students';
$string['undefined-platform'] = 'Undefined Platform';
$string['undefined-module'] = 'Undefined Module';
$string['show-progress-details'] = 'Show details';
$string['hide-progress-details'] = 'Hide details';

// Spreadsheet 'Students progress' strings.
$string['export-header-course'] = 'Course';
$string['export-header-module'] = 'Module';
$string['export-header-platform'] = 'Platform';
$string['export-header-progress'] = 'Progress (%)';
$string['export-header-status'] = 'Status';
$string['export-header-total'] = 'Course total';
$string['export-status-done'] = 'Done';
$string['export-status-inprogress'] = 'In progress';
$string['export-status-todo'] = 'To do';

// Theme settings strings.
$string['settings-color-title'] = 'Theme colours';
$string['settings-color-desc'] = 'Colours that define the identity of your site';
$string['settings-primarycolor'] = 'Primary colour';
$string['settings-primarycolor-desc'] = 'Colour used for the main items';
$string['settings-complementary-color'] = 'Complementary colour';
$string['settings-complementary-color-desc'] = 'Colour used for secondary items';
$string['settings-sidebarcolor'] = 'Sidebar background colour';
$string['settings-sidebarcolor-desc'] = 'Colour used for sidebar background';
$string['settings-footer'] = 'Footer';
$string['settings-footer-desc'] = 'Configuration of footer items';
$string['settings-helplink'] = 'Help Link';
$string['settings-helplink-desc'] = 'URL to external site hosting PAD+ help';
$string['settings-supportlink'] = 'Support';
$string['settings-supportlink-desc'] = 'URL to support form PAD+';
$string['settings-contactlink'] = 'Contact';
$string['settings-contactlink-desc'] = 'URL to contact form PAD+';
$string['settings-legalnoticeslink'] = 'Terms';
$string['settings-legalnoticeslink-desc'] = 'URL to legal notices PAD+';
$string['settings-privacylink'] = 'Privacy';
$string['settings-privacylink-desc'] = 'URL to privacy terms PAD+';
$string['settings-copyright'] = 'Copyright';
$string['settings-copyright-desc'] = 'Footer information';
$string['settings-sidebarmenu'] = 'Sidebar Menu';
$string['settings-sidebarmenu-desc'] = 'Display parameters for sidebar menu.';
$string['settings-workshopids-desc'] = "If user has access to one of the selected categories, display it with label 'Additional workshops' in the sidebar menu.";
$string['settings-catalogid-desc'] = "If user has access to the selected category, display it with label 'Professional resources' in the sidebar menu.";
$string['settings-catalogid-none'] = '[none]';
$string['settings-allcourses-desc'] = "Display first user-accessible category (not matched by above options) with label 'All sequences' in the sidebar menu.";
$string['settings-videocall'] = 'Video conference';
$string['settings-videocall-desc'] = 'BigBlueButton server parameters can be found under Site Administration / Plugins / Activity modules / BigBlueButton.';
$string['settings-videocallprofile'] = 'Video call on profile pages';
$string['settings-videocallprofile-desc'] = 'Display video call button on profile pages.';

// Enrolment strings.
$string['unenrolme'] = 'Unenrol me';

// Categories, course, section & activity page strings.
$string['actions-dropdown'] = 'Actions';
$string['addnewworkshop'] = 'Add new workshop';
$string['activity-session'] = '{$a->total} activity in this session:';
$string['activities-session'] = '{$a->total} activities in this session:';
$string['btn-coursebox'] = 'View this course';
$string['btn-workshopbox'] = 'More information';
$string['course-homepage'] = 'Course homepage';
$string['next-session'] = 'Next session';
$string['next-activity'] = 'Next activity';
$string['no-participants-enrolled'] = 'no students enrolled';
$string['participants-enrolled'] = 'Enrolled students';
$string['previous-session'] = 'Previous session';
$string['previous-activity'] = 'Previous activity';
$string['progress-session'] = 'Progress:';
$string['sidebar-summary-course'] = 'In this course';
$string['sidebar-summary-workshop'] = 'In this workshop';
$string['workshop-teacher'] = 'Organizer';
