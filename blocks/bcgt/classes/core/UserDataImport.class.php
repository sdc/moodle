<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserDataImport
 *
 * @author mchaney
 */
class UserDataImport {
    //put your code here
    protected $files;
    protected $summary;
    protected $success;
    
    function UserDataImport()
    {
        
    }
    
    public function get_headers()
    {
        $array1 = $this->get_header(1);
        $array2 = $this->get_header(2);
        $array3 = $this->get_header(3);
        $retval = '';
        $retval .= '<ul>';
        $retval .= '<li>'.get_string('usercriteriacsv','block_bcgt').' = ';
        foreach($array1 AS $header)
        {
            $retval .= $header.',';
        }
        $retval .= '</li>';
        $retval .= '<li>'.get_string('userunitcsv','block_bcgt').' = ';
        foreach($array2 AS $header)
        {
            $retval .= $header.',';
        }
        $retval .= '</li>';
        $retval .= '<li>'.get_string('userawardcsv','block_bcgt').' = ';
        foreach($array3 AS $header)
        {
            $retval .= $header.',';
        }
        $retval .= '</li>';
        $retval .= '</ul>';
        return $retval;
    }
    
    private function get_header($no)
    {
        switch($no)
        {
            case 1:
                return array("Family", "Type", "SubType", "Level", "Name", 
            "StudentUsername", "UnitType", "UnitLevel", "UnitName", "UniqueID", "CriteriaName", "ValueShortName", "Comments", 
            "UpdateByUsername", "DateUpdated");
                break;
            case 2:
                return array("Family", "Type", "SubType", 
                            "Level", "Name", "Student", "UnitType", 
                    "UnitLevel", "UnitName", "UniqueID","Award", "Comments", 
                    "UpdateByUsername", "DateUpdated");
                break;
            case 3:
                return array("Family", "Type", "SubType", 
                            "Level", "Name", "Student", "TargetGrade");
                break;
        }
    }
    
    public function get_examples()
    {
        return '<ul><li>'.get_string('usercriteriacsv','block_bcgt').' = BTEC,BTECHigher,HND,Level 5,'.
                'Graphics Design,jsmith,BTEC,nullorblank,Unit101 - Test Name,X101,P1,A,This is the comments that will go'.
                'into the cell they must be escaped,bsmothe,1312333123</li><li>'.
                get_string('userunitcsv','block_bcgt').' = BTEC,BTEC,Diploma,Level 3'.
                'Business,213122,BTECHigher,Level 4,UnitName,X103,Pass,Comments,jsmith,211233</li><li>'.
                get_string('userawardcsv','block_bcgt').' = BTEC,BTECLower,Award,Level 1,Sports,112uyu,Distinction</li>';
    }
    
    public function get_description()
    {
        return get_string('uddesc', 'block_bcgt');
    }
    
    public function get_file_names()
    {
        $files = $this->get_files_name_array();
        $retval = '<ul>';
        foreach($files AS $file)
        {
            $retval .= '<li>'.$file.'</li>';
        }
        $retval .= '</ul>';
        return $retval;
    }
    
    public function get_files_name_array()
    {
        return array('usercriteriadata.csv', 'userunitdata.csv', 'userawarddata.csv');
    }
    
    public function has_multiple()
    {
        return true;
    }
    
    public function get_file_options()
    {
        $retval = get_string('usercriteriacsv','block_bcgt').' : ';
        $retval .= '<input type="file" name="importfile1" value="file1" id="file1"/><br />';
        $retval .= get_string('userunitcsv','block_bcgt').' : ';
        $retval .= '<input type="file" name="importfile2" value="file2" id="file2"/><br />';
        $retval .= get_string('userawardcsv','block_bcgt').' : ';
        $retval .= '<input type="file" name="importfile3" value="file3" id="file3"/><br />';
        return $retval;
    }
    
    public function get_submitted_import_options()
    {
        
    }
    
    public function was_success()
    {
        return $this->success;
    }
    
