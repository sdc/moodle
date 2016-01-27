<?php
/**
 * Prints a particular instance of activitytypeheader
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_activitytypeheader
 * @copyright  2015 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//load Moodle
require_once('../../config.php');
//load the module library
require_once('lib.php');

//get the course module id from the url
$cmid = optional_param('id', 0, PARAM_INT); 
//or obtain the activitytypeheader id from the url 
$s  = optional_param('s', 0, PARAM_INT);

if($cmid) {
	//obtain the course module
    $cm         = get_coursemodule_from_id('activitytypeheader', $cmid, 0, false, MUST_EXIST);
    //obtain the course record from the database
	$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    //obtain the activitytypeheader record from the database
	$activitytypeheader  = $DB->get_record('activitytypeheader', array('id' => $cm->instance), '*', MUST_EXIST);
} else if($s) {
    //obtain the activitytypeheader record from the database
	$activitytypeheader  = $DB->get_record('activitytypeheader', array('id' => $s), '*', MUST_EXIST);
    //obtain the course record from the database
	$course     = $DB->get_record('course', array('id' => $activitytypeheader->course), '*', MUST_EXIST);
    //obtain the course module
	$cm         = get_coursemodule_from_instance('activitytypeheader', $activitytypeheader->id, $course->id, false, MUST_EXIST);
} else {
    //if we do not have either parameter, show an error message
	error('You must specify a course_module ID or an instance ID');
}

//read only access to this page
require_login($course, true, $cm);

//redirect to the course page and disable this page as this is solely for adding a header to the course page
redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);

