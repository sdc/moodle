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
 * Leap Web Services external web services template
 *
 * @package     local_leapwebservices
 * @copyright   2011 onwards Paul Vaughan, paulvaughan@southdevon.ac.uk
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/externallib.php');
require_once($CFG->libdir.'/gradelib.php');

/**
 * External Webservices API class
 * @copyright   2011 onwards Paul Vaughan, paulvaughan@southdevon.ac.uk
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_leapwebservices_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_user_courses_parameters() {
        return new external_function_parameters(
            array(
                'username' => new external_value(PARAM_TEXT, 'Username of user. If empty, fail.'),
            )
        );
    }

    /**
     * Get user courses
     * @param array $username
     * @return array
     */
    public static function get_user_courses($username) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");

        $params = self::validate_parameters(self::get_user_courses_parameters(),
            array('username' => $username));

        if ($params['username'] == '') {
            header($_SERVER["SERVER_PROTOCOL"].' 422 Unprocessable Entity ($params[\'username\'] empty.)', true, 422);
        }

        $courses = $DB->get_records_sql("SELECT DISTINCT c.id AS id, c.fullname, c.shortname, c.idnumber, c.visible
            FROM {role_assignments} ra, {user} u,
                {course} c, {context} cxt, {role} r
            WHERE ra.userid = u.id
            AND ra.contextid = cxt.id
            AND cxt.contextlevel = 50
            AND cxt.instanceid = c.id
            AND ra.roleid = r.id
            AND u.username LIKE ?
            ORDER BY fullname ASC;", $params);

        $coursesinfo = array();
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            try {
                self::validate_context($context);
            } catch (Exception $e) {
                $exceptionparam             = new stdClass();
                $exceptionparam->message    = $e->getMessage();
                $exceptionparam->courseid   = $course->id;
                throw new moodle_exception(
                    get_string('errorcoursecontextnotvalid', 'webservice', $exceptionparam));
            }
            require_capability('moodle/course:view', $context);

            $courseinfo = array();
            $courseinfo['id']           = $course->id;
            $courseinfo['shortname']    = $course->shortname;
            $courseinfo['fullname']     = $course->fullname;
            $courseinfo['idnumber']     = $course->idnumber;
            $courseinfo['visible']      = $course->visible;

            $user = $DB->get_record('user', array('username' => $params['username']));
            $context = context_course::instance($course->id);
            $courseinfo['canedit'] = has_capability('moodle/course:update', $context, $user->id) ? 1 : 0;

            $coursesinfo[] = $courseinfo;
        }

        return $coursesinfo;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_user_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'        => new external_value(PARAM_INT, 'course id'),
                    'shortname' => new external_value(PARAM_TEXT, 'course short name'),
                    'fullname'  => new external_value(PARAM_TEXT, 'full name'),
                    'idnumber'  => new external_value(PARAM_RAW, 'id number'),
                    'visible'   => new external_value(PARAM_INT, '1: available to student, 0: not available'),
                    'canedit'   => new external_value(PARAM_BOOL, '1: user can edit the course, 0: user cannot edit the course'),
                ), 'course'
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_courses_by_idnumber_parameters() {
        return new external_function_parameters(
            array(
                'idnumber' => new external_value(PARAM_TEXT, 'idnumber of course. If empty, fail.'),
            )
        );
    }

    /**
     * Get courses by idnumber
     * @param string $idnumber
     * @return array
     */
    public static function get_courses_by_idnumber($idnumber) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");

        $params = self::validate_parameters(self::get_courses_by_idnumber_parameters(), array('idnumber' => $idnumber));

        if ($params['idnumber'] == '') {
            header($_SERVER["SERVER_PROTOCOL"].' 422 Unprocessable Entity ($params[\'idnumber\'] empty.)', true, 422);
        }

        $courses = $DB->get_records_select('course', 'idnumber LIKE "%'.$idnumber.'%"', null, 'id', '*', null, null);

        $coursesinfo = array();
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            try {
                self::validate_context($context);
            } catch (Exception $e) {
                $exceptionparam             = new stdClass();
                $exceptionparam->message    = $e->getMessage();
                $exceptionparam->courseid   = $course->id;
                throw new moodle_exception(
                    get_string('errorcoursecontextnotvalid', 'webservice', $exceptionparam));
            }
            require_capability('moodle/course:view', $context);

            $courseinfo = array();
            $courseinfo['id']               = $course->id;
            $courseinfo['fullname']         = $course->fullname;
            $courseinfo['shortname']        = $course->shortname;
            $courseinfo['categoryid']       = $course->category;
            $courseinfo['summary']          = $course->summary;
            $courseinfo['summaryformat']    = $course->summaryformat;
            $courseinfo['format']           = $course->format;
            $courseinfo['startdate']        = $course->startdate;

            $courseadmin = has_capability('moodle/course:update', $context);
            if ($courseadmin) {
                $courseinfo['categorysortorder']        = $course->sortorder;
                $courseinfo['idnumber']                 = $course->idnumber;
                $courseinfo['showgrades']               = $course->showgrades;
                $courseinfo['showreports']              = $course->showreports;
                $courseinfo['newsitems']                = $course->newsitems;
                $courseinfo['visible']                  = $course->visible;
                $courseinfo['maxbytes']                 = $course->maxbytes;
                $courseinfo['groupmode']                = $course->groupmode;
                $courseinfo['groupmodeforce']           = $course->groupmodeforce;
                $courseinfo['defaultgroupingid']        = $course->defaultgroupingid;
                $courseinfo['lang']                     = $course->lang;
                $courseinfo['timecreated']              = $course->timecreated;
                $courseinfo['timemodified']             = $course->timemodified;
                $courseinfo['forcetheme']               = $course->theme;
                $courseinfo['enablecompletion']         = $course->enablecompletion;
                $courseinfo['completionnotify']         = $course->completionnotify;
            }

            if ($courseadmin or $course->visible
                    or has_capability('moodle/course:viewhiddencourses', $context)) {
                $coursesinfo[] = $courseinfo;
            }
        }

        return $coursesinfo;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_courses_by_idnumber_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'course id'),
                    'shortname' => new external_value(PARAM_TEXT, 'course short name'),
                    'categoryid' => new external_value(PARAM_INT, 'category id'),
                    'categorysortorder' => new external_value(PARAM_INT, 'sort order into the category', VALUE_OPTIONAL),
                    'fullname' => new external_value(PARAM_TEXT, 'full name'),
                    'idnumber' => new external_value(PARAM_RAW, 'id number', VALUE_OPTIONAL),
                    'summary' => new external_value(PARAM_RAW, 'summary'),
                    'summaryformat' => new external_value(PARAM_INT,
                        'the summary text Moodle format'),
                    'format' => new external_value(PARAM_ALPHANUMEXT,
                        'course format: weeks, topics, social, site,..'),
                    'showgrades' => new external_value(PARAM_INT,
                        '1 if grades are shown, otherwise 0', VALUE_OPTIONAL),
                    'newsitems' => new external_value(PARAM_INT,
                        'number of recent items appearing on the course page', VALUE_OPTIONAL),
                    'startdate' => new external_value(PARAM_INT,
                        'timestamp when the course start'),
                    'maxbytes' => new external_value(PARAM_INT,
                        'largest size of file that can be uploaded into the course', VALUE_OPTIONAL),
                    'showreports' => new external_value(PARAM_INT,
                        'are activity report shown (yes = 1, no =0)', VALUE_OPTIONAL),
                    'visible' => new external_value(PARAM_INT,
                        '1: available to student, 0:not available', VALUE_OPTIONAL),
                    'groupmode' => new external_value(PARAM_INT, 'no group, separate, visible', VALUE_OPTIONAL),
                    'groupmodeforce' => new external_value(PARAM_INT, '1: yes, 0: no', VALUE_OPTIONAL),
                    'defaultgroupingid' => new external_value(PARAM_INT, 'default grouping id', VALUE_OPTIONAL),
                    'timecreated' => new external_value(PARAM_INT,
                        'timestamp when the course have been created', VALUE_OPTIONAL),
                    'timemodified' => new external_value(PARAM_INT,
                        'timestamp when the course have been modified', VALUE_OPTIONAL),
                    'enablecompletion' => new external_value(PARAM_INT,
                        'Enabled, control via completion and activity settings. Disbaled,
                        not shown in activity settings.', VALUE_OPTIONAL),
                    'completionnotify' => new external_value(PARAM_INT,
                        '1: yes 0: no', VALUE_OPTIONAL),
                    'lang' => new external_value(PARAM_ALPHANUMEXT,
                        'forced course language', VALUE_OPTIONAL),
                    'forcetheme' => new external_value(PARAM_ALPHANUMEXT,
                        'name of the force theme', VALUE_OPTIONAL),
                ), 'course'
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_users_by_username_parameters() {
        return new external_function_parameters(
            array(
                'usernames' => new external_multiple_structure(new external_value(PARAM_RAW, 'username')),
            )
        );
    }

    /**
     * Get user information
     *
     * @param array $usernames  array of user ids
     * @return array An array of arrays describing users
     */
    public static function get_users_by_username($usernames) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/user/lib.php");
        require_once($CFG->dirroot . "/user/profile/lib.php");
        require_once($CFG->dirroot . "/user/externallib.php");

        $params = self::validate_parameters(self::get_users_by_username_parameters(),
            array('usernames' => $usernames));

        if (empty($params)) {
            header($_SERVER["SERVER_PROTOCOL"].' 422 Unprocessable Entity ($params[\'usernames\'] empty.)', true, 422);
        }

        $result = array();
        foreach ( $params['usernames'] as $uname ) {
            if ( $user = $DB->get_record( 'user', array( 'username' => $uname ), '*', 'id,username,firstname,lastname,email,deleted' ) ) {

                $context = context_user::instance($user->id);
                try {
                    self::validate_context($context);
                } catch (Exception $e) {
                    $exceptionparam             = new stdClass();
                    $exceptionparam->message    = $e->getMessage();
                    $exceptionparam->userid     = $user->id;
                    throw new moodle_exception(
                        get_string('errorusercontextnotvalid', 'local_leapwebservices', $exceptionparam));
                }
                require_capability('moodle/user:viewalldetails', $context);

                if ( empty( $user->deleted ) ) {

                    $userarray = array();
                    $userarray['id']                    = $user->id;
                    $userarray['username']              = $user->username;
                    $userarray['firstname']             = $user->firstname;
                    $userarray['lastname']              = $user->lastname;
                    $userarray['email']                 = $user->email;

                    $result[] = $userarray;
                }
            }
        }

        if ( count( $result ) == 0 ) {
            return array();
        } else {
            return $result;
        }

    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_users_by_username_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'                => new external_value(PARAM_NUMBER, 'ID of the user'),
                    'username'          => new external_value(PARAM_RAW, 'Username policy is defined in Moodle security config'),
                    'firstname'         => new external_value(PARAM_NOTAGS, 'The first name(s) of the user'),
                    'lastname'          => new external_value(PARAM_NOTAGS, 'The family name of the user'),
                    'email'             => new external_value(PARAM_TEXT, 'An email address - allow email as root@localhost'),
                    //'customfields'      => new external_multiple_structure(
                    //    new external_single_structure(
                    //        array(
                    //            'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the custom field'),
                    //            'value' => new external_value(PARAM_RAW, 'The value of the custom field')
                    //        )
                    //    ),
                    //'User custom fields (also known as user profile fields)',
                    //VALUE_OPTIONAL
                    //)
                )
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_assignments_by_username_parameters() {
        return new external_function_parameters(
            array(
                'username' => new external_value(PARAM_TEXT, 'Username. If empty, fail.'),
            )
        );
    }

    /**
     * Get user information
     *
     * @param array $username array of user ids
     * @return array An array of arrays describing users
     */
    public static function get_assignments_by_username($username) {
        global $CFG, $DB;

        $params = self::validate_parameters(self::get_assignments_by_username_parameters(), array('username' => $username));

        if ($params['username'] == '') {
            header($_SERVER["SERVER_PROTOCOL"].' 422 Unprocessable Entity ($params[\'username\'] empty.)', true, 422);
            exit;
        }

        $user = $DB->get_record('user', array('username' => $params['username'] ));
        $courses = enrol_get_users_courses($user->id, false, '*');
        if (!empty($courses)) {

            $contents = array();
            foreach ($courses as $course) {
                $contents[] = $DB->get_records('assign', array('course' => $course->id), 'duedate ASC', 'id, name, intro, allowsubmissionsfromdate, duedate, course');
            }

            $result = array();
            foreach ($contents as $morecontents) {
                foreach ($morecontents as $content) {
                    $assarray = array();
                    $assarray['id']                             = $content->id;
                    $assarray['name']                           = $content->name;
                    $assarray['intro']                          = strip_tags($content->intro);
                    $assarray['allowsubmissionsfromdate']       = $content->allowsubmissionsfromdate;
                    $assarray['allowsubmissionsfromdate-kev']   = date('c', $content->allowsubmissionsfromdate);
                    $assarray['duedate']                        = $content->duedate;
                    $assarray['duedate-kev']                    = date('c', $content->duedate);
                    $assarray['course']                         = $content->course;

                    $sql = "SELECT cm.id AS id
                        FROM ".$CFG->prefix."assign AS a, ".$CFG->prefix."course_modules AS cm, ".$CFG->prefix."modules AS m
                        WHERE a.course = ".$content->course."
                        AND a.course = cm.course
                        AND cm.module = m.id
                        AND m.name = 'assign'
                        AND cm.instance = a.id
                        AND a.id = ".$content->id.";";
                    $instance_res = $DB->get_record_sql($sql);
                    $assarray['instance'] = $instance_res->id;

                    $result[] = $assarray;
                }
            }

            return $result;
        }
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_assignments_by_username_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'                            => new external_value(PARAM_INTEGER, 'ID of the assignment'),
                    'name'                          => new external_value(PARAM_TEXT, 'Assignment name'),
                    'intro'                         => new external_value(PARAM_TEXT, 'Assignment introduction, may contain HTML'),
                    'allowsubmissionsfromdate'      => new external_value(PARAM_INTEGER, 'Date available from (set date)'),
                    'allowsubmissionsfromdate-kev'  => new external_value(PARAM_RAW, 'Date available from (set date) in Kev format'),
                    'duedate'                       => new external_value(PARAM_INTEGER, 'Date available to (due date)'),
                    'duedate-kev'                   => new external_value(PARAM_RAW, 'Date available to (due date) in Kev format'),
                    'course'                        => new external_value(PARAM_INTEGER, 'ID of the course the assignment is set against'),
                    'instance'                      => new external_value(PARAM_INTEGER, 'Module instance'),
                )
            )
        );
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_targets_by_username_parameters() {
        return new external_function_parameters(
            array(
                'username' => new external_value( PARAM_TEXT, 'Username. If empty, fail.' ),
            )
        );
    } // END function.


    /**
     * Get user information
     *
     * @param string $username EBS username, could be 8-digit int or string.
     * @return array An array describing targets (and metadata) for that user for all leapcore_* courses.
     */
    public static function get_targets_by_username( $username ) {
        global $CFG, $DB;

        $params = self::validate_parameters( self::get_targets_by_username_parameters(), array( 'username' => $username ) );

        if ( $params['username'] == '' ) {
            header( $_SERVER["SERVER_PROTOCOL"].' 422 Unprocessable Entity ($params[\'username\'] empty.)', true, 422 );
            exit(1);
        }

        // Require for the course completion code.
        require_once($CFG->libdir . '/completionlib.php');

        // Could do with knowing what this user's {user}.id is.
        $sql = "SELECT id from {user} WHERE username LIKE ?;";
        if ( !$user = $DB->get_record_sql( $sql, array( $params['username'] . '%' ) ) ) {
            header( $_SERVER["SERVER_PROTOCOL"].' 422 Unprocessable Entity ($params[\'username\'] could not be matched against a valid user.)', true, 422 );
            exit(1);
        }

        $targets    = array( 'TAG' );
        $out        = array();

        // Checking for user enrolled as student role, manual enrolments only.
        /*
        $sql = "SELECT DISTINCT c.id AS courseid, c.shortname AS shortname, c.fullname AS fullname, username
            FROM mdl_user u
                JOIN mdl_user_enrolments ue ON ue.userid = u.id
                JOIN mdl_enrol e ON e.id = ue.enrolid
                    -- AND e.enrol = 'manual'
                JOIN mdl_role_assignments ra ON ra.userid = u.id
                JOIN mdl_context ct ON ct.id = ra.contextid
                    AND ct.contextlevel = 50
                JOIN mdl_course c ON c.id = ct.instanceid
                    AND e.courseid = c.id
                JOIN mdl_role r ON r.id = ra.roleid
                    AND r.shortname = 'student'
            WHERE u.username LIKE '" . $params['username'] . "%'
                AND e.status = 0
                AND u.suspended = 0
                AND u.deleted = 0
                AND (
                    ue.timeend = 0
                    OR ue.timeend > NOW()
                )
                AND ue.status = 0;";
        */

        $sql = "SELECT DISTINCT c.id AS courseid, c.shortname AS shortname, c.fullname AS fullname, username
            FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt, mdl_role r
            WHERE ra.userid = u.id
                AND ra.contextid = cxt.id
                AND cxt.contextlevel = 50
                AND cxt.instanceid = c.id
                AND ra.roleid = r.id
                AND u.username LIKE '" . $params['username'] . "%'
            ORDER BY fullname ASC;";

        if ( !$results = $DB->get_records_sql( $sql ) ) {
            return false;
        }

        foreach ($results as $result) {

            $coursecontext  = context_course::instance( $result->courseid );
            if (!$blockrecord    = $DB->get_record( 'block_instances', array(
                'blockname'         => 'leap',
                'parentcontextid'   => $coursecontext->id
            ), '*' ) ) {
                continue;
            }

            $blockinstance  = block_instance( 'leap', $blockrecord );
            if (isset($blockinstance->config->trackertype) && !is_null($blockinstance->config->trackertype) && !empty($blockinstance->config->trackertype)) {
                $trackertype    = $blockinstance->config->trackertype;
            } else {
                continue;
            }
error_log($result->courseid);
error_log($trackertype);
            $out[$trackertype]['leapcore']          = $trackertype;
            $out[$trackertype]['course_id']         = $result->courseid;
            $out[$trackertype]['course_shortname']  = $result->shortname;
            $out[$trackertype]['course_fullname']   = $result->fullname;

            // Walk through a fair few objects to get the course's time modified, final grade and named grade.
            $gi         = new grade_item();
            // The course item is actually the right one to use, even if it is null.
            $gi_item    = $gi::fetch( array( 'courseid' => $out[$trackertype]['course_id'], 'itemtype' => 'course' ) );
            // This may get changed further down the script as we want the most recently changed item's date.
            $out[$trackertype]['course_total_modified'] = $gi_item->timemodified;

            $gg         = new grade_grade();
            $gg_grade   = $gg::fetch( array( 'itemid' => $gi_item->id, 'userid' => $user->id ) );

            // If the scale is going to be a U (or Refer, or Fail etc) as the L3VA is 0, pass null.
            if ( $gg_grade && $gg_grade->finalgrade > 0 ) {
                $out[$trackertype]['course_total'] = $gg_grade->finalgrade;

                $gs         = new grade_scale();
                $gs_scale   = $gs::fetch( array( 'id' => $gi_item->scaleid ) );
                if ( $gi_item->display != 0 ) {
                    // Check first for a non-zero 'display' variable, and run with that if found.
                    $out[$trackertype]['course_total_display'] = grade_format_gradevalue( $gg_grade->finalgrade, $gi_item, true, $gi_item->display );
                } else if ( $gs_scale ) {
                    // See if we have a scale and use that if found.
                    $out[$trackertype]['course_total_display'] = $gs_scale->get_nearest_item( $gg_grade->finalgrade );
                } else {
                    if ( is_numeric( $gg_grade->finalgrade ) ) {
                        $out[$trackertype]['course_total_display'] = round( $out[$trackertype]['course_total'], 0, PHP_ROUND_HALF_UP );
                    } else {
                        $out[$trackertype]['course_total_display'] = $out[$trackertype]['course_total'];
                    }
                }

            } else {
                $out[$trackertype]['course_total'] = 0;

                $out[$trackertype]['course_total_display'] = null;
            }

            // For each target, same as above.
            foreach ( $targets as $target ) {

                $gi         = new grade_item();
                $gi_item    = $gi::fetch( array( 'courseid' => $out[$trackertype]['course_id'], 'itemtype' => 'manual', 'itemname' => $target ) );

                $gg         = new grade_grade();
                $gg_grade   = $gg::fetch( array( 'itemid' => $gi_item->id, 'userid' => $user->id ) );

                if (!isset($gg_grade->finalgrade)) {
                    $gg_grade = new stdClass;
                    $gg_grade->finalgrade = 0;
                }
                $out[$trackertype][strtolower($target)]   = $gg_grade->finalgrade;


                // Get the named result (e.g. 'merit') only for targets which are not L3VA.
                if ( $target <> 'L3VA' ) {

                    $gs         = new grade_scale();
                    $gs_scale   = $gs::fetch( array( 'id' => $gi_item->scaleid ) );

                    // Updating the most recently modified date if it's newer.
                    if ( $gi_item->timemodified > $out[$trackertype]['course_total_modified'] ) {
                        $out[$trackertype]['course_total_modified'] = $gi_item->timemodified;
                    }

                    // If the scale is going to be a U (or Refer, or Fail etc) as the L3VA is 0, pass null.
                    if ( $gg_grade->finalgrade > 0 ) {
                        // If there's no scale, just pass the data across.
                        if ( $gs_scale ) {
                            $out[$trackertype][strtolower($target) . '_display'] = $gs_scale->get_nearest_item( $gg_grade->finalgrade );
                        } else {
                            $out[$trackertype][strtolower($target) . '_display'] = $gg_grade->finalgrade;
                        }
                    } else {
                        $out[$trackertype][strtolower($target) . '_display'] = null;
                    }

                } else {

                    $out[$trackertype][strtolower($target) . '_display'] = $out[$trackertype][strtolower($target)];
                }

                // Rounding.
                if ( is_numeric( $out[$trackertype][strtolower($target) . '_display'] ) ) {
                    $out[$trackertype][strtolower($target) . '_display'] = round( $out[$trackertype][strtolower($target) . '_display'], 2, PHP_ROUND_HALF_UP );
                }

            }

            // Default both of these to null.
            $out[$trackertype]['course_completion_total']     = null;
            $out[$trackertype]['course_completion_completed'] = null;

            // We could do with a course object to use.
            $sql = "SELECT id from {course} WHERE id LIKE ?;";
            if ( !$thiscourse = $DB->get_record_sql( $sql, array( $out[$trackertype]['course_id'] ) ) ) {
                exit(1);
            }

            if ( completion_info::is_enabled_for_site() ) {

                $info = new completion_info( $thiscourse );
                $completions = $info->get_completions($user->id);

                // If there's no completions, none have been configured so do nothing.
                if ( !empty( $completions ) ) {

                    $out[$trackertype]['course_completion_total']     = count( $completions );
                    $out[$trackertype]['course_completion_completed'] = $info->count_course_user_data( $user->id );

                    // Loop through each timecompleted value, ignore if null, update if more recent.
                    foreach ($completions as $completion) {
                        if ( !is_null( $completion->timecompleted ) && $completion->timecompleted > $out[$trackertype]['course_total_modified'] ) {
                            $out[$trackertype]['course_total_modified'] = $completion->timecompleted;
                        }
                    }
                }

            } // END completion info enabled for site check.

            // Stress reduction code.
            //$out[$trackertype]['meaning_of_life']  = '42';
            //$out[$trackertype]['smiley_face']      = ':)';

            // Incomplete course check.
            // TODO: make this better. We scan through all four 'leapcore_' tags (and all the new A2 ones) and get the results, 
            // but sometimes there aren't any.  So for the tags with no associated courses, we remove them.
            if ( !isset( $out[$trackertype]['course_shortname'] ) ) {
                unset($out[$trackertype]);
            }

        } // END foreach $courses.

        if ( !empty( $out ) ) {
            return $out;
        } else {
            return array();
        }

    } // END function.


    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_targets_by_username_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'leapcore'                      => new external_value( PARAM_TEXT,      'The type of core course found.' ),
                    'course_shortname'              => new external_value( PARAM_TEXT,      'The short course name.' ),
                    'course_fullname'               => new external_value( PARAM_TEXT,      'The full course name.' ),
                    'course_id'                     => new external_value( PARAM_INTEGER,   'The course ID number.' ),
                    //'mag'                           => new external_value( PARAM_FLOAT,     'Minimum Achievable Grade.' ),
                    //'mag_display'                   => new external_value( PARAM_TEXT,      'Minimum Achievable Grade (for display).' ),
                    'tag'                           => new external_value( PARAM_FLOAT,     'Target Achievable Grade.' ),
                    'tag_display'                   => new external_value( PARAM_TEXT,      'Target Achievable Grade (for display).' ),
                    //'l3va'                          => new external_value( PARAM_FLOAT,     'Level 3 Value Added.' ),
                    //'l3va_display'                  => new external_value( PARAM_TEXT,      'Level 3 Value Added (for display).' ),
                    'course_total'                  => new external_value( PARAM_FLOAT,     'Course total score.' ),
                    'course_total_display'          => new external_value( PARAM_TEXT,      'Course total score (for display).' ),
                    'course_total_modified'         => new external_value( PARAM_INTEGER,   'Course total modification timestamp.' ),
                    'course_completion_total'       => new external_value( PARAM_INTEGER,   'Course completion total.' ),
                    'course_completion_completed'   => new external_value( PARAM_INTEGER,   'Course completion complete.' ),
                    //'meaning_of_life'               => new external_value( PARAM_INTEGER,   'Meaning of life.' ),
                    //'smiley_face'                   => new external_value( PARAM_TEXT,      'Smiley face.' ),
                )
            )
        );

    } // END function.


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_badges_by_username_parameters() {
        return new external_function_parameters(
            array(
                'username' => new external_value( PARAM_TEXT, 'Username. If empty, fail.' ),
            )
        );
    } // END function.

    /**
     * Get user information
     *
     * @param string $username EBS username, could be 8-digit int or string.
     * @return array An array describing targets (and metadata) for that user for all leapcore_* courses.
     */
    /**
     * TODO: add 'leapcore' tag from this course's Leap block instance configuraion into the output.
     */
    public static function get_badges_by_username( $username ) {
        global $CFG, $DB;

        $params = self::validate_parameters( self::get_badges_by_username_parameters(), array( 'username' => $username ) );

        if ( $params['username'] == '' ) {
            header( $_SERVER["SERVER_PROTOCOL"].' 422 Unprocessable Entity ($params[\'username\'] empty.)', true, 422 );
            exit(1);
        }

        // Could do with knowing what this user's {user}.id is.
        $sql = "SELECT id from {user} WHERE username LIKE ?;";
        if ( !$user = $DB->get_record_sql( $sql, array( $params['username'] . '%' ) ) ) {
            header( $_SERVER["SERVER_PROTOCOL"].' 422 Unprocessable Entity ($params[\'username\'] could not be matched against a valid user.)', true, 422 );
            exit(1);
        }

        require_once($CFG->libdir . '/badgeslib.php');

        // Get the user's badges.
        $userbadges = badges_get_user_badges( $user->id );

        $output = array();
        if ( !$userbadges ) {

            return $output;

        } else {
            $output = array();
            $count = 1;
            foreach ( $userbadges as $hash => $ubadge ) {

                try {
                    // Skip if course not found or else will crash out with error and not release valid badge entries - OW 20170921
                    if ( ! ($coursecontext = context_course::instance( $ubadge->courseid )) )
                        continue;

                    $output[$count]['course_id']    = $ubadge->courseid;
                    $output[$count]['date_issued']  = $ubadge->dateissued;
                    $output[$count]['description']  = $ubadge->description;
                    $output[$count]['details_link'] = (string) new moodle_url('/badges/badge.php', array( 'hash' => $hash ));
                    $output[$count]['image_url']    = (string) badges_bake( $hash, $ubadge->id );
                    $output[$count]['name']         = $ubadge->name;

                    // Gets the 'leapcore' tag associated with the course associated with this badge.
                    // $coursecontext  = context_course::instance( $ubadge->courseid );
                    $blockrecord    = $DB->get_record( 'block_instances', array(
                        'blockname'         => 'leap',
                        'parentcontextid'   => $coursecontext->id
                    ), '*', MUST_EXIST );
                    $blockinstance  = block_instance( 'leap', $blockrecord );
                    $output[$count]['leapcore']     = $blockinstance->config->trackertype;

                    $count++;
                }
                catch (Exception $e) {
                    continue; 
                }

            }

        }

        return $output;

    } // END function.

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_badges_by_username_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'course_id'     => new external_value( PARAM_INT,   'Moodle ID of the course the badge is assigned to.' ),
                    'date_issued'   => new external_value( PARAM_INT,   'Timestamp in Unix epoch format.' ),
                    'description'   => new external_value( PARAM_TEXT,  'Badge description.' ),
                    'details_link'  => new external_value( PARAM_TEXT,  'Full URL to the issued page on Moodle.' ),
                    'image_url'     => new external_value( PARAM_TEXT,  'Full URL to the image.' ),
                    'name'          => new external_value( PARAM_TEXT,  'Badge name.' ),
                    'leapcore'      => new external_value( PARAM_TEXT,  'The type of core course found.' ),

                )
            )
        );

    } // END function.


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_users_with_mag_parameters() {
        return new external_function_parameters(
            array()
        );
    } // END function.

    /**
     * Get user information
     *
     * @param string $username EBS username, could be 8-digit int or string.
     * @return array An array describing targets (and metadata) for that user for all leapcore_* courses.
     */
    public static function get_users_with_mag() {
        global $CFG, $DB;

        // One query to get all the details we need.
        $sql = "SELECT DISTINCT gg.userid, u.username
                FROM {$CFG->prefix}grade_items gi, {$CFG->prefix}grade_grades gg, {$CFG->prefix}user u
                WHERE gi.itemname = 'MAG'
                    AND gi.id = gg.itemid
                    AND gg.userid = u.id;";

        if ( !$users = $DB->get_records_sql( $sql ) ) {
            return array();
            exit(1);
        }

        $output = array();
        $count = 0;
        foreach ( $users as $user ) {
            $count++;

            $output[$count]['userid'] = $user->userid;
            $tmp = explode( '@', $user->username);
            $output[$count]['username'] = $tmp[0];
        }

        return $output;

    } // END function.

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_users_with_mag_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'userid'    => new external_value( PARAM_INT,   'Moodle ID of the user.' ),
                    'username'  => new external_value( PARAM_TEXT,  'Username of the user.' ),
                )
            )
        );

    } // END function.

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_users_with_badges_parameters() {
        return new external_function_parameters(
            array()
        );
    } // END function.

    /**
     * Get user information
     *
     * @return array A list of users who have been assigned badges.
     */
    public static function get_users_with_badges() {
        global $CFG, $DB;

        // One query to get all the details we need.
        $now    = time();
        $then   = $now - ( 60 * 60 * 24 * 365 );  // One year ago.
        $sql = "SELECT DISTINCT u.id, u.username
                FROM {$CFG->prefix}user u, {$CFG->prefix}badge_issued bi
                WHERE u.id = bi.userid
                    AND bi.visible = 1
                    AND (
                        bi.dateexpire > {$now}
                        OR bi.dateexpire IS NULL
                    )
                    AND bi.dateissued > {$then}
                ORDER BY u.id ASC;";

        if ( !$users = $DB->get_records_sql( $sql ) ) {
            return array();
            exit(1);
        }

        $output = array();
        $count = 0;
        foreach ( $users as $user ) {
            $count++;

            $output[$count]['userid'] = $user->id;
            $tmp = explode( '@', $user->username);
            $output[$count]['username'] = $tmp[0];
        }

        return $output;

    } // END function.

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_users_with_badges_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'userid'    => new external_value( PARAM_INT,   'Moodle ID of the user.' ),
                    'username'  => new external_value( PARAM_TEXT,  'Username of the user.' ),
                )
            )
        );

    } // END function.

} // END class.
