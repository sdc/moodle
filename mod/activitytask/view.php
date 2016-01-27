<?php
/**
 * Prints a particular instance of activitytask
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_activitytask
 * @copyright  2015 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//load Moodle
require_once('../../config.php');
//load the module library
//require_once('locallib.php');

//get the course module id from the url
$cmid = optional_param('id', 0, PARAM_INT); 
//or obtain the activitytask id from the url 
$s  = optional_param('s', 0, PARAM_INT);

if($cmid) {
	//obtain the course module
    $cm = get_coursemodule_from_id('activitytask', $cmid, 0, false, MUST_EXIST);
    //obtain the course record from the database
	$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    //obtain the activitytask record from the database
	$activitytask = $DB->get_record('activitytask', array('id' => $cm->instance), '*', MUST_EXIST);
} else if($s) {
    //obtain the activitytask record from the database
	$activitytask = $DB->get_record('activitytask', array('id' => $s), '*', MUST_EXIST);
    //obtain the course record from the database
	$course = $DB->get_record('course', array('id' => $activitytask->course), '*', MUST_EXIST);
    //obtain the course module
	$cm = get_coursemodule_from_instance('activitytask', $activitytask->id, $course->id, false, MUST_EXIST);
} else {
    //if we do not have either parameter, show an error message
	error('You must specify a course_module ID or an instance ID');
}

//read only access 
require_course_login($course);

//pull the course context and module context
$coursecontext = context_course::instance($course->id);
$context = context_module::instance($cm->id);

//determine whether this is a teacher or student
//$isteacher = has_capability('mod/activitytask:addinstance', $coursecontext);
$isteacher = has_capability('mod/assign:grade', $coursecontext);

$params = array(
    'context' => $context,
    'objectid' => $activitytask->id
);
$event = \mod_activitytask\event\course_module_viewed::create($params);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('activitytask', $activitytask);
$event->trigger();

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);
	
//check whether or not we have the ability add an activity task (if so, likely a teacher)
if(!$isteacher && !$activitytask->intro) {
	
	//read only access to this page
	require_login($course, true, $cm);

	//redirect to the course page and disable this page as this is solely for adding a header to the course page
	//redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);

}

//pull the language strings for the module
$strmodname     = get_string('modulename', 'mod_activitytask');
$strmodnames    = get_string('modulenameplural', 'mod_activitytask');
$theadstu 		= get_string('theadstudent', 'mod_activitytask');
$theadstat 		= get_string('theadstatus', 'mod_activitytask');

//set the page theme layout
$PAGE->set_pagelayout('incourse');
//set the url for this page
$PAGE->set_url('/mod/activitytask/view.php', array('id' => $cmid));
//set the <title> of the page
$PAGE->set_title($course->shortname.': '.$strmodname);
//set the heading for the page
$PAGE->set_heading($course->fullname);

//tell the navbar to ignore the active page and add our module name 
//https://docs.moodle.org/dev/Navigation_API#Navbar
$PAGE->navbar->add($strmodname);

//display the header
echo $OUTPUT->header();
//display the heading
echo $OUTPUT->heading($strmodname.': '.$activitytask->name);

//pull the date the activity is due (if set) and show this
$duedate = false;
if($activitytask->duedate) {
	$duedate = new DateTime();
	$duedate->setTimestamp($activitytask->duedate);
	$passed = ($duedate > new DateTime());
	echo '<p class="alert alert-'.($passed ? 'danger' : 'success').'"><strong>Due Date:</strong> '.$duedate->format('D, M j, Y g:iA').'</p>';
}

//show the details
if(trim(strip_tags($activitytask->intro))) {
	//echo $activitytask->intro.'<hr>';
	echo $OUTPUT->box_start('mod_introbox', 'pageintro');
	echo format_module_intro('activitytask', $activitytask, $cm->id);
	echo $OUTPUT->box_end();
}
	
echo '<hr>';

//if we are not a teacher, provide a mark done button
if(!$isteacher) {
	
	//obtain the status for this student
	$params = array(
		'userid' => $USER->id, 
		'activitytask' => $cm->instance
	);
	$fields = 'datedone';		
	$status = $DB->get_record('activitytask_status', $params, $fields);	
	
	//if not done, then show a button
	if(!$status || !$status->datedone) {
		$done = '<a class="activitytask-button btn btn-primary"'.
				  ' href="'.new moodle_url('/mod/activitytask/markdone.php?id='.$cm->instance).'"'.
				  ' >'.get_string('btn_label_done', 'activitytask').'</a>';
	} else {
		$dt = new DateTime($status->datedone);
		$done = '<span class="activitytask-done"><strong>'.get_string('completed', 'activitytask').':</strong> '.$dt->format('D, M j, Y g:iA').'</span>'.
			    (!$duedate ? '' : ($dt < $duedate ? ' <span class="ontime">('.get_string('ontime', 'activitytask').')</span>' : ' <span class="late">('.get_string('late', 'activitytask').')</span>'));	
	}
	echo '<p>'.$done.'</p>';
	
//otherwise, show a list of the students
} else {
	
	echo '<h3>'.get_string('overviewheader', 'activitytask').'</h3>';
	
	//obtain a list of enrolled users and their activitytask statuses

	$sql = "
	SELECT u.id, u.firstname, u.lastname, a.name, s.datedone
	FROM {user_enrolments} e
	INNER JOIN {user} u ON u.id = e.userid
	INNER JOIN {enrol} n ON n.id = e.enrolid
	INNER JOIN {activitytask} a ON a.course = n.courseid
	LEFT JOIN {activitytask_status} s ON s.activitytask = a.id AND s.userid = e.userid
	WHERE n.courseid = ?
	";

	$sql = "
	SELECT u.id, u.firstname, u.lastname, u.email, u.picture, 
		u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename, u.imagealt,
		a.name, s.datedone, r.id AS roleid, l.name AS rolename		
	FROM {role_assignments} r
	INNER JOIN {user} u ON u.id = r.userid
	INNER JOIN {context} x ON x.id = r.contextid
	INNER JOIN {course} c ON c.id = x.instanceid AND x.contextlevel = 50
	INNER JOIN {role} l ON l.id = r.roleid 
	INNER JOIN {activitytask} a ON a.course = c.id
	LEFT JOIN {activitytask_status} s ON s.activitytask = a.id AND s.userid = u.id
	WHERE l.archetype = 'student' AND a.id = ?
	ORDER BY u.lastname, u.firstname
	";

	$students = $DB->get_records_sql($sql, array($activitytask->id));

	if(!$students) {
		//if there are none, then display a message indicating this
		//@param #1: message to display   //@param #2: link to use for the continue button
		notice(get_string('nostudents', 'activitytask'), new moodle_url('/course/view.php', array('id' => $course->id)));
	}

	//create a new html table
	$table = new html_table();
	//add the class to the table
	$table->attributes['class'] = 'generaltable activitytask-student-status';

	//set the table header row along with alignment
	$table->head  = array($theadstu, $theadstat);
	$table->align = array('left');

	//iterate students and create the table
	foreach($students as $stu) {
		//setup an array to save the table row data
		$row = array();	
		$pic = '<span class="stupic">'.$OUTPUT->user_picture($stu, array('courseid' => $course->id, 'link' => true)).'</span>';
		$name = '<span class="stuname">'.$stu->firstname.' '.$stu->lastname.'</span>';	
		$row[] = $pic.$name;
		if($stu->datedone) {
			$dt = new DateTime($stu->datedone);
			$row[] = '<span class="complete">'.get_string('completedon', 'activitytask').' '.$dt->format('D, M j, Y g:iA').'</span>'.
					 (!$duedate ? '' : ($dt < $duedate ? ' <span class="ontime">('.get_string('ontime', 'activitytask').')</span>' : ' <span class="late">('.get_string('late', 'activitytask').')</span>'));
		} else {
			$row[] = '<span class="incomplete">'.get_string('incomplete', 'activitytask').'</span>';
		}	
		//add the row to the table
		$table->data[] = $row;
	}

	//display the table
	echo html_writer::table($table);

}

//display the footer
echo $OUTPUT->footer();





