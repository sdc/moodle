<?php

/**
 * <title>
 * 
 * @copyright 2013 Bedford College
 * @package Bedford College Electronic Learning Blue Print (ELBP)
 * @version 1.0
 * @author Conn Warwicker <cwarwicker@bedford.ac.uk> <conn@cmrwarwicker.com>
 * 
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 * 
 */

namespace ELBP\Plugins;

require_once 'lib.php';
require_once 'classes/core/UserPriorLearning.class.php';
require_once 'classes/core/Reporting.class.php';


class elbp_target_grades extends Plugin {
    
    /**
     * Construct the plugin object
     * @param bool $install If true, we want to send the default info to the parent constructor, to install the record into the DB
     */
    public function __construct($install = false) {
        
        if ($install){
            parent::__construct( array(
                "name" => strip_namespace(get_class($this)),
                "title" => "Target & Predicted Grades",
                "path" => '/blocks/bcgt/',
                "version" => 2013101500
            ) );
        }
        else
        {
            parent::__construct( strip_namespace(get_class($this)) );
        }

    }
    
    
     /**
     * Install the plugin
     */
    public function install()
    {
        
        global $DB;
        
        $return = true;
        $pluginID = $this->createPlugin();
        $return = $return && $pluginID;
        
        
        // [Any extra tables are handled by the bcgt block itself]
        
        // Data
        
        // Reporting elements for bc_dashboard reporting wizard
        $DB->insert_record("lbp_plugin_report_elements", array("pluginid" => $this->id, "getstringname" => "reports:bcgt_target_grades:aspgrades", "getstringcomponent" => "block_bcgt"));
        
        // Hooks
        
        
        
        return $return;
    }
    
    /**
     * Upgrade the plugin from an older version to newer
     */
    public function upgrade(){
        
        global $DB;
        
        $return = true;
        $version = $this->version; # This is the current DB version we will be using to upgrade from     
        
       
        if ($version < 2013102401)
        {
            
            $DB->insert_record("lbp_plugin_report_elements", array("pluginid" => $this->id, "getstringname" => "reports:bcgt_target_grades:aspgrades", "getstringcomponent" => "block_bcgt"));
            $this->version = 2013102401;
            $this->updatePlugin();
            \mtrace("## Inserted plugin_report_element data for plugin: {$this->title}");
            
        }
    
    
        return $return; # Never actually seems to change..
        
        
    }
    
        
    /**
     * Load the summary box
     * @return type
     */
    public function getSummaryBox(){
        
        $TPL = new \ELBP\Template();
        
        $TPL->set("obj", $this);
        
        $courses = $this->getStudentsCourses();
                
        if ($courses)
        {
            foreach($courses as $course)
            {
                if (!is_null($course->qualid)){
                    $course->aspirationalGrade = $this->getAspirationalTargetGrade($course->id, $course->qualid);
                } else {
                    $course->aspirationalGrade = $this->getAspirationalTargetGrade($course->id);
                }
            }
        }
                
        $TPL->set("courses", $courses);
        
        try {
            return $TPL->load($this->CFG->dirroot . $this->path . 'tpl/elbp_target_grades/summary.html');
        }
        catch (\ELBP\ELBPException $e){
            return $e->getException();
        }
        
    }
    
    
    /**
     * For the bc_dashboard reporting wizard - get all the data we can about Targets for these students,
     * then return the elements that we want.
     * @param type $students
     * @param type $elements
     */
    public function getAllReportingData($students, $elements)
    {
        
        global $DB;
                
        if (!$students || !$elements) return false;
        
        $data = array();
        
        // Some overal variables for counting
        $totalStudents = count($students);
        
        $aspGrades = '-';
                  
        // We can't get any overalls here, so only bother once we're looking at indivudla students
        if ($totalStudents == 1)
        {
        
            // Loop students and find all their targets
            foreach($students as $student)
            {

                $this->loadStudent($student->id);

                $courses = $this->getStudentsCourses();
                
                $a = array();
                if ($courses)
                {
                    foreach($courses as $course)
                    {
                        
                        if (!is_null($course->qualid)){
                            $grade = $this->getAspirationalTargetGrade($course->id, $course->qualid);
                            if ($grade)
                            {
                                $grade = $grade->grade;
                            }
                            else
                            {
                                $grade = '-';
                            }
                            $a[] = $course->fullname . ' ['.$course->qual->get_name().'] (' . $grade . ')';
                        } else {
                            $grade = $this->getAspirationalTargetGrade($course->id, $course->qualid);
                            if ($grade)
                            {
                                $grade = $grade->grade;
                            }
                            else
                            {
                                $grade = '-';
                            }
                            $a[] = $course->fullname . ' (' . $grade . ')';
                        }
                        
                    }
                    
                    $aspGrades = implode(",\n ", $a);
                    
                }
                

            }
        
        }
                       
        
        // Totals
        $data['reports:bcgt_target_grades:aspgrades'] = $aspGrades;

        
        $names = array();
        $els = array();
        
        foreach($elements as $element)
        {
            $record = $DB->get_record("lbp_plugin_report_elements", array("id" => $element));
            $names[] = $record->getstringname;
            $els[$record->getstringname] = $record->getstringcomponent;
        }
        
        $return = array();
        foreach($names as $name)
        {
            if (isset($data[$name])){
                $newname = \get_string($name, $els[$name]);
                $return["{$newname}"] = $data[$name];
            }
        }
        
        return $return;
        
    }
    
    
    
