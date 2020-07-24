<?php
// This file is part of local_template_import for Moodle - http://moodle.org/
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
 * Template Import Plugin: Language strings (English)
 *
 * @package    local_template_import
 * @copyright  2020 onwards Stefan Weber <stewe1@gmx.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['activate'] = 'Activate';
$string['activate_help'] = 'Activate the plugin';
$string['backupfileadded'] = 'Backup file <b>{$a->filename}</b> has been added as a template for courses with the identifier <b>{$a->identifier}</b>.';
$string['backupfilealreadyonlist'] = 'The file <b>{$a->filename}</b> for courses with the identifier <b>{$a->identifier}</b> is already on the list of avaliable templates.';
$string['backupfileduplicate'] = 'Backup file <b>{$a->filename}</b> could not be added because another file with the same name already exist in the list of backup files.';
$string['backupfilenolongerexists'] = 'The file <b>{$a->filename}</b> for courses with the identifier <b>{$a->identifier}</b> has been renamed or deleted and was removed from the list of available templates.';
$string['backupfilenolongermatches'] = 'The file <b>{$a->filename}</b> for courses with the identifier <b>{$a->identifier}</b> no longer fits the template file naming pattern and was removed from the list of available templates.';
$string['backupfileslist'] = 'List of currently available templates';
$string['backupfilesnotfound'] = 'No suitable backup files have been found in the backup file area of this course. Make sure backup files start with <b>{$a}</b>.';
$string['clickoktoimport'] = 'Click OK to import this course.';
$string['clickyestoimport'] = 'Do you want to import this course?';
$string['courseidentifier_help'] = 'Templates are selected by their identifier, which is extracted from the course shortname<br><br>
You can select where to cut the course shortname to get the identifier, and specify the prefix for the backup files that should be used as templates<br><br>
Example: <br>
Course shortname: <b>BEW-1-WS2020-MATH/12345</b><br>
Cut shortname before: <b>-1-WS2020-</b><br>
Cut shortname after: <b>/</b><br>
Template names: <b>QUELLKURS-</b><br>
Result: The plugin will search for a backup file named <b>QUELLKURS-MATH.mbz</b>';
$string['courseshortname_post'] = 'Cut shortname after';
$string['courseshortname_post_help'] = 'Everything after and including this text will be cut off to get the course identifier.';
$string['courseshortname_pre'] = 'Cut shortname before';
$string['courseshortname_pre_help'] = 'Everything before and including this text will be cut off to get the course identifier.';
$string['createtemplatefile'] = 'Create templates from backup files';
$string['filenotfound'] = 'No suitable template for the course {$a->fullname} with the identifier <b>{$a->identifier}</b> has been found.';
$string['importpopuptext'] = 'A predefined template for the course <b>"{$a->fullname}"</b> with the identifier <b>{$a->identifier}</b> exists.<br><br>
Filename: <b>{$a->filename}</b>.';
$string['importpopupdismiss'] = 'You can always import templates later via the link in course settings.';
$string['pluginname'] = 'Automated Template Import';
$string['pluginnamesummary'] = 'Gives teachers the option to import pre-defined backup files into their course with a single mouseclick.';
$string['privacy:metadata'] = 'The Template Import plugin does not store any personal data.';
$string['report'] = 'Report of template import actions';
$string['report_usedimport'] = 'Used import';
$string['report_disclaimer'] = '*) Note: The last column will link to the backup file area that the file was originally saved in, even if the file itself does no longer exist.';
$string['report_dismissed'] = 'Dismissed popup';
$string['stage1'] = 'Restore initialized...';
$string['stage2'] = 'Loading backup file...';
$string['stage4'] = 'Deleting old course...';
$string['stage8'] = 'Confirming restore settings...';
$string['stage16'] = 'Processing...';
$string['stage32'] = 'Done.';
$string['startatcourseid'] = 'Start at courseid';
$string['startatcourseid_help'] = 'Only courses with a courseid higher than this will receive template suggestions via the popup. Remember that "1" is the moodle start page, so start at least at 2.';
$string['table_addedby'] = 'Added by';
$string['table_filename'] = 'Filename';
$string['table_location'] = 'Link to course backup area';
$string['table_identifier'] = 'Identifier';
$string['table_timeadded'] = 'Time added';
$string['template_import:createtemplatefile'] = 'Add backup files to the list of available templates';
$string['template_import:dismisspopup'] = 'Dismiss the popup notification about importing a course template';
$string['template_import:importviamenu'] = 'Get the course settings menu option to import a course template';
$string['template_import:seepopup'] = 'Receive the popup notification about importing a course template';
$string['templatenames'] = 'Template prefix';
$string['templatenames_help'] = 'Defines how the backup files are named. This will be added before the identifier. A file ending ".mbz" will be automatically added.';
$string['templatenames_warning'] = 'Warning! If you change the template prefix, existing templates might be automatically removed from the list of available templates when the list is refreshed!';
$string['warning'] = 'WARNING: Importing a template will delete all current content of the course!';
