<?php
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Unit test class to test condition class
 *
 * @package     availability_dedicationtime
 * @copyright   2024 Santosh N. <santosh.nag2217@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace availability_dedicationtime;

use advanced_testcase;
use availability_dedicationtime\condition;
use core_availability\{tree, mock_info, info_module, info_section};
use stdClass;

/**
 * Unit test class to test condition class
 *
 * @package     availability_dedicationtime
 * @copyright   2024 Santosh N. <santosh.nag2217@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \availability_dedicationtime\condition
 */
final class condition_test extends advanced_testcase {
    /** @var stdClass course. */
    private $course;

    /** @var stdClass user. */
    private $user;

    /**
     * Create course and page.
     */
    public function setUp(): void {
        global $CFG;
        parent::setUp();
        require_once($CFG->dirroot . '/availability/tests/fixtures/mock_info.php');
        require_once($CFG->dirroot . '/availability/tests/fixtures/mock_info_module.php');
        require_once($CFG->dirroot . '/availability/tests/fixtures/mock_info_section.php');
        require_once($CFG->libdir . '/completionlib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $CFG->enablecompletion = true;
        $CFG->enableavailability = true;
        set_config('enableavailability', true);
        $dg = $this->getDataGenerator();
        $now = time();
        $this->course = $dg->create_course(['startdate' => $now, 'enddate' => $now + 7 * WEEKSECS, 'enablecompletion' => 1]);
        $this->user = $dg->create_user(['timezone' => 'UTC']);
        $dg->enrol_user($this->user->id, $this->course->id, 5, time());
    }
    /**
     * Tests whether description is correct
     * @covers \availability_dedicationtime\condition
     * @return void
     */
    public function test_get_description(): void {
        $info = new \core_availability\mock_info();
        $structure = (object)['type' => 'dedicationtime', 'dedicationtime' => '5', 'unit' => 'minutes'];
        $cond = new condition($structure);
        $description = $cond->get_description(true, false, $info);
        $this->assertMatchesRegularExpression('~<strong>0</strong> hours and <strong>5</strong> minutes~', $description);

        $description = $cond->get_description(true, true, $info);
        $this->assertMatchesRegularExpression('~<strong>0</strong> hours and <strong>5</strong> minutes~', $description);
    }
    /**
     * Tests whether activity is available or not
     * @covers \availability_dedicationtime\condition
     * @return void
     */
    public function test_is_available(): void {
        global $DB, $USER;

        $data = new stdClass();
        $data->userid = $this->user->id;
        $data->courseid = $this->course->id;
        $data->timespent = 120;
        $data->timestart = time();
        $DB->insert_record('block_dedication', $data);

        $pg = $this->getDataGenerator()->get_plugin_generator('mod_page');
        $pg->create_instance(['course' => $this->course, 'completion' => COMPLETION_TRACKING_MANUAL]);

        $this->setUser($this->user);
        $info = new mock_info($this->course, $this->user->id);

        // Check whether activity is available.
        $structure = (object)['type' => 'dedicationtime', 'dedicationtime' => '2', 'unit' => 'minutes'];
        $cond = new condition($structure);
        $this->assertTrue($cond->is_available(false, $info, false, $USER->id));

        // Check whether activity is not available.
        $structure = (object)['type' => 'dedicationtime', 'dedicationtime' => '3', 'unit' => 'minutes'];
        $cond = new condition($structure);
        $this->assertFalse($cond->is_available(false, $info, false, $USER->id));
    }
    /**
     * Tests whether save() returns correct response
     * @covers \availability_dedicationtime\condition
     * @return void
     */
    public function test_save(): void {
        $structure = (object)['dedicationtime' => '5', 'unit' => 'minutes'];
        $cond = new condition($structure);
        $structure->type = 'dedicationtime';
        $this->assertEquals($structure, $cond->save());
    }
}
