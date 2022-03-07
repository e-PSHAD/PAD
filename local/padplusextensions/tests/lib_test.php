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

namespace local_padplusextensions;

class lib_test extends \advanced_testcase {

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    public function test_category_belongs_to_workshops() {
        $workshop1 = $this->create_test_category('Workshop 1');
        $subworkshop1 = $this->create_test_category('Workshop 1/Sub1', $workshop1->id);
        $workshop2 = $this->create_test_category('Workshop 2');
        $othercategory = $this->create_test_category('Coursecat');

        $theme = \theme_config::load('padplus');
        $theme->settings->sidebarworkshopids = "{$workshop1->id},{$workshop2->id}";

        $result = category_belongs_to_workshop($workshop1, $theme);
        $this->assertTrue(
            $result,
            'Top workshop category should belong to workshops.');

        $result = category_belongs_to_workshop($workshop2, $theme);
        $this->assertTrue(
            $result,
            'Top workshop category should belong to workshops.');

        $result = category_belongs_to_workshop($subworkshop1, $theme);
        $this->assertTrue(
            $result,
            'Workshop subcategory should to workshops.');

        $result = category_belongs_to_workshop($othercategory, $theme);
        $this->assertFalse(
            $result,
            'Other category should not belong to workshops.');

    }

    public function test_category_belongs_to_catalog() {
        $catalog = $this->create_test_category('Catalog');
        $subcatalog = $this->create_test_category('Catalog/Sub1', $catalog->id);
        $subsubcatalog = $this->create_test_category('Catalog/Sub1/Sub2', $subcatalog->id);
        $othercategory = $this->create_test_category('Coursecat');
        $othersubcategory = $this->create_test_category('Coursecat/Sub1', $othercategory->id);

        $theme = \theme_config::load('padplus');
        $theme->settings->sidebarcatalogid = $catalog->id;

        $result = category_belongs_to_catalog($catalog, $theme);
        $this->assertTrue(
            $result,
            'Catalog category should belong to itself.');

        $result = category_belongs_to_catalog($subcatalog, $theme);
        $this->assertTrue(
            $result,
            'Catalog direct subcategory should belong to catalog.');

        $result = category_belongs_to_catalog($subsubcatalog, $theme);
        $this->assertTrue(
            $result,
            'Catalog subcategory at any level should belong to catalog.');

        $result = category_belongs_to_catalog($othercategory, $theme);
        $this->assertFalse(
            $result,
            'Outside category should not belong to catalog.');

        $result = category_belongs_to_catalog($othersubcategory, $theme);
        $this->assertFalse(
            $result,
            'Outside subcategory at any level should not belong to catalog.');

    }

    public function create_test_category($name, $parent = null) {
        $data = new \stdClass();
        $data->name = $name;
        if ($parent) {
            $data->parent = $parent;
        }
        return \core_course_category::create($data);
    }
}
