<?php
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
 * A two column layout for the padplus theme, from boost theme.
 *
 * @package   theme_padplus
 * @copyright 2022 Epnak
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');

if (isloggedin()) {
    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'true');
} else {
    $navdraweropen = false;
}
$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;
$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu)
];

$nav = $PAGE->flatnav;

/*** PADPLUS: sidemenu customisation */
// We rebuild the side menu by selecting and reordering menu items from the default flat navigation.
$padnav = new flat_navigation($PAGE);

/* HERE BE DRAGONS.
 * "flat navigation" in Moodle is not so flat, since there are multiple navigation elements in sidebar,
 * one for each "collection" of items in the flat navigation list. But collections have no first-class representation
 * in the flat navigation list. The flat navigation list contains all menu items, and the first item of each collection
 * bears special attributes to support the collection.
 *
 * Rules are:
 * For each first item of a collection in the flat navigation list :
 * - first item should have a collectionlabel which sets the navigation label for this collection.
 * - first item in every other collection except the first should have the divider attribute set to true
 *   to separate navigation elements.
 */

// Group: global site navigation. Options left out: 'privatefiles', 'contentbank'.
// Set collectionlabel on home since we always want it to come first.
$home = $nav->get('home');
$home->set_collectionlabel(get_string('aria-main-nav', 'theme_padplus'));
fill_nav_from_menu_keys($nav, $padnav, ['home', 'myhome', 'mycoursespage', 'calendar', 'myprogresspage']);
// Always disable divider on myhome since it should follow home and never comes at top level in another collection.
$myhome = $padnav->get('myhome');
if (is_object($myhome)) {
    $myhome->set_showdivider(false);

    // BUG FIX: force active check for dashboard item to activate it for all users if need be.
    // Indeed, when user has ten or so courses, Moodle adds 'morenavigationlinks' item to the navigation hierarchy, which tends to
    // inactivate parents such as myhome in the hierarchy.
    // This is a weird bug due to Moodle complicated build stage for navigation and flat navigation.
    $myhome->check_if_active();
}

// @codingStandardsIgnoreStart
// Usual 'my courses' menu items in main pages.
// fill_nav_from_menu_keys($nav, $padnav, ['mycourses']);
// fill_nav_from_menu_type($nav, $padnav, navigation_node::TYPE_COURSE);
// fill_nav_from_menu_keys($nav, $padnav, ['courseindexpage']);
// @codingStandardsIgnoreEnd

// Only administrator and manager at system level have this capability, so it's a good way to discriminate.
$context = context_system::instance();
if (has_capability('moodle/category:manage', $context)) {
    // Group: site administration for administrators / instance manager.
    fill_nav_from_menu_keys($nav, $padnav, ['sitesettings', 'allcategories']);
} else {
    // Group: subcategories for all other users.
    $topcategories = select_user_top_categories(core_course_category::top()->get_children(), $PAGE->theme);

    $first = true;
    foreach ($topcategories as $index => $category) {
        $categorynode = navigation_node::create(
            $category->name,
            new moodle_url('/course/index.php', array('categoryid' => $category->id)),
            navigation_node::TYPE_CUSTOM,
            null,
            "category_{$category->id}",
            new pix_icon($category->icon ?? 'i/course', '')
        );
        $flatnode = new flat_navigation_node($categorynode, 0);
        if ($first) {
            $flatnode->set_showdivider(true, get_string('categories-menu-nav', 'theme_padplus'));
        }
        if (isset($category->showdivider)) {
            $flatnode->set_showdivider(true, $category->showdivider);
        }
        $padnav->add($flatnode);
        $first = false;
    }
}

// Group: current course. Options left out: 'badgesview', 'competencies'.
fill_nav_from_menu_keys($nav, $padnav, ['coursehome', 'participants', 'grades']);
// Leaving out section items: fill_nav_from_menu_type($nav, $padnav, navigation_node::TYPE_SECTION);
// Enable divider on coursehome since it comes after the main menu now & change nav label.
$coursehome = $padnav->get('coursehome');
if (is_object($coursehome)) {
    $labelkey = course_is_workshop($PAGE->course, $PAGE->theme) ? 'sidebar-summary-workshop' : 'sidebar-summary-course';
    $coursehome->text = get_string($labelkey, 'theme_padplus');
    $coursehome->set_showdivider(true, $coursehome->text);
}

// Replace icon by an indentation for these course subitems.
foreach (['participants', 'grades'] as $navkey) {
    $navitem = $padnav->get($navkey);
    if (is_object($navitem)) {
        $navitem->icon->pix = null;
        $navitem->set_indent(4);
    }
}

// Group: add block.
fill_nav_from_menu_keys($nav, $padnav, ['addblock']);

$templatecontext['flatnavigation'] = $padnav;
$templatecontext['firstcollectionlabel'] = $padnav->get_collectionlabel();

/*** PADPLUS: footer configuration */
$themesettings = $PAGE->theme->settings;
$padfooter = [
    'helplink' => $themesettings->helplink,
    'supportlink' => $themesettings->supportlink,
    'contactlink' => $themesettings->contactlink,
    'legalnoticeslink' => $themesettings->legalnoticeslink,
    'privacylink' => $themesettings->privacylink,
    'copyright' => $themesettings->copyright
];
$templatecontext['padfooter'] = $padfooter;
/*** PADPLUS END */

echo $OUTPUT->render_from_template('theme_boost/columns2', $templatecontext);
