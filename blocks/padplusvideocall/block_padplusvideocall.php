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

class block_padplusvideocall extends block_base {

    public function init() {
        $this->title = get_string('padplusvideocall', 'block_padplusvideocall');
    }

    public function get_content() {
        global $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;

        $categorycontext = get_top_category_context_with_capability('block/padplusvideocall:invitevideocall');
        if (! $categorycontext) {
            // Do not display block if user has no capability to invite/create a video call.
            $this->content->text = '';
            return $this->content;
        }

        $this->content->text = $OUTPUT->render_from_template(
            'block_padplusvideocall/main',
            $this->export_for_template($categorycontext)
        );

        return $this->content;
    }

    public function export_for_template($categorycontext) {
        $createvideocallurl = get_videocall_create_now_url($categorycontext->id);
        $videocallform = new block_padplusvideocall\videocall_form($createvideocallurl, (object) ['context' => $categorycontext]);

        return array(
            'videocallformhtml' => $videocallform->render_html(),
            'contextid' => $categorycontext->id
        );
    }
}
