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
 * Custom initialisation for the myoverview block on my-courses page.
 *
 * This effectively decouples this view from user preferences on dashboard.
 * - set custom start configuration so that user always falls back on the same view by default
 * - do no save user choices as preferences so as to not impact dashboard view
 *
 * Note this happens at runtime, so user may still catch a glimpse of its dashboard preferences
 * set in the backend generated template before the script replaces them.
 */

 define(
    [
        'jquery',
        'core/custom_interaction_events',
        'block_myoverview/selectors',
        'block_myoverview/view',
        'local_padplusextensions/mycourses_nav'
    ],
    function(
        $,
        CustomEvents,
        Selectors,
        View,
        MyCoursesNav
    ) {
        /**
         * Custom start configuration for the overview block.
         */
        const MYCOURSES_CONFIG = {
            grouping: 'allincludinghidden', // BLOCK_MYOVERVIEW_GROUPING_ALLINCLUDINGHIDDEN
            sort: 'ul.timeaccess desc', // Data-value for lastaccessed criteria (see nav-sort-selector.mustache)
            display: 'card', // BLOCK_MYOVERVIEW_VIEW_CARD
            paging: 12 // BLOCK_MYOVERVIEW_PAGING_12
        };

        /**
         * Initialise all of the modules for the overview block.
         *
         * @param {object} root The root element for the overview block.
         */
        var init = function(root) {
            root = $(root);
            // Override user preferences with start configuration for the courses-view component
            const dataRegion = root.find(Selectors.courseView.region);
            for (const [filter, value] of Object.entries(MYCOURSES_CONFIG)) {
                dataRegion.attr('data-' + filter, value);
            }

            // Proceed with component initialization.
            // Quirks: use setTimeout to delay initialization until next cycle.
            // Otherwise dropdown listeners are not triggered (presumable because not yet installed in current cycle).
            setTimeout(() => {
                // Filter and display select boxes are built on backend with user preferences.
                // We trigger start items to mirror the start configuration at runtime.
                const groupingStartItem =
                    root.find(`${MyCoursesNav.Selectors.FILTER_OPTION}[data-value=${MYCOURSES_CONFIG.grouping}]`);
                $(groupingStartItem).trigger(CustomEvents.events.activate);
                const displayStartItem =
                    root.find(`${MyCoursesNav.Selectors.DISPLAY_OPTION}[data-value=${MYCOURSES_CONFIG.display}]`);
                $(displayStartItem).trigger(CustomEvents.events.activate);

                // Initialise the course navigation elements.
                MyCoursesNav.init(root);
                // Initialise the courses view modules.
                View.init(root);
            });
        };

        return {
            init: init
        };
    });
