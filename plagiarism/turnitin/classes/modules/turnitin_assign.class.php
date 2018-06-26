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
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC *
 */

defined('MOODLE_INTERNAL') || die();

// TODO: Split out all module specific code from plagiarism/turnitin/lib.php.
class turnitin_assign {

    private $modname;
    public $gradestable;
    public $filecomponent;

    public function __construct() {
        $this->modname = 'assign';
        $this->gradestable = $this->modname.'_grades';
        $this->filecomponent = $this->modname.'submission_file';
    }

    public function is_tutor($context) {
        return has_capability($this->get_tutor_capability(), $context);
    }

    public function get_tutor_capability() {
        return 'mod/'.$this->modname.':grade';
    }

    public function user_enrolled_on_course($context, $userid) {
        return has_capability('mod/'.$this->modname.':submit', $context, $userid);
    }

    public function get_author($itemid) {
        global $DB;

        if ($submission = $DB->get_record('assign_submission', array('id' => $itemid), 'userid')) {
            return $submission->userid;
        } else {
            return 0;
        }
    }

    public function set_content($linkarray, $cm) {
        $onlinetextdata = $this->get_onlinetext($linkarray["userid"], $cm);

        return (empty($onlinetextdata->onlinetext)) ? '' : $onlinetextdata->onlinetext;
    }


    /**
     * Check if resubmissions in a Turnitin sense are allowed to an assignment.
     *
     * @param $assignid
     */
    public function is_resubmission_allowed($assignid, $reportgenspeed, $submissiontype, $attemptreopenmethod) {
        global $DB, $CFG;

        // Get the maximum number of file submissions allowed.
        $params = array('assignment' => $assignid,
            'subtype' => 'assignsubmission',
            'plugin' => 'file',
            'name' => 'maxfilesubmissions');

        $maxfilesubmissions = 0;
        if ($result = $DB->get_record('assign_plugin_config', $params, 'value')) {
            $maxfilesubmissions = $result->value;
        }

        if ($CFG->branch <= 32) {
            require_once($CFG->dirroot.'/mod/assign/lib.php');
        }

        return ($reportgenspeed > 0 && $attemptreopenmethod == ASSIGN_ATTEMPT_REOPEN_METHOD_NONE
            && ($submissiontype == 'text_content' || $maxfilesubmissions == 1));
    }

    public function get_onlinetext($userid, $cm) {
        global $DB;

        // Get latest text content submitted as we do not have submission id.
        $submissions = $DB->get_records_select('assign_submission', ' userid = ? AND assignment = ? ',
                                        array($userid, $cm->instance), 'id DESC', 'id', 0, 1);
        $submission = end($submissions);
        $moodletextsubmission = $DB->get_record('assignsubmission_onlinetext',
                                            array('submission' => $submission->id), 'onlinetext, onlineformat');

        $onlinetextdata = new stdClass();
        $onlinetextdata->itemid = $submission->id;
        $onlinetextdata->onlinetext = $moodletextsubmission->onlinetext;
        $onlinetextdata->onlineformat = $moodletextsubmission->onlineformat;

        return $onlinetextdata;
    }

    public function create_file_event($params) {
        return \assignsubmission_file\event\assessable_uploaded::create($params);
    }

    public function create_text_event($params) {
        return \assignsubmission_onlinetext\event\assessable_uploaded::create($params);
    }

    public function get_current_gradequery($userid, $moduleid, $itemid = 0) {
        global $DB;

        $currentgradesquery = $DB->get_records('assign_grades',
                                                    array('userid' => $userid, 'assignment' => $moduleid),
                                                    'id DESC'
                                                );
        return current($currentgradesquery);
    }

    public function initialise_post_date($moduledata) {
        return 0;
    }
}