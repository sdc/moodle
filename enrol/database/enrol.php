<?php  // $Id$

require_once("$CFG->dirroot/enrol/enrol.class.php");

class enrolment_plugin extends enrolment_base {

    var $log;    

/// Leave get_teacher_courses() function unchanged for the time being


/// Leave cron() function unchanged



/// Overide the base get_student_courses() function
function get_student_courses(&$user) {
    global $CFG;

    parent::get_student_courses($user);


    // This is a hack to workaround what seems to be a bug in ADOdb with accessing 
    // two databases of the same kind ... it seems to get confused when trying to access
    // the first database again, after having accessed the second.
    // The following hack will make the database explicit which keeps it happy
    if (strpos($CFG->prefix, $CFG->dbname) === false) {
        $CFG->prefix = "$CFG->dbname.$CFG->prefix";
    }
        

    // Connect to the external database
    $enroldb = &ADONewConnection($CFG->enrol_dbtype);
    if ($enroldb->PConnect($CFG->enrol_dbhost,$CFG->enrol_dbuser,$CFG->enrol_dbpass,$CFG->enrol_dbname)) {

        foreach ($user->student as $courseid=>$value) {
        
            /// Get the value of the local course field
            $localcoursevalue = get_field("course", $CFG->enrol_localcoursefield, "id", $courseid);
            
            /// Find a record in the external database that matches the local course field and local user field
            /// to the respective remote fields
            $rs = $enroldb->Execute("SELECT * FROM $CFG->enrol_dbtable 
                                     WHERE $CFG->enrol_remotecoursefield = '$localcoursevalue' 
                                     AND $CFG->enrol_remoteuserfield = '{$user->$CFG->enrol_localuserfield}' ");

            /// If no records existed then student has been unenrolled externally.
            /// Unenrol locally and remove entry from the $user->student array
            if (! ($rs->RecordCount()) ) {
                unenrol_student($user->id, $courseid);
                unset ($user->student[$courseid]);
            }
        }

        $enroldb->Close();

    }

}


/// Override the base print_entry() function
function print_entry($course) {
    global $CFG;

    if (! empty($CFG->enrol_allowinternal) ) {
        parent::print_entry($course);
    } else {
        print_header();
        notice(get_string("enrolmentnointernal"), $CFG->wwwroot);
    }
}


/// Override the base check_entry() function
function check_entry($form, $course) {
    global $CFG;

    if (! empty($CFG->enrol_allowinternal) ) {
        parent::check_entry($form, $course);
    }
}


/// Overide the get_access_icons() function
function get_access_icons($course) {
}


/// Overrise the base config_form() function
function config_form($frm) {
    global $CFG;
    include("$CFG->dirroot/enrol/database/config.html");
}

/// Override the base process_config() function
function process_config($config) {

    if (!isset($config->enrol_dbtype)) {
        $config->enrol_dbtype = 'mysql';
    }
    set_config('enrol_dbtype', $config->enrol_dbtype);

    if (!isset($config->enrol_dbhost)) {
        $config->enrol_dbhost = '';
    }
    set_config('enrol_dbhost', $config->enrol_dbhost);

    if (!isset($config->enrol_dbuser)) {
        $config->enrol_dbuser = '';
    }
    set_config('enrol_dbuser', $config->enrol_dbuser);

    if (!isset($config->enrol_dbpass)) {
        $config->enrol_dbpass = '';
    }
    set_config('enrol_dbpass', $config->enrol_dbpass);

    if (!isset($config->enrol_dbname)) {
        $config->enrol_dbname = '';
    }
    set_config('enrol_dbname', $config->enrol_dbname);

    if (!isset($config->enrol_dbtable)) {
        $config->enrol_dbtable = '';
    }
    set_config('enrol_dbtable', $config->enrol_dbtable);

    if (!isset($config->enrol_localcoursefield)) {
        $config->enrol_localcoursefield = '';
    }
    set_config('enrol_localcoursefield', $config->enrol_localcoursefield);

    if (!isset($config->enrol_localuserfield)) {
        $config->enrol_localuserfield = '';
    }
    set_config('enrol_localuserfield', $config->enrol_localuserfield);

    if (!isset($config->enrol_remotecoursefield)) {
        $config->enrol_remotecoursefield = '';
    }
    set_config('enrol_remotecoursefield', $config->enrol_remotecoursefield);

    if (!isset($config->enrol_remoteuserfield)) {
        $config->enrol_remoteuserfield = '';
    }
    set_config('enrol_remoteuserfield', $config->enrol_remoteuserfield);

    if (!isset($config->enrol_allowinternal)) {
        $config->enrol_allowinternal = '';
    }
    set_config('enrol_allowinternal', $config->enrol_allowinternal);
    
    return true;

}


} // end of class

?>
