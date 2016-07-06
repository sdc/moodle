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

class block_bcu_course_checks_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        if (get_config('bcu_course_checks', 'usersoptions')) {
            $mform->addElement('advcheckbox', 'config_coursesummary', get_string('coursesummaryopt', 'block_bcu_course_checks'),
                    null, null, array(0, 1));
            $mform->setDefault('config_coursesummary', get_config('bcu_course_checks', 'courseimage'));

            $mform->addElement('advcheckbox', 'config_courseimage', get_string('courseimageopt', 'block_bcu_course_checks'),
                    null, null, array(0, 1));
            $mform->setDefault('config_courseimage', get_config('bcu_course_checks', 'courseimage'));

            $mform->addElement('advcheckbox', 'config_coursevisible', get_string('coursevisibleopt', 'block_bcu_course_checks'),
                    null, null, array(0, 1));
            $mform->setDefault('config_coursevisible', get_config('bcu_course_checks', 'coursevisible'));

            $mform->addElement('advcheckbox', 'config_courseguest', get_string('courseguestopt', 'block_bcu_course_checks'),
                    null, null, array(0, 1));
            $mform->setDefault('config_courseguest', get_config('bcu_course_checks', 'courseguest'));

            $mform->addElement('advcheckbox' , 'config_renamedsections', get_string('courserenamesecopt', 'block_bcu_course_checks'),
                    null, null, array(0, 1));
            $mform->setDefault('config_renamedsections', get_config('bcu_course_checks', 'renamedsections'));

            $mform->addElement('advcheckbox' , 'config_summarysections', get_string('sectionsummaryopt', 'block_bcu_course_checks'),
                    null, null, array(0, 1));
            $mform->setDefault('config_summarysections', get_config('bcu_course_checks', 'summarysections'));

            $mform->addElement('advcheckbox' , 'config_contentsections', get_string('sectioncontentopt', 'block_bcu_course_checks'),
                    null, null, array(0, 1));
            $mform->setDefault('config_contentsections', get_config('bcu_course_checks', 'contentsections'));

            $mform->addElement('advcheckbox' , 'config_visiblesections', get_string('sectionvisibleopt', 'block_bcu_course_checks'),
                    null, null, array(0, 1));
            $mform->setDefault('config_visiblesections', get_config('bcu_course_checks', 'visiblesections'));
            
            $mform->addElement('advcheckbox' , 'config_assignmentchecks', get_string('assignmentcheckopt', 'block_bcu_course_checks'),
                    null, null, array(0, 1));
            $mform->setDefault('config_assignmentchecks', get_config('bcu_course_checks', 'assignmentchecks'));
        }
    }
}