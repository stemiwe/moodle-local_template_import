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
 * Template Import Plugin: Access rights
 *
 * @package   local_template_import
 * @copyright 2020 onwards Stefan Weber <stewe1@gmx.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'local/template_import:seepopup' => array(
            'riskbitmask' => RISK_DATALOSS,
            'captype' => 'write',
            'contextlevel' => CONTEXT_COURSE,
            'archetypes' => array(
                    'editingteacher' => CAP_ALLOW,
                    'manager' => CAP_ALLOW,
            ),
            'clonepermissionsfrom' => 'mod/course:update'
    ),
    'local/template_import:dismisspopup' => array(
            'captype' => 'read',
            'contextlevel' => CONTEXT_COURSE,
            'archetypes' => array(
                    'editingteacher'  => CAP_ALLOW,
                    'manager' => CAP_ALLOW,
            ),
            'clonepermissionsfrom' => 'mod/course:delete'
    ),
    'local/template_import:importviamenu' => array(
            'riskbitmask' => RISK_DATALOSS,
            'captype' => 'write',
            'contextlevel' => CONTEXT_COURSE,
            'archetypes' => array(
                    'editingteacher' => CAP_ALLOW,
                    'manager' => CAP_ALLOW,
            ),
            'clonepermissionsfrom' => 'mod/course:delete'
    ),
    'local/template_import:createtemplatefile' => array(
            'riskbitmask' => RISK_CONFIG,
            'captype' => 'write',
            'contextlevel' => CONTEXT_COURSE,
            'archetypes' => array(
                    'manager' => CAP_ALLOW,
            ),
            'clonepermissionsfrom' => 'mod/course:delete'
    ),
);
