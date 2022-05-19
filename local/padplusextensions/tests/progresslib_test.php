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

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot .'/local/padplusextensions/progresslib.php');

class progresslib_test extends \advanced_testcase {

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    public function test_get_students_in_courses() {
        $generator = $this->getDataGenerator();

        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();
        $user4 = $generator->create_user();

        $course1 = $generator->create_course();
        $course2 = $generator->create_course();

        $generator->enrol_user($user1->id, $course1->id, 'editingteacher'); // Teacher does not count.
        $generator->enrol_user($user2->id, $course1->id, 'student');
        $generator->enrol_user($user3->id, $course1->id, 'student');
        $generator->enrol_user($user3->id, $course2->id, 'student');
        $generator->enrol_user($user4->id, $course2->id, 'student');

        $result = get_students_in_courses([$course1, $course2]);
        $this->assertEquals(3, count($result), 'Should return the number of unique students');
    }

    public function test_get_course_regroup_contexts() {
        $generator = $this->getDataGenerator();

        $sitecategory = $this->getDataGenerator()->create_category();
        $platformcategory = $this->getDataGenerator()->create_category(array('parent' => $sitecategory->id));
        $modulecategory = $this->getDataGenerator()->create_category(array('parent' => $platformcategory->id));
        $blockcategory = $this->getDataGenerator()->create_category(array('parent' => $platformcategory->id));
        $blockmodulecategory = $this->getDataGenerator()->create_category(array('parent' => $blockcategory->id));

        $sitectx = \context_coursecat::instance($sitecategory->id);
        $platformctx = \context_coursecat::instance($platformcategory->id);
        $modulectx = \context_coursecat::instance($modulecategory->id);
        $blockmodulectx = \context_coursecat::instance($blockmodulecategory->id);

        $course1 = $generator->create_course(array('fullname' => 'Module Course', 'category' => $modulecategory->id));
        $course2 = $generator->create_course(array('fullname' => 'Block-Module Course', 'category' => $blockmodulecategory->id));
        $course3 = $generator->create_course(array('fullname' => 'Platform Course', 'category' => $platformcategory->id));
        $course4 = $generator->create_course(array('fullname' => 'Site Course', 'category' => $sitecategory->id));

        // Create/enrol user for the next step.
        $user1 = $generator->create_user();
        $generator->enrol_user($user1->id, $course1->id, 'student');
        $generator->enrol_user($user1->id, $course2->id, 'student');
        $generator->enrol_user($user1->id, $course3->id, 'student');
        $generator->enrol_user($user1->id, $course4->id, 'student');

        // Retrieve courses through enrol_get_users_courses to properly retrieve **ctxpath** in course records.
        $courses = enrol_get_users_courses($user1->id);

        foreach ($courses as $course) {
            switch ($course->fullname) {
                case 'Module Course':
                    $result = get_course_regroup_contexts($course);
                    $this->assertEquals(
                        [$platformctx->id, $modulectx->id],
                        $result,
                        'Module course regroup should return platform and module contexts');
                    break;

                case 'Block-Module Course':
                    $result = get_course_regroup_contexts($course);
                    $this->assertEquals(
                        [$platformctx->id, $blockmodulectx->id],
                        $result,
                        'Block-Module course regroup should return platform and module contexts');
                    break;

                case 'Platform Course':
                    $result = get_course_regroup_contexts($course);
                    $this->assertEquals(
                        [$platformctx->id, PADPLUS_UNDEFINED_REGROUP],
                        $result,
                        'Platform course regroup should return platform context and undefined module');
                    break;

                case 'Site Course':
                    $result = get_course_regroup_contexts($course);
                    $this->assertEquals(
                        [$sitectx->id, PADPLUS_UNDEFINED_REGROUP],
                        $result,
                        'Site course regroup should return site context and undefined module');
                    break;

                default:
                    $this->fail("Missing test case found for fixture '$course->fullname'!");
            }
        }
    }

    public function test_group_courses_by_category_context() {
        $fixtures = $this->create_category_courses_fixtures();
        list($user1) = $fixtures['users'];
        list($module1category, $module2category, $module3category, $platform1category, $platform2category)
            = $fixtures['categories'];
        list($course1, $course2, $course3, $course4) = $fixtures['courses'];

        $platform1ctx = \context_coursecat::instance($platform1category->id);
        $module1ctx = \context_coursecat::instance($module1category->id);
        $platform2ctx = \context_coursecat::instance($platform2category->id);
        $module2ctx = \context_coursecat::instance($module2category->id);
        $module3ctx = \context_coursecat::instance($module3category->id);

        list($progress, $categoriesbycontext) = group_courses_by_category_context(($user1->id));
        $this->assertEquals(2, count($progress), 'Progress should contain 2 top groups');

        // Assert top groups.
        $topgroup1 = $progress[$platform1ctx->id];
        $topgroup2 = $progress[$platform2ctx->id];
        $this->assertEquals(1, count($topgroup1), 'Top group should have one bottom group');
        $this->assertEquals(2, count($topgroup2), 'Top group should have some bottom groups');

        // Assert bottom group.
        $bottomgroup1 = $topgroup1[$module1ctx->id];
        $this->assertEquals(2, count($bottomgroup1), 'Bottom group should have some courses');

        // Assert course details.
        list($c2, $c1) = $bottomgroup1;
        $this->assertEquals($course1->fullname, $c1->fullname, 'Course should belong to bottom group');
        $this->assertEquals($course2->fullname, $c2->fullname, 'Course should belong to bottom group');

        // Assert bottom group.
        list($c3) = $topgroup2[$module2ctx->id];
        $this->assertEquals($course3->fullname, $c3->fullname, 'Course should belong to bottom group');

        // Assert bottom group.
        list($c4) = $topgroup2[$module3ctx->id];
        $this->assertEquals($course4->fullname, $c4->fullname, 'Course should belong to bottom group');

        // Assert map of context->category.
        $this->assertEquals(5, count($categoriesbycontext), 'Map should have as many categories as contexts');
        $this->assertEquals(
            $module1category->name,
            $categoriesbycontext[$module1ctx->id]->name,
            'Map should return category record matching context id');
        $this->assertEquals(
            $platform1category->name,
            $categoriesbycontext[$platform1ctx->id]->name,
            'Map should return category record matching context id');
    }