    public function display_summary()
    {        
        $retval = '<p><ul>';
        $retval .= '<li>'.get_string('dataimportedsuccess','block_bcgt').' : '.$this->summary->successCount.'</li>';
        $retval .= '<li>'.get_string('criteriadataimportedsuccess','block_bcgt').' : '.$this->summary->successCountCriteria.'</li>';
        $retval .= '<li>'.get_string('criteriadataupdated','block_bcgt').' : '.$this->summary->updatedCriteria.'</li>';
        $retval .= '<li>'.get_string('criteriadatainserted','block_bcgt').' : '.$this->summary->insertCriteria.'</li>';
        $retval .= '<li>'.get_string('unitdataimportedsuccess','block_bcgt').' : '.$this->summary->successCountUnit.'</li>';
        $retval .= '<li>'.get_string('unitdataupdated','block_bcgt').' : '.$this->summary->updatedUnit.'</li>';
        $retval .= '<li>'.get_string('unitdatainserted','block_bcgt').' : '.$this->summary->insertUnit.'</li>';
        $retval .= '<li>'.get_string('awarddataimportedsuccess','block_bcgt').' : '.$this->summary->successCountAward.'</li>';
        $retval .= '<li>'.get_string('awarddataupdated','block_bcgt').' : '.$this->summary->insertAward.'</li>';
        $retval .= '<li>'.get_string('awarddatainserted','block_bcgt').' : '.$this->summary->updatedAward.'</li>';
                
        if(!$this->success)
        {
            $retval .= '<li>'.get_string('qualsnotfound','block_bcgt').' : '.count($this->summary->qualsNotFound).'</li>'; 
            $retval .= '<li>'.get_string('unitsnotfound','block_bcgt').' : '.count($this->summary->unitsNotFound).'</li>';
            $retval .= '<li>'.get_string('criteriasnotfound','block_bcgt').' : '.count($this->summary->criteriasNotFound).'</li>';
            
            $retval .= '<li>'.get_string('breakdownsnotfound','block_bcgt').' : '.count($this->summary->breakdownNotFound).'</li>';
        }
        $retval .= '</ul></p>';
        return $retval;
    }
    
    public function validate($server = false)
    {
        global $CFG;
        $retval = new stdClass();
        $retval->errorMessage = '';
        $retval->retval = true;
        if($server)
        {
            $fileNo = 0;
            $files = $this->get_files_name_array();
            foreach($files AS $file)
            {
                $fullFile = $CFG->dataroot.'/bcgt/import/'.$file;
                $fileNo++;
                if(!file_exists($fullFile))
                {
                    $this->errorMessage = get_string('noimportfile','block_bcgt');
                    $retval->errorMessage = $this->errorMessage;
                    $retval->retval = false;
                    return $retval;
                } 
                if(substr(strrchr($fullFile,'.'),1) != 'csv')
                {
                    $retval->errorMessage = get_string('notcsvfile','block_bcgt');
                    $retval->retval = false;
                    return $retval;
                }
                $retval1 = $this->check_header($fullFile, $this->get_header($fileNo));
                if(!$retval1->retval)
                {
                    return $retval;
                }
            }
        }
        else
        {
            if(!isset($_POST['importfile1']) || !isset($_POST['importfile2']) || !isset($_POST['importfile3']))
            {
                $retval->errorMessage = get_string('noimportfile','block_bcgt');
                $retval->retval = false;
            }
            if(substr(strrchr($_FILES['importfile1']["name"],'.'),1) != 'csv' || 
                substr(strrchr($_FILES['importfile2']["name"],'.'),1) != 'csv' ||
                substr(strrchr($_FILES['importfile3']["name"],'.'),1) != 'csv')
            {
                $retval->errorMessage = get_string('notcsvfile','block_bcgt');
                $retval->retval = false;
            }
            $retval1 = $this->check_header($_FILES['importfile1']["tmp_name"], $this->get_header(1));
            if(!$retval1->retval)
            {
                return $retval;
            }
            $retval2 = $this->check_header($_FILES['importfile2']["tmp_name"], $this->get_header(2));
            if(!$retval2->retval)
            {
                return $retval;
            }
            $retval3 = $this->check_header($_FILES['importfile3']["tmp_name"], $this->get_header(3));
            if(!$retval3->retval)
            {
                return $retval;
            }
        }
        return $retval;
    
        
    }
    public function check_header($file, $headerArray)
    {
        $count = 0;
        $CSV = fopen($file, 'r');
        $header = '';
        while(($assessmentMark = fgetcsv($CSV)) !== false) {
            if($count === 1)
            {
                break;
            }
            $header = $assessmentMark;
            $count++;
        }
        return $this->validate_header($header, $headerArray);
    }
    
