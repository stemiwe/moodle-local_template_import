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
 * Dismiss page for local_template_import
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
$url = new moodle_url('/local/template_import/dismiss.php', array('contextid'=>$contextid));

// Security and access check.
require_login($course, false, $cm);
require_capability('local/template_import:dismisspopup', $context);

// Mark this course as popup dismissed.
$now = new DateTime("now", core_date::get_user_timezone_object());
$timestamp = $now->getTimestamp();
$dataobject = new stdClass();
$dataobject->courseid = $course->id;
$dataobject->usedimport = 0;
$dataobject->timeimported = $timestamp;
$dataobject->templatefileid = '';

$DB->insert_record('local_template_import', $dataobject, false);

// Back to course.
redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
