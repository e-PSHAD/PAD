// Allows us to add the folder icon to the page title when we are in a section page.
var sectionPageId = document.querySelectorAll('#page-course-view-topics');

if (sectionPageId) {
    window.onload = init; // We need first loading the page before picking the classes up.
    url = window.location.href;
    function init(){
        if (url.includes('/course/view.php') && url.includes('&section=')) {
            var sectionTitle = document.querySelector(".page-header-headings");
            sectionTitle.classList.add('section-title-icon');
        }
    }
}