    public function validate_header($headerCSV, $headerArray)
    {
        $retval = new stdCLass();
        if(count($headerArray) != count($headerCSV))
        {
            $retval->errorMessage = get_string('countheadersimport','block_bcgt');
            $retval->retval = false;
        }
        $headerCount = 0;
        foreach($headerArray AS $h)
        {
            if($headerCSV[$headerCount] != $h)
            {
                $retval->errorMessage = get_string('csvheadersdontmatch','block_bcgt');
                $retval->retval = false;
            }
            $headerCount++;
        }
        $retval->retval = true;
        return $retval;
    }

    public function get_files($server = false)
    {
        global $CFG;
        $retval = new stdClass();
        if($server)
        {
            $files = $this->get_files_name_array();
            $retval->criteriafile = $CFG->dataroot.'/bcgt/import/'.$files[0];
            $retval->unitfile = $CFG->dataroot.'/bcgt/import/'.$files[1];
            $retval->awardfile = $CFG->dataroot.'/bcgt/import/'.$files[2];
        }
        else
        {
            $retval->criteriafile = $_FILES['importfile1'];
            $retval->unitfile = $_FILES['importfile2'];
            $retval->awardfile = $_FILES['importfile3'];
        }
        $this->files = $retval;
        return $retval;
    }
    
