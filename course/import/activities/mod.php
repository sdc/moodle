<?php  // $Id$

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    require_once($CFG->dirroot.'/course/lib.php');
    require_once($CFG->dirroot.'/backup/restorelib.php');

    // if we're not a course creator , we can only import from our own courses.
    if (iscreator()) {
        $creator = true;
    }

    $strimport = get_string("importdata");
 
    $tcourseids = '';
 
    if ($teachers = get_records_select('user_teachers', "userid = $USER->id AND editall = 1",'','id,course')) {
        foreach ($teachers as $teacher) {
            if ($teacher->course != $course->id && $teacher->course != SITEID){
                $tcourseids .= $teacher->course.',';
            }
        }
    }

    $taught_courses = array();
    if (!empty($tcourseids)) {
        $tcourseids = substr($tcourseids,0,-1);
        $taught_courses = get_records_list('course', 'id', $tcourseids);
    }

    if (!empty($creator)) {
        $cat_courses = get_courses($course->category);
    } else {
        $cat_courses = array();
    }

    print_heading(get_string("importactivities"));

    $options = array();
    foreach ($taught_courses as $tcourse) {
        if ($tcourse->id != $course->id && $tcourse->id != SITEID){
            $options[$tcourse->id] = $tcourse->fullname;
        }
    }

    if (empty($options) && empty($creator)) {
        notify(get_string('courseimportnotaught'));
        return; // yay , this will pass control back to the file that included or required us.
    }

    $fm = '<form method="post" action="'.$CFG->wwwroot.'/course/import/activities/index.php"><input type="hidden" name="id" value="'.$course->id.'" />';
    $submit = '<input type="submit" value="'.get_string('usethiscourse').'" /></form>';

    if (count($options) > 0) {
        $table->data[] = array($fm.'<b>'.get_string('coursestaught').'</b>',
                               choose_from_menu($options,"fromcourse","","choose","","0",true),
                               $submit);
    }

    unset($options);

    $options = array();
    foreach ($cat_courses as $ccourse) {
        if ($ccourse->id != $course->id && $ccourse->id != SITEID) {
            $options[$ccourse->id] = $ccourse->fullname;
        }
    }
    $cat = get_record("course_categories","id",$course->category);

    if (count($options) > 0) {
        $table->data[] = array($fm.'<b>'.get_string('coursescategory').' ('.$cat->name .')</b>',
                               choose_from_menu($options,"fromcourse","","choose","","0",true),
                               $submit);
    }

    if (!empty($creator)) {
        $table->data[] = array($fm.'<b>'.get_string('searchcourses').'</b>',
                               '<input type="text" name="fromcoursesearch" />',
                               '<input type="submit" value="'.get_string('searchcourses').'" /></form>');
    }

    if (!empty($fromcoursesearch) && !empty($creator)) {
        $totalcount = 0;
        $courses = get_courses_search(explode(" ",$fromcoursesearch),"fullname ASC",$page,50,$totalcount);
        if (is_array($courses) and count($courses) > 0) {
            $table->data[] = array('<b>'.get_string('searchresults').'</b>','','');
            foreach ($courses as $scourse) {
                if ($course->id != $scourse->id) {
                    $table->data[] = array('',$scourse->fullname,
                                           '<a href="'.$CFG->wwwroot.'/course/import/activities/index.php?id='.$course->id.'&fromcourse='.$scourse->id.'">'
                                           .get_string('usethiscourse'));
                }
            }
        }
        else {
            $table->data[] = array('',get_string('noresults'),'');
        }
    }

    print_table($table);

?>
