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
 * Execute restore process for local_template_import
 *
 * @package   local_template_import
 * @copyright  2020 onwards Stefan Weber <stewe1@gmx.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
defined('MOODLE_INTERNAL') || die();
global $USER, $DB;

// Restore of large courses requires extra memory. Use the amount configured
// in admin settings.
raise_memory_limit(MEMORY_EXTRA);

// Get params.
$contextid = required_param('contextid', PARAM_INT);
$fileid = required_param('fileid', PARAM_INT);
list($context, $course, $cm) = get_context_info_array($contextid);
if (!$course = $DB->get_record('course', array('id' => $course->id))) {
    print_error('invalidcourseid');
}
$courseurl = course_get_url($course->id);
$backupmode = backup::MODE_GENERAL;

// Security and access check.
require_login($course, null, $cm);
require_capability('moodle/restore:restorecourse', $context);

// Create page.
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/template_import/restore.php', array('contextid'=>$contextid)));
$PAGE->set_title($course->shortname . ': ' . get_string('pluginname', 'local_template_import'));
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);

echo $OUTPUT->header();

// Go through all the restore stages.
// It might be unelegant to code this in a linear fashion, but
// it is much easier to read/understand :).
// Note that stages are base 2 (1, 2, 4, 8, etc...)

// Stage 1: STAGE_CONFIRM ------------------------------------------
$stage = restore_ui::STAGE_CONFIRM;
$_POST['stage'] = $stage;

// Processing Stage 1 creates the backup files in /moodledata/temp/backup
$restore = restore_ui::engage_independent_stage($stage, $contextid);
$restore->process();

// Get filepath from POST url that would be rendered on the button in stage 1
// We will continue to write variables directly into POST, since subsystems
// collect their data from there.
$renderer = $PAGE->get_renderer('local_template_import');
$filepath = $restore->display($renderer);
$renderer = $PAGE->get_renderer('core','backup');

// Finish Stage 1.
$restore->destroy();
unset($restore);
echo local_template_import_write_stage_log_line($stage);


// Stage 2: STAGE_DESTINATION ------------------------------------------
$stage = restore_ui::STAGE_DESTINATION;
$_POST['stage'] = $stage;
$_POST['sesskey'] = sesskey();
$_POST['contextid'] = $contextid;
$_POST['filepath'] = $filepath;

// Process Stage 2.
$restore = restore_ui::engage_independent_stage($stage, $contextid);
$restore->process();

// Finish Stage 2.
$restore->destroy();
unset($restore);
echo local_template_import_write_stage_log_line($stage);


// Stage 4: STAGE_SETTINGS - PART 1-------------------------------------
$stage = restore_ui::STAGE_SETTINGS;
$_POST['stage'] = $stage;
$_POST['target'] = "0";
$_POST['targetid'] = $course->id;

// Process Stage 4 Part 1.
$restore = restore_ui::engage_independent_stage($stage/2, $contextid);
if ($restore->process()) {
    $rc = new restore_controller($restore->get_filepath(), $restore->get_course_id(), backup::INTERACTIVE_YES,
            $backupmode, $USER->id, $restore->get_target());
}
$restore->process();

// Finish Stage 4 Part 1.
$restore->destroy();
unset($restore);

// Stage 4: STAGE_SETTINGS - PART 2-------------------------------------
$_POST['_qf__restore_settings_form'] = "1";
$_POST['setting_root_competencies'] = "0";
$_POST['setting_root_enrolments'] = "0";
$_POST['setting_root_activities'] = "1";
$_POST['setting_root_blocks'] = "1";
$_POST['setting_root_filters'] = "1";
$_POST['setting_root_calendarevents'] = "1";
$_POST['setting_root_groups'] = "1";
$_POST['setting_root_customfields'] = "1";

// Check if the format conversion must happen first.
if ($rc->get_status() == backup::STATUS_REQUIRE_CONV) {
    $rc->convert();
}

// Process Stage 4 Part 2.
$restore = new restore_ui($rc, array('contextid'=>$context->id));
// Get Restoreid.
$restoreid = $restore->get_restoreid();
$_POST['restore'] = $restoreid;
$restore->process();

// End Stage 4 Part 2.
$restore->save_controller();
$restore->destroy();
unset($restore);
echo local_template_import_write_stage_log_line($stage);


// Stage 8: STAGE_SCHEMA ------------------------------------------
$stage = restore_ui::STAGE_SCHEMA;
$_POST['stage'] = $stage;
$_POST['_qf__restore_schema_form'] = "1";
$_POST['setting_course_overwrite_conf'] = "1";
$_POST['setting_course_keep_roles_and_enrolments'] = "1";
$_POST['setting_course_keep_groups_and_groupings'] = "1";

