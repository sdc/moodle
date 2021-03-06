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
 * Leap enrolment plugin main library file.
 *
 * @package    enrol_leap
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class enrol_leap_plugin extends enrol_plugin {

    protected $lastenroller = null;
    protected $lastenrollerinstanceid = 0;

    protected $errorlogtag = '[ENROL_LEAP] ';
    /**
     * Debugging options
     * 1. Set $logging = true to write useful information to Apache's error log.
     * 2. Set $fulllogging = true to write debug-level useful information to Apache's error log.
     */
    protected $logging = true;
    protected $fulllogging = true;

    public function roles_protected() {
        // Users may tweak the roles later.
        return false;
    }

    public function allow_enrol(stdClass $instance) {
        // Users with enrol cap may unenrol other users manually.
        return true;
    }

    public function allow_unenrol(stdClass $instance) {
        // Users with unenrol cap may unenrol other users manually.
        return true;
    }

    public function allow_manage(stdClass $instance) {
        // Users with manage cap may tweak period and status.
        return true;
    }

    /**
     * Returns link to Leap enrol UI if exists.
     * Does the access control tests automatically.
     *
     * @param stdClass $instance
     * @return moodle_url
     */
    public function get_leap_enrol_link($instance) {
        $name = $this->get_name();
        if ($instance->enrol !== $name) {
            throw new coding_exception('invalid enrol instance!');
        }

        if (!enrol_is_enabled($name)) {
            return NULL;
        }

        $context = context_course::instance($instance->courseid, MUST_EXIST);

        if (!has_capability('enrol/leap:enrol', $context)) {
            // Note: manage capability not used here because it is used for editing
            // of existing enrolments which is not possible here.
            return NULL;
        }

        return new moodle_url('/enrol/leap/manage.php', array('enrolid'=>$instance->id, 'id'=>$instance->courseid));
    }

    /**
     * Returns enrolment instance manage link.
     *
     * By defaults looks for manage.php file and tests for manage capability.
     *
     * @param navigation_node $instancesnode
     * @param stdClass $instance
     * @return moodle_url;
     */
    public function add_course_navigation($instancesnode, stdClass $instance) {
        if ($instance->enrol !== 'leap') {
             throw new coding_exception('Invalid enrol instance type!');
        }

        $context = context_course::instance($instance->courseid);
        if (has_capability('enrol/leap:config', $context)) {
            $managelink = new moodle_url('/enrol/leap/edit.php', array('courseid'=>$instance->courseid));
            $instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
        }
    }

    /**
     * Returns edit icons for the page with list of instances.
     * @param stdClass $instance
     * @return array
     */
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        if ($instance->enrol !== 'leap') {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = context_course::instance($instance->courseid);

        $icons = array();

        if (has_capability('enrol/leap:enrol', $context) or has_capability('enrol/leap:unenrol', $context)) {
            $managelink = new moodle_url("/enrol/leap/manage.php", array('enrolid'=>$instance->id));
            $icons[] = $OUTPUT->action_icon($managelink, new pix_icon('t/enrolusers', get_string('enrolusers', 'enrol_leap'), 'core', array('class'=>'iconsmall')));
        }
        if (has_capability('enrol/leap:config', $context)) {
            $editlink = new moodle_url("/enrol/leap/edit.php", array('courseid'=>$instance->courseid));
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('t/edit', get_string('edit'), 'core',
                    array('class' => 'iconsmall')));
        }

        return $icons;
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        global $DB;

        $context = context_course::instance($courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/leap:config', $context)) {
            return NULL;
        }

        if ($DB->record_exists('enrol', array('courseid'=>$courseid, 'enrol'=>'leap'))) {
            return NULL;
        }

        return new moodle_url('/enrol/leap/edit.php', array('courseid'=>$courseid));
    }

    /**
     * Add new instance of enrol plugin with default settings.
     * @param stdClass $course
     * @return int id of new instance, null if can not be created
     */
    public function add_default_instance($course) {
        $expirynotify = $this->get_config('expirynotify', 0);
        if ($expirynotify == 2) {
            $expirynotify = 1;
            $notifyall = 1;
        } else {
            $notifyall = 0;
        }
        $fields = array(
            'status'          => $this->get_config('status'),
            'roleid'          => $this->get_config('roleid', 0),
            'enrolperiod'     => $this->get_config('enrolperiod', 0),
            'expirynotify'    => $expirynotify,
            'notifyall'       => $notifyall,
            'expirythreshold' => $this->get_config('expirythreshold', 86400),
        );
        return $this->add_instance($course, $fields);
    }

    /**
     * Add new instance of enrol plugin.
     * @param stdClass $course
     * @param array instance fields
     * @return int id of new instance, null if can not be created
     */
    public function add_instance($course, array $fields = NULL) {
        global $DB;

        if ($DB->record_exists('enrol', array('courseid'=>$course->id, 'enrol'=>'leap'))) {
            // only one instance allowed, sorry
            return NULL;
        }

        return parent::add_instance($course, $fields);
    }

    /**
     * Returns a button to manually enrol users through the manual enrolment plugin.
     *
     * By default the first manual enrolment plugin instance available in the course is used.
     * If no manual enrolment instances exist within the course then false is returned.
     *
     * This function also adds a quickenrolment JS ui to the page so that users can be enrolled
     * via AJAX.
     *
     * @param course_enrolment_manager $manager
     * @return enrol_user_button
     */
    public function get_leap_enrol_button(course_enrolment_manager $manager) {
        global $CFG;
        require_once($CFG->dirroot.'/cohort/lib.php');

        $instance = null;
        $instances = array();
        foreach ($manager->get_enrolment_instances() as $tempinstance) {
            if ($tempinstance->enrol == 'leap') {
                if ($instance === null) {
                    $instance = $tempinstance;
                }
                $instances[] = array('id' => $tempinstance->id, 'name' => $this->get_instance_name($tempinstance));
            }
        }
        if (empty($instance)) {
            return false;
        }

        if (!$leaplink = $this->get_leap_enrol_link($instance)) {
            return false;
        }

        $button = new enrol_user_button($leaplink, get_string('enrolusers', 'enrol_leap'), 'get');
        $button->class .= ' enrol_leap_plugin';

        $startdate = $manager->get_course()->startdate;
        $startdateoptions = array();
        $timeformat = get_string('strftimedatefullshort');
        if ($startdate > 0) {
            $startdateoptions[2] = get_string('coursestart') . ' (' . userdate($startdate, $timeformat) . ')';
        }
        $today = time();
        $today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);
        $startdateoptions[3] = get_string('today') . ' (' . userdate($today, $timeformat) . ')' ;
        $defaultduration = $instance->enrolperiod > 0 ? $instance->enrolperiod / 86400 : '';

        $modules = array('moodle-enrol_leap-quickenrolment', 'moodle-enrol_leap-quickenrolment-skin');
        $arguments = array(
            'instances'           => $instances,
            'courseid'            => $instance->courseid,
            'ajaxurl'             => '/enrol/leap/ajax.php',
            'url'                 => $manager->get_moodlepage()->url->out(false),
            'optionsStartDate'    => $startdateoptions,
            'defaultRole'         => $instance->roleid,
            'defaultDuration'     => $defaultduration,
            'disableGradeHistory' => $CFG->disablegradehistory,
            'recoverGradesDefault'=> '',
            'cohortsAvailable'    => cohort_get_available_cohorts($manager->get_context(), COHORT_WITH_NOTENROLLED_MEMBERS_ONLY, 0, 1) ? true : false
        );

        if ($CFG->recovergradesdefault) {
            $arguments['recoverGradesDefault'] = ' checked="checked"';
        }

        $function = 'M.enrol_leap.quickenrolment.init';
        $button->require_yui_module($modules, $function, array($arguments));
        $button->strings_for_js(array(
            'ajaxoneuserfound',
            'ajaxxusersfound',
            'ajaxnext25',
            'enrol',
            'enrolmentoptions',
            'enrolusers',
            'enrolxusers',
            'errajaxfailedenrol',
            'errajaxsearch',
            'foundxcohorts',
            'none',
            'usersearch',
            'unlimitedduration',
            'startdatetoday',
            'durationdays',
            'enrolperiod',
            'finishenrollingusers',
            'recovergrades'), 'enrol');
        $button->strings_for_js(array('browseusers', 'browsecohorts'), 'enrol_leap');
        $button->strings_for_js('assignroles', 'role');
        $button->strings_for_js('startingfrom', 'moodle');

        return $button;
    }

    /**
     * Enrol cron support.
     * @return void
     */
    public function cron() {
        $trace = new text_progress_trace();
        $this->sync($trace, null);
        $this->send_expiry_notifications($trace);
    }

    /**
     * Sync all meta course links.
     *
     * @param progress_trace $trace
     * @param int $courseid one course, empty mean all
     * @return int 0 means ok, 1 means error, 2 means plugin disabled
     */
    public function sync(progress_trace $trace, $courseid = null) {
        global $DB;

        if (!enrol_is_enabled('leap')) {
            $trace->finished();
            return 2;
        }

        // Unfortunately this may take a long time, execution can be interrupted safely here.
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_HUGE);

        $trace->output('Verifying leap enrolment expiration...');

        $params = array('now'=>time(), 'useractive'=>ENROL_USER_ACTIVE, 'courselevel'=>CONTEXT_COURSE);
        $coursesql = "";
        if ($courseid) {
            $coursesql = "AND e.courseid = :courseid";
            $params['courseid'] = $courseid;
        }

        // Deal with expired accounts.
        $action = $this->get_config('expiredaction', ENROL_EXT_REMOVED_KEEP);

        if ($action == ENROL_EXT_REMOVED_UNENROL) {
            $instances = array();
            $sql = "SELECT ue.*, e.courseid, c.id AS contextid
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'leap')
                      JOIN {context} c ON (c.instanceid = e.courseid AND c.contextlevel = :courselevel)
                     WHERE ue.timeend > 0 AND ue.timeend < :now
                           $coursesql";
            $rs = $DB->get_recordset_sql($sql, $params);
            foreach ($rs as $ue) {
                if (empty($instances[$ue->enrolid])) {
                    $instances[$ue->enrolid] = $DB->get_record('enrol', array('id'=>$ue->enrolid));
                }
                $instance = $instances[$ue->enrolid];
                // Always remove all manually assigned roles here, this may break enrol_self roles but we do not want hardcoded hacks here.
                role_unassign_all(array('userid'=>$ue->userid, 'contextid'=>$ue->contextid, 'component'=>'', 'itemid'=>0), true);
                $this->unenrol_user($instance, $ue->userid);
                $trace->output("unenrolling expired user $ue->userid from course $instance->courseid", 1);
            }
            $rs->close();
            unset($instances);

        } else if ($action == ENROL_EXT_REMOVED_SUSPENDNOROLES or $action == ENROL_EXT_REMOVED_SUSPEND) {
            $instances = array();
            $sql = "SELECT ue.*, e.courseid, c.id AS contextid
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'leap')
                      JOIN {context} c ON (c.instanceid = e.courseid AND c.contextlevel = :courselevel)
                     WHERE ue.timeend > 0 AND ue.timeend < :now
                           AND ue.status = :useractive
                           $coursesql";
            $rs = $DB->get_recordset_sql($sql, $params);
            foreach ($rs as $ue) {
                if (empty($instances[$ue->enrolid])) {
                    $instances[$ue->enrolid] = $DB->get_record('enrol', array('id'=>$ue->enrolid));
                }
                $instance = $instances[$ue->enrolid];
                if ($action == ENROL_EXT_REMOVED_SUSPENDNOROLES) {
                    // Remove all manually assigned roles here, this may break enrol_self roles but we do not want hardcoded hacks here.
                    role_unassign_all(array('userid'=>$ue->userid, 'contextid'=>$ue->contextid, 'component'=>'', 'itemid'=>0), true);
                    $this->update_user_enrol($instance, $ue->userid, ENROL_USER_SUSPENDED);
                    $trace->output("suspending expired user $ue->userid in course $instance->courseid, roles unassigned", 1);
                } else {
                    $this->update_user_enrol($instance, $ue->userid, ENROL_USER_SUSPENDED);
                    $trace->output("suspending expired user $ue->userid in course $instance->courseid, roles kept", 1);
                }
            }
            $rs->close();
            unset($instances);

        } else {
            // ENROL_EXT_REMOVED_KEEP means no changes.
        }

        $trace->output('...Leap enrolment updates finished.');
        $trace->finished();

        return 0;
    }

    /**
     * Returns the user who is responsible for Leap enrolments in given instance.
     *
     * Usually it is the first editing teacher - the person with "highest authority"
     * as defined by sort_by_roleassignment_authority() having 'enrol/leap:manage'
     * capability.
     *
     * @param int $instanceid enrolment instance id
     * @return stdClass user record
     */
    // TODO: This function should always return the 'Leap user' as defined by the Leap Webservices plugin.
    protected function get_enroller($instanceid) {
        global $DB;

        if ($this->lastenrollerinstanceid == $instanceid and $this->lastenroller) {
            return $this->lastenroller;
        }

        $instance = $DB->get_record('enrol', array('id'=>$instanceid, 'enrol'=>$this->get_name()), '*', MUST_EXIST);
        $context = context_course::instance($instance->courseid);

        if ($users = get_enrolled_users($context, 'enrol/leap:manage')) {
            $users = sort_by_roleassignment_authority($users, $context);
            $this->lastenroller = reset($users);
            unset($users);
        } else {
            $this->lastenroller = parent::get_enroller($instanceid);
        }

        $this->lastenrollerinstanceid = $instanceid;

        return $this->lastenroller;
    }

    /**
     * Gets an array of the user enrolment actions.
     *
     * @param course_enrolment_manager $manager
     * @param stdClass $ue A user enrolment object
     * @return array An array of user_enrolment_actions
     */
    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {
        $actions = array();
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;
        if ($this->allow_unenrol_user($instance, $ue) && has_capability("enrol/leap:unenrol", $context)) {
            $url = new moodle_url('/enrol/unenroluser.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''), get_string('unenrol', 'enrol'), $url, array('class'=>'unenrollink', 'rel'=>$ue->id));
        }
        if ($this->allow_manage($instance) && has_capability("enrol/leap:manage", $context)) {
            $url = new moodle_url('/enrol/editenrolment.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/edit', ''), get_string('edit'), $url, array('class'=>'editenrollink', 'rel'=>$ue->id));
        }
        return $actions;
    }

    /**
     * The Leap plugin has several bulk operations that can be performed.
     * @param course_enrolment_manager $manager
     * @return array
     */
    public function get_bulk_operations(course_enrolment_manager $manager) {
        global $CFG;
        require_once($CFG->dirroot.'/enrol/leap/locallib.php');
        $context = $manager->get_context();
        $bulkoperations = array();
        if (has_capability("enrol/leap:manage", $context)) {
            $bulkoperations['editselectedusers'] = new enrol_leap_editselectedusers_operation($manager, $this);
        }
        if (has_capability("enrol/leap:unenrol", $context)) {
            $bulkoperations['deleteselectedusers'] = new enrol_leap_deleteselectedusers_operation($manager, $this);
        }
        return $bulkoperations;
    }

    /**
     * Restore instance and map settings.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $course
     * @param int $oldid
     */
    public function restore_instance(restore_enrolments_structure_step $step, stdClass $data, $course, $oldid) {
        global $DB;
        // There is only one Leap enrol instance allowed per course.
        if ($instances = $DB->get_records('enrol', array('courseid'=>$data->courseid, 'enrol'=>'leap'), 'id')) {
            $instance = reset($instances);
            $instanceid = $instance->id;
        } else {
            $instanceid = $this->add_instance($course, (array)$data);
        }
        $step->set_mapping('enrol', $oldid, $instanceid);
    }

    /**
     * Restore user enrolment.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $instance
     * @param int $oldinstancestatus
     * @param int $userid
     */
    public function restore_user_enrolment(restore_enrolments_structure_step $step, $data, $instance, $userid, $oldinstancestatus) {
        global $DB;

        // Note: this is a bit tricky because other types may be converted to manual enrolments,
        //       and manual is restricted to one enrolment per user.

        $ue = $DB->get_record('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$userid));
        $enrol = false;
        if ($ue and $ue->status == ENROL_USER_ACTIVE) {
            // We do not want to restrict current active enrolments, let's kind of merge the times only.
            // This prevents some teacher lockouts too.
            if ($data->status == ENROL_USER_ACTIVE) {
                if ($data->timestart > $ue->timestart) {
                    $data->timestart = $ue->timestart;
                    $enrol = true;
                }

                if ($data->timeend == 0) {
                    if ($ue->timeend != 0) {
                        $enrol = true;
                    }
                } else if ($ue->timeend == 0) {
                    $data->timeend = 0;
                } else if ($data->timeend < $ue->timeend) {
                    $data->timeend = $ue->timeend;
                    $enrol = true;
                }
            }
        } else {
            if ($instance->status == ENROL_INSTANCE_ENABLED and $oldinstancestatus != ENROL_INSTANCE_ENABLED) {
                // Make sure that user enrolments are not activated accidentally,
                // we do it only here because it is not expected that enrolments are migrated to other plugins.
                $data->status = ENROL_USER_SUSPENDED;
            }
            $enrol = true;
        }

        if ($enrol) {
            $this->enrol_user($instance, $userid, null, $data->timestart, $data->timeend, $data->status);
        }
    }

    /**
     * Restore role assignment.
     *
     * @param stdClass $instance
     * @param int $roleid
     * @param int $userid
     * @param int $contextid
     */
    public function restore_role_assignment($instance, $roleid, $userid, $contextid) {
        // This is necessary only because we may migrate other types to this instance,
        // we do not use component in Leap or self enrol.
        role_assign($roleid, $userid, $contextid, '', 0);
    }

    /**
     * Restore user group membership.
     * @param stdClass $instance
     * @param int $groupid
     * @param int $userid
     */
    public function restore_group_member($instance, $groupid, $userid) {
        global $CFG;
        require_once("$CFG->dirroot/group/lib.php");

        // This might be called when forcing restore as manual enrolments.

        groups_add_member($groupid, $userid);
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param object $instance
     * @return bool
     */
    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/leap:config', $context);
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/leap:config', $context);
    }

    /**
     * Enrol all not enrolled cohort members into course via enrol instance.
     *
     * @param stdClass $instance
     * @param int $cohortid
     * @param int $roleid optional role id
     * @param int $timestart 0 means unknown
     * @param int $timeend 0 means forever
     * @param int $status default to ENROL_USER_ACTIVE for new enrolments, no change by default in updates
     * @param bool $recovergrades restore grade history
     */
    public function enrol_cohort(stdClass $instance, $cohortid, $roleid = null, $timestart = 0, $timeend = 0, $status = null, $recovergrades = null) {
        global $DB;
        $context = context_course::instance($instance->courseid);
        list($esql, $params) = get_enrolled_sql($context);
        $sql = "SELECT cm.userid FROM {cohort_members} cm LEFT JOIN ($esql) u ON u.id = cm.userid ".
            "WHERE cm.cohortid = :cohortid AND u.id IS NULL";
        $params['cohortid'] = $cohortid;
        $members = $DB->get_fieldset_sql($sql, $params);
        foreach ($members as $userid) {
            $this->enrol_user($instance, $userid, $roleid, $timestart, $timeend, $status, $recovergrades);
        }
    }

    // Ideas, code and help from http://docs.moodle.org/dev/Enrolment_plugins#Automated_enrolment.
    // TODO: Pulled directly from the 2.7 (et al) plugin. Refactor!!
    public function sync_user_enrolments( $user ) {

        global $CFG, $DB;

        // Reuse a function from the Leap block (which this plugin is dependent on).
        require_once( $CFG->dirroot . '/blocks/leap/locallib.php' );
        require_once( $CFG->dirroot . '/lib/grouplib.php' );
        require_once( $CFG->dirroot . '/group/lib.php' );


        // Create a string containing the current academic year; store for later use.
        $now    = time();
        $year   = date( 'y', $now );
        $month  = date( 'm', $now );
        if ( $month >= 8 && $month <= 12 ) {
            $acadyear = $year . '/' . ( $year + 1 );
        } else {
            $acadyear = ( $year - 1 ) . '/' . $year;
        }

        if ( $this->logging ) {
            error_log( $this->errorlogtag . '- Starting plugin instance' );
        }

        // Quick checks to ensure we have the bits we need to continue.
        if ( !is_object( $user ) or !property_exists( $user, 'id' ) ) {
            throw new coding_exception( 'Invalid $user parameter in sync_user_enrolments()' );
            if ( $this->logging ) {
                error_log( $this->errorlogtag . ' >Invalid $user parameter: serious error about here.' );
            }
        }

        // Get the user's id number.
        // TODO: Do we need this any more? We don't seem to use this anywhere else in the code and the db is empty.
        /*
        if ( !property_exists( $user, 'idnumber' ) ) {
            if ( $this->logging ) {
                error_log( $this->errorlogtag . '  Missing "idnumber" for ' . $user->id );
            }
            $user = $DB->get_record( 'user', array( 'id' => $user->id ) );

        } else {
            if ( $this->fulllogging ) {
                error_log( $this->errorlogtag . ' <"idnumber" found for ' . $user->id );
            }
        } */

        // Take Shibboleth's "@[student.]southdevon.ac.uk" off the username.
        $unameparts = explode( '@', $user->username );
        $uname = $unameparts[0];

        // A list of users for which we bail out of doing anything else regarding enrolment.
        // Note: Admin users get excluded from this process automatically by Moodle itself.
        if ( $uname == 'leapuser' ) {
            if ( $this->logging ) {
                error_log( $this->errorlogtag . 'x Bailing out! Not processing enrolment for "' . $uname . '"' );
            }
            return false;

        } else {
            if ( $this->logging ) {
                error_log( $this->errorlogtag . '  Enrolment/s for "' . $uname . '" (' . $user->id . ')' );
            }
        }

        // TODO: This check may be needed in the future if we need to transform very old usernames.
/*
        // Staff usernames and 8-digit student usernames stay unchanged.
        // E, N and S-prefix 6-digit usernames are changed.
        if ( preg_match( '/^[s][0-9]{6}/', $uname ) ) {
            $uname = '10' . substr( $uname, 1, 6 );

        } else if ( preg_match( '/^[n][0-9]{6}/', $uname ) ) {
            $uname = '20'.substr( $uname, 1, 6 );

        } else if ( preg_match( '/^[e][0-9]{6}/', $uname ) ) {
            $uname = '30'.substr( $uname, 1, 6 );
        }
        if ( $this->logging ) {
            error_log( $this->errorlogtag . '  EBS username is "' . $uname . '"' );
        }
*/

        $leap_url = get_config( 'block_leap', 'leap_url' );
        $auth_token = get_config( 'block_leap', 'auth_token' );
        $url = $leap_url . '/people/' . $uname . '/views/courses.json?token=' . $auth_token;

        // Some epic logging, prob not needed unless extreme debugging is taking place.
        if ( $this->fulllogging ) {
            error_log( $this->errorlogtag . ' <Leap URL: ' . $url );
        }

        $leap_json = '';
        if ( !$leap_json = file_get_contents( $url ) ) {
            error_log( $this->errorlogtag . ' >Bailing out: couldn\'t get the JSON from Leap' );
            return false;
        }

        if ( $this->fulllogging ) {
            error_log( $this->errorlogtag . ' <Leap JSON: ' . $leap_json );
        }

        $leap_data = json_decode( $leap_json );

        //$events_total = count( $leap_data->events );
        $events_total = count( $leap_data );
        $events_count = 0;

        // Check the JSON for conditions and pull out what we need.
        //foreach ( $leap_data->events as $event ) {
        foreach ( $leap_data as $event ) {

            $events_count++;

            // Everything suddenly seems to be one layer deeper. I don't know what changed.
            $event = $event->event;

            if ( $this->logging ) {
                error_log( $this->errorlogtag . '  Processing enrolment ' . $events_count . ' of ' . $events_total );
            }

            // If the enrolment status is anything other than 'current'.
            if ( $event->eventable->status != 'current' ) {
            //if ( $event->eventable->status != 'complete' ) {
                if ( $this->logging ) {
                    error_log( $this->errorlogtag . '   Found non-current status "' . $event->eventable->status . '", so bailing' );
                }
                continue;
            } else {
                if ( $this->logging ) {
                    error_log( $this->errorlogtag . '   Found status "' . $event->eventable->status . '"' );
                }
            }

            // If there's no course code.
            if ( !isset( $event->eventable->course->code ) ) {
                if ( $this->logging ) {
                    error_log( $this->errorlogtag . '   Course code not found in JSON, so bailing' );
                }
                continue;
            } else {
                $coursecode = $event->eventable->course->code;
                if ( $this->logging ) {
                    error_log( $this->errorlogtag . '   Found course code "' . $coursecode . '"' );
                }
            }

            // If not this academic year.
            if ( $this->fulllogging ) {
                error_log( $this->errorlogtag . '  <Course ' . $coursecode . "'s academic year is " . $event->eventable->course->year );
            }
            //if ( $event->eventable->course->year == $acadyear ) {
            if ( $event->eventable->course->year != $acadyear ) {
                if ( $this->logging ) {
                    error_log( $this->errorlogtag . '   Course not running in this (' . $acadyear . ') academic year, so bailing' );
                }
                continue;
            } else {
                if ( $this->logging ) {
                    error_log( $this->errorlogtag . '   Course academic year in JSON matches this (' . $acadyear . ') academic year' );
                }
            }


            /**
             * Key logic block: this decides what to do with the user.
             * Do nothing, enrolment (including sorting out groups and group enrolment) or unenrolment depending on the start and end dates.
             */
            $now = time();
            if ( strtotime( $event->eventable->start_date > $now ) ) {
                // This course hasn't started yet.
                if ( $this->logging ) {
                    error_log( $this->errorlogtag . '  This course hasn\'t started yet, so bailing' );
                }
                continue;

            } else if ( strtotime( $event->eventable->end_date ) < $now ) {
                // This course has passed.
                // TODO: unenrolment (if enrolled by this plugin), otherwise nothing.


            } else if ( strtotime( $event->eventable->start_date ) < $now && strtotime( $event->eventable->end_date ) > $now ) {
                // This course is live, so enrol if not already.
                // Order this by ID?
                $courses = $DB->get_records( 'course', null, null, 'id,shortname,fullname' );


                $toenrol = array();
                foreach ( $courses as $course ) {
                    if ( $course->id != 1 ) {

                        $coursecontext  = context_course::instance( $course->id );
                        if ( !$blockrecord = $DB->get_record( 'block_instances', array( 'blockname' => 'leap', 'parentcontextid' => $coursecontext->id ) ) ) {
                            if ( $this->logging ) {
                                error_log( $this->errorlogtag . '    No Leap block found for course "' . $course->id . '" (' . $course->shortname . '), so bailing' );
                            }
                            continue;
                        }

                        if ( !$blockinstance  = block_instance( 'leap', $blockrecord ) ) {
                            if ( $this->logging ) {
                                error_log( $this->errorlogtag . '    No Leap block instance found for course "' . $course->id . '" (' . $course->shortname . '), so bailing' );
                            }
                            continue;
                        }

// OW 20170209 arghh - don't compare strings as 'GC2MAT1F' would be found in 'GC2MAT1FHS,GC2MAT2FHS,GC2MTH2FHS,GC2MAH1F' when it shouldn't be!!
//                      if ( isset ( $blockinstance->config->coursecodes ) && stripos( $blockinstance->config->coursecodes, $coursecode ) !== false ) {
                        if ( isset ( $blockinstance->config->coursecodes ) && in_array( strtoupper($coursecode), explode( ",", strtoupper($blockinstance->config->coursecodes)) ) ) {
                            $toenrol[] = $course;
                            if ( $this->logging ) {
                                error_log( $this->errorlogtag . '    Course code "' . $coursecode . '" found in course ' . $course->id . ' (' . $course->shortname . ')' );
                            }
                        } else {
                            // Just do logging, nothing else.
                            if ( $this->fulllogging ) {
//                                error_log( $this->errorlogtag . '   <Course code "' . $coursecode . '" not found in course ' . $course->id . ' (' . $course->shortname . ')' );
                            }
                        }

                    }
                }

                // If we found no courses with the course code in the course Leap block instance.
                if ( empty( $toenrol ) ) {
                    if ( $this->logging ) {
                        error_log( $this->errorlogtag . '   No Moodle course with course code "' . $coursecode . '" found, so bailing' );
                    }
                    continue;
                }

                // TODO: Some kind of counter here for consistency/debugging?
                foreach ( $toenrol as $enrolme ) {

                    // Get the enrolment plugin instance. Enrol plugin name / course ID / role ID.
                    if ( !$enrolinstance = $DB->get_record( 'enrol', array( 'enrol' => 'leap', 'courseid' => $enrolme->id, 'roleid' => $this->get_config('roleid') ), '*', MUST_EXIST ) ) {
                        if ( $this->logging ) {
                            error_log( $this->errorlogtag . '  >No Leap enrolment plugin instance for course ' . $enrolme->id . '/' . $enrolme->shortname . ' so bailing' );
                        }
                        continue;
                    }

                    // TODO: Skip if already enrolled...
                    if ( !is_enrolled( context_course::instance( $enrolme->id ), $user->id, null, true ) ) {
                        // Course and role enrolment all in one go.
                        $this->enrol_user( $enrolinstance, $user->id, $this->get_config( 'roleid' ), time(), time() + $this->get_config( 'enrolperiod' ), ENROL_USER_ACTIVE, true);
                        if ( $this->logging ) {
                            error_log( $this->errorlogtag . '  Leap-enrolled ' . $user->id . ' onto course ' . $enrolme->id . ' (probably)' );
                        }
                    } else {

                    }

                //} // For each course code found, do the group thing too.

                    // Group scanning, creation and adding/removal.
                    // TODO: transform the tutorial code, e.g. to uppercase?
                    $event->eventable->tutorgroup = strtoupper( trim( $event->eventable->tutorgroup ) );
                    if  ( !isset( $event->eventable->tutorgroup ) || empty( $event->eventable->tutorgroup ) ) {
                        if ( $this->logging ) {
                            error_log( $this->errorlogtag . '   No tutorial groups specified' );
                        }
                        // Do we want to bail if there are no turorgroups?
                        //continue;
                    } else {
                        // Group specified.
                        if ( $this->logging ) {
                            error_log( $this->errorlogtag . '   Tutorial group "' . $event->eventable->tutorgroup . '"" found in JSON' );
                        }

                        // Does the group exist? If not, create it.
                        if ( !$groupid = groups_get_group_by_name( $enrolme->id, $event->eventable->tutorgroup )) {
                            // Create the named group.
                            if ( $this->logging ) {
                                error_log( $this->errorlogtag . '   Tutorial group "' . trim( $event->eventable->tutorgroup ) . '" needs creating' );
                            }

                            $newgroup = new stdClass();
                            $newgroup->courseid             = $enrolme->id;
                            $newgroup->name                 = trim( $event->eventable->tutorgroup );
                            $newgroup->description          = 'Tutorial Group ' . trim( $event->eventable->tutorgroup );
                            $newgroup->descriptionformat    = FORMAT_HTML;

                            if ( $groupid = groups_create_group( $newgroup ) ) {
                                if ( $this->logging ) {
                                    error_log( $this->errorlogtag . '   Tutorial group "' . trim( $event->eventable->tutorgroup ) . '" [' . $groupid . '] created' );
                                }
                            } else {
                                if ( $this->logging ) {
                                    error_log( $this->errorlogtag . ' x Tutorial group "' . trim( $event->eventable->tutorgroup ) . '" could not be created' );
                                }
                            }


                        } else {
                            // Group already exists.
                            if ( $this->logging ) {
                                error_log( $this->errorlogtag . '   Tutorial group "' . trim( $event->eventable->tutorgroup ) . '" [' . $groupid . '] already exists' );
                            }
                        }

                        // $groupid should now be the id of the existing/new group
                        if ( !$grouped = groups_is_member( $groupid, $user->id ) ) {
                            // Add the user in the group.
                            if ( groups_add_member( $groupid, $user->id ) ) {
                                if ( $this->logging ) {
                                    error_log( $this->errorlogtag . '   User added to the "' . trim( $event->eventable->tutorgroup ) . '" [' . $groupid . '] tutorial group' );
                                }
                            } else {
                                if ( $this->logging ) {
                                    error_log( $this->errorlogtag . ' x User not added to the "' . trim( $event->eventable->tutorgroup ) . '" [' . $groupid . '] tutorial group for some reason' );
                                }
                            }

                        } else {
                            if ( $this->logging ) {
                                error_log( $this->errorlogtag . '   User is already in the "' . trim( $event->eventable->tutorgroup ) . '" [' . $groupid . '] tutorial group' );
                            }
                        }

                    } // END group logic.

                } // END 'enrol me' loop.

            } // END... key logic block.

        } // End courses loop.

        // Bye bye now.
        if ($this->logging) {
            error_log($this->errorlogtag . '  Finished setting up enrolments for "'.$uname.'"');
        }

        return true;

    } // End public function.

} // END class.
