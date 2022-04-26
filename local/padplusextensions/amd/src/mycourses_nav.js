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
 * Manage the timeline view navigation for the overview block.
 * This has the same behavior as the original without saving user preferences.
 *
 * @see /block_myoverview/view_nav module for original code.
 */

define(
[
    'jquery',
    'core/custom_interaction_events',
    'block_myoverview/view',
    'block_myoverview/selectors'
],
function(
    $,
    CustomEvents,
    View,
    Selectors
) {

    var SELECTORS = {
        FILTERS: '[data-region="filter"]',
        FILTER_OPTION: '[data-filter]',
        DISPLAY_OPTION: '[data-display-option]'
    };

    /**
     * Event listener for the Display filter (cards, list).
     *
     * @param {object} root The root element for the overview block
     */
    var registerSelector = function(root) {

        var Selector = root.find(SELECTORS.FILTERS);

        CustomEvents.define(Selector, [CustomEvents.events.activate]);
        Selector.on(
            CustomEvents.events.activate,
            SELECTORS.FILTER_OPTION,
            function(e, data) {
                var option = $(e.target);

                if (option.hasClass('active')) {
                    // If it's already active then we don't need to do anything.
                    return;
                }

                var filter = option.attr('data-filter');
                var customfieldvalue = option.attr('data-customfieldvalue');

                root.find(Selectors.courseView.region).attr('data-' + filter, option.attr('data-value'));

                if (customfieldvalue) {
                    root.find(Selectors.courseView.region).attr('data-customfieldvalue', customfieldvalue);
                }

                // Reset the views.
                View.init(root);

                data.originalEvent.preventDefault();
            }
        );

        CustomEvents.define(Selector, [CustomEvents.events.activate]);
        Selector.on(
            CustomEvents.events.activate,
            SELECTORS.DISPLAY_OPTION,
            function(e, data) {
                var option = $(e.target);

                if (option.hasClass('active')) {
                    return;
                }

                root.find(Selectors.courseView.region).attr('data-display', option.attr('data-value'));
                View.reset(root);
                data.originalEvent.preventDefault();
            }
        );
    };

    /**
     * Initialise the timeline view navigation by adding event listeners to
     * the navigation elements.
     *
     * @param {object} root The root element for the myoverview block
     */
    var init = function(root) {
        root = $(root);
        registerSelector(root);
    };

    return {
        Selectors: SELECTORS,
        init: init
    };
});
