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
 * Import page for local_template_import
 *
 * @package   local_template_import
 * @copyright 2020 onwards Stefan Weber <stewe1@gmx.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/local/template_import/template_import_form.php');
defined('MOODLE_INTERNAL') || die();

// Get params.
$contextid = required_param('contextid', PARAM_INT);
list($context, $course, $cm) = get_context_info_array($contextid);

// Define URLs.
$url = new moodle_url('/local/template_import/import.php', array('contextid'=>$contextid));

// Security and access check.
require_login($course, false, $cm);
require_capability('local/template_import:importviamenu', $context);

// Get backup file for this course.
$identifier = local_template_import_get_course_identifier($COURSE->shortname);
$backupfile = local_template_import_get_backup_file($identifier);

// Create page.
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_template_import'));
$PAGE->set_heading(get_string('pluginname', 'local_template_import'));
$PAGE->requires->js_call_amd('core_backup/async_backup', 'asyncBackupAllStatus', array($context->id));

// Output.
echo $OUTPUT->header();
echo $OUTPUT->container_start();

if ($backupfile) {
    $restoreurl = new moodle_url('/local/template_import/restore.php',
        array('contextid' => $contextid,
              'pathnamehash' => $backupfile->pathnamehash,
              'contenthash' => $backupfile->contenthash,
              'fileid' => $backupfile->id,
          ));
    $a = new stdClass();
    $a->fullname = $COURSE->fullname;
    $a->identifier = $identifier;
    $a->filename = $backupfile->filename;
    echo '<div class="p-1">' . get_string('importpopuptext', 'local_template_import', $a) . '</div>';
    echo '<div class="p-1">' . get_string('clickyestoimport', 'local_template_import') . '</div>';
    echo '<div class="p-1"><b>' . get_string('warning', 'local_template_import') . '</b></div>';
    echo '<div class="p-1 text-center"><a class="btn btn-primary" href="' . $restoreurl . '"> ' . get_string('ok') . '</a></div>';

} else {
    $returnurl = new moodle_url('/course/view.php', array('id' => $course->id));
    $a = new stdClass();
    $a->fullname = $COURSE->fullname;
    $a->identifier = $identifier;
    echo '<div class="p-1">' . get_string('filenotfound', 'local_template_import', $a) . '</div>';
    echo '<div class="p-1 text-center"><a class="btn btn-primary" href="' . $returnurl . '"> ' . get_string('back') . '</a></div>';
}

echo $OUTPUT->container_end();
echo $OUTPUT->footer();