// Collect all sections and activities.
$info = $rc->get_info();
$sections = array_keys($info->sections);
$activities = array_keys($info->activities);
foreach ($sections as $section) {
    $_POST['setting_section_' . $section . '_included'] = "1";
}
foreach ($activities as $activity) {
    $_POST['setting_activity_' . $activity . '_included'] = "1";
}

// Process Stage 8.
$rc = restore_ui::load_controller($restoreid);
$restore = new restore_ui($rc, array('contextid'=>$contextid));
$restore->process();

// End Stage 8.
$restore->save_controller();
$restore->destroy();
unset($restore);
echo local_template_import_write_stage_log_line($stage);


// Stage 16: STAGE_REVIEW ------------------------------------------
$stage = restore_ui::STAGE_REVIEW;
$_POST['stage'] = $stage;
$_POST['_qf__restore_review_form'] = "1";
$_POST['setting_root_users'] = "0";
$_POST['setting_root_comments'] = "0";
$_POST['setting_root_badges'] = "0";
$_POST['setting_root_role_assignments'] = "0";
$_POST['setting_root_userscompletion'] = "0";
$_POST['setting_root_logs'] = "0";
$_POST['setting_root_grade_histories'] = "0";
$_POST['setting_course_overwrite_conf'] = "1";
$_POST['setting_course_course_shortname'] = $course->shortname;
$_POST['setting_course_course_fullname'] = $course->fullname;
$_POST['setting_course_course_startdate'] = $course->startdate;
$_POST['setting_course_keep_roles_and_enrolments'] = "1";
$_POST['setting_course_keep_groups_and_groupings'] = "1";

// Process Stage 16.
$rc = restore_ui::load_controller($restoreid);
$restore = new restore_ui($rc, array('contextid'=>$contextid));
$restore->process();

// End Stage 16.
$restore->save_controller();
$restore->destroy();
unset($restore);
echo local_template_import_write_stage_log_line($stage);


// Stage 32: STAGE_PROCESS ------------------------------------------
$stage = restore_ui::STAGE_PROCESS;
$_POST['stage'] = $stage;

// Process Stage 32.
$rc = restore_ui::load_controller($restoreid);
$restore = new restore_ui($rc, array('contextid'=>$contextid));
$restore->process();

// Process restore.
try {
    // Div used to hide the 'progress' step once the page gets onto 'finished'.
    echo html_writer::start_div('', array('id' => 'executionprogress'));
    // Show the current restore state (header with bolded item).
    echo $renderer->progress_bar($restore->get_progress_bar());
    // Start displaying the actual progress bar percentage.
    $restore->get_controller()->set_progress(new \core\progress\display());
    // Prepare logger.
    $logger = new core_backup_html_logger($CFG->debugdeveloper ? backup::LOG_DEBUG : backup::LOG_INFO);
    $restore->get_controller()->add_logger($logger);
    // Do actual restore.
    $restore->execute();
    // Get HTML from logger.
    if ($CFG->debugdisplay) {
        $loghtml = $logger->get_html();
    }
    // Hide this section because we are now going to make the page show 'finished'.
    echo html_writer::end_div();
    echo html_writer::script('document.getElementById("executionprogress").style.display = "none";');
} catch(Exception $e) {
    $restore->cleanup();
    throw $e;
}

// End Stage 32.
echo local_template_import_write_stage_log_line($stage);
echo $restore->display($renderer);
$restore->destroy();
unset($restore);

// Set original course parameters.
// (The setting to leave the old names was not included in the POST variables
// Since it required arrays and led to problems with parameter cleaning).
$conditions = array('id' => $course->id);
$DB->set_field('course', 'fullname', $course->fullname, $conditions);
$DB->set_field('course', 'shortname', $course->shortname, $conditions);
$DB->set_field('course', 'startdate', $course->startdate, $conditions);
$DB->set_field('course', 'enddate', $course->enddate, $conditions);
$DB->set_field('course', 'idnumber', $course->idnumber, $conditions);
$DB->set_field('course', 'visible', $course->visible, $conditions);

// Write log to this plugin's DB table
$now = new DateTime("now", core_date::get_user_timezone_object());
$timestamp = $now->getTimestamp();
$dataobject = new stdClass();
$dataobject->courseid = $course->id;
$dataobject->usedimport = 1;
$dataobject->timeimported = $timestamp;
$dataobject->templatefileid = $fileid;

$DB->insert_record('local_template_import', $dataobject, false);

echo $OUTPUT->footer();
