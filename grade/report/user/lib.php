<?php // $Id$

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
/**
 * File in which the user_report class is defined.
 * @package gradebook
 */

require_once($CFG->dirroot . '/grade/report/lib.php');
require_once($CFG->libdir.'/tablelib.php');

/**
 * Class providing an API for the user report building and displaying.
 * @uses grade_report
 * @package gradebook
 */
class grade_report_user extends grade_report {

    /**
     * The user.
     * @var object $user
     */
    var $user;

    /**
     * A flexitable to hold the data.
     * @var object $table
     */
    var $table;

    /**
     * Flat structure similar to grade tree
     */
    var $gseq;

    /**
     * show student ranks
     */
    var $showrank;

    /**
     * Show hidden items even when user does not have required cap
     */
    var $showhiddenitems;

    /**
     * Constructor. Sets local copies of user preferences and initialises grade_tree.
     * @param int $courseid
     * @param object $gpr grade plugin return tracking object
     * @param string $context
     * @param int $userid The id of the user
     */
    function grade_report_user($courseid, $gpr, $context, $userid) {
        global $CFG;
        parent::grade_report($courseid, $gpr, $context);

        $this->showrank        = grade_get_setting($this->courseid, 'report_user_showrank', !empty($CFG->grade_report_user_showrank));
        $this->showhiddenitems = grade_get_setting($this->courseid, 'report_user_showhiddenitems', !empty($CFG->grade_report_user_showhiddenitems));

        $switch = grade_get_setting($this->courseid, 'aggregationposition', $CFG->grade_aggregationposition);

        // Grab the grade_seq for this course
        $this->gseq = new grade_seq($this->courseid, $switch);

        // get the user (for full name)
        $this->user = get_record('user', 'id', $userid);

        // base url for sorting by first/last name
        $this->baseurl = $CFG->wwwroot.'/grade/report?id='.$courseid.'&amp;userid='.$userid;
        $this->pbarurl = $this->baseurl;

        // no groups on this report - rank is from all course users
        $this->setup_table();
    }

    /**
     * Prepares the headers and attributes of the flexitable.
     */
    function setup_table() {
        global $CFG;
        /*
         * Table has 5-6 columns
         *| itemname/description | final grade | percentage final grade | rank (optional) | feedback |
         */

        // setting up table headers
        if ($this->showrank) {
            // TODO: this is broken if hidden grades present!!
            $tablecolumns = array('itemname', 'category', 'grade', 'percentage', 'rank', 'feedback');
            $tableheaders = array($this->get_lang_string('gradeitem', 'grades'), $this->get_lang_string('category'), $this->get_lang_string('grade'),
                $this->get_lang_string('percent', 'grades'), $this->get_lang_string('rank', 'grades'),
                $this->get_lang_string('feedback'));
        } else {
            $tablecolumns = array('itemname', 'category', 'grade', 'percentage', 'feedback');
            $tableheaders = array($this->get_lang_string('gradeitem', 'grades'), $this->get_lang_string('category'), $this->get_lang_string('grade'),
                $this->get_lang_string('percent', 'grades'), $this->get_lang_string('feedback'));
        }

        $this->table = new flexible_table('grade-report-user-'.$this->courseid);

        $this->table->define_columns($tablecolumns);
        $this->table->define_headers($tableheaders);
        $this->table->define_baseurl($this->baseurl);

        $this->table->set_attribute('cellspacing', '0');
        $this->table->set_attribute('id', 'user-grade');
        $this->table->set_attribute('class', 'boxaligncenter generaltable');

        // not sure tables should be sortable or not, because if we allow it then sorted results distort grade category structure and sortorder
        $this->table->set_control_variables(array(
                TABLE_VAR_SORT    => 'ssort',
                TABLE_VAR_HIDE    => 'shide',
                TABLE_VAR_SHOW    => 'sshow',
                TABLE_VAR_IFIRST  => 'sifirst',
                TABLE_VAR_ILAST   => 'silast',
                TABLE_VAR_PAGE    => 'spage'
                ));

        $this->table->setup();
    }