    public function process_import_csv($process = false)
    {
        $processRecords = optional_param('count', 0, PARAM_INT);
        global $DB;
        //needs to get the first file and process it, then the second, then third and so on. 
        $userCriteriaCSV = $this->files->criteriafile;
        $CSV = fopen($userCriteriaCSV, 'r');
        $count = 1;
        $qualNotFound = array();
        $unitNotFound = array();
        $studentNotFound = array();
        $valueNotFound = array();
        $criteriaNotFound = array();
        $moreRecentUpdate = array();
        $moreRecentUnitUpdate = array();
        $awardNotFound = array();
        $breakdownNotFound = array();
        $moreRecentAwardUpdate = array();
        $teachersFound = array();
        $updatedRecords = 0;
        $insertedRecords = 0;
        $successCount = 0;
        global $CFG;
        //do 100 lines at a time.
        $studentsFound = array();
//        echo "Processing ".($processRecords * 250)." to ".($processRecords * 250 + 250).'<br />';
        while(($userCriteria = fgetcsv($CSV)) !== false) {
            if($userCriteria[0] != '' && $count != 1)
            {    
                
                //&& ($count >= ($processRecords * 250)) && ($count < ($processRecords * 250 + 250))
                if(array_key_exists($userCriteria[5], $studentsFound))
                {
                    $studentID = $studentsFound[$userCriteria[5]];
                }
                else
                {
                    $student = $this->find_user($userCriteria[5]);
                    if(!$student)
                    {
                        $studentNotFound[$userCriteria[5]] = $userCriteria[5];
                        continue;
                    }
                    $studentID = $student->id;
                    $studentsFound[$userCriteria[5]] = $studentID;
                }
                
                if(array_key_exists($userCriteria[13], $teachersFound))
                {
                    $teacherID = $teachersFound[$userCriteria[13]];
                }
                else
                {
                    $teacher = $this->find_user($userCriteria[13]);
                    $teacherID = -1;
                    if($teacher)
                    {
                        $teacherID = $teacher->id;
                        $teachersFound[$userCriteria[13]] = $teacherID;
                    }
                }
                
                
                //find the qual
                $quals = $this->find_qual($userCriteria[0], $userCriteria[1], $userCriteria[2], $userCriteria[3], $userCriteria[4]);
                if(!$quals)
                {
                    print_object($userCriteria);
                    echo ''.$userCriteria[0].' '.$userCriteria[1].' '.$userCriteria[2].
                            ' '.$userCriteria[3].' '.$userCriteria[4].' Qual Not Found <br />';
                    $qualNotFound[$userCriteria[0].' '.$userCriteria[1].' '.$userCriteria[2].' '.$userCriteria[3].' '.
                        $userCriteria[4]] = $userCriteria[0].' '.$userCriteria[1].' '.$userCriteria[2].
                            ' '.$userCriteria[3].' '.$userCriteria[4];
                    continue;
                }
                if(count($quals) != 1)
                {
                    continue;
                }
                $qual = end($quals);
                $qualID = $qual->id;
                $typeID = $qual->typeid;
                
                //find the unit
                $unit = $this->find_unit($userCriteria[0], $userCriteria[6], $userCriteria[7], $userCriteria[8], $userCriteria[9], $qualID);
                if(!$unit)
                {
                    $unitNotFound[$userCriteria[0].' '.$userCriteria[6].' '.
                        $userCriteria[7].' '.$userCriteria[8].' '.$userCriteria[9].
                        ' '.$qualID] = $userCriteria[0].' '.$userCriteria[6].' '.
                            $userCriteria[7].' '.$userCriteria[8].' '.$userCriteria[9].
                            ' '.$qualID;
                    
                    //can we find it with just the name and the qualificationid?

                    continue;
                }
                elseif(count($unit) > 1)
                {
                    continue;
                }
                $unitObj = end($unit);
                $unitID = $unitObj->id;
                
                //find the criteria
                $criteria = $this->find_criteria($userCriteria[10],$unitID);
                if(!$criteria)
                {
                    $criteriaNotFound[$userCriteria[10]." ".$unitID] = $userCriteria[10]." ".$unitID;
                    continue;
                }
                $criteriaID = $criteria->id;
                //find the value
                $value = $this->find_value($userCriteria[11], $typeID);
                if(!$value)
                {
                    $valueNotFound[$userCriteria[11]." ".$typeID] = $userCriteria[11]." ".$typeID;
                    continue;
                }
                $valueID = $value->id;
                
                $record = new stdClass();
                $record->userid = $studentID;
                $record->bcgtqualificationid = $qualID;
                $record->bcgtcriteriaid = $criteriaID;
                $record->bcgtvalueid = $valueID;
                $record->updatebyuserid = $teacherID;
                $record->comments = $userCriteria[12];
                
                $usersCriteriaRecord = $this->find_users_criteria_record($studentID, $criteriaID, $qualID);
                if($usersCriteriaRecord)
                {
                    //has it been updated more recently than our last?
                    if(!$usersCriteriaRecord->dateupdated || $usersCriteriaRecord->dateupdated < $userCriteria[14])
                    {
                        //then we want to update it
                        $record->id = $usersCriteriaRecord->id;
                        $DB->update_record('block_bcgt_user_criteria', $record);
                        $updatedRecords++;
                    }
                    else
                    {
                        $moreRecentUpdate[] = $count; 
                    }
                }
                else 
                {
                    //lets insert a brand new one
                    $DB->insert_record('block_bcgt_user_criteria', $record);
                    $insertedRecords++;
                }     
                $successCount++;
            }
//            elseif($count >= (($processRecords * 250) + 250))
//            {
//                //then we are after the number and so we want to reload
//                $processRecords = $processRecords + 1;
//                redirect($CFG->wwwroot.'/blocks/bcgt/forms/import.php?a=ud&server=1&count='.$processRecords);
//            }
            $count++;
        }  
        fclose($CSV);
        $summary = new stdClass();
        $success = true;
        if(count($qualNotFound) > 0 || count($unitNotFound) > 0 || count($criteriaNotFound) > 0)
        {
            $success = false;
        }
        
        $summary->successCount = $successCount;
        $summary->successCountCriteria = $successCount;
        $summary->qualsNotFound = $qualNotFound;
        $summary->criteriasNotFound = $criteriaNotFound;
        $summary->unitsNotFound = $unitNotFound;
        $summary->insertCriteria = $insertedRecords;
        $summary->updatedCriteria = $updatedRecords;
//        pn($summary);
        
        $userUnitCSV = $this->files->unitfile;
        $CSV = fopen($userUnitCSV, 'r');
        $count = 1;
        $insertedRecords = 0;
        $updatedRecords = 0;
        $unitSuccess = 0;
        while(($userUnit = fgetcsv($CSV)) !== false) {
            if($count != 1)
            {
                //need to find the qual
                //need to find the unit

                if(array_key_exists($userUnit[5], $studentsFound))
                {
                    $studentID = $studentsFound[$userUnit[5]];
                }
                else
                {
                    $student = $this->find_user($userUnit[5]);
                    if(!$student)
                    {
                        $studentNotFound[$userUnit[5]] = $userUnit[5];
                        continue;
                    }
                    $studentID = $student->id;
                    $studentsFound[$userUnit[5]] = $studentID;
                }
                if(array_key_exists($userUnit[12], $teachersFound))
                {
                    $teacherID = $teachersFound[$userUnit[12]];
                }
                else
                {
                    $teacher = $this->find_user($userUnit[12]);
                    $teacherID = null;
                    if($teacher)
                    {
                        $teacherID = $teacher->id;
                        $teachersFound[$userUnit[12]] = $teacherID;
                    }
                    
                }
                   
//                
                //find the qual
                //$family, $type, $subtype, $level, $name
                $quals = $this->find_qual($userUnit[0], $userUnit[1], $userUnit[2], $userUnit[3], $userUnit[4]);
                if(!$quals)
                {
                    $qualNotFound[$userUnit[0].' '.$userUnit[1].' '.$userUnit[2].' '.$userUnit[3].' '.
                        $userUnit[4]] = $userUnit[0].' '.$userUnit[1].' '.$userUnit[2].
                            ' '.$userUnit[3].' '.$userUnit[4];
                    continue;
                }
                elseif(count($quals) > 1)
                {
                    continue;
                }
                $qual = end($quals);
                $qualID = $qual->id;
                $typeID = $qual->typeid;
                
                //find the unit
                //$family, $type, $level, $name, $uniqueID, $qualID = -1
                $unit = $this->find_unit($userUnit[0], $userUnit[6], $userUnit[7], $userUnit[8], $userUnit[9], $qualID);
                if(!$unit)
                {
                    $unitNotFound[$userUnit[0].' '.$userUnit[6].' '.
                        $userUnit[7].' '.$userUnit[8].' '.$userUnit[9].
                        ' '.$qualID] = $userUnit[0].' '.$userUnit[6].' '.
                            $userUnit[7].' '.$userUnit[8].' '.$userUnit[9].
                            ' '.$qualID;
                    
                    //can we find it with just the name and the qualificationid?

                    continue;
                }
                elseif(count($unit) > 1)
                {
                    continue;
                }
                $unitObj = end($unit);
                $unitID = $unitObj->id;
                
                $award = $this->find_award($userUnit[10], $typeID);
                if(!$award)
                {
                    $awardNotFound[$userUnit[10].' '.$typeID] = $userUnit[10].' '.$typeID;
                    continue;
                }
                $awardID = $award->id;
                //need to find the award
                //need to find the teacher
                //need to find the student
                $record = new stdClass();
                $record->userid = $studentID;
                $record->updatedbyuserid = $teacherID;
                $record->bcgtunitid = $unitID;
                $record->bcgtqualificationid = $qualID;
                $record->bcgttypeawardid = $awardID;
                $record->comments = $userUnit[11];
                
                //now find the user unit record;
                $userUnitRecord = $this->find_users_unit_record($studentID, $unitID, $qualID);
                if($userUnitRecord)
                {
                    if(!$userUnitRecord->dateupdated || $userUnitRecord->dateupdated < $userCriteria[12])
                    {
                        //update
                        $record->id = $userUnitRecord->id;
                        $updatedRecords++;
                        $DB->update_record('block_bcgt_user_unit', $record);
                    }
                    else
                    {
                        $moreRecentUnitUpdate[] = $count; 
                    }
                }
                else
                {
                    //insert
                    $insertedRecords++;
                    $DB->insert_record('block_bcgt_user_unit', $record);
                }
                $unitSuccess++;
                $successCount++;
            }
            $count++;
            
        }  
        fclose($CSV);
//        
        if(count($qualNotFound) > 0 || count($unitNotFound) > 0)
        {
            $success = false;
        }
        
        $summary->successCount = $successCount;
        $summary->successCountUnit = $unitSuccess;
        $summary->qualsNotFound = $qualNotFound;
        $summary->unitsNotFound = $unitNotFound;
        $summary->insertUnit = $insertedRecords;
        $summary->updatedUnit = $updatedRecords;
//        pn($summary);
        
        $insertedRecords = 0;
        $updatedRecords = 0;
        $userAwardCSV = $this->files->awardfile;
        $CSV = fopen($userAwardCSV, 'r');
        $count = 1;
        $awardSuccess = 0;
        while(($userAward = fgetcsv($CSV)) !== false) {
            if($count != 1)
            {
                //need to find the qual
                //need to find the unit
                //need to find the award
                //need to find the teacher
                //need to find the student
                if(array_key_exists($userAward[5], $studentsFound))
                {
                    $studentID = $studentsFound[$userAward[5]];
                }
                else
                {
                    $student = $this->find_user($userAward[5]);
                    if(!$student)
                    {
                        $studentNotFound[$userAward[5]] = $userUnit[5];
                        continue;
                    }
                    $studentID = $student->id;
                    $studentsFound[$userAward[5]] = $studentID;
                }
                
                //find the qual
                //$family, $type, $subtype, $level, $name
                $quals = $this->find_qual($userAward[0], $userAward[1], $userAward[2], $userAward[3], $userAward[4]);
                if(!$quals)
                {
                    $qualNotFound[$userAward[0].' '.$userAward[1].' '.$userAward[2].' '.$userAward[3].' '.
                        $userAward[4]] = $userAward[0].' '.$userAward[1].' '.$userAward[2].
                            ' '.$userAward[3].' '.$userAward[4];
                    continue;
                }
                $qual = end($quals);
                $qualID = $qual->id;
                $targetQualID = $qual->bcgttargetqualid;

                $breakdown = $this->find_breakdown($userAward[6], $targetQualID);
                if(!$breakdown)
                {
                    $breakdownNotFound[$userAward[6].' '.$targetQualID] = $userAward[6].' '.$targetQualID;
                    continue;
                }
                $breakdownID = $breakdown->id;
                //need to find the award
                //need to find the teacher
                //need to find the student
                $record = new stdClass();
                $record->userid = $studentID;
                $record->bcgtqualificationid = $qualID;
                $record->bcgtbreakdownid = $breakdownID;
                $record->type = 'Import';
                $record->warning = '';
                
                //now find the user unit record;
                $userAwardRecord = $this->find_users_award_record($studentID, $qualID);
                if($userAwardRecord)
                {
                    //update
                    foreach($userAwardRecord AS $award)
                    {
                        $record->id = $award->id;
                        $updatedRecords++;
                        $DB->update_record('block_bcgt_user_award', $record);
                    }
                }
                else
                {
                    //insert
                    $DB->insert_record('block_bcgt_user_award', $record);
                    $insertedRecords++;
                }
                $awardSuccess++;
                $successCount++;
            }
            $count++;
            
        }  
        fclose($CSV);
        
        if(count($qualNotFound) > 0 || count($breakdownNotFound) > 0)
        {
            $success = false;
        }
        $summary->successCount = $successCount;
        $summary->successCountAward = $awardSuccess;
        $summary->qualsNotFound = $qualNotFound;
        $summary->breakdownNotFound = $breakdownNotFound;
        $summary->insertAward = $insertedRecords;
        $summary->updatedAward = $updatedRecords;
//        pn($summary);
        
        $this->summary = $summary;
        $this->success = $success;
    }
    
