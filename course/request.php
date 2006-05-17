<?php  // $Id$
 
    /// this allows a student to request a course be created for them.

    require_once('../config.php');
  
    require_login();

    if (isguest()) {
        error("No guests here!");
    }

    if (empty($CFG->enablecourserequests)) {
        error(get_string('courserequestdisabled'));
    }

    $strtitle = get_string('courserequest');

    print_header($strtitle,$strtitle,$strtitle);

    if (($form = data_submitted()) and confirm_sesskey()) {
        validate_form($form,$err) ;

        if (empty($err)) {
            $form->requester = $USER->id;

            if (insert_record('course_request',$form)) {
                notice(get_string('courserequestsuccess'));
            }
            else {
                notice(get_string('courserequestfailed'));
            }
            print_footer();
            exit;
        } else {
            $form = stripslashes_safe($form);
        }

        // !! no db access using data from $form beyond this point !!
    }

//    print_simple_box(get_string('courserequestintro'),'center');
    print_simple_box_start("center");
    print_string('courserequestintro'); 
    include("request.html");
    print_simple_box_end();

    print_footer($course);

    if ($usehtmleditor) {
        use_html_editor("summary");
        use_html_editor("reason");
    }

    exit;


function validate_form(&$form,&$err) {

    if (empty($form->shortname)) {
        $err['shortname'] = get_string('missingshortname');
    }
    
    if (empty($form->fullname)) {
        $err['fullname'] = get_string('missingfullname');
    }

    if (empty($form->summary)) {
        $err["summary"] = get_string("missingsummary");
    }

    if (empty($form->reason)) {
        $err["reason"] = get_string("missingreqreason");
    }
    
    $foundcourses = null;
    $foundreqcourses = null;

    if (!empty($form->shortname)) {
        $foundcourses = get_records("course", "shortname", $form->shortname);
        $foundreqcourses = get_records("course_request", "shortname", $form->shortname);
    }
    if (!empty($foundreqcourses)) {
        if (!empty($foundcourses)) {
            $foundcourses = array_merge($foundcourses,$foundreqcourses);
        } else {
            $foundcourses = $foundreqcourses;
        }
    }

    if (!empty($foundcourses)) {
        if (!empty($course->id)) {
            unset($foundcourses[$course->id]);
        }
        if (!empty($foundcourses)) {
            foreach ($foundcourses as $foundcourse) {
                if ($foundcourse->requester) {
                    $pending = 1;
                    $foundcoursenames[] = $foundcourse->fullname.' [*]';
                }
                else {
                    $foundcoursenames[] = $foundcourse->fullname;
                }
            }
            $foundcoursenamestring = addslashes(implode(',', $foundcoursenames));
            
            $err["shortname"] = get_string("shortnametaken", "", $foundcoursenamestring);
            if (!empty($pending)) {
                $err["shortname"] .= '<br />'.get_string('starpending');
            }
        }
    }
}


?>