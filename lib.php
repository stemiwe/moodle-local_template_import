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
 * Template Import Plugin: Libraries
 *
 * @package   local_template_import
 * @copyright  2020 onwards Stefan Weber <stewe1@gmx.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// Add option for template import to course settings menu
function local_template_import_extend_settings_navigation($navigation, $context) {
    global $CFG, $COURSE, $PAGE;

    // Is the plugin activated?
    if (get_config('local_template_import', 'activate') !== "1") {
        return;
    }

    // If not in a course context, then leave.
    if ($context == null || $context->contextlevel != CONTEXT_COURSE) {
        return;
    }

    // Are we on the course main page?
    if (!strpos($PAGE->url, '/course/view.php')) {
        return;
    }

    // Keeps us off the front page.
    if (null == ($courseadminnode = $navigation->get('courseadmin'))) {
        return;
    }

    // Add link to import templates.
    if (has_capability('local/template_import:importviamenu', $context)) {
        $urltext = get_string('pluginname', 'local_template_import');
        $url = new moodle_url($CFG->wwwroot . '/local/template_import/import.php', array('contextid' => $context->id));
        $icon = new pix_icon('i/restore', '');
        $node = $courseadminnode->create($urltext, $url, navigation_node::TYPE_SETTING, null, 'local_template_import_restore', $icon);
        $courseadminnode->add_node($node, 'import');
    }

    // Add link to create templates.
    if (has_capability('local/template_import:createtemplatefile', $context)) {
        $urltext = get_string('createtemplatefile', 'local_template_import');
        $url = new moodle_url($CFG->wwwroot . '/local/template_import/settemplates.php', array('contextid' => $context->id));
        $icon = new pix_icon('i/backup', '');
        $node = $courseadminnode->create($urltext, $url, navigation_node::TYPE_SETTING, null, 'local_template_import_create', $icon);
        $courseadminnode->add_node($node, 'import');
    }
}

//Insert code at start of course page
function local_template_import_before_standard_top_of_body_html() {
    global $COURSE, $DB, $PAGE;

    // Is the plugin activated?
    if (get_config('local_template_import', 'activate') !== "1") {
        return;
    }

    // Are we allowed to see the popup?
    $context = context_course::instance($COURSE->id);
    if (!has_capability('local/template_import:seepopup', $context)) {
        return;
    }

    // Are we in a course? Is the course new enough?
    if ($COURSE->id < 2 || $COURSE->id < get_config('local_template_import', 'startatcourseid')) {
        return;
    }

    // Are we on the course main page?
    if (!strpos($PAGE->url, '/course/view.php')) {
        return;
    }

    // Has this course already been processed?
    $conditions = array('courseid' => $COURSE->id);
    if (!$DB->record_exists('local_template_import', $conditions)) {
        $identifier = local_template_import_get_course_identifier($COURSE->shortname);
        $backupfile = local_template_import_get_backup_file($identifier);

        // If a backup file exists, show the popup.
        if ($backupfile) {
            $a = new stdClass();
            $a->fullname = $COURSE->fullname;
            $a->identifier = $identifier;
            $a->filename = $backupfile->filename;
            return(local_template_import_display_import_message($a, $backupfile));
        }
    }
}

/**
 * Find backup file for a course
 *
 * @param string $identifier the course identifier
 * @return object $file the file object for the backup file
 */
function local_template_import_get_backup_file($identifier) {
    global $DB;
    $conditions = array('identifier' => $identifier);
    $fileid = $DB->get_field('local_template_import_files', 'fileid', $conditions);
    if ($fileid) {
        $conditions = array('id' => $fileid);
        return $DB->get_record('files', $conditions);
    }
}


/**
 * Returns the identifier for this course.
 *
 * @param string $shortname the course shortname
 * @return string $identifier the identifier for this course
 */
 function local_template_import_get_course_identifier($shortname) {
     $csprecut = get_config('local_template_import', 'courseshortname_pre');
     $cspostcut = get_config('local_template_import', 'courseshortname_post');
     // Cut before
     $identifier = explode($csprecut, $shortname);
     if (array_key_exists(1, $identifier)) {
         $identifier = $identifier[1];
     } else {
         $identifier = $identifier[0];
     }
     // Cut after
     $identifier = explode($cspostcut, $identifier);
     $identifier = $identifier[0];
     return $identifier;
 }


/**
 * Displays the popup to ask for import of template.
 *
 * @param object $a the parameters customizing the langstring text
 * @param object $backupfile the parameters defining the backupfile
 * @return string popup html
 */
function local_template_import_display_import_message($a, $backupfile) {
    global $COURSE, $CFG, $PAGE;
    $context = context_course::instance($COURSE->id);
    $header = get_string('pluginname', 'local_template_import');
    $text = get_string('importpopuptext', 'local_template_import', $a);
    $restoreurl = new moodle_url('/local/template_import/restore.php',
        array('contextid' => $context->id,
              'pathnamehash' => $backupfile->pathnamehash,
              'contenthash' => $backupfile->contenthash,
              'fileid' => $backupfile->id,
          ));

    // Start modal.
    $popup = '<div class="modal show" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="m-auto">' . $header . '</h5>
                        </div>
                        <div class="modal-body">
                            <p>' . $text . '</p>';

    // If the user is allowed to dismiss the popup, render two buttons.
    if (has_capability('local/template_import:dismisspopup', $context)) {
        $popup .=          '<p>' . get_string('clickyestoimport', 'local_template_import') . '</p>
                            <p>' . get_string('warning', 'local_template_import') . '</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <a class="btn btn-primary" href="' . $restoreurl . '">'
                            . get_string('yes') .
                            '</a>
                            <a class="btn btn-secondary" href="' . new moodle_url($CFG->wwwroot . '/local/template_import/dismiss.php', array('contextid' => $context->id)) . '">'
                            . get_string('no') .
                            '</a>
                        </div>';
    // If not, render only OK button.
    } else {
        $popup .=          '<p>' . get_string('clickoktoimport', 'local_template_import') . '</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <a class="btn btn-primary" href="' . new moodle_url($CFG->wwwroot . '/local/template_import/import.php', array('contextid' => $context->id)) . '">'
                            . get_string('ok') .
                            '</a>
                        </div>';
    }

    // Close divs.
    $popup .=       '</div>
                </div>
            </div>';

    return $popup;

}


/**
 * Writes a log line .
 *
 * @param int $stage the current stage of the backup.
 * @return string html
 */
function local_template_import_write_stage_log_line($stage) {
    $html = "<br>";
    $html .= html_writer::span(get_string('stage' . $stage, 'local_template_import'));
    return $html;
}
