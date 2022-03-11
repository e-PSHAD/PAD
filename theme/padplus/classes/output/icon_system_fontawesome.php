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

        $iconmap['core:i/dashboard'] = 'fa-th-large'; // Override Moodle default fa-tachometer.
        $iconmap['core:i/mycourses'] = 'fa-layer-group';

        return $iconmap;
    }
}
