<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FAStudentCetas
 *
 * @author mchaney
 */
class FAStudentCetas extends CoreReports{
    //put your code here
    protected $freezepane = 'D2';
    
    function FAStudentCetas()
    {
        
    }
    
    function get_name()
    {
        return "Formal Assessment Student CETAS";
    }
    
    function get_description()
    {
        return "All students, with their subjects and their current CETAS";
    }
    
    function display_options()
    {
        
        $out = '<div>';
        $out .= '<div>';
        $out .= $this->get_core_options();
        $out .= '</div>';
//        $out .= '<div>';
//        $out .= $this->get_target_grade_options();
//        $out .= '</div>';
        $out .= '<div>';
        $out .= $this->get_display_selectors();
        $out .= '</div>';
        $out .= '</div>';
        return $out;
    }
    
    function run_display_report()
    {
        $this->build_report();
        $out = $this->display_report();
        return $out;
    }
    
    function export_report()
    {
        $this->build_report();
        $this->perform_export();
    }
    
    function build_report()
    {
        global $CFG;
        //which display type are we doing?
        $displayType = $this->options['displayoption'];
        $header = array();
        $this->options['oncourse'] = true;
        $this->options['users'] = true;
        if($displayType == 'bysubject')
        {
            $qualifications = bcgt_search_quals_report($this->options);
        }
        
        $students = bcgt_search_users_report($this->options, array('student'));
        
        $qualSummary = array();
        $totalGrades = array();
        $studentCount = array();
        $studentSummary = array();
        
        //to do the grades
        //another array, as it loops over the grades
        //the grade becomes the array key 
        //the value is incremented number
        //ranking based on the object
        $studentData = array();
        if($displayType == 'bysubject')
        {
            foreach($students AS $student)
            {
                $studentSummaryObj = new stdClass();
                $data = array();
                $data[] = $student->username;
                $data[] = $student->firstname;
                $data[] = $student->lastname;
                foreach($qualifications AS $qual)
                {
                    $qualSummaryObj = (array_key_exists($qual->id, $qualSummary) ? $qualSummary[$qual->id] : new stdClass());                    
                    //are they on the qual?
                    //if yes: Then find their ceta
                    //if no then skip
                    if(bcgt_is_user_on_qual($qual->id, $student->id))
                    {
                        if(array_key_exists($qual->id, $studentCount))
                        {
                            $count = $studentCount[$qual->id];
                            $count++;
                        }
                        else
                        {
                            $count = 1;
                        }
                        $studentCount[$qual->id] = $count;
                        //get the latest ceta for this qual.
                        $grade = Qualification::get_current_ceta($qual->id, $student->id);
                        if($grade)
                        {
//                            $grade = end($grades);
                            $data[] = $grade->grade;
                            $totalGrades[$grade->ranking] = $grade->grade;
                            $param = $grade->grade;
                            $qualGradeCount = (isset($qualSummaryObj->{$param}) ? $qualSummaryObj->{$param} : 0);
                            $qualGradeCount++; 
                            $qualSummaryObj->{$param} = $qualGradeCount;
                            
                            $studentGradeCount = (isset($studentSummaryObj->{$param}) ? $studentSummaryObj->{$param} : 0);
                            $studentGradeCount++;
                            $studentSummaryObj->{$param} = $studentGradeCount;
                        }
                        else
                        {
                            $data[] = 'N/A';
                            $qualGradeCount = (isset($qualSummaryObj->nograde) ? $qualSummaryObj->nograde : 0);
                            $qualGradeCount++;
                            $qualSummaryObj->nograde = $qualGradeCount;
                            
                            $studentGradeCount = (isset($studentSummaryObj->nograde) ? $studentSummaryObj->nograde : 0);
                            $studentGradeCount++;
                            $studentSummaryObj->nograde = $studentGradeCount;
                        }
                    }
                    else
                    {
                        $data[] = '';
                    }
                    $qualSummary[$qual->id] = $qualSummaryObj;
                }
                $studentSummary[$student->id] = $studentSummaryObj;
                $studentData[$student->id] = $data;
            }
        }
        else 
        {
            $currentMaxQuals = 0;
            $studentData = array();
            require_once $CFG->dirroot . '/blocks/bcgt/classes/core/Level.class.php';
            //we just want to search for each student
            foreach($students AS $student)
            {
                $data = array();
                $data[] = $student->username;
                $data[] = $student->firstname;
                $data[] = $student->lastname;
                //what quals are the students on?
                $quals = bcgt_get_users_quals($student->id);
                if($quals)
                {
                    if($currentMaxQuals < count($quals))
                    {
                        $currentMaxQuals = count($quals);
                    }
                    foreach($quals AS $qual)
                    {
                        $cellContent = '';
                        $grade = Qualification::get_current_ceta($qual->id, $student->id);
                        if($displayType == 'byoption')
                        {
                            $cellContent = Level::get_short_version($qual->levelid).' '.$qual->subtypeshort.' '.$qual->name.' : ';
                        }
                        if($grade)
                        {
//                            $grade = end($grades);
                            $data[] = $cellContent.$grade->grade;
                        }
                        else 
                        {
                            $data[] = $cellContent.'N/A';
                        }
                        
                    }
                }
                $studentData[$student->id] = $data;
            }
        }
        $header = array();
        if($displayType == 'bysubject')
        {
            $header[] = get_string('username');
            $header[] = get_string('firstname');
            $header[] = get_string('lastname');
            foreach($qualifications AS $qual)
            {
                $header[] = Level::get_short_version($qual->levelid).' '.$qual->subtypeshort.' '.$qual->name;
            }
        }
        else
        {
            $header[] = get_string('username');
            $header[] = get_string('firstname');
            $header[] = get_string('lastname');
            //need to know the total number of subjects?
            $letter = 'A';
            for($i=0;$i<$currentMaxQuals;$i++)
            {
                $header[] = 'Subject'.++$letter;
            }
            
        }
        
        krsort($totalGrades);
        //now we can add the summary
        if($displayType == 'bysubject')
        {            
//            $studentData[] = array(); //blank row
            $blankRow = array('','','',''); //one extra for the blank column
            $gradesBlank = array('');//one extra for the blank column
            $data = array();
            $data[] = '';
            $data[] = 'Students : ';
            $data[] = '';
            foreach($qualifications AS $qual)
            {
                $blankRow[] = '';
                //how many students:
                if(array_key_exists($qual->id, $studentCount))
                {
                    $data[] = $studentCount[$qual->id];
                }
                else
                {
                    $data[] = 0;
                }
            }
            $blankRow[] = '';
            $data[] = ''; //one extra for the blank row
            $data[] = ''; //one extra for the no grade
            foreach($totalGrades AS $ranking=>$grade)
            {
                $gradesBlank[] = '';
                $blankRow[] = '';
                $data[] = '';
            }
            $studentData[] = $blankRow;
            $studentData[] = $data;
            
            foreach($totalGrades AS $ranking=>$grade)
            {
                $data = array();
                $data[] = '';
                $data[] = '('.$grade.') : ';
                $data[] = '';
                foreach($qualifications AS $qual)
                {
                    if(array_key_exists($qual->id, $qualSummary))
                    {
                        $qualSummaryObj = $qualSummary[$qual->id];
                        if(isset($qualSummaryObj->{$grade}))
                        {
                            $data[] = $qualSummaryObj->{$grade};
                        }
                        else
                        {
                            $data[] = '';
                        }
                    }
                    else
                    {
                        $data[] = '';
                    }
                }
                $data[] = '';//one extra for the no grade
                $data = array_merge($data, $gradesBlank);
                $studentData[] = $data;
            }
            $data = array();
            $data[] = '';
            $data[] = '(N/A) : ';
            $data[] = '';
            foreach($qualifications AS $qual)
            {
                if(array_key_exists($qual->id, $qualSummary))
                {
                    $qualSummaryObj = $qualSummary[$qual->id];
                    if(isset($qualSummaryObj->nograde))
                    {
                        $data[] = $qualSummaryObj->nograde;
                    }
                    else
                    {
                        $data[] = '';
                    }
                } 
            }
            $data[] = '';//one extra for the blank column.
            $data = array_merge($data, $gradesBlank);
            $studentData[] = $data;
            //blank column
            $header[] = '';
            foreach($totalGrades AS $ranking=>$grade)
            {
                $header[] = $grade;
            }
            $header[] = 'N/A';
            foreach($students AS $student)
            {
                //now we want to summarise their data
                $studentRow = $studentData[$student->id];
                $studentRow[] = '';//blank column
                $studentSummaryObj = $studentSummary[$student->id];
                foreach($totalGrades AS $ranking=>$grade)
                {
                    if(isset($studentSummaryObj->$grade))
                    {
                        $studentRow[] = $studentSummaryObj->$grade;
                    }
                    else
                    {
                        $studentRow[] = '';
                    }
                }
                if(isset($studentSummaryObj->nograde))
                {
                    $studentRow[] = $studentSummaryObj->nograde;
                }
                else
                {
                    $studentRow[] = '';
                }
                $studentData[$student->id] = $studentRow;
            }
        }
        else
        {
            //we need to append the extra blank cells on:
            foreach($students AS $student)
            {
                $studentRow = $studentData[$student->id];
                $quals = bcgt_get_users_quals($student->id);
                if(($quals && count($quals) < $currentMaxQuals) || !$quals)
                {
                    
                    $blankCells = count($quals);
                    if($quals)
                    {
                        $blankCells = $currentMaxQuals - count($quals);
                    }
                    for($i=0;$i<$blankCells;$i++)
                    {
                        $studentRow[] = '';
                    }
                }
                $studentData[$student->id] = $studentRow;
            }
            
        }
        
        $this->data = $studentData;
        $this->header = $header;
    }
}

?>