    public function test_get_user_overall_progress() {
        $fixtures = $this->create_category_courses_fixtures();
        list($user1) = $fixtures['users'];
        list($module1category, $module2category, $module3category, $platform1category, $platform2category)
            = $fixtures['categories'];
        list($course1, $course2, $course3, $course4) = $fixtures['courses'];

        $progress = get_user_overall_progress(($user1->id));
        $this->assertEquals(2, count($progress), 'Progress should contain 2 top groups');

        // Assert top groups.
        list($topgroup1, $topgroup2) = $progress;
        $this->assertEquals($platform1category->name, $topgroup1['name'], 'Top group should be a platform');
        $this->assertEquals(1, count($topgroup1['modules']), 'Top group should have a module');
        $this->assertEquals($platform2category->name, $topgroup2['name'], 'Top group should be a platform');
        $this->assertEquals(2, count($topgroup2['modules']), 'Top group should have some modules');

        // Assert bottom group.
        list($module1) = $topgroup1['modules'];
        $this->assertEquals($module1category->name, $module1['name'], 'Bottom group should be a module');
        $this->assertEquals(2, count($module1['courses']), 'Bottom group should have some courses');

        // Assert course details.
        list($c2, $c1) = $module1['courses'];
        $this->assertEquals($course1->fullname, $c1['fullname'], 'Course should have its fullname');
        $this->assertEquals($course2->fullname, $c2['fullname'], 'Course should have its fullname');
        $this->assertStringEndsWith("/course/view.php?id={$course2->id}", $c2['viewurl'], 'Course should have a URL');
        $expectedprogress = array(
            'percent' => 0.0,
            'status' => 'todo',
            'done' => false,
            'inprogress' => false,
            'todo' => true
        );
        $this->assertEquals($expectedprogress, $c2['progress'], 'Course should have some progess data');

        // Assert bottom group.
        list($module2, $module3) = $topgroup2['modules'];
        $this->assertEquals($module2category->name, $module2['name'], 'Bottom group should be a module');
        list($c3) = $module2['courses'];
        $this->assertEquals($course3->fullname, $c3['fullname'], 'Course should have its fullname');

        // Assert bottom group.
        $this->assertEquals($module3category->name, $module3['name'], 'Bottom group should be a module');
        list($c4) = $module3['courses'];
        $this->assertEquals($course4->fullname, $c4['fullname'], 'Course should have its fullname');
    }

    /**
     * Create a hierarchy of categories and courses for a single user.
     *
     * - Platform 1
     *   - Module 1
     *     - Course A M1
     *     - Course B M1
     * - Platform 2
     *   - Module 2
     *     - Course C M2
     *   - Module 3
     *     - Course D M3
     */
    public function create_category_courses_fixtures() {
        $generator = $this->getDataGenerator();

        $sitecategory = $this->getDataGenerator()->create_category();
        $platform1category = $this->getDataGenerator()->create_category(
            array('name' => 'Platform 1', 'parent' => $sitecategory->id));
        $module1category = $this->getDataGenerator()->create_category(
            array('name' => 'Module 1', 'parent' => $platform1category->id));
        $platform2category = $this->getDataGenerator()->create_category(
            array('name' => 'Platform 2', 'parent' => $sitecategory->id));
        $module2category = $this->getDataGenerator()->create_category(
            array('name' => 'Module 2', 'parent' => $platform2category->id));
        $module3category = $this->getDataGenerator()->create_category(
            array('name' => 'Module 3', 'parent' => $platform2category->id));

        $course1 = $generator->create_course(array('fullname' => 'Course A M1', 'category' => $module1category->id));
        $course2 = $generator->create_course(array('fullname' => 'Course B M1', 'category' => $module1category->id));
        $course3 = $generator->create_course(array('fullname' => 'Course C M2', 'category' => $module2category->id));
        $course4 = $generator->create_course(array('fullname' => 'Course D M3', 'category' => $module3category->id));

        // Create/enrol user for the next step.
        $user1 = $generator->create_user();
        $generator->enrol_user($user1->id, $course1->id, 'student');
        $generator->enrol_user($user1->id, $course2->id, 'student');
        $generator->enrol_user($user1->id, $course3->id, 'student');
        $generator->enrol_user($user1->id, $course4->id, 'student');

        return array(
            'users' => [$user1],
            'categories' =>
                [$module1category, $module2category, $module3category, $platform1category, $platform2category, $sitecategory],
            'courses' => [$course1, $course2, $course3, $course4]
        );
    }
}
