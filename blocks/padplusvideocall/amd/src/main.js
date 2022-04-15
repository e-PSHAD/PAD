export const handleVideoCallRequest = (buttonId, viewersid) => {
    const button = document.getElementById(buttonId);

    button.addEventListener('click', function(e) {
        e.preventDefault();

        let creationurl = button.getAttribute('href');
        if (viewersid) {
            const viewersparam = viewersid.join(',');
            creationurl = `${creationurl}&viewersid=${viewersparam}`;
        }

        window.open(creationurl);
    });
};