    public function getStudentsCourses(){
        
        global $DB;
        
        if (!$this->student) return false;
        
        global $DB;
        
        $DBC = new \ELBP\DB();
        
        $courses = $DBC->getStudentsCourses($this->student->id);
                
        if (!$courses) return $courses; # Empty array
        
        $courseType = $this->getSetting('course_types');
                        
        $array = array();
        
        foreach($courses as $course)
        {

            $checkEnrol = $DB->get_records("enrol", array("enrol" => "meta", "courseid" => $course->id));
            
            // Meta
            if ($courseType == 'meta' && $checkEnrol) $array[] = $course;
            
            // Child
            elseif ($courseType == 'child' && !$checkEnrol) $array[] = $course;
            
            elseif ($courseType == 'both') $array[] = $course;

        }
        
        $return = array();
        
        // Multiple rows for each qual this course is on
        foreach($array as $course)
        {
            
            $quals = $DB->get_records("block_bcgt_course_qual", array("courseid" => $course->id));
            if ($quals)
            {
                foreach($quals as $qual)
                {
                    
                    $loadParams = new \stdClass();
                    $loadParams->loadLevel = \Qualification::LOADLEVELMIN;
                    $qualObj = \Qualification::get_qualification_class_id($qual->bcgtqualificationid);
                    
                    $obj = new \stdClass();
                    $obj->id = $course->id;
                    $obj->fullname = $course->fullname;
                    $obj->shortname = $course->shortname;
                    $obj->qualid = $qualObj->get_id();
                    $obj->qual = $qualObj;
                    $return[] = $obj;
                }
            }
            else
            {
                $obj = new \stdClass();
                $obj->id = $course->id;
                $obj->fullname = $course->fullname;
                $obj->shortname = $course->shortname;
                $obj->qualid = null;
                $obj->qual = null;
                $return[] = $obj;
            }
            
        }              
        
        return $return;
        
    }
    
