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
 * Restrict access by dedication time
 *
 * @package     availability_dedicationtime
 * @copyright   2024 Santosh N. <santosh.nag2217@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace availability_dedicationtime;
/**
 * Restrict access by dedication time
 *
 * @package     availability_dedicationtime
 * @copyright   2024 Santosh N. <santosh.nag2217@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontend extends \core_availability\frontend {
    /**
     * Gets a list of string identifiers (in the plugin's language file) that are required in JavaScript for this plugin.
     *
     * @return Array of required string identifiers
     */
    protected function get_javascript_strings() {
        return ['title', 'description'];
    }
    /**
     * Delivers parameters to the javascript part of the plugin
     *
     * @param  \stdClass $course Course object
     * @param  \cm_info $cm Course-module currently being edited (null if none)
     * @param  \section_info $section Section currently being edited (null if none)
     * @return array Array of parameters for the JavaScript function
     */
    protected function get_javascript_init_params($course, \cm_info $cm = null, \section_info $section = null) {
        global $OUTPUT;
        $html = $OUTPUT->render_from_template('availability_dedicationtime/dedication', []);
        return [$html];
    }
}
