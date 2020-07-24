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
 * Form for local_template_import
 *
 * @package   local_template_import
 * @copyright 2019 Stefan Weber <webers@technikum-wien.at>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/local/template_import/lib.php');

class template_import_form extends moodleform {
    public function definition() {
        global $CFG, $COURSE, $DB;

        $mform = $this->_form;
        $courseid = $this->_customdata['courseid'];
        $context = $this->_customdata['context'];

        // Add form elements
        $filename = get_backup_file('asdf');        

        if ($filename) {
            $mform->addElement('html', '<div class="p-1">' . get_string('filefound', 'local_template_import', $filename) . '</div>');

            $mform->addElement('html', '<div class="p-1">' . get_string('clickoktoimport', 'local_template_import') . '</div>');

            $mform->addElement('html', '<div class="p-1"><b>' . get_string('warning', 'local_template_import') . '</b></div>');

            $this->add_action_buttons($cancel = true,$submitlabel = get_string('import'));
        } else {
            $mform->addElement('html', '<div class="p-1">' . get_string('filenotefound', 'local_template_import', $COURSE->shortname) . '</div>');

            $this->add_action_buttons($cancel = false,$submitlabel = get_string('ok'));
        }

        $mform->addElement('hidden', 'courseid', $courseid);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'filename', $filename);
        $mform->setType('filename', PARAM_TEXT);

    }

    public function validation($data, $files) {
    $errors = parent::validation($data, $files);
    return $errors;
    }

}