    public function getAllPossibleGrades(){
        
        global $DB;
        
        $processedGrades = array();
        $return = array();
        
        
        $grades = $DB->get_records_sql("SELECT id, grade FROM {block_bcgt_target_grades} GROUP BY grade");
        foreach($grades as $grade)
        {
            if (!in_array($grade->grade, $processedGrades))
            {
                $return[] = array("id" => $grade->id, "grade" => $grade->grade, "location" => "block_bcgt_target_grades");
                $processedGrades[] = $grade->grade;
            }        
        }
        
        
        $breakdowns = $DB->get_records_sql("SELECT id, targetgrade FROM {block_bcgt_target_breakdown} GROUP BY targetgrade");
        foreach($breakdowns as $breakdown)
        {
            if (!in_array($breakdown->targetgrade, $processedGrades))
            {
                $return[] = array("id" => $breakdown->id, "grade" => $breakdown->targetgrade, "location" => "block_bcgt_target_breakdown");
                $processedGrades[] = $breakdown->targetgrade;
            }
        }
        
        
        $grades = $DB->get_records_sql("SELECT id, grade FROM {block_bcgt_custom_grades} GROUP BY grade");
        foreach($grades as $grade)
        {
            if (!in_array($grade->grade, $processedGrades))
            {
                $return[] = array("id" => $grade->id, "grade" => $grade->grade, "location" => "block_bcgt_custom_grades");
                $processedGrades[] = $grade->grade;
            }        
        }
        
        usort($return, function($a, $b){
            return strcasecmp($a['grade'], $b['grade']);
        });

        return $return;
        
        
    }
    
    
    
    
    
    
    public function getDisplay($params = array()){
                
        global $DB;
        
        $output = "";
        
        $TPL = new \ELBP\Template();
        
        $possibleGrades = $this->getAllPossibleGrades();
                
        $courses = $this->getStudentsCourses();
                
        if ($courses)
        {
            foreach($courses as $course)
            {
                
                if (!is_null($course->qualid)){
                    $course->aspirationalGrade = $this->getAspirationalTargetGrade($course->id, $course->qualid);
                } else {
                    $course->aspirationalGrade = $this->getAspirationalTargetGrade($course->id);
                }
                
                    
                if (!is_null($course->qualid))
                {
                    
                    $qual = $DB->get_record("block_bcgt_qualification", array("id" => $course->qualid));
                    if ($qual)
                    {
                        
                        // Check breakdown first
                        $breakdown = $DB->get_records("block_bcgt_target_breakdown", array("bcgttargetqualid" => $qual->bcgttargetqualid));
                        if ($breakdown)
                        {
                            
                            $courseGrades = array();
                            foreach($breakdown as $b)
                            {
                                $courseGrades[] = array("id" => $b->id, "grade" => $b->targetgrade, "location" => "block_bcgt_target_breakdown");
                            }
                            
                            $course->possibleGrades = $courseGrades;
                            
                        }
                        
                        
                        else
                        {
                            
                            // If not, try target_grades
                            $targetgrades = $DB->get_records("block_bcgt_target_grades", array("bcgttargetqualid" => $qual->bcgttargetqualid));
                            if ($targetgrades)
                            {

                                $courseGrades = array();
                                foreach($targetgrades as $b)
                                {
                                    $courseGrades[] = array("id" => $b->id, "grade" => $b->grade, "location" => "block_bcgt_target_grades");
                                }

                                $course->possibleGrades = $courseGrades;

                            }
                            
                        }
                        
                    }
                    
                }
                
                if (isset($course->possibleGrades) && $course->possibleGrades)
                {
                                        
                    usort($course->possibleGrades, function($a, $b){
                        return strcasecmp($a['grade'], $b['grade']);
                    });
                                        
                }
                
                else
                {
                    $course->possibleGrades = $possibleGrades;
                }
                
            }
            
        }
        
                
        
        
        $TPL->set("courses", $courses);
        $TPL->set("possibleGrades", $possibleGrades);
        $TPL->set("obj", $this);
        $TPL->set("access", $this->access);      
        
        try {
            $output .= $TPL->load($this->CFG->dirroot . $this->path . 'tpl/elbp_target_grades/expanded.html');
        } catch (\ELBP\ELBPException $e){
            $output .= $e->getException();
        }

        return $output;
        
    }
    
    public function loadJavascript() {
        
//        $this->js = array(
//            '/blocks/bcgt/elbp_prior_learning.js'
//        );
        
        parent::loadJavascript();
    }
      
   
    public function getAspirationalTargetGrade($courseID, $qualID = false){
        
        if (!$this->student) return false;
        
        return bcgt_get_aspirational_target_grade($this->student->id, $courseID, $qualID);
        
    }
    
    public function saveConfig($settings){
                
        parent::saveConfig($settings);
        
    }
    
    public function ajax($action, $params, $ELBP){
        
        global $DB, $USER;
        
        switch($action)
        {
            
            case 'load_display_type':
                                
                // Correct params are set?
                if (!$params || !isset($params['studentID']) || !$this->loadStudent($params['studentID'])) return false;
                
                // We have the permission to do this?
                $access = $ELBP->getUserPermissions($params['studentID']);
                if (!$ELBP->anyPermissionsTrue($access)) return false;
                                
                $TPL = new \ELBP\Template();
                $TPL->set("obj", $this)
                    ->set("access", $access);
                                
                try {
                    $TPL->load( $this->CFG->dirroot . $this->path . 'tpl/elbp_target_grades/'.$params['type'].'.html' );
                    $TPL->display();
                } catch (\ELBP\ELBPException $e){
                    echo $e->getException();
                }
                exit;                
                
            break;
        }
        
    }
    
   
    
}