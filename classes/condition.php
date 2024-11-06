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
 * Condition to restrict access by dedication time
 *
 * @package     availability_dedicationtime
 * @copyright   2024 Santosh N. <santosh.nag2217@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace availability_dedicationtime;
use core_availability\info;
/**
 * Dedication time condition.
 *
 * @package availability_dedicationtime
 * @copyright 2024 Santosh N. <santosh.nag2217@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {

    /** @var string dedication time value */
    protected $dedicationtime;

    /** @var string dedication time unit */
    protected $unit;

    /**
     * Constructor.
     *
     * @param \stdClass $structure Data structure from JSON decode
     * @throws \coding_exception If invalid data structure.
     */
    public function __construct($structure) {
        $this->dedicationtime = $structure->dedicationtime;
        $this->unit = $structure->unit;
    }

    /**
     * Determines whether this item is availabile.
     *
     * @param  bool $not Set true if we are inverting the condition
     * @param  \core_availability\info $info Item we're checking
     * @param  bool $grabthelot if true, caches information required for all course-modules
     * @param  int $userid User ID to check availability for
     * @return bool true if available
     */
    public function is_available($not, info $info, $grabthelot, $userid) {
        global $DB;
        $modinfo = $info->get_modinfo();
        $course = $modinfo->get_course();
        $sql = "SELECT SUM(timespent) as timespent
                  FROM {block_dedication}
                 WHERE userid = ?
                       AND courseid = ?";
        $data = $DB->get_record_sql($sql, [$userid, $course->id]);
        $dtime = $this->get_dedication_time();
        if ($not) {
            if ($data->timespent == $dtime) {
                return false;
            }
            return true;
        }
        if ($data->timespent >= $dtime) {
            return true;
        }
        return false;
    }

    /**
     * Returns the dedication time in seconds
     * @return int time in seconds
     */
    protected function get_dedication_time() {
        if ($this->unit == 'hours') {
            return $this->dedicationtime * 3600;
        }
        return $this->dedicationtime * 60;
    }

    /**
     * Converts seconds into hours and minutes time format
     * @param int $seconds Seconds
     * @return array time value array
     */
    protected function seconds_to_hours_minutes($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        return [
            'hours' => $hours,
            'minutes' => $minutes,
        ];
    }

    /**
     * Obtains a string describing this restriction (whether or not it actually applies).
     *
     * @param  bool $full Set true if this is the 'full information' view
     * @param  bool $not Set true if we are inverting the condition
     * @param  \core_availability\info $info Item we're checking
     * @return string Information string (for admin) about all restrictions on this item
     */
    public function get_description($full, $not, info $info) {
        $dseconds = $this->get_dedication_time();
        $dtime = $this->seconds_to_hours_minutes($dseconds);
        if ($not) {
            return get_string('requires_notfinish', 'availability_dedicationtime', $dtime);
        }

        return get_string('requires_finish', 'availability_dedicationtime', $dtime);
    }

    /**
     * Obtains a representation of the options of this condition as a string, for debugging.
     *
     * @return string dedication time value
     */
    protected function get_debug_string() {
        return $this->dedicationtime . ' ' . $this->unit;
    }

    /**
     * Saves tree data back to a structure object.
     *
     * @return \stdClass Structure object (ready to be made into JSON format)
     */
    public function save() {
        return (object)[
            'type' => 'dedicationtime',
            'dedicationtime' => $this->dedicationtime,
            'unit' => $this->unit,
        ];
    }
}
