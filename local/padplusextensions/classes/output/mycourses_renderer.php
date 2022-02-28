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

use plugin_renderer_base;

/**
 * PAD+ custom renderer for My Courses page.
 */
class mycourses_renderer extends plugin_renderer_base {

    /**
     * Render 'My courses' main content.
     */
    public function render_main() {
        $bmo = block_instance('myoverview');
        $bmo->page = $this->page;
        $blockcontent = $bmo->get_content()->text;

        return "<div>$blockcontent</div>";
    }
}
