<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoreReports
 *
 * @author mchaney
 */
abstract class CoreReports {
    //put your code here
    
    protected $options = array();
    
    public function CoreReports()
    {
        
    }
    
    public static function display_view_reports()
    {
        set_time_limit(0);
        global $CFG;
        $retval = '';
        
        //needs to load all of the classes in the folder
        //then needs to instatiate them
        //then needs to output their names. 
        
        $directories = scandir($CFG->dirroot.'/blocks/bcgt/classes/reports/');
        if($directories)
        {
            $retval .= '<table>';
            $retval .= '<thead>';
            $count = 0;
            //there is always a '.' and a '..' directory
            foreach($directories AS $directory)
            {
                $count++;
                if($directory != '.' && $directory != '..')
                {
                    //require_once the class
                    require_once($CFG->dirroot.'/blocks/bcgt/classes/reports/'.$directory);
                    //get the class name
                    $className = strtok($directory, '.');
                    $classObject = new $className();
                    
                    $retval .= '<tr>';
                    $retval .= "<td><a href='my_dashboard.php?tab=reporting&action=bespoke&id={$count}' target='_blank'>{$classObject->get_name()}</a></td>"; 
                    $retval .= '<td>'.$classObject->get_description().'</td>';
                    $retval .= '</tr>';
                }
            }
            $retval .= '</thead>';
            $retval .= '</table>';
        }
        
        return $retval;
    }
    
    public static function display_view_report($number, $export = false)
    {
        set_time_limit(0);
        global $CFG, $PAGE;
        $retval = '';
        $directories = scandir($CFG->dirroot.'/blocks/bcgt/classes/reports/');
        if($directories)
        {
            $count = 0;
            //there is always a '.' and a '..' directory
            foreach($directories AS $directory)
            {
                $count++;
                if($directory != '.' && $directory != '..' && ($count == $number))
                {
                    //require_once the class
                    require_once($CFG->dirroot.'/blocks/bcgt/classes/reports/'.$directory);
                    //get the class name
                    $className = strtok($directory, '.');
                    $classObject = new $className();
                    if(!$export)
                    {
                        $jsModule = array(
                        'name'     => 'block_bcgt',
                        'fullpath' => '/blocks/bcgt/js/block_bcgt.js',
                        'requires' => array('base', 'io', 'node', 'json', 'event', 'button')
                        );
                        $PAGE->requires->js_init_call('M.block_bcgt.initcorereports', array($CFG->wwwroot.'/blocks/bcgt/forms/export_core_report.php',$CFG->wwwroot.'/blocks/bcgt/forms/my_dashboard.php?tab=reporting&action=bespoke'), true, $jsModule);
                        require_once($CFG->dirroot.'/blocks/bcgt/lib.php');
                        load_javascript();
                    }
                    
                    $retval .= '<form name="runreport" id="corereportrun" method="POST" action="my_dashboard.php?tab=reporting&action=bespoke">';
                    $retval .= '<div id="reportoptions">';
                    $retval .= '<h2 id="optionsHeader">'.get_string('reportoptions','block_bcgt').'</h2>';
                    $retval .= '<div id="optionsContent" class="content_collapse">';
                    $retval .= $classObject->display_options();
                    $retval .= '</div>';
                    $retval .= $classObject->get_output_options($number);
                    $retval .= '</div>';
                    $retval .= '<input type="hidden" name="id" value="'.$number.'"/>';
                    $retval .= '</form>';
                    
                    //if it was run or export run
                    if(isset($_POST['run']) && !$export)
                    {
                        $retval .= '<div id="reportResults">';
                        $retval .= '<h2>'.get_string('results', 'block_bcgt').'</h2>';
                        $retval .= '<div id="results">';
                        $retval .= $classObject->run_display_report();
                        $retval .= '</div></div>';
                    }
                    elseif($export)
                    {
                        $classObject->export_report();
                    }
                    break;
                }
            }
            
        }
        return $retval;
    }
    
    abstract function get_name();
    
    abstract function get_description();
    
    abstract function display_options();
    
    abstract function run_display_report();
    
    abstract function export_report();
    
