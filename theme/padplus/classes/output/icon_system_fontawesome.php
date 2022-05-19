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

class icon_system_fontawesome extends \core\output\icon_system_fontawesome {
    public function get_core_icon_map() {
        $iconmap = parent::get_core_icon_map();

        $iconmap['core:i/course'] = 'fa-folder-open'; // Override Moodle default fa-graduation-cap.
        $iconmap['core:i/dashboard'] = 'fa-th-large'; // Override Moodle default fa-tachometer.
        // bullhorn icon is only used for Moodle feedback system, which we won't activate.
        $iconmap['core:i/bullhorn'] = 'fa-video'; // Override Moodle default fa-bullhorn for video call.
        $iconmap['core:i/mycourses'] = 'fa-layer-group';
        $iconmap['core:i/myprogress'] = 'fa-user-graduate';
        $iconmap['core:i/halfcircle'] = 'fa-adjust'; // In-progress course icon.

        return $iconmap;
    }
}
