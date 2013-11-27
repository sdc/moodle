<?php
/*
 * Moodle Gradetracker V1.0 – This code is copyright of Bedford College and is 
 * supplied for evaluation purposes only. The code may not be used for any 
 * purpose without permission from The Learning Technologies Team, 
 * Bedford College:  moodlegrades@bedford.ac.uk
 * 
 * Author mchaney@bedford.ac.uk
 */

global $COURSE, $CFG, $DB, $PAGE, $OUTPUT;
require_once('../../../config.php');
require_once('../lib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
$originalCourseID = optional_param('oCID', -1, PARAM_INT);
if($originalCourseID != -1)
{
    $context = context_course::instance($originalCourseID);
}
else
{
    $context = context_course::instance($COURSE->id);
}
require_login();
$PAGE->set_context($context);
$courseID = optional_param('cID', -1, PARAM_INT);
if(isset($_POST['save']))
{
    //loop over all of the users, quals and see if the user is now doing them. 
    bcgt_process_course_qual_users($courseID);
}
require_capability('block/bcgt:editqualscourse', $context);
$url = '/blocks/bcgt/forms/edit_course_qual.php';
$PAGE->set_url($url, array());
$PAGE->set_title(get_string('edituserscoursequal', 'block_bcgt'));
$PAGE->set_heading(get_string('edituserscoursequal', 'block_bcgt'));
$PAGE->set_pagelayout('login');
$PAGE->add_body_class(get_string('edituserscoursequals', 'block_bcgt'));
$PAGE->navbar->add(get_string('pluginname', 'block_bcgt'),'my_dashboard.php','title');
$PAGE->navbar->add(get_string('myDashboard', 'block_bcgt'),'my_dashboard.php?tab=dash','title');
$PAGE->navbar->add(get_string('dashtabadm', 'block_bcgt'),'my_dashboard.php?tab=adm','title');
$PAGE->navbar->add(get_string('editcoursequalusers', 'block_bcgt'),null,'title');

$jsModule = array(
    'name'     => 'block_bcgt',
    'fullpath' => '/blocks/bcgt/js/block_bcgt.js',
    'requires' => array('base', 'io', 'node', 'json', 'event', 'button')
);
$PAGE->requires->js_init_call('M.block_bcgt.initcoursequalsusers', null, true, $jsModule);
require_once($CFG->dirroot.'/blocks/bcgt/lib.php');
load_javascript();
$out = $OUTPUT->header();
//get the courses qualifications

//get the courses students, by child course. 

$currentQuals = bcgt_get_course_quals($courseID);
$role = bcgt_get_role('student');
$users = bcgt_get_course_students($courseID);
$heading = '';
$course = $DB->get_record_select('course', 'id = ?', array($courseID));
if($course)
{
    $heading = ''.$course->shortname.' : '.$course->fullname;
}
$out .= html_writer::tag('h2', get_string('editcoursequalusers','block_bcgt'), 
        array('class'=>'formheading'));
$out .= html_writer::tag('h2', $heading, 
        array('class'=>'formheading'));
$out .= html_writer::start_tag('div', array('class'=>'bcgt_admin_controls', 
    'id'=>'editCourseQualUsers'));

$out .= '<form name="editCourseQual" action="edit_course_qual_user.php" method="POST" id="editCourseQualForm">';
$out .= '<input type="submit" name="save" value="'.get_string('save', 'block_bcgt').'"/>';
$out .= '<input type="hidden" name="cID" value="'.$courseID.'"/>';
$out .= '<input type="hidden" name="oCID" value="'.$originalCourseID.'"/>';
$out .= '<table id="courseQualUserTable1" class="courseQualUserTable">';
$out .= '<thead><tr><th>'.get_string('enrolment', 'block_bcgt').'</th><th></th><th>'.get_string('username').
        '</th><th>'.get_string('name').'</th><th></th>';
if($currentQuals)
{
    foreach($currentQuals AS $qual)
    {
        //Select all Students for this Qual
        $out .= '<th>'.$qual->family.'<br />'.$qual->trackinglevel.'<br />'.
                $qual->subtype.'<br />'.$qual->name.'<br /><br />'.
                '<a href="edit_course_qual_user?cID='.$courseID.'" 
                    title="'.get_string('selectallstudentsqual', 'block_bcgt').'">'.
                        '<img src="'.$CFG->wwwroot.'/blocks/bcgt/images/arrowdown.jpg"'. 
                        'width="25" height="25" class="qualColumnAll" id="q'.$qual->id.'"/>'.
                        '</a></th>';
    }
}
$out .= '</tr>';
$out .= '</thead><tbody>';
$count = 0;
$lastCourse = $courseID;
foreach($users AS $student)
{
    $count++;
    $out .= '<tr>';
    $currentCourse = $student->courseid;
    if($count == 1)
    {
        $out .= '<td>'.get_string('direct', 'block_bcgt').'</td><td></td><td></td><td></td><td></td>';
        foreach($currentQuals AS $qual)
        {
            //Select all Students on this course for this Qual
            $out .= '<td class="qualSelect"><a class="qualSelect" href="edit_course_qual_user?cID='.$courseID.'" 
                    title="'.get_string('courseualusersselectall','block_bcgt').'">'.
                        '<img src="'.$CFG->wwwroot.'/blocks/bcgt/images/arrowdown.jpg"'. 
                        'width="25" height="25" class="qualColumnCourse" '.
                    'id="q'.$qual->id.'c'.$currentCourse.'"/></a></td>';
        }
        $out .= '</tr>';
        $out .= '<tr>';
    }
    if($currentCourse != $lastCourse)
    {
        $lastCourse = $currentCourse;
        $out .= '<td>'.$student->courseshortname.'</td><td></td><td></td><td></td><td></td>';
        foreach($currentQuals AS $qual)
        {
            //Select all Students on this course for this Qual
            $out .= '<td class="qualSelect"><a class="qualSelect" href="edit_course_qual_user?cID='.$courseID.'" 
                    title="'.get_string('courseualusersselectall', 'block_bcgt').'">'.
                        '<img src="'.$CFG->wwwroot.'/blocks/bcgt/images/arrowdown.jpg"'. 
                        'width="25" height="25" class="qualColumnCourse" '.
                    'id="q'.$qual->id.'c'.$currentCourse.'"/></a></td>';
        }
        $out .= '</tr>';
        $out .= '<tr>';
    }
    $out .= '<td></td>';
    //if commenting this back in dont forget that the student object doesnt have
    //the id as the user id, is the the role assignment id
    //so $userObj = $student
    //userObj->id = $student->userid
//    $out .= '<td>'.$OUTPUT->user_picture($student, array(1)).'</td>';
    $out .= '<td></td>';
    $out .= '<td>'.$student->username.'</td>';
    $out .= '<td>'.$student->firstname.' '.$student->lastname.'</td>';
    //, 
    $out .= '<td class="qualSelect"><a class="qualSelect" href="edit_course_qual_course.php?qID='.$qual->id.'&sID='.$student->userid.'"'.
            'title="'.get_string('selectallusersquals', 'block_bcgt').'">'.
            '<img src="'.$CFG->wwwroot.'/blocks/bcgt/images/arrowright.jpg"'. 
            'width="25" height="25" class="studentRow" id="s'.$student->userid.'"/>'.
            '</a></td>';
    foreach($currentQuals AS $qual)
    {
        $checked = '';
        if(Qualification::check_user_on_qual($student->userid, $role->id, $qual->id))
        {
            $checked = 'checked';
        }
        $out .= '<td class="qualSelect"><input type="checkbox" name="chq'.$qual->id.'s'.$student->userid.'"'.
            'id="" class="qualSelect chq'.$qual->id.' chq'.$qual->id.'c'.$currentCourse.' '.
                'chs'.$student->userid.'" '.$checked.'/></td>';
    }
    $out .= '</tr>';
}
$out .= '</tbody></table>';
$out .= '<input type="submit" name="save" value="'.get_string('save', 'block_bcgt').'"/>';
$out .= '</form>';





//get the students enrolled on this course, and where they are enrolled find the method, where the
//method is child course//get the child course. 

$out .= html_writer::end_tag('div');//end main column
$out .= $OUTPUT->footer();
echo $out;
?>
