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
 * Template Import Plugin: Admin settings
 *
 * @package   local_template_import
 * @copyright 2020 onwards Stefan Weber <stewe1@gmx.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ( $hassiteconfig ){
    $ADMIN->add('root',new admin_category('local_template_import_settings', get_string('pluginname', 'local_template_import')));
	$settings = new admin_settingpage( 'local_template_import', get_string('pluginname', 'local_template_import'));
    $ADMIN->add( 'local_template_import_settings', $settings );

    $url = new moodle_url('/local/template_import/report.php');
    $settings->add( new admin_setting_description(
        'local_template_import/templatenames_report',
        get_string('report', 'local_template_import'),
        '<a class="btn btn-secondary" href="' . $url . '">' .
        get_string('show') . '</a>'
    ) );

    $settings->add( new admin_setting_configcheckbox(
        'local_template_import/activate',
        get_string('activate', 'local_template_import'),
        get_string('activate_help', 'local_template_import'),
        0
    ) );

	$settings->add( new admin_setting_configtext(
		'local_template_import/startatcourseid',
		get_string('startatcourseid', 'local_template_import'),
		get_string('startatcourseid_help', 'local_template_import'),
		2,
		PARAM_INT
	) );

    $settings->add( new admin_setting_description(
        'local_template_import/templatenames_warning',
        get_string('warning'),
        get_string('templatenames_warning', 'local_template_import')
    ) );

    $settings->add( new admin_setting_configtext(
		'local_template_import/templatenames',
		get_string('templatenames', 'local_template_import'),
		get_string('templatenames_help', 'local_template_import'),
		'TEMPLATE-',
		PARAM_TEXT
	) );

    $settings->add( new admin_setting_configtext(
		'local_template_import/courseshortname_pre',
		get_string('courseshortname_pre', 'local_template_import'),
		get_string('courseshortname_pre_help', 'local_template_import'),
		'-1-WS2020-',
		PARAM_TEXT
	) );

    $settings->add( new admin_setting_configtext(
		'local_template_import/courseshortname_post',
		get_string('courseshortname_post', 'local_template_import'),
		get_string('courseshortname_post_help', 'local_template_import'),
		'/',
		PARAM_TEXT
	) );

}
