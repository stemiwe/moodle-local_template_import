<?php
// This file is part of local_template_import for Moodle - http://moodle.org/
//
// It is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// It is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Report page for local_template_import
 *
 * @package   local_template_import
 * @copyright 2020 onwards Stefan Weber <stewe1@gmx.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('./classes/report_table.php');

defined('MOODLE_INTERNAL') || die();

$context = context_system::instance();
$url = new moodle_url('/local/template_import/report.php');
$PAGE->set_context($context);
$PAGE->set_url($url);
echo $OUTPUT->header();

$table = new \local_template_import\report_table ('uniqueid');
$tablecolumns = array('id',
                      'courseid',
                      'usedimport',
                      'timeimported',
                      'templatefileid'
                 );
$tableheaders = array('id',
                      get_string('course'),
                      get_string('status'),
                      get_string('time'),
                      get_string('file') . ' *)',
                 );
$table->define_columns($tablecolumns);
$table->define_headers($tableheaders);

echo "<h3>" . get_string('report', 'local_template_import') . "</h3>";

// Print table.
$table->set_sql('*', "{local_template_import}", '1=1');
$table->define_baseurl($url);
$table->out(40, true);

// Print footer.
echo get_string('report_disclaimer', 'local_template_import');
echo "<br>";
echo $OUTPUT->single_button(new moodle_url('/admin/settings.php',
    array('section' => 'local_template_import')),
    get_string('back'));
echo $OUTPUT->footer();