    function perform_export()
    {
        global $CFG, $USER;
        $name = preg_replace("/[^a-z 0-9]/i", "", $this->get_name());
    
        ob_clean();
        header("Pragma: public");
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$name.'.xlsx"');     
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);

        require_once $CFG->dirroot . '/blocks/bcgt/lib/PHPExcel/Classes/PHPExcel.php';
    
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()
                     ->setCreator(fullname($USER))
                     ->setLastModifiedBy(fullname($USER))
                     ->setTitle($this->get_name())
                     ->setSubject($this->get_name())
                     ->setDescription($this->get_description());

        // Remove default sheet
        $objPHPExcel->removeSheetByIndex(0);
        
//        // Style for blank cells - criteria not on that unit
//        $blankCellStyle = array(
//            'fill' => array(
//                'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                'color' => array('rgb' => 'E0E0E0')
//            )
//        );

        $sheetIndex = 0;
        
        // Set current sheet
        $objPHPExcel->createSheet($sheetIndex);
        $objPHPExcel->setActiveSheetIndex($sheetIndex);
        $objPHPExcel->getActiveSheet()->setTitle("Report");
        
        $rowNum = 1;

        // Headers
        if(isset($this->header))
        {
            $col = 0;
            foreach($this->header AS $head)
            {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowNum, $head);
                $col++;
            }
        }
        
        //data
        if(isset($this->data))
        {
            $row = 1;
            foreach($this->data AS $data)
            {
                $row++;
                $col = 0;
                foreach($data AS $cell)
                {  
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $cell);
                    $col++;
                } 
            }
        }
        
        // Freeze rows and cols (everything to the left of D and above 2)
        $objPHPExcel->getActiveSheet()->freezePane($this->freezepane);
        
        // End
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        ob_clean();
        $objWriter->save('php://output');
        
        exit;
    }
    
    function get_core_options($data = null)
    {
        $families = get_qualification_type_families_used();
        $levels = bcgt_get_all_levels();
        $subtypes = bcgt_get_all_subtypes();
        
        $out = '<div id="coreOptions">';
        $out .= '<table><thead><tr>';
        $out .= '<th>'.get_string('categories').'</th>';
        $out .= '<th>'.get_string('family', 'block_bcgt').'</th>';
        $out .= '<th>'.get_string('levels', 'block_bcgt').'</th>';
        $out .= '<th>'.get_string('subtypes', 'block_bcgt').'</th>';
        $out .= '</tr></thead>';
        $out .= '<tbody>';
        $out .= '<tr>';
        $out .= '<td>';
        $out .= "<div>";
            
        $categories = get_config('bcgt', 'reportingcats');
        if ($categories)
        {
            $out .= "<select name='start_cat' style='max-width:90%;'>";
            $out .= "<option value=''>All</option>";
            //$output .= "<option value='all' ".( (isset($data) && $data['start_point'] == 'all') ? 'selected' : '' ).">All</option>";
            $catArray = explode(",", $categories);
            foreach($catArray as $catID)
            {    
                $catName = ReportingSystem::get_course_category_name_with_parent($catID);
                $out .= "<option value='{$catID}' ".( (isset($_POST['start_cat']) && $_POST['start_cat'] == $catID) ? 'selected' : '' ).">{$catName}</option>";
            }
            $out .= "</select>";
        }
        else
        {
            $out .= "<p>No Categories Defined in BCGT Settings</p>";
        }
        if(isset($_POST['start_cat']))
        {
            $this->options['categoryid'] = $_POST['start_cat'];
        }
        
        $out .= "</div>";
        $out .= '</td>';
        $out .= '<td>';
        $this->options['family'] = array();
        foreach($families AS $family)
        {
            $checked = '';
            if(isset($_POST['family_'.$family->id]))
            {
                $this->options['family'][$family->id] = $family->id;
                $checked = 'checked';
            }
            $out .= '<span>'.$family->family.' : <input type="checkbox" '.$checked.' name="family_'.$family->id.'"/></span>';
        }
        $out .= '</td>';
        $out .= '<td>';
        $this->options['level'] = array();
        foreach($levels AS $level)
        {
            $checked = '';
            if(isset($_POST['level_'.$level->id]))
            {
                $this->options['level'][$level->id] = $level->id;
                $checked = 'checked';
            }
            $out .= '<span>'.$level->trackinglevel.' : <input type="checkbox" '.$checked.' name="level_'.$level->id.'"/></span>';
        }
        $out .= '</td>';
        $out .= '<td>';
        $this->options['subtype'] = array();
        foreach($subtypes AS $subtype)
        {
            $checked = '';
            if(isset($_POST['subtype_'.$subtype->id]))
            {
                $this->options['subtype'][$subtype->id] = $subtype->id;
                $checked = 'checked';
            }
            $out .= '<span>'.$subtype->subtype.' : <input type="checkbox" '.$checked.' name="subtype_'.$subtype->id.'"/></span>';
        }
        $out .= '</td>';
        $out .= '</tr>';
        $out .= '</tbody></table>';
        $out .= '</div>';
        return $out;
    }
    
    function get_grade_options($data = null)
    {
        //wants to return, for all targetqualids, the possible grades
        
    }
    
    function get_target_grade_options()
    {
        global $CFG;
        require_once $CFG->dirroot . '/blocks/bcgt/classes/core/TargetGrade.class.php';
        //wants to return, for all targetqualids, the possible target grades
        $targetQuals = bcgt_get_all_target_qual();
        $out = '<table>';
        
        $targetGradeObj = new TargetGrade();
        foreach($targetQuals AS $targetQual)
        {
            $out .= '<tr>';
            $out .= '<td>'.$targetQual->family.'</td>';
            $out .= '<td>'.$targetQual->trackinglevel.'</td>';
            $out .= '<td>'.$targetQual->subtype.'</td>';
            $targetGrades = $targetGradeObj->get_all_target_grades($targetQual->id);
            foreach($targetGrades AS $targetGrade)
            {
                $out .= '<td>';
                $out .= $targetGrade->grade.' : <input type="checkbox" name=""/>';
                $out .= '</td>';
            }
            $out .= '</tr>';
        }
        $out .= '</table>';
        return $out;
    }
    
    function get_display_selectors()
    {
        $out = '<div>';
        //the three options
        $out .= get_string('displayoptions', 'block_bcgt');
        if(isset($_POST['displayoptions']))
        {
            $this->options['displayoption'] = $_POST['displayoptions'];
        }
        $out .= '<select name="displayoptions">';
        $out .= '<option value="anonymous" '.((isset($_POST['displayoptions']) && $_POST['displayoptions'] == 'anonymous') ? "selected": "").'>No Subject Information: Just show students and their grades</option>';
        $out .= '<option value="bysubject" '.((isset($_POST['displayoptions']) && $_POST['displayoptions'] == 'bysubject') ? "selected": "").'>Every subject: Show a column for every subject</option>';
        $out .= '<option value="byoption" '.((isset($_POST['displayoptions']) && $_POST['displayoptions'] == 'byoption') ? "selected": "").'>Options: A column per option with the subject in.</option>';
        $out .= '</select>';
        $out .= '</div>';
        return $out;
    }
    
    function get_output_options($id)
    {
        global $CFG;
        $out = '<div id="outputcommands">';
        $out .= '<input type="submit" id="runsub" name="run" value="'.get_string('run', 'block_bcgt').'"/>';
        $out .= '<input type="submit" id="exportsub" name="run" value="'.get_string('export', 'block_bcgt').'"/>';
        $out .= '</div>';
        
        return $out;
    }
    
    function display_report()
    {
        $out = '<div>';
        $out .= '<table id="resultsTable">';
        $out .= '<thead>';
        if(isset($this->header))
        {
            $out .= '<tr>';
            foreach($this->header AS $head)
            {
                $out .= '<th>'.$head.'</th>';
            }    
            $out .= '</tr>';
        }
        $out .= '</thead>';
        $out .= '<tbody>';
        if(isset($this->data))
        {
            foreach($this->data AS $row)
            {
                $out .= '<tr>';
                foreach($row AS $cell)
                {
                    $out .= '<td>'.$cell.'</td>';
                }  
                $out .= '</tr>';
            }
        }
        $out .= '</tbody>';
        $out .= '</table>';
        $out .= '</div>';
        
        return $out;
    }
    
}

?>
