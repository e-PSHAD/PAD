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
 * Custom initialisation for the myoverview block.
 * Do not save user choices as preferences, it seems to disturb them,
 * especially as the block appears on both dashboard and 'My Courses' page.
 *
 * For a working solution which decouples block from user preferences
 * on 'My courses' page only, see commit history:
 * - 'Customize myoverview block configuration on my-courses page'
 */

define(
[
    'jquery',
    'block_myoverview/view',
    'theme_padplus/myoverview_nav'
],
function(
    $,
    View,
    MyOverviewNav
) {
    /**
     * Initialise all of the modules for the overview block.
     *
     * @param {object} root The root element for the overview block.
     */
    var init = function(root) {
        root = $(root);
        // Initialise the course navigation elements.
        MyOverviewNav.init(root);
        // Initialise the courses view modules.
        View.init(root);
    };

    return {
        init: init
    };
});
