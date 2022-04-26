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

namespace block_padplusvideocall;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

use moodleform;

class videocall_form extends moodleform {

    public function definition() {
        $attributes = array(
            'placeholder' => get_string('addroomname_placeholder', 'block_padplusvideocall')
        );
        $this->_form->addElement('text', 'videocallname', get_string('addroomname', 'block_padplusvideocall'), $attributes);
        $this->_form->setType('videocallname', PARAM_ALPHANUM);

        $attributes = array(
            'ajax' => 'block_padplusvideocall/form_user_selector',
            'multiple' => true,
            'contextid' => $this->_customdata->context->id,
            'placeholder' => get_string('addparticipants_placeholder', 'block_padplusvideocall')
        );
        $this->_form->addElement(
            'autocomplete',
            'videocallviewers',
            get_string('addparticipants', 'block_padplusvideocall'),
            array(), // Empty options at start.
            $attributes);

        $this->_form->addElement('submit', 'launch-videocall', get_string('launch', 'block_padplusvideocall'));
    }

    public function render_html() {
        ob_start();
        $this->display();
        $formhtml = ob_get_contents();
        ob_end_clean();
        return $formhtml;
    }
}
