<?php
    require_once('../../config.php');
    require_once('locallib.php');
        
    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    $scoid = required_param('scoid', PARAM_INT);  // sco ID
//    $attempt = required_param('attempt', PARAM_INT);  // attempt number
    $attempt = $SESSION->scorm_attempt;


    if (!empty($id)) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
        if (! $scorm = get_record("scorm", "id", $cm->instance)) {
            error("Course module is incorrect");
        }
    } else if (!empty($a)) {
        if (! $scorm = get_record("scorm", "id", $a)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $scorm->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    } else {
        error('A required parameter is missing');
    }

    require_login($course->id, false, $cm);

    if (confirm_sesskey() && (!empty($scoid))) {
        $result = true;
        if (has_capability('mod/scorm:savetrack', get_context_instance(CONTEXT_MODULE,$cm->id))) {
            foreach ($_POST as $element => $value) {
                if (substr($element,0,3) == 'cmi') {
                    $element = str_replace('__','.',$element);
                    $element = preg_replace('/_(\d+)/',".\$1",$element);
                    $result = scorm_insert_track($USER->id, $scorm->id, $scoid, $attempt, $element, $value) && $result;
                }
            }
        }
        if ($result) {
            echo "true\n0";
        } else {
            echo "false\n101";
        }
    }
?>
