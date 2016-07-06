<?php
// This file is part of Moodle - http://moodle.org/
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
 * Version details
 *
 * @package    block
 * @subpackage bcu_course_checks
 * @copyright  2014 Michael Grant <michael.grant@bcu.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/blocks/bcu_course_checks/locallib.php');
if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configcheckbox(
                'bcu_course_checks/coursecleanup',
                get_string('coursecleanupopt', 'block_bcu_course_checks'),
                get_string('coursecleanupopt', 'block_bcu_course_checks'),
                '1'
            ));

    $settings->add(new admin_setting_configcheckbox(
                'bcu_course_checks/usersoptions',
                get_string('courseuseropt', 'block_bcu_course_checks'),
                get_string('courseuseropt', 'block_bcu_course_checks'),
                '1'
            ));

    $settings->add(new admin_setting_configcheckbox(
                'bcu_course_checks/coursesummary',
                get_string('coursesummaryopt', 'block_bcu_course_checks'),
                get_string('coursesummaryopt', 'block_bcu_course_checks'),
                '1'
            ));

    $settings->add(new admin_setting_configcheckbox(
                'bcu_course_checks/courseimage',
                get_string('courseimageopt', 'block_bcu_course_checks'),
                get_string('courseimageopt', 'block_bcu_course_checks'),
                '1'
            ));

    $settings->add(new admin_setting_configcheckbox(
                'bcu_course_checks/coursevisible',
                get_string('coursevisibleopt', 'block_bcu_course_checks'),
                get_string('coursevisibleopt', 'block_bcu_course_checks'),
                '1'
            ));

    $settings->add(new admin_setting_configcheckbox(
                'bcu_course_checks/courseguest',
                get_string('courseguestopt', 'block_bcu_course_checks'),
                get_string('courseguestopt', 'block_bcu_course_checks'),
                '1'
            ));

    $settings->add(new admin_setting_configcheckbox(
                'bcu_course_checks/renamedsections',
                get_string('courserenamesecopt', 'block_bcu_course_checks'),
                get_string('courserenamesecopt', 'block_bcu_course_checks'),
                '1'
            ));

    $settings->add(new admin_setting_configcheckbox(
                'bcu_course_checks/summarysections',
                get_string('sectionsummaryopt', 'block_bcu_course_checks'),
                get_string('sectionsummaryopt', 'block_bcu_course_checks'),
                '1'
            ));

    $settings->add(new admin_setting_configcheckbox(
                'bcu_course_checks/contentsections',
                get_string('sectioncontentopt', 'block_bcu_course_checks'),
                get_string('sectioncontentopt', 'block_bcu_course_checks'),
                '1'
            ));

    $settings->add(new admin_setting_configcheckbox(
                'bcu_course_checks/visiblesections',
                get_string('sectionvisibleopt', 'block_bcu_course_checks'),
                get_string('sectionvisibleopt', 'block_bcu_course_checks'),
                '1'
            ));
    
    $settings->add(new admin_setting_configcheckbox(
                'bcu_course_checks/assignmentchecks',
                get_string('assignmentcheckopt', 'block_bcu_course_checks'),
                get_string('assignmentcheckopt', 'block_bcu_course_checks'),
                '1'
            ));

    $settings->add(new admin_setting_configmultiselect(
                'bcu_course_checks/blocksenabled',
                get_string('blocksenabledopt', 'block_bcu_course_checks'),
                get_string('blocksenabledopt', 'block_bcu_course_checks'),
                array(),
                block_bcu_course_checks_get_all_blocks()
            ));

    $settings->add(new admin_setting_configmultiselect(
                'bcu_course_checks/formatsname',
                get_string('formatsnameopt', 'block_bcu_course_checks'),
                get_string('formatsnameopt', 'block_bcu_course_checks'),
                array(),
                block_bcu_course_checks_get_all_formats()
            ));
}