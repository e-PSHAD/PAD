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
import Notification from 'core/notification';

/**
 * Handle click on button to start a video call.
 * This is used on profile pages only.
 *
 * @param {String} buttonId The id of the button element.
 */
export const handleVideoCallRequest = (buttonId) => {
    const button = document.getElementById(buttonId);

    button.addEventListener('click', function(e) {
        const launchvideocallurl = button.getAttribute('href');
        requestVideoCall(e, launchvideocallurl);
    });
};

/**
 * Handle video form in block to start a video call.
 *
 * @param {String} videocallform The form element which contains data.
 */
export const handleVideoCallFormRequest = (videocallform) => {
    const submit = videocallform.querySelector('input[type=submit]');

    submit.addEventListener('click', function(e) {
        const formData = new FormData(videocallform);
        const meetingname = formData.get('videocallname');
        const viewersid = formData.getAll('videocallviewers[]');
        const launchvideocallurl = videocallform.getAttribute('action');
        requestVideoCall(e, launchvideocallurl, viewersid, meetingname);
    });
};

/**
 * Start a video call in a new window with the given launch URL and optional parameters.
 *
 * @param {String} e The user event which triggers the call.
 * @param {String} launchvideocallurl The base URL to start with.
 * @param {Array}  viewersid (optional) An array of user ids to invite.
 * @param {String} meetingname (optional) The meeting name.
 */
export const requestVideoCall = (e, launchvideocallurl, viewersid, meetingname) => {
    e.preventDefault();

    if (viewersid) {
        const viewersparam = viewersid.join(',');
        launchvideocallurl = `${launchvideocallurl}&viewersid=${viewersparam}`;
    }
    if (meetingname) {
        launchvideocallurl = `${launchvideocallurl}&name=${meetingname}`;
    }

    window.open(launchvideocallurl);
};

/**
 * Setup JS interactions for VideoCall block.
 *
 * @param {Element} root
 */
export const setupVideoCallBlock = (root) => {
    // Containers.
    const containerSharedLinks = root.querySelector("[data-container=padplusvideocall-shared-links]");

    // Btns.
    const btnReset = root.querySelector("[data-action=reset");
    const btnsCopy = root.querySelectorAll("[data-action=copy]");
    const btnsRadio = root.querySelectorAll('[name=padplusvideocall-mode]');

    // Inputs.
    const moderatorInput = root.querySelector("[data-input=moderator]");
    const viewerInput = root.querySelector("[data-input=viewer]");

    // Handle show & hide of the different blocks corresponding to the videocall modes: planned/unplanned.
    btnsRadio.forEach(radio => {
        const unplannedVideoCallGroup = root.querySelector('[data-container=padplusvideocall-unplanned]');
        const plannedVideoCallGroup = root.querySelector('[data-container=padplusvideocall-planned]');
        radio.addEventListener('change', event => {
            if (event.target.value === 'planned') {
                toggleDisplay(unplannedVideoCallGroup, plannedVideoCallGroup);
            } else {
                toggleDisplay(plannedVideoCallGroup, unplannedVideoCallGroup);
            }
        });
    });

    const initialRequestContainer = root.querySelector('[data-container=padplusvideocall-initial-links]');
    const requestLinksButtons = root.querySelectorAll('[data-action=request-links]');
    // Handle click on buttons to request videocall links. Both have the same behavior.
    requestLinksButtons.forEach(button => {
        button.addEventListener('click', () => {
            toggleDisplay(containerSharedLinks);
            return requestNewMeetingLinks(root).then((data) => {
                setInputValue(moderatorInput, data.moderatorurl);
                setInputValue(viewerInput, data.viewerurl);
                toggleDisplay(initialRequestContainer, containerSharedLinks);
                return;
            });
        });
    });

    // Handle click on button to reset videocall block.
    btnReset.addEventListener('click', function() {
        toggleDisplay(containerSharedLinks, initialRequestContainer);
        moderatorInput.setAttribute('value', '');
        viewerInput.setAttribute('value', '');
    });

    // Handle click on buttons to copy videocall links.
    btnsCopy.forEach(btnCopy => {
        btnCopy.addEventListener('click', function() {
            const user = btnCopy.getAttribute('data-input');
            const input = root.querySelector(`[data-input=${user}]`);
            const icon = root.querySelector(`[data-icon=${user}]`);
            toggleDisplay(input, icon);
            setTimeout(function() {
                toggleDisplay(icon, input);
            }, 1200);

            navigator.clipboard.writeText(input.value);
        });
    });
};

/**
 * Toggle display between given elements.
 *
 * @param {Element} elementToHide
 * @param {Element} elementToShow
 */
function toggleDisplay(elementToHide, elementToShow = null) {
    elementToHide.classList.add('hidden');

    if (elementToShow) {
        elementToShow.classList.remove('hidden');
    }
}

/**
 * Set input value and force cursor so that the string end appears in view.
 * Since value can be long URLs, user is more likely to see them change after each call.
 *
 * @param {Element} inputElement
 * @param {string} value
 */
function setInputValue(inputElement, value) {
    inputElement.setAttribute('value', value);
    // Here goes some weird DOM/Javascript update stuff, first wait for DOM update
    setTimeout(() => {
        // Set cursor at end
        inputElement.setSelectionRange(value.length, value.length);
        // Put focus on element otherwise cursor won't show
        inputElement.focus();
        // Immediately blur focus because we don't want the input highlighted
        inputElement.blur();
    });
}

/**
 * Generate meeting links.
 *
 * @param {object} root Input that contains the contextid.
 * @returns {data}
 */
function requestNewMeetingLinks(root) {
    const contextid = root.querySelector('[data-contextid]').getAttribute('data-contextid');
    return Ajax.call([{
        methodname: 'block_padplusvideocall_generate_meeting_links',
        args: {contextid},
    }])[0].catch(Notification.exception);
}
