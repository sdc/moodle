<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('../../../config.php');

global $COURSE, $CFG, $PAGE, $OUTPUT, $USER, $DB;
require_once('../lib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
$cID = optional_param('cID', -1, PARAM_INT);
$context = context_course::instance($COURSE->id);
require_login();
$PAGE->set_context($context);

$eID = optional_param('eID', -1, PARAM_INT);
$e2ID = optional_param('e2ID', -1, PARAM_INT);
$type = optional_param('type', '', PARAM_TEXT);
$showHTML = optional_param('html', false, PARAM_BOOL);
set_time_limit(0);
$display = '';
switch($type)
{
    case "type":
        //then get all of the quals that are below this type
        $display = Qualification::get_quals_and_alsp_report($eID);
        break;
    case "qual":
        $group = new Group();
        $groups = $group->get_groups_on_qual($eID);
        if(get_config('bcgt','usegroupsingradetracker') && $groups)
        {
            //then show groups
            $display = Qualification::get_qual_alsp_group_report($eID, $e2ID);
        }
        else
        {
            //else show users.
            $display = Qualification::get_qual_alsp_users_report($eID, $e2ID);
        }
        break;
    default:
}

$output = array(
    "type"=>$type,
    "eID"=>$eID,
    "e2ID"=>$e2ID,
    "display"=>$display
);
if($showHTML)
{
    echo $display;
}
else
{
    echo json_encode( $output );
}
?>

