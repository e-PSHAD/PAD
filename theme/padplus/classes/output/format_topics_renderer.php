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

namespace theme_padplus\output;

use completion_info,
    html_writer,
    stdClass;

/**
 * Basic renderer for topics format.
 *
 */
class format_topics_renderer extends \format_topics_renderer {
    /**
     * Generate a summary of a section for display on the 'course index page'
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param array    $mods (argument not used)
     * @return string HTML to output.
     */
    protected function section_summary($section, $course, $mods) {
        $classattr = 'section main section-summary clearfix';
        $linkclasses = '';

        // If section is hidden then display grey section link.
        if (!$section->visible) {
            $classattr .= ' hidden';
            $linkclasses .= ' dimmed_text';
        } else if (course_get_format($course)->is_section_current($section)) {
            $classattr .= ' current';
        }

        $title = get_section_name($course, $section);
        $o = '';
        $o .= html_writer::start_tag('li', [
            'id' => 'section-'.$section->section,
            'class' => $classattr,
            'role' => 'region',
            'aria-label' => $title,
            'data-sectionid' => $section->section
        ]);

        /*** PADPLUS: Deleted containers to fix page style */
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        if ($section->uservisible) {
            $title = html_writer::tag('a', $title,
                    array('href' => course_get_url($course, $section->section), 'class' => $linkclasses));
        }
        $o .= $this->output->heading($title, 3, 'section-title');

        $o .= $this->section_availability($section);
        $o .= html_writer::start_tag('div', array('class' => 'summarytext'));

        if ($section->uservisible || $section->visible) {
            // Show summary if section is available or has availability restriction information.
            // Do not show summary if section is hidden but we still display it because of course setting.
            // "Hidden sections are shown in collapsed form".
            $o .= $this->format_summary_text($section);
        }
        $o .= html_writer::end_tag('div');

        /*** PADPLUS: Container added to be able to set the section progression style */
        $o .= html_writer::start_tag('div', array('class' => 'summary-card-bottom'));
        $o .= $this->section_activity_summary($section, $course, null);
        $o .= html_writer::end_tag('div');
        /*** PADPLUS END */
        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');

        return $o;
    }

    /**
     * Generate a summary of the activites in a section
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course the course record from DB
     * @param array    $mods (argument not used)
     * @return string HTML to output.
     */
    protected function section_activity_summary($section, $course, $mods) {
        $modinfo = get_fast_modinfo($course);
        if (empty($modinfo->sections[$section->section])) {
            return '';
        }

        // Generate array with count of activities in this section.
        $sectionmods = array();
        $total = 0;
        $complete = 0;
        $cancomplete = isloggedin() && !isguestuser();
        $completioninfo = new completion_info($course);
        foreach ($modinfo->sections[$section->section] as $cmid) {
            $thismod = $modinfo->cms[$cmid];

            if ($thismod->uservisible) {
                if (isset($sectionmods[$thismod->modname])) {
                    $sectionmods[$thismod->modname]['name'] = $thismod->modplural;
                    $sectionmods[$thismod->modname]['count']++;
                } else {
                    $sectionmods[$thismod->modname]['name'] = $thismod->modfullname;
                    $sectionmods[$thismod->modname]['count'] = 1;
                }
                if ($cancomplete && $completioninfo->is_enabled($thismod) != COMPLETION_TRACKING_NONE) {
                    $total++;
                    $completiondata = $completioninfo->get_data($thismod, true);
                    if ($completiondata->completionstate == COMPLETION_COMPLETE ||
                            $completiondata->completionstate == COMPLETION_COMPLETE_PASS) {
                        $complete++;
                    }
                }
            }
        }

        if (empty($sectionmods)) {
            // No sections.
            return '';
        }

        /*** PADPLUS: Return section activities summary & session progress. */
        $a = new stdClass;
        $a->complete = $complete;
        $a->total = $total;

        // Output section activities summary.
        $o = '';
        $o .= html_writer::start_tag('div', array('class' => 'section-summary-activities pr-2 mdl-right'));
        $label = ($total == 1) ? 'activity-session' : 'activities-session';
        $o .= html_writer::tag('span',
            get_string($label, 'theme_padplus', $a),
            array('class' => 'activity-count'));

        foreach ($sectionmods as $mod) {
            $o .= html_writer::start_tag('span', array('class' => 'activity-count'));
            $o .= $mod['count'] . ' ' . $mod['name'];
            if ($mod != end($sectionmods)) {
                $o .= ', ';
            }
            $o .= html_writer::end_tag('span');
        }
        $o .= html_writer::end_tag('div');

        // Output section completion data.
        $o .= html_writer::start_tag('div', array('class' => 'section-summary-activities pr-2 mdl-right'));
        $o .= html_writer::tag('div', get_string('progress-session', 'theme_padplus') . ' ', array('class' => 'activity-count'));
        $o .= html_writer::tag('span', $a->complete . ' / ' . $a->total, array('class' => 'activity-count'));
        $o .= html_writer::end_tag('div');
        /*** PADPLUS END */

        return $o;
    }

}
