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
 * The class for displaying the report table.
 *
 * @package   local_template_import
 * @copyright 2020 onwards Stefan Weber <stewe1@gmx.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_template_import;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use table_sql;
use moodle_url;
use DateTime;

/**
 * The class for displaying the import report table.
 *
 * @copyright  2020 onwards Stefan Weber <stewe1@gmx.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_table extends table_sql {

    /**
     * Turns data from column "courseid" into link to that course.
     *
     * @param \stdClass $data The row data.
     * @return string Link to course.
     */
    public function col_courseid($data) {
        $url = new moodle_url('/course/view.php', array('id' => $data->courseid));
        return '<a href="' . $url . '">' . $data->courseid . '</a>';
    }

    /**
     * Turns data from column "usedimport" into user-friendly string.
     *
     * @param \stdClass $data The row data.
     * @return string language string.
     */
    public function col_usedimport($data) {
        if ($data->usedimport == 1) {
            return get_string('report_usedimport', 'local_template_import');
        } else {
            return get_string('report_dismissed', 'local_template_import');
        }
    }

    /**
     * Turns data from column "usedimport" into user-friendly string.
     *
     * @param \stdClass $data The row data.
     * @return string User's full name.
     */
    public function col_timeimported($data) {
        $date = new DateTime();
        $date->setTimestamp($data->timeimported);
        return userdate($date->getTimestamp());
    }
}
