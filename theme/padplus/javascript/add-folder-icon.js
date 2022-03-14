// Allows us to add the folder icon to the page title when we are in a section/activity page.
var sectionPageClass = document.querySelectorAll('.format-topics');

if (sectionPageClass.length > 0) { // We check if the class is empty or not. It allows us to exclude workshop page.
    window.onload = init; // We need first loading the page before picking the classes up.
    var url = window.location.href;
    function init(){
        // We include folder icon if we are on section or activity page.
        if ((url.includes('/course/view.php') && url.includes('&section=')) || url.includes('/mod/')) {
            var sectionTitle = document.querySelector(".page-header-headings");
            sectionTitle.classList.add('section-title-icon');
        }
    }
}
