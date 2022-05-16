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

require_once($CFG->dirroot . '/local/padplusextensions/lib.php');
require_once($CFG->dirroot . '/local/padplusextensions/progresslib.php');

class block_padplusmyprogress extends block_base {

    public function init() {
        $this->title = get_string('myprogress-student-title', 'theme_padplus');
    }

    public function get_content() {
        global $OUTPUT, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;

        $professionalctx = get_top_category_context_for_professional();
        if ($professionalctx) {
            // Do not display block for professionals, only for students.
            $this->content->text = '';
            return $this->content;
        }

        $templatecontext = compute_total_courses_by_status($USER->id);
        $this->content->text = $OUTPUT->render_from_template(
            'block_padplusmyprogress/main',
            $templatecontext
        );

        return $this->content;
    }
}
