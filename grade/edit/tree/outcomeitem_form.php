<?php  //$Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once $CFG->libdir.'/formslib.php';

class edit_outcomeitem_form extends moodleform {
    function definition() {
        global $COURSE, $CFG;

        $mform =& $this->_form;

/// visible elements
        $mform->addElement('header', 'general', get_string('gradeoutcomeitem', 'grades'));

        $mform->addElement('text', 'itemname', get_string('itemname', 'grades'));
        $mform->addRule('itemname', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'iteminfo', get_string('iteminfo', 'grades'));
        $mform->setHelpButton('iteminfo', array(false, get_string('iteminfo', 'grades'),
                false, true, false, get_string('iteminfohelp', 'grades')));

        $mform->addElement('text', 'idnumber', get_string('idnumber'));
        $mform->setHelpButton('idnumber', array(false, get_string('idnumber'),
                false, true, false, get_string('idnumberhelp', 'grades')));

        // allow setting of outcomes on module items too
        $options = array();
        if ($outcomes = grade_outcome::fetch_all_available($COURSE->id)) {
            foreach ($outcomes as $outcome) {
                $options[$outcome->id] = $outcome->get_name();
            }
        }
        $mform->addElement('select', 'outcomeid', get_string('outcome', 'grades'), $options);
        $mform->setHelpButton('outcomeid', array(false, get_string('outcomeid', 'grades'),
                false, true, false, get_string('outcomeidhelp', 'grades')));
        $mform->addRule('outcomeid', get_string('required'), 'required');

        $options = array(0=>get_string('none'));
        if ($coursemods = get_course_mods($COURSE->id)) {
            foreach ($coursemods as $coursemod) {
                $mod = get_coursemodule_from_id($coursemod->modname, $coursemod->id);
                $options[$coursemod->id] = format_string($mod->name);
            }
        }
        $mform->addElement('select', 'cmid', get_string('linkedactivity', 'grades'), $options);
        $mform->setHelpButton('cmid', array(false, get_string('linkedactivity', 'grades'),
                false, true, false, get_string('linkedactivityhelp', 'grades')));
        $mform->setDefault('cmid', 0);

        //$mform->addElement('text', 'calculation', get_string('calculation', 'grades'));

        /*$mform->addElement('text', 'gradepass', get_string('gradepass', 'grades'));
        $mform->setHelpButton('gradepass', array(false, get_string('gradepass', 'grades'),
                false, true, false, get_string('gradepasshelp', 'grades')));*/

        $mform->addElement('text', 'aggregationcoef', get_string('aggregationcoef', 'grades'));

        /// hiding
        /// advcheckbox is not compatible with disabledIf !!
        $mform->addElement('checkbox', 'hidden', get_string('hidden', 'grades'));
        $mform->setHelpButton('hidden', array('hidden', get_string('hidden', 'grades'), 'grade'));
        $mform->addElement('date_time_selector', 'hiddenuntil', get_string('hiddenuntil', 'grades'), array('optional'=>true));
        $mform->setHelpButton('hiddenuntil', array('hiddenuntil', get_string('hiddenuntil', 'grades'), 'grade'));
        $mform->disabledIf('hidden', 'hiddenuntil[off]', 'notchecked');

        //locking
        $mform->addElement('advcheckbox', 'locked', get_string('locked', 'grades'));
        $mform->setHelpButton('locked', array('locked', get_string('locked', 'grades'), 'grade'));
        $mform->addElement('date_time_selector', 'locktime', get_string('locktime', 'grades'), array('optional'=>true));
        $mform->setHelpButton('locktime', array('locktime', get_string('locktime', 'grades'), 'grade'));

/// hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', $COURSE->id);
        $mform->setType('courseid', PARAM_INT);

/// add return tracking info
        $gpr = $this->_customdata['gpr'];
        $gpr->add_mform_elements($mform);

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }


/// tweak the form - depending on existing data
    function definition_after_data() {
        global $CFG, $COURSE;

        $mform =& $this->_form;

        if ($id = $mform->getElementValue('id')) {
            $grade_item = grade_item::fetch(array('id'=>$id));

            //remove the aggregation coef element if not needed
            if ($grade_item->is_course_item()) {
                $mform->removeElement('aggregationcoef');

            } else {
                if ($grade_item->is_category_item()) {
                    $category = $grade_item->get_item_category();
                    $parent_category = $category->get_parent_category();
                } else {
                    $parent_category = $grade_item->get_parent_category();
                }

                $parent_category->apply_forced_settings();

                if (!$parent_category->is_aggregationcoef_used() or !$parent_category->aggregateoutcomes) {
                    $mform->removeElement('aggregationcoef');
                } else {
                    $agg_el =& $mform->getElement('aggregationcoef');
                    if ($parent_category->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN) {
                        $agg_el->setLabel(get_string('aggregationcoefweight', 'grades'));
                        $mform->setHelpButton('aggregationcoef', array(false, get_string('aggregationcoefweight', 'grades'),
                                false, true, false, get_string('aggregationcoefweighthelp', 'grades')));
                    } else if ($parent_category->aggregation == GRADE_AGGREGATE_EXTRACREDIT_MEAN) {
                        $agg_el->setLabel(get_string('aggregationcoefextra', 'grades'));
                        $mform->setHelpButton('aggregationcoef', array(false, get_string('aggregationcoefextra', 'grades'),
                                false, true, false, get_string('aggregationcoefextrahelp', 'grades')));
                    }
                }
            }

        } else {
            $parent_category = grade_category::fetch_course_category($COURSE->id);
            if (!$parent_category->is_aggregationcoef_used() or !$parent_category->aggregateoutcomes) {
                $mform->removeElement('aggregationcoef');
            } else {
                $agg_el =& $mform->getElement('aggregationcoef');
                if ($parent_category->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN) {
                    $agg_el->setLabel(get_string('aggregationcoefweight', 'grades'));
                    $mform->setHelpButton('aggregationcoef', array(false, get_string('aggregationcoefweight', 'grades'),
                            false, true, false, get_string('aggregationcoefweighthelp', 'grades')));
                } else if ($parent_category->aggregation == GRADE_AGGREGATE_EXTRACREDIT_MEAN) {
                    $agg_el->setLabel(get_string('aggregationcoefextra', 'grades'));
                    $mform->setHelpButton('aggregationcoef', array(false, get_string('aggregationcoefextra', 'grades'),
                            false, true, false, get_string('aggregationcoefextrahelp', 'grades')));
                }
            }
        }
    }


/// perform extra validation before submission
    function validation($data){
        $errors= array();

        if (array_key_exists('idnumber', $data)) {
            if ($data['id']) {
                $grade_item = new grade_item(array('id'=>$data['id'], 'courseid'=>$data['courseid']));
            } else {
                $grade_item = null;
            }
            if (!grade_verify_idnumber($data['idnumber'], $grade_item, null)) {
                $errors['idnumber'] = get_string('idnumbertaken');
            }
        }

        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }
    }

}
?>
