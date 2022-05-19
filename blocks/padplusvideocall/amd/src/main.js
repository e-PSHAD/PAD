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

const Selectors = {
    DATA_CONTEXT: '[data-contextid]',
    INPUT_MODE: '[name=videocall-mode]',
    INPUT_MEETING_LINK: '[data-input=meeting-link]',
    CONTAINER_MODE_DIRECT: '[data-container=videocall-mode-direct]',
    CONTAINER_MODE_LINK: '[data-container=videocall-mode-link]',
    CONTAINER_LINK_INITIAL: '[data-container=videocall-link-initial]',
    CONTAINER_MEETING_LINK: '[data-container=videocall-meeting-link]',
    CONTAINER_COPY_SUCCESS: '[data-container=videocall-copy-success]',
    ACTION_REQUEST_LINK: '[data-action=request-link]',
    ACTION_RESET_LINK: '[data-action=reset]',
    ACTION_COPY_LINK: '[data-action=copy]'
};

/**
 * Setup JS interactions for VideoCall block.
 *
 * @param {Element} root
 */
 export const setupVideoCallBlock = (root) => {
    const btnsRadio = root.querySelectorAll(Selectors.INPUT_MODE);
    // Toggle display of direct or link modes for videocalls
    btnsRadio.forEach(radio => {
        const directVideoCallGroup = root.querySelector(Selectors.CONTAINER_MODE_DIRECT);
        const linkVideoCallGroup = root.querySelector(Selectors.CONTAINER_MODE_LINK);
        radio.addEventListener('change', event => {
            if (event.target.value === 'direct') {
                toggleDisplay(linkVideoCallGroup, directVideoCallGroup);
            } else {
                toggleDisplay(directVideoCallGroup, linkVideoCallGroup);
            }
        });
    });

    setupVideoCallDirectMode(root);
    setupVideoCallLinkMode(root);
};

/**
 * Setup form from videocall_form.php to start a video call
 *
 * @param {Element} root
 */
 function setupVideoCallDirectMode(root) {
    const directVideoCallGroup = root.querySelector(Selectors.CONTAINER_MODE_DIRECT);
    const videocallform = directVideoCallGroup.querySelector('form');
    const submit = videocallform.querySelector('input[type=submit]');

    submit.addEventListener('click', function(e) {
        const formData = new FormData(videocallform);
        const viewersid = formData.getAll('videocallviewers[]');
        const launchvideocallurl = videocallform.getAttribute('action');
        requestVideoCall(e, launchvideocallurl, viewersid);
    });
}

/**
 * Handle click on button to start a video call.
 *
 * Note: this is used on profile pages only.
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
 * Setup JS interactions for videocall link generation.
 *
 * @param {Element} root
 */
function setupVideoCallLinkMode(root) {
    // Containers.
    const initialRequestContainer = root.querySelector(Selectors.CONTAINER_LINK_INITIAL);
    const meetingLinkContainer = root.querySelector(Selectors.CONTAINER_MEETING_LINK);

    // Input.
    const meetingLinkInput = root.querySelector(Selectors.INPUT_MEETING_LINK);

    const btnRequestLink = root.querySelector(Selectors.ACTION_REQUEST_LINK);
    // Handle click on button to request videocall link.
    btnRequestLink.addEventListener('click', () => {
        const contextid = root.querySelector(Selectors.DATA_CONTEXT).getAttribute('data-contextid');
        toggleDisplay(meetingLinkContainer);
        return requestNewMeetingLink(contextid).then(meetingurl => {
            meetingLinkInput.setAttribute('value', meetingurl);
            toggleDisplay(initialRequestContainer, meetingLinkContainer);
            return;
        }).catch(Notification.exception);
    });

    const btnReset = root.querySelector(Selectors.ACTION_RESET_LINK);
    // Handle click on button to reset videocall block.
    btnReset.addEventListener('click', function() {
        // Reset group to initial state
        toggleDisplay(meetingLinkContainer, initialRequestContainer);
        meetingLinkInput.setAttribute('value', '');

        // Reset mode selection
        const linkVideoCallGroup = root.querySelector(Selectors.CONTAINER_MODE_LINK);
        toggleDisplay(linkVideoCallGroup);
        const directModeBtn = root.querySelector(Selectors.INPUT_MODE + '[value=link]');
        directModeBtn.checked = false;
    });

    const successContainer = root.querySelector(Selectors.CONTAINER_COPY_SUCCESS);
    const btnCopy = root.querySelector(Selectors.ACTION_COPY_LINK);
    // Handle click on button to copy videocall link.
    btnCopy.addEventListener('click', function() {
        navigator.clipboard.writeText(meetingLinkInput.value);

        toggleDisplay(meetingLinkInput, successContainer);
        setTimeout(function() {
            toggleDisplay(successContainer, meetingLinkInput);
        }, 1200);
    });
}

/**
 * Request a new meeting link to share.
 *
 * @param {number} contextid context id in which meeting link generation is authorized
 * @returns {string}
 */
 function requestNewMeetingLink(contextid) {
    return Ajax.call([{
        methodname: 'block_padplusvideocall_generate_meeting_link',
        args: {contextid},
    }])[0].then(data => data.meetingurl);
}

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
