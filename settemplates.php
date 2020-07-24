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
 * Set templates page for local_template_import
 *
 * @package   local_template_import
 * @copyright  2020 onwards Stefan Weber <stewe1@gmx.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(dirname(dirname(__FILE__))) . '/config.php');

global $DB, $USER;

// Get params.
$contextid = required_param('contextid', PARAM_INT);
list($context, $course, $cm) = get_context_info_array($contextid);
if (!$course = $DB->get_record('course', array('id' => $course->id))) {
    print_error('invalidcourseid');
}
$pattern = get_config('local_template_import', 'templatenames');
$patternlength = strlen($pattern);

// Security and access check.
require_login($course, null, $cm);
require_capability('local/template_import:createtemplatefile', $context);

// Create page.
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url($CFG->wwwroot . '/local/template_import/settemplates.php',
    array('contextid' => $contextid)));
$PAGE->set_title(get_string('pluginname', 'local_template_import'));
$PAGE->set_heading(get_string('pluginname', 'local_template_import'));
$PAGE->set_context($context);

echo $OUTPUT->header();

echo html_writer::start_tag('ul');

// Get current list of templates.
$templatelist = $DB->get_records('local_template_import_files');

// Update current list of templates.
foreach ($templatelist as $template) {
    // Remove no longer existing files from our list of templates.
    $conditions = array('id' => $template->fileid);
    if (!$DB->record_exists('files', $conditions)) {
        $conditions = array('fileid' => $template->fileid);
        $DB->delete_records('local_template_import_files', $conditions);
        echo html_writer::start_tag('li', array('class' => 'alert-warning'));
        echo get_string('backupfilenolongerexists', 'local_template_import', array(
            'filename' => $template->filename,
            'identifier' => $template->identifier
        ));
        echo html_writer::end_tag('li');
    }

    // Remove backup files that no longer match the template prefix.
    if (!$pattern == substr($template->filename, 0, $patternlength)) {
        $conditions = array('filename' => $template->filename);
        $DB->delete_records('local_template_import_files', $conditions);
        echo html_writer::start_tag('li', array('class' => 'alert-warning'));
        echo get_string('backupfilenolongermatches', 'local_template_import', array(
            'filename' => $template->filename,
            'identifier' => $template->identifier
        ));
        echo html_writer::end_tag('li');
    }
}

// Get all backup files from this course
// (sorting them out later is easier than using SQL LIKE)
$conditions = array('contextid' => $context->id, 'component' => 'backup');
$sort = 'timemodified DESC';
$fields = 'id, contenthash, pathnamehash, filename, timemodified';
$allbackupfiles = $DB->get_records('files', $conditions, $sort, $fields);

// Filter backupfiles which match our pattern.
foreach ($allbackupfiles as $id => $backupfile) {
    if ($pattern == substr($backupfile->filename, 0, $patternlength)) {
        // Get identifier for this backup file.
        $identifier = substr($backupfile->filename, $patternlength);
        $identifier = str_replace('.mbz', '', $identifier);
        // Add to list of backupfiles to be added to template list.
        $fileobj = (object) array(
            'fileid' => $id,
            'identifier' => $identifier,
            'filename' => $backupfile->filename,
        );
        $backupfiles[$id] = $fileobj;
    }
}

// Add new files to our list of templates.
if (isset($backupfiles)) {
    foreach ($backupfiles as $id => $backupfile) {
        // Get identifier for this backup file.
        $identifier = substr($backupfile->filename, $patternlength);
        $identifier = str_replace('.mbz', '', $identifier);
        
        // Check if this file already exists.
        $conditions = array('fileid' => $id);
        if ($DB->record_exists('local_template_import_files', $conditions)) {
            echo html_writer::start_tag('li', array('class' => 'alert-warning'));
            echo get_string('backupfilealreadyonlist', 'local_template_import', array(
                'filename' => $backupfile->filename,
                'identifier' => $backupfile->identifier
            ));
            echo html_writer::end_tag('li');
            continue;
        }
        // Check if file with this identifier already exists.
        $conditions = array('identifier' => $identifier);
        if ($DB->record_exists('local_template_import_files', $conditions)) {
            echo html_writer::start_tag('li', array('class' => 'alert-danger'));
            echo get_string('backupfileduplicate', 'local_template_import', array(
                'filename' => $backupfile->filename,
                'identifier' => $backupfile->identifier
            ));
            echo html_writer::end_tag('li');
        } else {
            // Get user.
            $backupfile->addedby = $USER->id;
            // Get timestamp.
            $now = new DateTime("now", core_date::get_user_timezone_object());
            $timestamp = $now->getTimestamp();
            $backupfile->timeadded = $timestamp;
            $backupfile->contextid = $context->id;
            // Add this file to the list of available templates.
            $DB->insert_record('local_template_import_files', $backupfile);
            echo html_writer::start_tag('li', array('class' => 'alert-success'));
            echo get_string('backupfileadded', 'local_template_import', array(
                'filename' => $backupfile->filename,
                'identifier' => $backupfile->identifier
            ));
            echo html_writer::end_tag('li');
        }
    }
} else {
    // No suitable template files found.
    echo html_writer::start_tag('li', array('class' => 'alert-warning'));
    echo get_string('backupfilesnotfound', 'local_template_import', $pattern);
    echo html_writer::end_tag('li');
}

echo html_writer::end_tag('ul');

// Show current list of templates.
$templatelist = $DB->get_records('local_template_import_files');
echo html_writer::tag('h3', get_string('backupfileslist', 'local_template_import'));

// TODO: turn this into a proper moodle table
echo html_writer::start_tag('table');
echo html_writer::start_tag('tr');
echo "<th class='p-1'>" . get_string('table_identifier', 'local_template_import') . "</th>
      <th class='p-1'>" . get_string('table_filename', 'local_template_import') . "</th>
      <th class='p-1'>" . get_string('table_addedby', 'local_template_import') . "</th>
      <th class='p-1'>" . get_string('table_timeadded', 'local_template_import') . "</th>
      <th class='p-1'>" . get_string('table_location', 'local_template_import') . "</th>";
echo html_writer::end_tag('tr');

foreach ($templatelist as $template) {
    echo html_writer::start_tag('tr');
    $username = $DB->get_field('user', 'username', array('id' => $template->addedby));
    $filelink = new moodle_url('/backup/restorefile.php', array('contextid' => $template->contextid));
    echo "<td class='p-1'>" . $template->identifier . "</td>
          <td class='p-1'>" . $template->filename . "</td>
          <td class='p-1'>" . $username . "</td>
          <td class='p-1'>" . userdate($template->timeadded) . '</td>
          <td class="p-1"><a href="' . $filelink . '">' .
          $template->contextid . "</td>";
    echo html_writer::end_tag('tr');
}
echo html_writer::end_tag('table');

$returnurl = new moodle_url('/course/view.php', array('id' => $course->id));
echo '<div class="p-1 text-center"><a class="btn btn-primary" href="'
 . $returnurl . '"> ' . get_string('back') . '</a></div>';

echo $OUTPUT->footer();