    public function display_import_options()
    {
        $retval = '<table>';
        $retval .= '<tr><td><label for="option1">'.get_string('udoverwrightdata', 'block_bcgt').' : </label></td>';
        $retval .= '<td><input type="checkbox" checked="" name="option1"/></td>';
        $retval .= '<td><span class="description">('.get_string('udoverwrightdatadesc', 'block_bcgt').')</span></td></tr>';
        $retval .= '</table>';
        return $retval;
    }
    
    private function find_qual($family, $type, $subtype, $level, $name)
    {
        global $DB;
        $sql = "SELECT distinct(qual.id) as id, targetqual.id AS bcgttargetqualid, type.id as typeid, 
            type.type, subtype.id AS subtypeid, family.id AS familyid, level.id as levelid, qual.name 
            FROM {block_bcgt_qualification} qual 
            JOIN {block_bcgt_target_qual} targetqual ON targetqual.id = qual.bcgttargetqualid 
            JOIN {block_bcgt_type} type ON type.id = targetqual.bcgttypeid 
            JOIN {block_bcgt_subtype} subtype ON subtype.id = targetqual.bcgtsubtypeid 
            JOIN {block_bcgt_level} level ON level.id = targetqual.bcgtlevelid 
            JOIN {block_bcgt_type_family} family ON family.id = type.bcgttypefamilyid 
            WHERE family.family = ? AND type.type = ? AND subtype.subtype = ? AND level.trackinglevel = ? 
            AND qual.name = ?";
        $records = $DB->get_records_sql($sql, array($family, $type, $subtype, $level, $name));
        if(count($records) > 1)
        {
//            print_object($records);
        }
        return $records;
    }
    
