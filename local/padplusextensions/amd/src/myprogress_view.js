// This file is part of Moodle - https://moodle.org/
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

import Ajax from 'core/ajax';
import Autocomplete from 'core/form-autocomplete';
import Notification from 'core/notification';
import * as Str from 'core/str';
import Templates from 'core/templates';
import {computeSummaryDataForTopGroups} from './myprogress';

const Selectors = {
    DATA_CARRIER: '[data-studentid]',
    INPUT_STUDENT: '[data-input=student-selection]',
    INPUT_TOGGLE_DETAILS: '[data-input=toggle-details]',
    CONTAINER_LOADER: '[data-container=loader]',
    CONTAINER_MAIN_PROGRESS: '[data-container=main-progress]',
    CONTAINER_TOPGROUP: '[data-container=topgroup]',
    CONTAINER_SUMMARY: '[data-container=progress-summary]',
    CONTAINER_DETAILS: '[data-container=progress-details]',
};

const STRING_KEYS = [
    'user-progress-search-placeholder',
    'user-progress-no-selection',
    'show-progress-details',
    'hide-progress-details',
    'course-singular',
    'course-plural',
    'course-done',
    'course-done-plural',
    'course-inprogress',
    'course-inprogress-plural',
    'course-todo',
    'course-todo-plural',
];

/**
 * Fetch translated strings.
 *
 * Str.get_strings return strings in a array, which makes it impractical to retrieve later on.
 * This function remaps data into a map with their respective key.
 *
 * @return {object} a map of i18n strings indexed by key
 */
async function fetchStrings() {
    const strings = await Str.get_strings(
        STRING_KEYS.map(key => ({key, component: 'theme_padplus'}))
    );
    return strings.reduce((obj, value, index) => {
        obj[STRING_KEYS[index]] = value;
        return obj;
    }, {});
}

/**
 * Setup JS interactions for My Progress page.
 *
 * @param {Element} root
 */
export async function setupPage(root) {
    const STRINGS = await fetchStrings();
    const dataCarrier = root.querySelector(Selectors.DATA_CARRIER);
    const studentId = dataCarrier.getAttribute('data-studentid');

    if (studentId) {
        // Student view: load and display progress immediately
        updateProgress(root, STRINGS, studentId);

    } else {
        // Professional view: setup autocomplete select box for students

        // eslint-disable-next-line promise/catch-or-return
        Autocomplete.enhance(
            Selectors.INPUT_STUDENT,
            false, // No tags
            false, // No ajax
            STRINGS['user-progress-search-placeholder'],
            false, // Case insensitive
            true, // Show suggestions on keystroke
            STRINGS['user-progress-no-selection'],
            true, // Close after select
        ).then(() => {
            // Hide Autocomplete selection box, keep it for screen reader only
            const studentSelectionBox = root.querySelector('.form-autocomplete-selection');
            studentSelectionBox.classList.add('sr-only');
            // Deselect Autocomplete default selection on first option by triggering the empty option
            const defaultOption = root.querySelector('[data-value=""]');
            defaultOption.click();

            // Setup autocomplete change listener
            const studentSelection = root.querySelector(Selectors.INPUT_STUDENT);
            studentSelection.addEventListener('change', () => {
                const studentId = studentSelection.value;
                dataCarrier.setAttribute('data-studentid', studentId);
                if (studentId) {
                    const contextId = dataCarrier.getAttribute('data-contextid');
                    updateProgress(root, STRINGS, studentId, contextId);
                } else {
                    // Deselection, hide current content
                    toggleDisplay(root, Selectors.CONTAINER_MAIN_PROGRESS);
                }
            });

            // Autocomplete setup triggers flash of unstyled components, so the whole form is hidden at first. Now we can reveal it
            const hiddenForm = root.querySelector('.hidden-when-loading');
            hiddenForm.classList.remove('hidden-when-loading');
            // Hide loader, setup is complete and there is no request in progress
            toggleDisplay(root, Selectors.CONTAINER_LOADER);

            return;
        });
    }
}

/**
 * Fetch progress data for given student and render progress component.
 *
 * @param {Element} root root element of component
 * @param {object}  STRINGS map of i18n strings
 * @param {string}  userid user id
 * @param {string}  contextid context id, optional
 */
