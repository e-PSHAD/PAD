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

/**
 * Handle click on button to start a video call.
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