    private function find_unit($family, $type, $level, $name, $uniqueID, $qualID = -1)
    {
        global $DB;
        $sql = "SELECT distinct(unit.id) as id FROM {block_bcgt_unit} unit 
            JOIN {block_bcgt_type} type ON unit.bcgttypeid = type.id 
            JOIN {block_bcgt_type_family} family ON family.id = type.bcgttypefamilyid 
            JOIN {block_bcgt_level} level ON level.id = unit.bcgtlevelid";
        if($qualID != -1)
        {
            $sql .= " JOIN {block_bcgt_qual_units} qualunits ON qualunits.bcgtunitid = unit.id";
        }
        $params = array($family, $type, $level, $name, $uniqueID);
        $sql .= ' WHERE family.family = ? AND type.type = ? AND level.trackinglevel =? AND unit.name = ? 
            AND unit.uniqueid = ?';
        if($qualID != -1)
        {
            $sql .= ' AND qualunits.bcgtqualificationid = ?';
            $params[] = $qualID;
        }
        return $DB->get_records_sql($sql, $params);
        
    }
    
    private function find_criteria($name, $unitID)
    {
        global $DB;
        $sql = "SELECT criteria.* FROM {block_bcgt_criteria} criteria 
            WHERE criteria.name = ? AND criteria.bcgtunitid = ?";
        return $DB->get_record_sql($sql, array($name, $unitID));
    }
    
