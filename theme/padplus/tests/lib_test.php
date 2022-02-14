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

namespace theme_padplus;

class lib_test extends \advanced_testcase {

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    public function test_select_user_top_categories() {
        $maincategory = $this->create_test_category('Séquences');
        $workshops = $this->create_test_category('Ateliers collectifs');
        $catalog = $this->create_test_category('Catalogue');

        $theme = new \stdClass();
        $theme->settings = new \stdClass();
        // Enable standard PAD+ options in settings.
        $theme->settings->sidebarallcourses = 1;
        $theme->settings->sidebarworkshopids = "1,2,{$workshops->id}";
        $theme->settings->sidebarcatalogid = $catalog->id;

        $expectedmain = $this->build_expected_item($maincategory->id, get_string('allcourses-menu', 'theme_padplus'));
        $expectedworkshop = $this->build_expected_item($workshops->id, get_string('workshop-menu', 'theme_padplus'), 'i/group');
        $expectedcatalog = $this->build_expected_item(
            $catalog->id,
            get_string('catalog-menu', 'theme_padplus'),
            'i/open',
            get_string('catalog-menu', 'theme_padplus'));

        $result = select_user_top_categories(array($maincategory, $workshops, $catalog), $theme);
        $this->assertEquals(
            array(
                $expectedmain,
                $expectedworkshop,
                $expectedcatalog),
            $result,
            'Professional should have 3 categories in order.');

        $result = select_user_top_categories(array($workshops), $theme);
        $this->assertEquals(
            array(
                $expectedworkshop),
            $result,
            'Student should only have 1 workshop category.');

        $workshop2 = $this->create_test_category('Ateliers secondaires');
        $result = select_user_top_categories(array($workshops, $maincategory, $workshop2), $theme);
        $this->assertEquals(
            array(
                $expectedmain,
                $expectedworkshop,
                $this->build_other_entry_from($workshop2)),
            $result,
            'First category to match one of workshop ids should be marked as workshop and put in second.');

        $result = select_user_top_categories(array($maincategory, $catalog, $catalog), $theme);
        $this->assertEquals(
            array(
                $expectedmain,
                $this->build_other_entry_from($catalog),
                $expectedcatalog),
            $result,
            'First category to match catalog id should be marked as catalog and put at the end with a divider.');

        $other = $this->create_test_category('Catégorie secondaire');
        $result = select_user_top_categories(array($workshops, $maincategory, $other), $theme);
        $this->assertEquals(
            array(
                $expectedmain,
                $expectedworkshop,
                $this->build_other_entry_from($other)),
            $result,
            "First category not matching catalog or workshop should be marked as 'all courses'"
            ." and put in first if 'all courses' flag is enabled.");

        $other2 = $this->create_test_category('Catégorie tertiaire');
        $result = select_user_top_categories(array($maincategory, $other, $other2), $theme);
        $this->assertEquals(
            array(
                $expectedmain,
                $this->build_other_entry_from($other),
                $this->build_other_entry_from($other2)),
            $result,
            "All categories should be kept even if unmatched.");

        $result = select_user_top_categories(array($maincategory, $workshops, $catalog, $other), $theme);
        $this->assertEquals(
            array($maincategory, $workshops, $catalog, $other),
            $result,
            "All categories should be kept 'as-is' if more than 3 categories.");

        $theme->settings->sidebarallcourses = 0;
        $result = select_user_top_categories(array($workshops, $maincategory, $other), $theme);
        $this->assertEquals(
            array(
                $expectedworkshop,
                $this->build_other_entry_from($maincategory),
                $this->build_other_entry_from($other)),
            $result,
            "Category not matching catalog or workshop should be kept 'as-is' if 'all courses' flag is disabled.");
    }

    public function create_test_category($name) {
        $data = new \stdClass();
        $data->name = $name;
        return \core_course_category::create($data);
    }

    public function build_expected_item($id, $name, $icon = null, $showdivider = null) {
        $data = new \stdClass();
        $data->id = $id;
        $data->name = $name;
        $data->icon = $icon;
        $data->showdivider = $showdivider;
        return (object) array_filter((array) $data);
    }

    public function build_other_entry_from($category) {
        $data = new \stdClass();
        $data->id = $category->id;
        $data->name = $category->name;
        return $data;
    }
}
