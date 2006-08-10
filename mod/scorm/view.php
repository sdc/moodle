<?php  // $Id$

/// This page prints a particular instance of scorm
/// (Replace scorm with the name of your module)

    require_once("../../config.php");
    require_once('locallib.php');

    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    //$organization = optional_param('organization', '', PARAM_INT); // organization ID

    if (!empty($id)) {
        if (! $cm = $cm = get_coursemodule_from_id('scorm', $id)) {
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

    if (isset($SESSION->scorm_scoid)) {
        unset($SESSION->scorm_scoid);
    }

    $strscorms = get_string("modulenameplural", "scorm");
    $strscorm  = get_string("modulename", "scorm");

    if ($course->category != 0) { 
        $navigation = "<a target=\"{$CFG->framename}\" href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
        if ($scorms = get_all_instances_in_course('scorm', $course)) {
            // The module SCORM activity with the least id is the course  
            $firstscorm = current($scorms);
            if (!(($course->format == 'scorm') && ($firstscorm->id == $scorm->id))) {
                $navigation .= "<a target=\"{$CFG->framename}\" href=\"index.php?id=$course->id\">$strscorms</a> ->";
            }       
        }
    } else {
        $navigation = "<a target=\"{$CFG->framename}\" href=\"index.php?id=$course->id\">$strscorms</a> ->";
    }

    $pagetitle = strip_tags($course->shortname.': '.format_string($scorm->name));

    add_to_log($course->id, 'scorm', 'pre-view', 'view.php?id='.$cm->id, "$scorm->id");

    //
    // Print the page header
    //
    if (!$cm->visible and !isteacher($course->id)) {
        print_header($pagetitle, "$course->fullname", "$navigation ".format_string($scorm->name), '', '', true,
                     update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm));
        notice(get_string('activityiscurrentlyhidden'));
    } else {
        print_header($pagetitle, "$course->fullname",
                     "$navigation <a target=\"{$CFG->framename}\" href=\"view.php?id=$cm->id\">".format_string($scorm->name,true)."</a>",
                     '', '', true, update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm));

        if (isteacher($course->id)) {
            $trackedusers = get_record('scorm_scoes_track', 'scormid', $scorm->id, '', '', '', '', 'count(distinct(userid)) as c');
            if ($trackedusers->c > 0) {
                echo "<div class=\"reportlink\"><a target=\"{$CFG->framename}\" href=\"report.php?id=$cm->id\">".get_string('viewallreports','scorm',$trackedusers->c).'</a></div>';
            } else {
                echo '<div class="reportlink">'.get_string('noreports','scorm').'</div>';
            }
        }
        // Print the main part of the page

        print_heading(format_string($scorm->name));

        print_simple_box(format_text($scorm->summary), 'center', '70%', '', 5, 'generalbox', 'intro');
        scorm_view_display($USER, $scorm, 'view.php?id='.$cm->id, $cm);
        print_footer($course);
    }
?>
