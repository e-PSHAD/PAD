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
 * @package   theme_padplus
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    // Theme colours setting.
    $settings->add(new admin_setting_heading(
        'colorconfig',
        get_string('settings-color-title', 'theme_padplus'),
        get_string('settings-color-desc', 'theme_padplus')
    ));

    $setting = new admin_setting_configcolourpicker(
        'theme_padplus/primarycolor',
        get_string('settings-primarycolor', 'theme_padplus'),
        get_string('settings-primarycolor-desc', 'theme_padplus'),
        '#008A28');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    $setting = new admin_setting_configcolourpicker(
        'theme_padplus/sidebarcolor',
        get_string('settings-sidebarcolor', 'theme_padplus'),
        get_string('settings-sidebarcolor-desc', 'theme_padplus'),
        '#F7FBF0');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Footer settings.
    $settings->add(new admin_setting_heading(
        'footerconfig',
        get_string('settings-footer', 'theme_padplus'),
        get_string('settings-footer-desc', 'theme_padplus')
    ));

    $settings->add(new admin_setting_configtext(
        'theme_padplus/contactlink',
        get_string('settings-contactlink', 'theme_padplus'),
        get_string('settings-contactlink-desc', 'theme_padplus'),
        '',
        PARAM_URL
    ));

    $settings->add(new admin_setting_configtext(
        'theme_padplus/helplink',
        get_string('settings-helplink', 'theme_padplus'),
        get_string('settings-helplink-desc', 'theme_padplus'),
        '',
        PARAM_URL
    ));

    $settings->add(new admin_setting_configtext(
        'theme_padplus/sitepolicylink',
        get_string('settings-sitepolicylink', 'theme_padplus'),
        get_string('settings-sitepolicylink-desc', 'theme_padplus'),
        '',
        PARAM_URL
    ));

    $settings->add(new admin_setting_configtext(
        'theme_padplus/privacylink',
        get_string('settings-privacylink', 'theme_padplus'),
        get_string('settings-privacylink-desc', 'theme_padplus'),
        '',
        PARAM_URL
    ));

    $settings->add(new admin_setting_configtext(
        'theme_padplus/legalnoticeslink',
        get_string('settings-legalnoticeslink', 'theme_padplus'),
        get_string('settings-legalnoticeslink-desc', 'theme_padplus'),
        '',
        PARAM_URL
    ));

    $settings->add(new admin_setting_configtext(
        'theme_padplus/copyright',
        get_string('settings-copyright', 'theme_padplus'),
        get_string('settings-copyright-desc', 'theme_padplus'),
        'PAD+ 2022',
        PARAM_TEXT
    ));

    // Sidebar menu settings.
    $settings->add(new admin_setting_heading(
        'sidebarmenuconfig',
        get_string('settings-sidebarmenu', 'theme_padplus'),
        get_string('settings-sidebarmenu-desc', 'theme_padplus')
    ));

    $topcategories = core_course_category::top()->get_children();
    $categoryitems = array_map(fn($cat) => $cat->name, $topcategories);

    $settings->add(new admin_setting_configmultiselect(
        'theme_padplus/sidebarworkshopids',
        get_string('workshop-menu', 'theme_padplus'),
        get_string('settings-workshopids-desc', 'theme_padplus'),
        array(),
        $categoryitems));

    $nonechoice = [0 => get_string('settings-catalogid-none', 'theme_padplus')];
    $settings->add(new admin_setting_configselect(
        'theme_padplus/sidebarcatalogid',
        get_string('catalog-menu', 'theme_padplus'),
        get_string('settings-catalogid-desc', 'theme_padplus'),
        0,
        array_replace($nonechoice, $categoryitems) // Function array_replace preserves ids as index.
    ));

    $settings->add(new admin_setting_configcheckbox(
        'theme_padplus/sidebarallcourses',
        get_string('allcourses-menu', 'theme_padplus'),
        get_string('settings-allcourses-desc', 'theme_padplus'),
        1));

    // Videocall settings.
    $settings->add(new admin_setting_heading(
        'videocallconfig',
        get_string('settings-videocall', 'theme_padplus'),
        get_string('settings-videocall-desc', 'theme_padplus')
    ));


    $settings->add(new admin_setting_configcheckbox(
        'theme_padplus/videocallinprofile',
        get_string('settings-videocallprofile', 'theme_padplus'),
        get_string('settings-videocallprofile-desc', 'theme_padplus'),
        1));
}
