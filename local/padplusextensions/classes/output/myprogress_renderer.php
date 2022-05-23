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

namespace local_padplusextensions\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/padplusextensions/progresslib.php');

use plugin_renderer_base;

/**
 * PAD+ custom renderer for My Progress page.
 */
class myprogress_renderer extends plugin_renderer_base {

    /**
     * Render 'My progress' main content.
     */
    public function render_main() {
        $professionnalctx = get_top_category_context_for_professional();
        $templatecontext =
            $professionnalctx ? $this->context_for_professional($professionnalctx) : $this->context_for_student();

        return $this->render_from_template('local_padplusextensions/myprogress_main', $templatecontext);
    }

    public function context_for_professional($ctx) {
        global $USER;

        $mycourses = enrol_get_all_users_courses($USER->id);
        $uniquestudents = get_students_in_courses($mycourses);
        $studentlist = array_map(
            fn($user) => array('value' => $user->id, 'label' => fullname($user)),
            $uniquestudents);

        return array(
            'students' => $studentlist,
            'contextid' => $ctx->id,
            'visible' => true, // For loading overlay.
        );
    }

    public function context_for_student() {
        global $USER;
        return array(
            'studentid' => $USER->id,
            'visible' => true, // For loading overlay.
        );
    }
}
