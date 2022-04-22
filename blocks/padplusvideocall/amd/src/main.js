import Ajax from 'core/ajax';
import Notification from 'core/notification';

export const handleVideoCallRequest = (buttonId, contextid, viewersid) => {
    const button = document.getElementById(buttonId);

    button.addEventListener('click', function(e) {
        e.preventDefault();

        const request = {
            methodname: 'block_padplusvideocall_initialize_videocall',
            args: {contextid, viewersid},
        };

        const [promise] = Ajax.call([request]);

        promise.done(response => {
            const {moderatorurl} = response;
            window.open(moderatorurl);
        }).fail(Notification.exception);
    });
};