    private function find_value($shortvalue, $typeID)
    {
        global $DB;
        $sql = "SELECT value.* FROM {block_bcgt_value} value 
            WHERE value.shortvalue = ? AND value.bcgttypeid = ?";
        return $DB->get_record_sql($sql, array($shortvalue, $typeID));
    }
    
    private function find_user($username)
    {
        global $DB;
        $sql = "SELECT * FROM {user} WHERE username = ?";
        return $DB->get_record_sql($sql, array($username));
    }
    
    private function find_award($award, $typeID)
    {
        global $DB;
        $sql = "SELECT award.* FROM {block_bcgt_type_award} award 
            WHERE award.award = ? AND award.bcgttypeid = ?";
        return $DB->get_record_sql($sql, array($award, $typeID));
    }
    
    private function find_breakdown($targetGrade, $targetQualID)
    {
        global $DB;
        $sql = "SELECT breakdown.* FROM {block_bcgt_target_breakdown} breakdown 
            WHERE breakdown.targetgrade = ? AND breakdown.bcgttargetqualid = ?";
        return $DB->get_record_sql($sql, array($targetGrade, $targetQualID));
    }
    
    private function find_users_criteria_record($studentID, $criteriaID, $qualID)
    {
        global $DB;
        $sql = "SELECT * FROM {block_bcgt_user_criteria} WHERE 
            userid = ? AND bcgtcriteriaid = ? AND bcgtqualificationid = ?";
        return $DB->get_record_sql($sql, array($studentID, $criteriaID, $qualID));
    }
    
    private function find_users_unit_record($studentID, $unitID, $qualID)
    {
        global $DB;
        $sql = "SELECT * FROM {block_bcgt_user_unit} WHERE 
            userid = ? AND bcgtunitid = ? AND bcgtqualificationid = ?";
        return $DB->get_record_sql($sql, array($studentID, $unitID, $qualID));
    }
    
    private function find_users_award_record($studentID, $qualID)
    {
        global $DB;
        $sql = "SELECT distinct(useraward.id), useraward.courseid, 
            useraward.bcgtqualificationid, useraward.userid, useraward.bcgtbreakdownid, 
            useraward.bcgttargetgradesid, useraward.type, useraward.warning, useraward.dateupdated, useraward.overallgrade
            FROM {block_bcgt_user_award} useraward WHERE 
            userid = ? AND bcgtqualificationid = ?";
        return $DB->get_records_sql($sql, array($studentID, $qualID));
    }
}

?>