function updateProgress(root, STRINGS, userid, contextid = undefined) {
    // Hide current content (if any) and show the loader
    toggleDisplay(root, Selectors.CONTAINER_MAIN_PROGRESS, Selectors.CONTAINER_LOADER);

    // Fetch progress data for given user
    Ajax.call([{
        methodname: 'local_padplusextensions_progress_overview',
        args: {userid, contextid},
    }])[0].then((data) => {
        // Compute summary data from response
        const topgroupsData = computeSummaryDataForTopGroups(data.progress, STRINGS);

        // Build template context for rendering
        const context = {
            username: data.username,
            forprofessional: contextid !== undefined,
            totalByStatus: data.totalByStatus,
            ...topgroupsData,
        };
        return Templates.renderForPromise('local_padplusextensions/myprogress_content', context).then(({html, js}) => {
            Templates.replaceNodeContents(Selectors.CONTAINER_MAIN_PROGRESS, html, js);
            // Setup handlers once toggle buttons have been rendered
            setupToggleDetails(root, STRINGS);
            // Rendering done, time to hide the loader and show the content!
            toggleDisplay(root, Selectors.CONTAINER_LOADER, Selectors.CONTAINER_MAIN_PROGRESS);
            return;
        });
    }).catch(Notification.exception);
}

/**
 * Setup event handlers on all 'toggle details' button links.
 *
 * By default summary containers are opened and details container are closed.
 *
 * @param {Element} root root element of component
 * @param {object}  STRINGS map of i18n strings
 */
function setupToggleDetails(root, STRINGS) {
    const toggleDetailsElements = root.querySelectorAll(Selectors.INPUT_TOGGLE_DETAILS);

    toggleDetailsElements.forEach(element => {
        const topgroup = element.closest(Selectors.CONTAINER_TOPGROUP);
        const summaryId = topgroup.querySelector(Selectors.CONTAINER_SUMMARY).id;
        const detailsId = topgroup.querySelector(Selectors.CONTAINER_DETAILS).id;
        element.setAttribute('aria-controls', `${summaryId} ${detailsId}`);

        // Set default state to closed details (opened summary)
        hideDetails(element, topgroup, STRINGS);

        element.addEventListener('click', (e) => {
            e.preventDefault();

            const toggleElement = e.target;
            const detailsState = toggleElement.getAttribute('data-details');
            if (detailsState === 'closed') {
                showDetails(toggleElement, topgroup, STRINGS);
            } else {
                hideDetails(toggleElement, topgroup, STRINGS);
            }
        });
    });
}

/**
 * Show details container and hide summary container in given topgroup container.
 *
 * @param {Element} toggleElement
 * @param {Element} topgroupContainer
 * @param {array}   STRINGS
 */
function showDetails(toggleElement, topgroupContainer, STRINGS) {
    toggleDisplay(topgroupContainer, Selectors.CONTAINER_SUMMARY, Selectors.CONTAINER_DETAILS);
    toggleElement.textContent = STRINGS['hide-progress-details'];
    toggleElement.classList.remove('toggle-unexpanded-icon');
    toggleElement.classList.add('toggle-expanded-icon');
    toggleElement.setAttribute('data-details', 'opened');
}

/**
 * Show summary container and hide details container in given topgroup container.
 *
 * @param {Element} toggleElement
 * @param {Element} topgroupContainer
 * @param {array}   STRINGS
 */
function hideDetails(toggleElement, topgroupContainer, STRINGS) {
    toggleDisplay(topgroupContainer, Selectors.CONTAINER_DETAILS, Selectors.CONTAINER_SUMMARY);
    toggleElement.textContent = STRINGS['show-progress-details'];
    toggleElement.classList.remove('toggle-expanded-icon');
    toggleElement.classList.add('toggle-unexpanded-icon');
    toggleElement.setAttribute('data-details', 'closed');
}

/**
 * Toggle display between given elements, e.g. for a loader/content couple.
 *
 * @param {Element} root
 * @param {string}  selectorToHide
 * @param {string}  selectorToShow
 */
function toggleDisplay(root, selectorToHide, selectorToShow = null) {
    const elementToHide = root.querySelector(selectorToHide);
    elementToHide.style.display = 'none';

    if (selectorToShow) {
        const elementToShow = root.querySelector(selectorToShow);
        elementToShow.style.display = 'block';
    }
}
