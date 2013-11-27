<?php
require_once '../../../../config.php';
require_once '../../lib.php';
require_once $CFG->dirroot . '/blocks/elbp/lib.php';

require_login();

$ELBP = ELBP\ELBP::instantiate();

$action = $_POST['action'];
$params = $_POST['params'];

if ($action == 'save')
{
    
    if (!isset($params['courseID']) || !isset($params['studentID'])) exit;
    
    $courseID = $params['courseID'];
    $studentID = $params['studentID'];
    $qualID = (isset($params['qualID'])) ? $params['qualID'] : false;
    
    $access = $ELBP->getUserPermissions($studentID);
    
    $grades = array();
    
    if (elbp_has_capability('block/bcgt:editasptargetgrade', $access))
    {
        if (isset($params['aspirationalgrade'])){
            
            if ($params['aspirationalgrade'] == "OTHER")
            {
                
                $grade = $params['aspirationalcustom'];
                if (!empty($grade))
                {
                    $grades[] = array(
                        "type" => "aspirational",
                        "custom" => $grade
                    );
                }
                
            }
            else
            {
                
                $explode = explode(":", $params['aspirationalgrade']);
                $grades[] = array(
                    "type" => "aspirational",
                    "grade" => $explode[2],
                    "recordid" => $explode[1],
                    "location" => $explode[0]
                );
                
            }
            
            
            
        }
    }
                    
    if ($grades)
    {
        
        foreach($grades as $grade)
        {
            
            // If custom
            if (isset($grade['custom']))
            {
                
                $ins = new stdClass();
                $ins->grade = $grade['custom'];
                $ins->ranking = 1;
                $grade['recordid'] = $DB->insert_record("block_bcgt_custom_grades", $ins);
                $grade['location'] = 'block_bcgt_custom_grades';
                $grade['grade'] = $grade['custom'];
                
            }
            
            if ($qualID){
                $check = $DB->get_record("block_bcgt_stud_course_grade", array("userid" => $studentID, "courseid" => $courseID, "qualid" => $qualID, "type" => $grade['type']));
            } else {
                $check = $DB->get_record("block_bcgt_stud_course_grade", array("userid" => $studentID, "courseid" => $courseID, "type" => $grade['type']));
            } 
            
            if ($check)
            {
                $check->recordid = $grade['recordid'];
                $check->location = $grade['location'];
                $check->setbyuserid = $USER->id;
                $check->settime = time();
                $DB->update_record("block_bcgt_stud_course_grade", $check);
            }
            else
            {
                $ins = new stdClass();
                $ins->userid = $studentID;
                $ins->courseid = $courseID;
                $ins->type = $grade['type'];
                $ins->recordid = $grade['recordid'];
                $ins->location = $grade['location'];
                $ins->setbyuserid = $USER->id;
                $ins->settime = time();
                
                if ($qualID){
                    $ins->qualid = $qualID;
                }
                
                $DB->insert_record("block_bcgt_stud_course_grade", $ins);
            }
            
            echo " $('#{$grade['type']}_info_{$courseID}_{$qualID}').html('<h3>{$grade['grade']}</h3><small>".get_string('setby', 'block_elbp')." ".fullname($USER)."</small>'); ";
            
        }
        
    }
    
    exit;
    
}