    function fill_table() {
        global $CFG;
        $numusers = $this->get_numusers(false); // total course users
        $items =& $this->gseq->items;
        $grades = array();

        $canviewhidden = has_capability('moodle/grade:viewhidden', get_context_instance(CONTEXT_COURSE, $this->courseid));

        // fetch or create all grades
        foreach ($items as $key=>$unused) {
            if (!$grade_grade = grade_grade::fetch(array('itemid'=>$items[$key]->id, 'userid'=>$this->user->id))) {
                $grade_grade = new grade_grade();
                $grade_grade->userid = $this->user->id;
                $grade_grade->itemid = $items[$key]->id;
            }
            $grades[$key] = $grade_grade;
            $grades[$key]->grade_item =& $items[$key];
        }

        if ($canviewhidden) {
            $altered = array();
            $unknown = array();
        } else {
            $hiding_affected = grade_grade::get_hiding_affected($grades, $items);
            $altered = $hiding_affected['altered'];
            $unknown = $hiding_affected['unknown'];
            unset($hiding_affected);
        }

        foreach ($items as $itemid=>$unused) {
            $grade_item  =& $items[$itemid];
            $grade_grade =& $grades[$itemid];

            if (!$this->showhiddenitems and !$canviewhidden and $grade_item->is_hidden()) {
                continue;
            }

            $class = 'gradeitem';
            if ($grade_item->is_course_item()) {
                $class = 'courseitem';
            } else if ($grade_item->is_category_item()) {
                $class = 'categoryitem';
            }

            if (in_array($itemid, $unknown)) {
                $gradeval = null;
            } else if (array_key_exists($itemid, $altered)) {
                $gradeval = $altered[$itemid];
            } else {
                $gradeval = $grade_grade->finalgrade;
            }

            $data = array();

            /// prints grade item name
            if ($grade_item->is_course_item() or $grade_item->is_category_item()) {
                $data[] = '<span class="'.$class.'">'.$grade_item->get_name().'</span>';
            } else {
                $data[] = '<span class="'.$class.'">'.$this->get_module_link($grade_item->get_name(), $grade_item->itemmodule, $grade_item->iteminstance).'</span>';
            }

            /// prints category
            $cat = $grade_item->get_parent_category();
            $data[] = '<span class="'.$class.'">'.$cat->get_name().'</span>';

            /// prints the grade
            if ($grade_grade->is_excluded()) {
                $excluded = get_string('excluded', 'grades').' ';
            } else {
                $excluded = '';
            }

            if ($grade_item->needsupdate) {
                $data[] = '<span class="'.$class.' gradingerror">'.get_string('error').'</span>';

            } else if (!empty($CFG->grade_hiddenasdate) and !is_null($grade_grade->finalgrade) and !$canviewhidden and $grade_grade->is_hidden()
                   and !$grade_item->is_category_item() and !$grade_item->is_course_item()) {
                // the problem here is that we do not have the time when grade value was modified, 'timemodified' is general modification date for grade_grades records
                $data[] = '<span class="'.$class.' gradeddate">'.$excluded.get_string('gradedon', 'grades', userdate($grade_grade->timemodified, get_string('strftimedatetimeshort'))).'</span>';

            } else {
                $data[] = '<span class="'.$class.'">'.$excluded.grade_format_gradevalue($gradeval, $grade_item, true);
            }

            /// prints percentage
            if ($grade_item->needsupdate) {
                $data[] = '<span class="'.$class.'gradingerror">'.get_string('error').'</span>';

            } else {
                $data[] = '<span class="'.$class.'">'.grade_format_gradevalue($gradeval, $grade_item, true, GRADE_DISPLAY_TYPE_PERCENTAGE).'</span>';
            }

            /// prints rank
            if ($this->showrank) {
                // TODO: this is broken if hidden grades present!!
                if ($grade_item->needsupdate) {
                    $data[] = '<span class="'.$class.'gradingerror">'.get_string('error').'</span>';

                } else if (is_null($gradeval)) {
                    // no grade, no rank
                    $data[] = '<span class="'.$class.'">-</span>';;

                } else {
                    /// find the number of users with a higher grade
                    $sql = "SELECT COUNT(DISTINCT(userid))
                              FROM {$CFG->prefix}grade_grades
                             WHERE finalgrade > {$grade_grade->finalgrade}
                                   AND itemid = {$grade_item->id}";
                    $rank = count_records_sql($sql) + 1;

                    $data[] = '<span class="'.$class.'">'."$rank/$numusers".'</span>';
                }
            }

            /// prints feedback
            if (empty($grade_grade->feedback) or (!$canviewhidden and $grade_grade->is_hidden())) {
                $data[] = '<div class="feedbacktext">&nbsp;</div>';

            } else {
                $data[] = '<div class="feedbacktext">'.format_text($grade_grade->feedback, $grade_grade->feedbackformat).'</div>';
            }

            $this->table->add_data($data);
        }

        return true;
    }

    /**
     * Prints or returns the HTML from the flexitable.
     * @param bool $return Whether or not to return the data instead of printing it directly.
     * @return string
     */
    function print_table($return=false) {
        ob_start();
        $this->table->print_html();
        $html = ob_get_clean();
        if ($return) {
            return $html;
        } else {
            echo $html;
        }
    }

    /**
     * Processes the data sent by the form (grades and feedbacks).
     * @var array $data
     * @return bool Success or Failure (array of errors).
     */
    function process_data($data) {
    }
}

function grade_report_user_settings_definition(&$mform) {
    global $CFG;

    $options = array(-1 => get_string('default', 'grades'),
                      0 => get_string('hide'),
                      1 => get_string('show'));

    if (empty($CFG->grade_report_user_showrank)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $mform->addElement('select', 'report_user_showrank', get_string('showrank', 'grades'), $options);
    $mform->setHelpButton('report_user_showrank', array(false, get_string('showrank', 'grades'),
                          false, true, false, get_string('configshowrank', 'grades')));


    $options = array(-1 => get_string('default', 'grades'),
                      0 => get_string('hide'),
                      1 => get_string('show'));

    if (empty($CFG->grade_report_user_showhiddenitems)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $mform->addElement('select', 'report_user_showhiddenitems', get_string('showhiddenitems', 'grades'), $options);
    $mform->setHelpButton('report_user_showhiddenitems', array(false, get_string('showhiddenitems', 'grades'),
                          false, true, false, get_string('configshowhiddenitems', 'grades')));

}
?>
