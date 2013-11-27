<?php
/*
 * Moodle Gradetracker V1.0 – This code is copyright of Bedford College and is 
 * supplied for evaluation purposes only. The code may not be used for any 
 * purpose without permission from The Learning Technologies Team, 
 * Bedford College:  moodlegrades@bedford.ac.uk
 * 
 * Author mchaney@bedford.ac.uk
 */
global $CFG;
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/Qualification.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/plugins/bcgtbtec/lib.php');
class BTECQualification extends Qualification {
   
    const ID = 2;
	const NAME = 'BTEC';
	const FAMILYID = 2;
	protected $credits;
    protected $usedCriteriaNames;
	
	function BTECQualification($qualID, $params, $loadParams)
	{
		parent::Qualification($qualID, $params, $loadParams);
        //if we know the id then lets go get the credits from the database
		if($qualID != -1)
		{
			//gets the credits from the database
			$creditsObj = BTECQualification::retrieve_credits($qualID);
			if($creditsObj && $creditsObj->credits)
			{
				$this->credits = $creditsObj->credits;
			}
            else 
            {
                $this->credits = $this->get_default_credits();
            }
		}
		else
		{
			//then we have been passed the credits.
            if($params && isset($params->credits))
            {
                $this->credits = $params->credits; 
            }
            else 
            {
                $this->credits = $this->get_default_credits();
            }      
			
		}
	}
	
	function get_type()
	{
		return BTECQualification::NAME;
	}
    
    function get_family()
	{
		return BTECQualification::NAME;
	}
	
	function get_class_ID()
	{
		return BTECQualification::ID;
	}
	
	function get_family_ID()
	{
		return BTECQualification::FAMILYID;
	}
    			
	function get_credits()
	{
        if($this->credits)
        {
            return $this->credits;
        }
        $credits = $this->get_default_credits();
        $this->credits = $credits;
        return $credits;
	}
    
    function add_unit(Unit $unit)
	{
		$added = parent::add_unit_qual($unit);
		return $added;
	}
	
	function remove_unit(Unit $unit)
	{
		$removed = parent::remove_units_qual($unit);
		return $removed;	
	}
    
    /**
	 * Get the value for the MET
	 */
	public static function get_criteria_met_val()
	{
		//This gets the one criteria value that will go towards having
		// the criteria met and thus the unit award
		//THIS ASSUMES ONE!!!!!
		global $DB;
		$sql = "SELECT * FROM {block_bcgt_value} WHERE bcgttypeid = ? AND shortvalue = ?";
        $params = array(BTECQualification::ID, 'A');
		$record = $DB->get_record_sql($sql, $params);
		if($record)
		{
			return $record->id;
		}
		return -1;
	}
    
    public static function has_formal_assessments()
    {
        return true;
    }
    
    public function has_units()
    {
        return true;
    }
    
    public function get_criteria_met_value()
    {
        return BTECQualification::get_criteria_met_val();
    }
    
    public function get_criteria_not_met_value()
    {
        return BTECQualification::get_criteria_not_met_val();
    }
    
    /**
	 * Get the value for not met
	 */
	public static function get_criteria_not_met_val()
	{
		//This gets the one criteria value that will go towards having
		// the criteria not met
		//THIS ASSUMES ONE!!!!!
		global $DB;
		$sql = "SELECT * FROM {block_bcgt_value} WHERE bcgttypeid = ? AND shortvalue = ?";
        $params = array(BTECQualification::ID, 'X');
		$record = $DB->get_record_sql($sql, $params);
		if($record)
		{
			return $record->id;
		}
		return -1;
	}
	
	public function get_work_submitted_value()
	{
		global $DB;
		$sql = "SELECT * FROM {block_bcgt_value} WHERE bcgttypeid = ? AND shortvalue = ?";
        $params = array(BTECQualification::ID, 'WS');
		$record = $DB->get_record_sql($sql, $params);
		if($record)
		{
			return $record->id;
		}
		return -1;
	}
	
	public function get_work_not_submitted_value()
	{
		global $DB;
		$sql = "SELECT * FROM {block_bcgt_value} WHERE bcgttypeid = ? AND shortvalue = ?";
        $params = array(BTECQualification::ID, 'WNS');
		$record = $DB->get_record_sql($sql, $params);
		if($record)
		{
			return $record->id;
		}
		return -1;
	}
	
	public function get_work_late_value()
	{
		global $DB;
		$sql = "SELECT * FROM {block_bcgt_value} WHERE bcgttypeid = ? AND shortvalue = ?";
        $params = array(BTECQualification::ID, 'L');
		$record = $DB->get_record_sql($sql, $params);
		if($record)
		{
			return $record->id;
		}
		return -1;
	}
    
    protected function get_simple_qual_report_tabs()
    {
        $tabs = parent::get_simple_qual_report_tabs();
        return $tabs + array("u"=>"units", "co"=>"classoverview");
    }
    
    protected function get_possible_unit_awards()
    {
        return array('Pass', 'Merit', 'Distinction');
    }
    
    public static function get_edit_form_menu($disabled = '', $qualID = -1, $typeID = -1)
	{
        $jsModule = array(
            'name'     => 'mod_bcgtbtec',
            'fullpath' => '/blocks/bcgt/plugins/bcgtbtec/js/bcgtbtec.js',
            'requires' => array('base', 'io', 'node', 'json', 'event', 'button')
        );
        global $PAGE;
        $PAGE->requires->js_init_call('M.mod_bcgtbtec.bteciniteditqual', null, true, $jsModule);
		$levelID = optional_param('level', -1, PARAM_INT);
		$subtypeID = optional_param('subtype', -1, PARAM_INT);
        
		$levels = get_level_from_type(-1, BTECQualification::FAMILYID);
		$subTypes = get_subtype_from_type(-1, $levelID, BTECQualification::FAMILYID);
		if($qualID != -1)
		{
			$qualLevel = Qualification::get_qual_level($qualID);
			$qualSubType = Qualification::get_qual_subtype($qualID);
		}
		$retval = "";
		$retval .= '<div class="inputContainer"><div class="inputLeft">';
        $retval .= '<label for="level"><span class="required">*</span>';
        $retval .= get_string('level', 'block_bcgt');
        $retval .= ' : </label></div>';
		$retval .= '<div class="inputRight"><select '.$disabled.' name="level" id="qualLevel">';
			if($levels)
			{
				if(count($levels) > 1)
				{
					$retval .= '<option value="-1">Please Select one</option>';
				}				
				foreach($levels as $level) {
                        $selected = '';
                        if($qualID != -1 && ($level->get_id() == $qualLevel->id))
                        {
                            $selected = 'selected';
                        }
                        elseif($levelID != -1 && ($levelID == $level->get_id()))
                        {
                            $selected = 'selected';
                        }
                        else
                        {
                            if(count($levels) == 1)
                            {
                                $selected = 'selected';
                            }
                        }
                        $retval .= '<option '.$selected.' value="'.$level->get_id().'">';
                        $retval .= $level->get_level().'</option>';
				}	
			}
			else
			{
				$retval .= '<option value="-1">'.get_string('nolevels', 'block_bcgt').'</option>';
			}
		$retval .= '</select></div></div>';
		$retval .= '<div class="inputContainer"><div class="inputLeft">';
        $retval .= '<label for="subtype"><span class="required">*</span>';
        $retval .= get_string('subtype', 'block_bcgt');
        $retval .= '</label></div>';
		$retval .= '<div class="inputRight"><select '.$disabled.' name="subtype" id="qualSubtype">';
			if($subTypes)
			{
				if(count($subTypes) > 1)
				{
					$retval .= '<option value="-1">Please Select one</option>';
				}
				foreach($subTypes as $subType) {
					$selected = '';
					if($qualID != -1 && ($subType->get_id() == $qualSubType->id))
					{
						$selected = 'selected';
					}
					elseif($subtypeID != -1 && $subtypeID == $subType->get_id())
					{
						$selected = 'selected';
					}
					else
					{
						if(count($subTypes) == 1)
						{	
							$selected = 'selected';
						}
					}
					$retval .= '<option '.$selected.' value="'.$subType->get_id().'">';
                    $retval .= $subType->get_subtype().'</option>';
				}	
			}
			else
			{
				$retval .= '<option value="-1">'.get_string('nosubtypes', 'block_bcgt').'</option>';
			}
		$retval .= '</select></div></div>';
		return $retval;
	}
    
    /**
	 * Returns the form fields that go on the edit qualification form for this qual type
	 */
	public function get_edit_form_fields()
	{
		//What adding or editing BTEC qual then we need to be able to
		//have crfedits. Not all quals have credits so this 
		//is qual specific.
		$retval = '<div class="inputContainer"><div class="inputLeft">';
        $retval .= '<label for="credits">'.get_string('bteccredits', 'block_bcgt')
                .': </label></div>';
		$retval .= '<div class="inputRight">'.
                '<input type="text" name="credits" value="'.$this->credits.'"/></div></div>';
        return $retval;
	} 
    
    /**
	 * Sets the credits from the post data as set in edit_qualifications_form
	 */
	public function get_submitted_edit_form_data()
	{
		//Because when editing the qual we need to get the qual specific fields
		//passed by post.
		$this->credits = $_POST['credits'];	
	}
    
    public function qual_specific_student_load_information($studentID, $qualID)
    {
        return true;
    }
    
    /**
	 * Using the object values inserts the qualification into the database
	 */
	public function insert_qualification()
	{
        global $DB;
		//as each qual is different its easier to do this hear. 
		$dataobj = new stdClass();
		$dataobj->name = $this->name;
        $dataobj->additionalname = $this->additionalName;
		$dataobj->code = $this->code;
		$dataobj->credits = $this->credits;
        $dataobj->noyears = $this->noYears;
		$targetQualID = parent::get_target_qual(BTECQualification::ID);
		$dataobj->bcgttargetqualid = $targetQualID;
		$id = $DB->insert_record("block_bcgt_qualification", $dataobj);
		$this->id = $id;
        logAction(LOG_MODULE_GRADETRACKER, LOG_ELEMENT_GRADETRACKER_QUALIFICATION, LOG_VALUE_GRADETRACKER_INSERTED_QUAL, null, $this->id, null, null, null);
	}
	
	/**
	 * Call a parent delete_qualification_main
	 */
	public function delete_qualification()
	{
		//do we need tto do anythng thats BTEC Qual specific?
		return $this->delete_qual_main();
	}
	
	/**
	 * Using the object updates the database
	 */
	public function update_qualification()
	{	
        global $DB;
		$dataobj = new stdClass();
		$dataobj->id = $this->id;
		$dataobj->name = $this->name;
        $dataobj->additionalname = $this->additionalName;
		$dataobj->code = $this->code;
        $dataobj->noyears = $this->noYears;
		$dataobj->credits = $this->credits;
		$DB->update_record("block_bcgt_qualification", $dataobj);
        logAction(LOG_MODULE_GRADETRACKER, LOG_ELEMENT_GRADETRACKER_QUALIFICATION, LOG_VALUE_GRADETRACKER_UPDATED_QUAL, null, $this->id, null, null, null);
	}
    
    /**
     * Loads the users for this role onto this qualification
     * @global type $DB
     * @param type $role
     * @param type $loadStudentQuals
     * @param type $loadLevel
     * @param type $loadAward
     * @param type $courseID
     */
    //$loadLevel = Qualification::LOADLEVELUNITS, $loadAward = false
    public function load_users($role = 'student', $loadStudentQuals = false, 
            $loadParams = null, $courseID = -1)
    {
        global $DB;
        $roleDB = $DB->get_record_sql('SELECT id FROM {role} WHERE shortname = ?', array($role));
        $users = $this->get_users($roleDB->id, '', 'lastname ASC', $courseID);
        $usersQuals = array();
        if($users)
        {
            $property = 'users'.$role;
            $propertyQual = 'usersQuals'.$role;
            $this->$property = $users;
            if($loadStudentQuals)
            {
                foreach($users AS $user)
                {
                    $studentQual = Qualification::get_qualification_class_id($this->id, $loadParams);
                    if($studentQual)
                    {
                        $studentQual->load_student_information($user->id, 
                            $loadParams);
                        $usersQuals[$user->id] = $studentQual;
                    }
                }
                $this->$propertyQual = $usersQuals;
            }
        }
    }
    
    /**
     * This processes the students units selection
     * Loops over all of the students,and their units
     * checks if its been checked now and before 
     * updates accordingly
     * saves
     */
    public function process_edit_students_units_page($courseID = -1)
    {
        //loop over all of the students
            //load the students qual
            //add/remove the units
            //then save
        if(isset($_POST['saveAll']) || isset($_POST['save'.$this->id]))
        {
            if(!isset($this->usersstudent))
            {
                //load the users and load their qual objects
                $loadParams = new stdClass();
                $loadParams->loadLevel = Qualification::LOADLEVELUNITS;
                $this->load_users('student', true, 
                        $loadParams, $courseID);
            }
            foreach($this->usersstudent AS $student)
            {
                $studentQual = $this->usersQualsstudent[$student->id];
                if($studentQual)
                {
                    foreach($studentQual->get_units() AS $unit)
                    {
                        //get the check boxes
                        //name is in the format of $name='s'.$student->id.'U'.$unit->get_id().'';
                        $fieldToCheck = 'q'.$this->id.'S'.$student->id.'U'.$unit->get_id().'';
                        $this->process_edit_students_units($unit, $fieldToCheck, $student->id);
                    }
                }
            }
        }
        //then we get rid of the session variable.
        $_SESSION['new_students'] = urlencode(serialize(array()));
        $_SESSION['new_quals'] = urlencode(serialize(array()));
    }
    
    public static function edit_activity_view_page($courseID, $unitID, $activityID)
    {
        //form that will allow editing of an activity. 
        
    }
    
    public static function add_activity_view_page($courseID, $unitID, $activityID)
    {
        $jsModule = array(
            'name'     => 'mod_bcgtbtec',
            'fullpath' => '/blocks/bcgt/plugins/bcgtbtec/js/bcgtbtec.js',
            'requires' => array('base', 'io', 'node', 'json', 'event', 'button')
        );
        global $PAGE, $CFG;
        $PAGE->requires->js_init_call('M.mod_bcgtbtec.btecaddactivity', null, true, $jsModule);
		
        
        $newUnitID = optional_param('nUID', -1, PARAM_INT);
        $newUnits = optional_param('newUnits', '', PARAM_TEXT);
        if($newUnitID != -1)
        {
            $newUnits .= ','.$newUnitID;
            $unitID = $newUnitID;
        }
        $newActivityID = optional_param('nAID', -1, PARAM_INT);
        $page = optional_param('page', 'addact', PARAM_TEXT);
        $newActvities = optional_param('newActivities', '', PARAM_TEXT);
        if($newActivityID != -1)
        {
            $newActvities .= ','.$newActivityID;
            $activityID = $newActivityID;
        }
        
        //two boxes. 
            //one that is an activity
            //then units to add
            //second is unit
            //then activities to add
        $activities = get_coursemodules_in_course('assign', $courseID, 'm.duedate');
        $units = bcgt_get_course_units($courseID, BTECQualification::FAMILYID);
        $activityUnits = get_activity_units($activityID);
        
        //*************** ACTIVTIES*****************//
        
        
        
        //this is actually the coursemoduleid
        //lets load the unit up
        $unit = null;
        $qualsUnitOn = null;
        if($unitID != -1)
        {
            $loadParams = new stdClass();
            $loadParams->loadLevel = Qualification::LOADLEVELCRITERIA;
            $unit = Unit::get_unit_class_id($unitID, $loadParams);
            $qualsUnitOn = $unit->get_quals_on('', -1, -1, $courseID );
        }
        
        // **********************************************************************************************
        // Show the correct form according to what is passed in aID (aka activityID) which defaults to -1
        // **********************************************************************************************
        
        if($page == 'addunit')
        {
           
            
            if(isset($_POST['saveUnitsAcc']))
        {
            //then we are saving the units on the activitues
            //we need to check the originals and get the new
            //new:            
            if($newUnits != '')
            {
                $insertUnits = explode(',', $newUnits);
                //then we are saving
                foreach($insertUnits AS $uID)
                {
                    if($uID == '')
                    {
                        continue;
                    }
                    //is it marked as delete
                    if(isset($_POST['remU_'.$uID]))
                    {
                        continue;
                    }
                    $loadParams = new stdClass();
                    $loadParams->loadLevel = Qualification::LOADLEVELCRITERIA;
                    $unit = Unit::get_unit_class_id($uID, $loadParams);
                    $criterias = $unit->get_criteria();
                    $criteriasUsed = array();
                    //I really dont want to loop through all criteria for every qual possible
                    foreach($criterias AS $criteria)
                    {
                       if(isset($_POST['u_'.$uID.'_c_'.$criteria->get_id().'']))
                       {
                           //then we want to insert it
                           $criteriasUsed[] = $criteria->get_id();
                       }
                    }
                    $qualsUnitOn = $unit->get_quals_on('', -1, -1, $courseID );
                    //is it on a qual?
                    foreach($qualsUnitOn AS $qual)
                    {
                        if(isset($_POST['q_'.$qual->id.'_u_'.$uID]))
                        {
                            //is on this qual so lets insert it. 
                            //we need to get the criteriaIDs. We know the unitID
                            $stdObj = new stdClass();
                            $stdObj->coursemoduleid = $activityID;
                            $stdObj->bcgtunitid = $uID;
                            $stdObj->bcgtqualificationid = $qual->id;
                            foreach($criteriasUsed AS $criteriaID)
                            {
                                $stdObj->bcgtcriteriaid = $criteriaID;
                                insert_activity_onto_unit($stdObj);
                            }
                        }
                    }
                }
                $newUnits = '';
            }
            //originals:
            if($activityUnits)
            {
                foreach($activityUnits AS $activityUnit)
                {
                    //is it marked as removed?
                    if(isset($_POST['remU_'.$activityUnit->id]))
                    {
                        //then lets delete it
                        delete_activity_from_unit($activityID, $activityUnit->id);
                        continue;
                    }
                    $loadParams = new stdClass();
                    $loadParams->loadLevel = Qualification::LOADLEVELCRITERIA;
                    $unit = Unit::get_unit_class_id($activityUnit->id, $loadParams);
                    $qualsUnitOn = $unit->get_quals_on('', -1, -1, $courseID);
                    //now check quals. 
                    foreach($qualsUnitOn AS $qual)
                    {
                        //is it checked?
                        if(!isset($_POST['q_'.$qual->id.'_u_'.$activityUnit->id]))
                        {
                            unset($qualsUnitOn[$qual->id]);
                            delete_activity_by_qual_from_unit($activityID, $qual->id, $activityUnit->id);
                        }
                    }
                    $criterias = $unit->get_criteria();
                    foreach($criterias AS $criteria)
                    {
                        //was it checked before?
                        $criteriaOnActivity = get_activity_criteria($activityID, $qualsUnitOn);
                        if(isset($_POST['u_'.$activityUnit->id.'_c_'.$criteria->get_id()])
                                && !array_key_exists($criteria->get_id(), $criteriaOnActivity))
                        {
                            //so its been checked and it wasnt in the array from the database
                            //therefore INSERT!
                            foreach($qualsUnitOn AS $qual)
                            {
                                $stdObj = new stdClass();
                                $stdObj->coursemoduleid = $activityID;
                                $stdObj->bcgtunitid = $activityUnit->id;
                                $stdObj->bcgtqualificationid = $qual->id;
                                $stdObj->bcgtcriteriaid = $criteria->get_id();
                                insert_activity_onto_unit($stdObj);
                            }
                        }
                        elseif(!isset($_POST['u_'.$activityUnit->id.'_c_'.$criteria->get_id()])
                                && array_key_exists($criteria->get_id(), $criteriaOnActivity))
                        {
                            //its in the array from before and its no longer checked!
                            //therefore delete
                            delete_activity_by_criteria_from_unit($activityID, $criteria->get_id(), $activityUnit->id);
                        }
                        //is it checked? 
                    }
                }
            }
            redirect($CFG->wwwroot.'/blocks/bcgt/forms/activities.php?cID='.$courseID.'&tab=act');
        }
            
        // **********************************************************************************************
        // ADD UNITS AND CRITERIA TO AN ASSIGNMENT ******************************************************
        // **********************************************************************************************
        
        //$retval = '<div class="bcgt_float_container bcgt_two_c_container">';
        //$retval .= '<div class="bcgt_col bcgt_admin_left">';
        $retval = '<div class="bcgt_col">';
        $retval .= '<h2>Add Units and Criteria to an Assignment</h2>';
        $retval .= '<div class="bcgt_col bgctSelectActivity">';
        $retval .= '<label for="aID">Select Assignment : </label>';
        $retval .= '<select name="aID" id="aID">';
        $retval .= '<option value="-1"></option>';
        //get the activities on the course
        if($activities)
        {
            foreach($activities AS $activity)
            {
                $selected = '';
                if($activityID == $activity->id)
                {
                    $selected = 'selected';
                }
                $retval .= '<option '.$selected.' value="'.$activity->id.'">'.$activity->name.'</option>';
            }
        }
        $retval .= '</select><br />';
        $retval .= '</div>';
        
        
        
        $retval .= '<h3>Current Units/Criteria</h3>';
        //then load the units currently on the activity
        if($activityUnits)
        {
            foreach($activityUnits AS $activityUnit)
            { 
                $retval .= '<div class="bcgt_col bgctActivityUnit">';
                $retval .= get_btec_activity_unit_table($activityID, $activityUnit->id, $courseID);
                $retval .= '</div>';
            }
        }
        else
        {
            $retval .= 'There are no Current Units/Criteria on this Activity.';
        }
        //then allow them to be added.
        $retval .= '<h3>Add new Units/Criteria</h3>';
        //do we have any to remove?
        $finUnits = '';
        if($newUnits != '')
        {
            $newUnits = explode(',', $newUnits);
            $count = 0;
            foreach($newUnits AS $newUnit)
            {
                if($newUnit == '')
                {
                    continue;
                }
                if(isset($_POST['remU_'.$newUnit.'']))
                {
                    //if remove has been selected then delete it from the array
                    unset($newUnits[$count]);
                }
                else
                {
                    $finUnits = ','.$newUnit;
                }
                $count++;
            }
        }
        else
        {
            $newUnits = array();
        }
        
        $retval .= '<div class="bcgt_col bgctSelectActivity">';
        $retval .= '<label>Select to Add a new Unit : </label><br />';
        $retval .= '<select name="nUID" id="nUID">';
        $retval .= '<option value="-1"></option>';
        //get the units on the course there are the new ones that can be selected
        if($units)
        {
            foreach($units AS $newUnit)
            {
                //do we already have it in the new ones being added?
                if(!in_array ($newUnit->id, $newUnits) && !array_key_exists($newUnit->id, $activityUnits))
                {
                    $retval .= '<option value="'.$newUnit->id.'">'.$newUnit->name.'</option>';
                }
            }
        }
        $retval .= '</select>';
        $retval .= '<input type="hidden" name="newUnits" value="'.$finUnits.'"/>';
        $retval .= '<input type="hidden" name="page" value="'.$page.'"/>';
        $retval .= '<input type="submit" name="addUnit" value="Add Unit"/>';
        $retval .= '</div>';
        
        foreach($newUnits AS $newUnitID)
        {
            if($newUnitID == '')
            {
                continue;
            }
            $retval .= '<div class="bcgt_col bgctActivityUnit">';
            $retval .= get_btec_activity_unit_table($activityID, $newUnitID, $courseID, true);
            $retval .= '</div>';
        }
        $retval .= '<br /><br /><input type="submit" name="saveUnitsAcc" value="Save Assignment"/>';
        
        $retval .= '</div>';
       
        
        
        
        


//******************** UNITS ****************************//
        
        // **********************************************************************************************    
        } // Show the form: Activity -> unit & criteria instead
        Else {
        // **********************************************************************************************     
            $unitActivities = BTECQualification::get_unit_activities($courseID, $unitID);
        if(isset($_POST['saveAccUnits']) && $unit)
        {
            //then we are saving the activities on the Unit
            //we need to check the originals and get the new
            //new:
            if($newActvities != '')
            {
                $insertActivities = explode(',', $newActvities);
                //then we are saving
                foreach($insertActivities AS $cmID)
                {
                    //is it marked as delete
                    if(isset($_POST['rem_'.$cmID]))
                    {
                        continue;
                    }
                    $criterias = $unit->get_criteria();
                    $criteriasUsed = array();
                    //I really dont want to loop through all criteria for every qual possible
                    foreach($criterias AS $criteria)
                    {
                       if(isset($_POST['a_'.$cmID.'_c_'.$criteria->get_id().'']))
                       {
                           //then we want to insert it
                           $criteriasUsed[] = $criteria->get_id();
                       }
                    }
                    //is it on a qual?
                    foreach($qualsUnitOn AS $qual)
                    {
                        if(isset($_POST['q_'.$qual->id.'_a_'.$cmID]))
                        {
                            //is on this qual so lets insert it. 
                            //we need to get the criteriaIDs. We know the unitID
                            $stdObj = new stdClass();
                            $stdObj->coursemoduleid = $cmID;
                            $stdObj->bcgtunitid = $unitID;
                            $stdObj->bcgtqualificationid = $qual->id;
                            foreach($criteriasUsed AS $criteriaID)
                            {
                                $stdObj->bcgtcriteriaid = $criteriaID;
                                insert_activity_onto_unit($stdObj);
                            }
                        }
                    }
                }
                $newActvities = '';
            }
            //originals:
            if($unitActivities)
            {
                foreach($unitActivities AS $unitActivity)
                {
                    //is it marked as removed?
                    if(isset($_POST['rem_'.$unitActivity->id]))
                    {
                        //then lets delete it
                        delete_activity_from_unit($unitActivity->id, $unitID);
                        continue;
                    }
                    //now check quals. 
                    foreach($qualsUnitOn AS $qual)
                    {
                        //is it checked?
                        if(!isset($_POST['q_'.$qual->id.'_a_'.$unitActivity->id]))
                        {
                            unset($qualsUnitOn[$qual->id]);
                            delete_activity_by_qual_from_unit($unitActivity->id, $qual->id, $unitID);
                        }
                    }
                    $criterias = $unit->get_criteria();
                    foreach($criterias AS $criteria)
                    {
                        //was it checked before?
                        $criteriaOnActivity = get_activity_criteria($unitActivity->id, $qualsUnitOn);
                        if(isset($_POST['a_'.$unitActivity->id.'_c_'.$criteria->get_id()])
                                && !array_key_exists($criteria->get_id(), $criteriaOnActivity))
                        {
                            //so its been checked and it wasnt in the array from the database
                            //therefore INSERT!
                            foreach($qualsUnitOn AS $qual)
                            {
                                $stdObj = new stdClass();
                                $stdObj->coursemoduleid = $unitActivity->id;
                                $stdObj->bcgtunitid = $unitID;
                                $stdObj->bcgtqualificationid = $qual->id;
                                $stdObj->bcgtcriteriaid = $criteria->get_id();
                                insert_activity_onto_unit($stdObj);
                            }
                        }
                        elseif(!isset($_POST['a_'.$unitActivity->id.'_c_'.$criteria->get_id()])
                                && array_key_exists($criteria->get_id(), $criteriaOnActivity))
                        {
                            //its in the array from before and its no longer checked!
                            //therefore delete
                            delete_activity_by_criteria_from_unit($unitActivity->id, $criteria->get_id(), $unitID);
                        }
                        //is it checked? 
                    }
                }
            }
            redirect($CFG->wwwroot.'/blocks/bcgt/forms/activities.php?cID='.$courseID.'&tab=unit');
        }
        
        // **********************************************************************************************
        // ADD ASSIGNMENT TO A UNIT AND CRITERIA ******************************************************
        // **********************************************************************************************
        //$retval .= '<div class="bcgt_col bcgt_admin_right">';
        $retval = '<div class="bcgt_col">';
        $retval .= '<h2>Add an Assignment to a Unit and Criteria</h2>';
        $retval .= '<div class="bcgt_col bgctSelectActivity">';
        $retval .= '<label for="aID">Select Unit : </label>';
        $retval .= '<select name="uID" id="uID">';
        $retval .= '<option value="-1"></option>';
        //get the units on the course
        if($units)
        {
            foreach($units AS $unitOnCourse)
            {
                $selected = '';
                if($unitID == $unitOnCourse->id)
                {
                    $selected = 'selected';
                }
                $retval .= '<option '.$selected.' value="'.$unitOnCourse->id.'">'.$unitOnCourse->name.'</option>';
            }
        }
        $retval .= '</select>';
        $retval .= '</div>';
        $retval .= '<h3>Current Activities</h3>';
        //load the assignments already on this unit. 
        $unitActivities = BTECQualification::get_unit_activities($courseID, $unitID);
        if($unitActivities)
        {
            foreach($unitActivities AS $unitActivity)
            {
                $retval .= '<div class="bcgt_col bgctActivityUnit">';
                //get the activity and the criteria
                $retval .= get_btec_unit_activity_table($unitActivity->id, $unit, $courseID);
                $retval .= '</div>';
            }
        }
        else
        {
            $retval .= 'There are currently no Activities connected to this Unit';
        }
        $retval .= '<h3>Add a new Activity</h3>';
        //do we have any to remove?
        $finActivities = '';
        if($newActvities != '')
        {
            $newActvities = explode(',', $newActvities);
            $count = 0;
            foreach($newActvities AS $newActivity)
            {
                if($newActivity == '')
                {
                    continue;
                }
                if(isset($_POST['rem_'.$newActivity.'']))
                {
                    //if remove has been selected then delete it from the array
                    unset($newActvities[$count]);
                }
                else
                {
                    $finActivities .= ','.$newActivity;
                }
                $count++;
            }
        }
        else
        {
            $newActvities = array();
        }
        $retval .= '<div class="bcgt_col bgctSelectActivity">';
        $retval .= '<label>Select to Add a new Activity : </label><br />';
        $retval .= '<select name="nAID" id="aID">';
        $retval .= '<option value="-1"></option>';
        //get the activities on the course
        //loop through them and show the, BUT dont show where they are already on this unit
        //dont show where they are being added next.
        if($activities)
        {
            foreach($activities AS $activity)
            {
                if(!in_array($activity->id, $newActvities) && !array_key_exists($activity->id, $unitActivities))
                {
                    $retval .= '<option value="'.$activity->id.'">'.$activity->name.'</option>';
                }
            }
        }
        $retval .= '</select>';
        //print_object($finActivities);
        $retval .= '<input type="hidden" name="newActivities" value="'.$finActivities.'"/>';
        $retval .= '<input type="hidden" name="page" value="'.$page.'"/>';
        $retval .= '<input type="submit" name="addAcc" value="Add Activity"/>';
        $retval .= '</div>';
        foreach($newActvities AS $newActivityID)
        {
            if($newActivityID == '')
            {
                continue;
            }
            $retval .= '<div class="bcgt_col bgctActivityUnit">';
            $retval .= get_btec_unit_activity_table($newActivityID, $unit, $courseID, true);
            $retval .= '</div>';
        }
        $retval .= '<br /><br /><input type="submit" name="saveAccUnits" value="Save Assignment"/>';
        $retval .= '</div>'; 
        $retval .= '</div>'; 
        
    // **********************************************************************************************  
   } // Finished with the forms
   // ********************************************************************************************** 
    return $retval; //Prints out the forms
   
    }
    
    public static function activity_view_page($courseID, $tab)
    {
        if($tab == 'unit')
        {
            return btec_activity_by_unit_page($courseID);
        }
        elseif($tab == 'act')
        {
            return btec_activity_by_activity_page($courseID);
        }
    }
    
    public static function get_unit_activities($courseID, $unitID)
    {
        //this needs to get all of the activities for this course for this unit
        //order by due date
        return bcgt_unit_activities($courseID, $unitID);
        
    }
    
    public function display_student_grid($fullGridView = true, $studentView = true)
    {
        return $this->display_student_grid_btec($fullGridView, $studentView);
    }
    
    public function display_subject_grid()
    {
        return "";
    }
    
    public function get_family_instance_id()
    {
        return BTECQualification::ID;
    }
    
    public function has_advanced_mode()
    {
        return true;
    }
    
    public function call_display_student_grid_external()
    {
        return $this->display_student_grid_btec(false, true, false, true);
    }
    
    public function display_student_grid_btec($fullGridView = true, $studentView = true, $subCriteria = false, $basicView = false)
    {
        global $COURSE, $PAGE, $CFG, $OUTPUT;
        $grid = optional_param('g', 's', PARAM_TEXT);
        $late = optional_param('late', false, PARAM_BOOL);
        $courseID = optional_param('cID', -1, PARAM_INT);
        $context = context_course::instance($COURSE->id);
        
        $retval = '<div>';
                
        if (!$basicView)
        {
        
            $retval .= '<div class="bcgtgridbuttons">';
            $retval .= "<input type='submit' id='viewsimple' class='gridbuttonswitch viewsimple' name='viewsimple' value='View Simple'/>";
            $retval .= "<input type='submit' id='viewadvanced' class='gridbuttonswitch viewadvanced' name='viewadvanced' value='View Advanced'/>";
            $retval .= "<br>";  

            if($courseID != -1)
            {
                $context = context_course::instance($courseID);
            }
            if(has_capability('block/bcgt:editstudentgrid', $context))
            {	
                $retval .= "<input type='submit' id='editsimple' class='gridbuttonswitch editsimple' name='editsimple' value='Edit Simple'/>";
                $retval .= "<input type='submit' id='editadvanced' class='gridbuttonswitch editadvanced' name='editadvanced' value='Edit Advanced'/>"; 
            }
        
        }
        
        if ($basicView)
        {
            $retval .= "<p class='c'><a href='".$CFG->wwwroot."/blocks/bcgt/grids/print_grid.php?sID={$this->studentID}&qID={$this->id}' target='_blank'><img src='".$OUTPUT->pix_url('t/print', 'core')."' alt='' /> ".get_string('printgrid', 'block_bcgt')."</a></p>";
        }
        
        $retval .= '<input type="hidden" id="grid" name="g" value="'.$grid.'"/>';
        $retval .= '<input type="hidden" id="sID" value="'.$this->studentID.'" />';
        $retval .= '<input type="hidden" id="qID" value="'.$this->id.'" />';
        
        $editing = false;
        $advancedMode = false;
        if($grid == 'ae' || $grid == 'se')
        {
            $editing = true;
        }
        if($grid == 'a' || $grid == 'ae')
        {
            $advancedMode = true;
        }    
        
        if (!$basicView)
        {
        
            if(!$advancedMode && !$editing && has_capability('block/bcgt:viewbteclatetracking', $context))
            {
                $retval .= '<br /><span id="showLateFunc">Show Late History : ';
                $retval .= '<input type="checkbox" name="late" id="showlate"';
                if($late)
                {
                    $retval .= ' checked="checked" ';
                }
                $retval .= '/></span>';
            }
        
        }
        
        $jsModule = array(
            'name'     => 'mod_bcgtbtec',
            'fullpath' => '/blocks/bcgt/plugins/bcgtbtec/js/bcgtbtec.js',
            'requires' => array('base', 'io', 'node', 'json', 'event', 'button')
        );
        
        if ($basicView){
            $retval .= <<< JS
            <script type='text/javascript' src='{$CFG->wwwroot}/blocks/bcgt/plugins/bcgtbtec/js/bcgtbtec.js'></script>
JS;
        } else {
            $PAGE->requires->js_init_call('M.mod_bcgtbtec.initstudentgrid', array($this->id, $this->studentID, $grid), true, $jsModule);
        }
        
        require_once($CFG->dirroot.'/blocks/bcgt/lib.php');
        $retval .= load_javascript(true, $basicView);
        $retval .= "<link rel='stylesheet' type='text/css' href='{$CFG->wwwroot}/blocks/bcgt/css/start/jquery-ui-1.10.3.custom.min.css' />";
        $retval .= '</div>'; //bcgtgridbuttons
        $retval .= "
		<div class='gridKey adminRight'>";
		if($studentView)
		{
			$retval .= "<h2>Key</h2>";
			//Are we looking at a student or just the actual criteria for the grid.
			//if students then get the key that tells everyone what things stand for
			$retval .= BTECQualification::get_grid_key();
		}
		$retval .= "</div>";
        
        //the grid -> ajax
        $retval .= '<div id="btecStudentGrid">';
        
        
        $retval .= "<div id='studentGridDiv' class='studentGridDiv ".
        $grid."StudentGrid tableDiv'><table align='center' class='student_grid".
                $grid."FixedTables' id='BTECStudentGrid'>";
        
		//we will reuse the header at the bottom of the table.
		$totalCredits = $this->get_students_total_credits($studentView);
		//for all of the units on this qual, lets check which crieria names
		//have actually been used. i.e. dont show P17 if no unit has a p17
		$criteriaNames = $this->get_used_criteria_names();
        require_once($CFG->dirroot.'/blocks/bcgt/classes/sorters/CriteriaSorter.class.php');
        $criteriaSorter = new CriteriaSorter();
		usort($criteriaNames, array($criteriaSorter, "ComparisonDelegateByArrayNameLetters"));
        
		$subCriteriaArray = false;
		if($subCriteria)
		{
			//This brings back an array that consists of:
			//(('P1',(P1.1, P1.2)),('P2', (P2.1, P2.2)),('M3', (M3.1, M3.2))) ect
			$subCriteriaArray = $this->get_used_sub_criteria_names($criteriaNames);
		}
		$headerObj = $this->get_grid_header($totalCredits, $studentView, $criteriaNames, $grid, $subCriteriaArray);
		$criteriaCountArray = $headerObj->criteriaCountArray;
        $this->criteriaCount = $criteriaCountArray;
		$header = $headerObj->header;	
        $totalCellCount = $headerObj->totalCellCount;
		if($subCriteria)
		{
            $subCriteriaNo = 0;
            if(isset($headerObj->subCriteriaNo))
            {
                $subCriteriaNo = $headerObj->subCriteriaNo;
            }
		}
		$retval .= $header;
		
		$retval .= "<tbody>";
        //the body is loaded through an ajax call. This ajax call
        //is called in the js file of bcgtbtec.js and is in the initstdentgrid
        //it calls ajax and calls ajax/get_student_grid.php
        $retval .= "</tbody>";
        $retval .= "</table>";
        
        // Qual Comment
        $retval .= "<div id='qualComment'></div>";
        
        if($studentView && !$editing)
		{
            //>>BEDCOLL TODO this need to be taken from the qual object
            //as foundatonQual is different
            $retval .= '<table id="summaryAwardGrades">';
			//if we are looking at the student then show the qual award
            if(has_capability('block/bcgt:viewbtectargetgrade', $context))
            {
                $retval .= $this->show_target_grade();
            }
			$retval .= $this->show_predicted_qual_award($this->studentAward, $context);
            if(has_capability('block/bcgt:editstudentgrid', $context))
            {
                $retval .= '<tr><td><a class="refreshpredgrade" href="#">'.get_string('refreshpredgrade','block_bcgt').'</a></td></tr>';
            }
            $retval .= '</table>';
            
        }
        $retval .= "</div>";
        $retval .= '</div>';
        $retval .= '</div>';
        
        if ($basicView){
            $retval .= " <script>$(document).ready( function(){
                M.mod_bcgtbtec.initstudentgrid(Y, {$this->id}, {$this->studentID}, '{$grid}');
            } ); </script> ";
        }
        
        return $retval;
    }
    
    protected function show_target_grade()
	{
		$retval = "";
		
		$retval .= "<tr>";
		$retval .= "<td>".get_string('targetgrade', 'block_bcgt')."</td>";
		$retval .= "<td>";
        $userCourseTarget = new UserCourseTarget();
        $grade = 'N/A';
        $targetGrade = $userCourseTarget->retrieve_users_target_grades($this->studentID, $this->id);
        if($targetGrade)
        {
            $targetGradeObj = $targetGrade[$this->id];
            if($targetGradeObj)
            {
                $breakdown = $targetGradeObj->breakdown;
                if($breakdown)
                {
                    $grade = $breakdown->get_target_grade();
                }
            }
        }
        $retval .= $grade;
		//$priorGrade = $this->get_user_course_targets_by_qual($this->studentID, $this->id);
//		if($priorGrade)
//		{
//			foreach($priorGrade AS $grade)
//			{
//				$retval .= $grade->targetgrade;
//			}
//		}
//		else
//		{
//			$retval .= "N/A";
//		}
		
		$retval .= "</td>";
		$retval .= "</tr>";
		
		return $retval;
	}
	
    public function has_min_award()
    {
        return true;
    }
    
    public function has_max_award()
    {
        return true;
    }
    
	protected function show_predicted_qual_award($studentAward, $context)
	{
        //TODO CHANGE THIS TO USE THE STUDENT AWARD
		$retval = "";
        if($this->has_min_award() && has_capability('block/bcgt:viewbtecmingrade', $context))
        {
            if($this->minAward)
            {
                $minAward = $this->minAward->get_award();
            }
            else {
                $minAwards = $this->get_default_award('Min');
                if($minAwards)
                {
                    $minAward = end($minAwards)->targetgrade;
                }
            }
            $retval .= '<tr><td>'.get_string('predictedminaward','block_bcgt').
                    '</td><td><span id="minAward">'.$minAward.'</span></td></tr>';
        }
        
        if($this->has_max_award() && has_capability('block/bcgt:viewbtecmaxgrade', $context))
        {
            if($this->maxAward)
            {
                $maxAward = $this->maxAward->get_award();
            }
            else
            {
                $maxAwards = $this->get_default_award('Max');
                if($maxAwards)
                {
                    $maxAward = end($maxAwards)->targetgrade;
                }
            }
            //extra cells for unit comments and projects
            $retval .= '<tr><td>'.get_string('predictedmaxaward','block_bcgt').
                    '</td><td><span id="maxAward">'.$maxAward.'</span></td></tr>';
        }
        
        if(has_capability('block/bcgt:viewbtecavggrade', $context))
        {
            $retval .= "<tr><td>";
            $type = get_string('predictedavgaward','block_bcgt');
            $award = 'N/A';
            if($studentAward)
            {
                if(isset($studentAward->finalAward))
                {
                    $type = get_string('predictedfinalaward','block_bcgt');
                    $award = $studentAward->finalAward->get_award();
                }
                elseif(isset($studentAward->averageAward))
                {
                    $award = $studentAward->averageAward->get_award();
                }
                $retval .= "<span id='qualAwardType'>$type</span></td><td><span id='qualAward'>".$award."</span></td>";	
            }
            elseif($this->predictedAward)
            {
                $predAward = $this->predictedAward->get_award();
                $retval .= "<span id='qualAwardType'>$type</span></td><td>".
                        "<span id='qualAward'>$predAward</span></td>";
            }
            else
            {
                $retval .= "<span id='qualAwardType'>$type</span></td><td>".
                        "<span id='qualAward'>$award</span></td>";
            }
            $retval .= "</tr>";
        }
        
        return $retval;
	}
    
    public function has_sub_criteria()
    {
        return false;
    }
    
    /**
     * 
     * @global type $COURSE
     * @global type $CFG
     * @param type $advancedMode
     * @param type $editing
     * @param type $studentView
     * @return string
     */
    public function get_student_grid_data($advancedMode, $editing, 
            $studentView)
    {
        global $DB, $OUTPUT;
        $subCriteria = $this->has_sub_criteria();
        //$this->load_student();
        if (isset($this->criteriaCount)){
            $criteriaCountArray = $this->criteriaCount;
        }
        $user = $DB->get_record_sql('SELECT * FROM {user} WHERE id = ?', array($this->studentID));
        $subCriteriaArray = false;
        
        if (!isset($this->usedCriteriaNames)){
            $criteriaNames = $this->get_used_criteria_names();
        }
        
        $criteriaNames = $this->usedCriteriaNames;
		if($subCriteria)
		{
			//This brings back an array that consists of:
			//(('P1',(P1.1, P1.2)),('P2', (P2.1, P2.2)),('M3', (M3.1, M3.2))) ect
			$subCriteriaArray = $this->get_used_sub_criteria_names($criteriaNames);
		}
        $rowsArray = array();
        global $COURSE, $CFG;
        $courseID = optional_param('cID', -1, PARAM_INT);
        $context = context_course::instance($COURSE->id);
        if($courseID != -1)
        {
            $context = context_course::instance($courseID);
        }
        //get all of the units
        //get all of the units and sort them by their names.
        require_once($CFG->dirroot.'/blocks/bcgt/classes/sorters/CriteriaSorter.class.php');
        $criteriaSorter = new CriteriaSorter();
		usort($criteriaNames, array($criteriaSorter, "ComparisonDelegateByArrayNameLetters"));
        
		$units = $this->units;
        $unitSorter = new UnitSorter();
		usort($units, array($unitSorter, "ComparisonDelegateByType"));
        $possibleValues = null;
        if($advancedMode && $editing)
        {
           $possibleValues = $this->get_possible_values(BTECQualification::ID, true); 
        }
		if($editing && !$advancedMode)
        {
            $unitAwards = Unit::get_possible_unit_awards($this->get_class_ID());
        }
        $rowCount = 0;
        foreach($units AS $unit)
		{
			if(($studentView && $unit->is_student_doing()) || !$studentView)
			{	
                $rowArray = array();
				//Are we looking at the student? 
				//Is the student doing the unit. 
				//If they are not then we dont want to show the unit. 
				$rowClass = 'rO';
				if($rowCount % 2)
				{
					$rowClass = 'rE';
				}				
				$award = 'N/S';
				$rank = 'nr';
				if($studentView)
				{
					//get the users award from the unit
					$unitAward = $unit->get_user_award();
					if($unitAward)
					{
						$rank = $unitAward->get_rank();
						$award = $unitAward->get_award();
					}	
				}
				
				$extraClass = '';
				if($rowCount == 1)
				{
					$extraClass = 'firstRow';
				}
				elseif($rowCount == count($units))
				{
					$extraClass = 'lastRow';
				}
				//the row class
				//$retval .= "<tr class='$rowClass $extraClass ".$unit->get_unit_type()." prU".$unit->get_id()."' id='".$unit->get_id()."'>";
                
                //the first json value
                //$retval .= '<td></td>';
                $rowArray[] = '';
				
                // Unit Comment
                $getComments = $unit->get_comments();
                
                $cellID = "cmtCell_U_{$unit->get_id()}_S_{$user->id}_Q_{$this->get_id()}";
                
		        
                $username = htmlentities( $user->username, ENT_QUOTES );
                $fullname = htmlentities( fullname($user), ENT_QUOTES );
                $unitname = htmlentities( $unit->get_name(), ENT_QUOTES);
                $critname = "N/A";   
                
                $retval = "";

                if($advancedMode && $editing)
                {

                    if(!empty($getComments))
                    {                
                        $retval .= "<img id='{$cellID}' grid='student' username='{$username}' fullname='{$fullname}' unitname='{$unitname}' critname='{$critname}' type='button' class='editCommentsUnit' title='Click to Edit Unit Comments' src='{$CFG->wwwroot}/blocks/bcgt/plugins/bcgtbtec/pix/grid_symbols/comments.jpg' />";
                        $retval .= "<div class='tooltipContent'>".nl2br( htmlentities($getComments, ENT_QUOTES) )."</div>";
                    }
                    else
                    {                        
                        $retval .= "<img id='{$cellID}' grid='student' username='{$username}' fullname='{$fullname}' unitname='{$unitname}' critname='{$critname}' type='button' class='addCommentsUnit' title='Click to Add Unit Comment' src='{$CFG->wwwroot}/blocks/bcgt/plugins/bcgtbtec/pix/grid_symbols/plus.png' />";
                    }

                }
                else
                {
                    if(!empty($getComments)){
                        $retval .= "<img src='{$CFG->wwwroot}/blocks/bcgt/plugins/bcgtbtec/pix/grid_symbols/comment-icon.png' class='showCommentsUnit' />";
                        $retval .= "<div class='tooltipContent'>".nl2br( htmlentities($getComments, ENT_QUOTES) )."</div>";
                    }
                    
                }
                $rowArray[] = $retval;
                // End Unit Comment  
                //$retval .= "<td class='unitName r".$unit->get_id()." '>";
				$studentID = -1;
				if($studentView)
				{
					//This is used to link to another page.
					//if studentID = -1 then we know we are not
					//looking at the student but the qual in general
					$studentID = $this->studentID;
				}
                $link = '';
                if(has_capability('block/bcgt:editunit', $context))
                {
                    $link = '<a href="'.$CFG->wwwroot.'/blocks/bcgt/grids/'.
                            'unit_grid.php?uID='.$unit->get_id().
                            '&qID='.$this->id.'">'.$unit->get_name().'</a>';
                }
                else
                {
                    $link = $unit->get_name();
                }
				$retval = "<span id='uID_".$unit->get_id()."' class='uNToolTip unitName".$unit->get_id()."' title=''>".$link."</span>";
				
                $retval .= "<span style='color:grey;font-size:85%;'><br />(".$unit->get_credits()." Credits)</span>";	
				
                //if has capibility
				if(has_capability('block/bcgt:editunit', $context))
				{		
                    $retval .= "<a class='editing_update editUnit' href='{$CFG->wwwroot}/blocks/bcgt/forms/edit_unit.php?unitID=".$unit->get_id()."' title = 'Update Unit'>
					<img class='iconsmall editUnit' alt='Update Unit' src='".$OUTPUT->pix_url("t/edit", "core")."'/></a>";
				}
                
                $retval .= "<img src='".$CFG->wwwroot."/blocks/bcgt/pix/info.png' height='12' width='12' class='uNToolTipInfo' unitID='{$unit->get_id()}' />";
                
				//$retval .= "</td>";
                $rowArray[] = $retval;
				if($studentView)
				{
					if($editing && !$advancedMode)
					{
						$retval = $this->edit_unit_award($unit, $rank, $award, $unitAwards);
                        $rowArray[] = $retval;
                        
                    }
					else
					{
						//print out the unit award column
						//$retval .= "<td id='unitAward_".$unit->get_id()."' class='unitAward r".$unit->get_id()." rank$rank'>".$award."</td>";
                        $rowArray[] = '<span id="unitAwardAdv_'.$unit->get_id().'">'.$award.'</span>';
                    }
				}
                                
                // % Completion
                if($this->has_percentage_completions() && $studentView){
                    $retval .= "<td id='tdPercentCompleted'>".$unit->display_percentage_completed()."</td>";
                }
				
				if($criteriaNames)
				{
					//if we have found the used criteria names. 
					$criteriaCount = 0;
					$previousLetter = '';						
					foreach($criteriaNames AS $criteriaName)
					{	
						//TODO
						$criteriaCount++;
						$letter = substr($criteriaName, 0, 1);
						if($previousLetter != '' && $previousLetter != $letter)
						{
							//if we have moved from P to M then put the divider in. 
                            $rowArray[] = "";
						}
						$previousLetter = $letter;	
						
						if($studentView)
						{
							//if its the student view then lets print
							//out the students unformation
							if($studentCriteria = $unit->get_single_criteria(-1, $criteriaName))
							{	
								$retval = $this->set_up_criteria_grid($studentCriteria, '', 
                                        $possibleValues, $editing, $advancedMode, false, $unit, 0, $user);
                                $rowArray[] = $retval;
								if($subCriteria)
								{
									//Get the used Sub Criteria Names from the heading for this criteriaName
									//for example get the p1.1, P1.2 ect for the P1
                                    if(array_key_exists($criteriaName, $subCriteriaArray))
                                    {
                                        $criteriaSubCriteriasUsed = $subCriteriaArray[$criteriaName];
                                        //Lets see if this Criteria has the subcriteria that matches the heading
                                        $cellCount = count($criteriaSubCriteriasUsed);
                                        $i = 0;
                                        foreach($criteriaSubCriteriasUsed AS $subCriteriaUsed)
                                        {
                                            $firstLast = 0;
                                            $i++;
                                            $extraClass = '';
                                            if($i == 1)
                                            {
                                                $extraClass = 'startSubCrit';
                                                if(count($criteriaSubCriteriasUsed) == 1)
                                                {
                                                    $extraClass .= " endSubCrit";
                                                }
                                                $firstLast = 1;
                                            }
                                            elseif($i == $cellCount)
                                            {
                                                $extraClass = 'endSubCrit';
                                                $firstLast = -1;
                                            }
                                            $criteriaCount++;
                                            $actualSubCriteria = $studentCriteria->get_single_criteria(-1, $subCriteriaUsed);
                                            if($actualSubCriteria)
                                            {
                                                //then create the grid
                                                $rowArray[] = $this->set_up_criteria_grid($actualSubCriteria, 
                                                        $extraClass.' subCriteria subCriteria_'.$criteriaName, 
                                                        $possibleValues, $editing, $advancedMode, 
                                                        true, $unit, $firstLast, $user);
                                            }
                                            else
                                            {
                                                $rowArray[] = '';
//                                                $retval .= "<td display='none' class='grid_cell_blank $extraClass subCriteria subCriteria_$criteriaName'></td>";	
                                            }//end else not actualSubCriteria
                                        }//end loop sub Criteria
                                    }
                                    else
                                    {
                                        $rowArray[] = '';
                                    }
								}//end if there is sub criteria
							}//end if student criteria
							else //not student criteria (i.e. the criteria doesnt exist on that unit)
							{                                
                                //retval needs to be an array of the columns
                                if (isset($criteriaCountArray)){
                                    $rowArray = $this->get_criteria_not_on_unit($criteriaCount, $criteriaCountArray, 
                                    $advancedMode, $editing, $criteriaName, $subCriteriaArray, $rowArray);
                                } else {
                                    $rowArray[] = '';
                                }
//                                $rowArray[] = $retval;
							}//end else not sudent criteria	
						}
						else//its not the student view
						{//This means we are just showing the qual as a whole. 
							//then lets just test if he unit has that criteria
							//and mark it as present or not
							
							$retval .= $this->get_non_student_view_grid($criteriaCount, $criteriaCountArray, $criteriaName, $unit, $subCriteriaArray);
                            $rowArray[] = $retval;
                            
                        }
						
					}//end for each criteria
				}//end if criteria names
				$rowsArray[] = $rowArray;
//				$retval .= "</tr>";	
			}//end if student view and student doing the unit.
		}//end for each unit
        return $rowsArray;
    }
    
    /**
	 * Returns the possible values that can be selected for this qualification type
	 * when updating criteria for students
	 */
	public static function get_possible_values($typeID, $enabled = false)
	{
		global $DB;
		$sql = "SELECT value.*, settings.id as settingid, settings.coreimg, 
            settings.customimg, settings.coreimglate, settings.customimglate 
            FROM {block_bcgt_value} value
            JOIN {block_bcgt_value_settings} settings ON settings.bcgtvalueid = value.id
		WHERE value.bcgttypeid = ?";
        if($enabled)
        {
            $sql .= " AND value.enabled = ?";
        }
        $params = array($typeID, 1);
		return $DB->get_records_sql($sql, $params);
		
	}
    
    protected function get_non_student_view_grid($criteriaCount, $criteriaCountArray, $criteriaName, $unit, $subCriteriaArray)
	{
		$retval = "";
		global $CFG;
		$retval .= "<td";
		if($criteria = $unit->get_single_criteria(-1, $criteriaName))
		{
			//if the crieria is on the unit then mark as so and build the on hover tooltip
			$retval .= " class='crit'><span class='critValue'><img class='criteriaPresent' src=\"{$CFG->wwwroot}/mod/qualification/pix/blackX.jpg\">";
			$retval .= "</span>";
			//$retval .= $criteria->build_criteria_value_popup($unit->get_id(), false);
			$retval .= "</td>";
			if($subCriteriaArray)
			{
				$criteriaSubCriteriasUsed = $subCriteriaArray[$criteriaName];
				if($criteriaSubCriteriasUsed)
				{
					$i = 0;
					foreach($criteriaSubCriteriasUsed AS $subCriteriaUsed)
					{
						$i++;
						$extraClass = '';
						if($i == 1)
						{
							$extraClass = 'startSubCrit';
						}
						elseif($i == count($criteriaSubCriteriasUsed))
						{
							$extraClass = 'endSubCrit';
						}
						$subCriteria = $criteria->get_single_criteria(-1, $subCriteriaUsed);
						if($subCriteria)
						{
							$retval .= "<td class='$extraClass subCriteria subCriteria_$criteriaName crit'><span class='critValue'><img class='criteriaPresent' src=\"{$CFG->wwwroot}/mod/qualification/pix/blackX.jpg\">";
							$retval .= "</span>";
							//$retval .= $subCriteria->build_criteria_value_popup($unit->get_id(), false);
							$retval .= "</td>";
						}
						else
						{
							$retval .= "<td class='$extraClass subCriteria subCriteria_$criteriaName critNo'></td>";
						}
						
					}
				}
			}//end if sub
		}
		else
		{
			//else the criteria isnt on the unit
			$retval .= " class='critNo'></td>";
			if($subCriteriaArray)
			{
				$criteriaSubCriteriasUsed = $subCriteriaArray[$criteriaName];
				if($criteriaSubCriteriasUsed)
				{
					$i=0;
					foreach($criteriaSubCriteriasUsed AS $subCriteriaUsed)
					{
						$i++;
						$extraClass = '';
						if($i == 1)
						{
							$extraClass = 'startSubCrit';
						}
						elseif($i == count($criteriaSubCriteriasUsed))
						{
							$extraClass = 'endSubCrit';
						}
						$retval .= "<td class='$extraClass subCriteria subCriteria_$criteriaName critNo'></td>";	
					}
				}
			}//end if subcriteria
		}//end else the criteria isnt on the unit.
		return $retval;
	}
    
    protected function get_criteria_not_on_unit($criteriaCount, 
            $criteriaCountArray, $advancedMode, 
            $editing, $criteriaName, 
            $subCriteriaArray = false, $row = array())
	{
//		$retval = "";
		//if the criteria isnt on the unit
		//create a blank cell
//		$retval .= "<td class='grid_cell_blank'></td>";
        $row[] = '<span class="grid_cell_blank"></span>';
		if($subCriteriaArray)
		{
			//get the sub criteria that should be here and output a blank cell for each
			//Get the used Sub Criteria Names from the heading for this criteriaName
			//for example get the p1.1, P1.2 ect for the P1
			$criteriaSubCriteriasUsed = $subCriteriaArray[$criteriaName];
			//Lets see if this Criteria has the subcriteria that matches the heading
			$i= 0;
			foreach($criteriaSubCriteriasUsed AS $subCriteriaUsed)
			{
				$extraClass = '';
				$i++;
				if($i == 1)
				{
					$extraClass = 'startSubCrit';
				}
				elseif($i == count($criteriaSubCriteriasUsed))
				{
					$extraClass = 'endSubCrit';
				}
				$criteriaCount++;
                $row[] = '<span class="grid_cell_blank '.$extraClass.' subCriteria subCriteria_'.$criteriaName.'"></span>';
            }
		}
		return $row;
	}
    
    protected function set_up_criteria_grid($criteria, $extraCellClass, 
	$possibleValues, $editing, $advancedMode, $sub, $unit, 
            $firstLast, $user)
	{
		global $CFG;
		$criteriaName = $criteria->get_name();
		$retval = "";
		//get the students criteria information
		//lets get the comments that have been added to the students criteria. 
		$studentComments = '';
		if($criteria->get_comments() && $criteria->get_comments() != '')
		{
			$studentComments = $criteria->get_comments();
		}	
               
		//get the actual object. I.e. what value has been given to 
		//the students criteria. 
		$studentValueObj = $criteria->get_student_value();	
        if(!$studentValueObj)
        {
            //then we need to create a default one
            $studentValueObj = new Value();
            $studentValueObj->create_default_object('N/A', BTECQualification::FAMILYID);
        }
		$cellClass = 'noComments';
		$comments = false;
		if($studentComments != '')
		{
			// do we have comments?
			$cellClass = 'criteriaComments';
			$comments = true;
		}
		
		//ok now lets output the actual cell/s containing
		//the student info
		if($editing)
		{
			//if we are editing then we need input options
			if($advancedMode)
			{
				//advanced mode allows
				//drop down options and comments
				//this td is used as the hover over tooltip.
				$retval .= $this->advanced_edit_grid($criteria, $unit, 
												$studentComments, 
                        $possibleValues, $studentValueObj, $extraCellClass, 
                        $firstLast);
			}
			else //editing but simple mode
			{			
				$retval .= $this->simple_edit_grid($criteria, $unit, 
						$studentValueObj, $comments);
			}
		}
		else //NOT EDITING
		{
			
			if($advancedMode)
			{
				$retval .= $this->advanced_not_edit_grid($criteria, $studentValueObj, 
								$unit, $comments);
			}
			else //not editing but simple mode
			{
				$retval .= $this->simple_not_edit_grid($criteria, $studentValueObj, 
							$unit, $comments);
			}//end else simple mode. when not editing
			
		}//end else not editing
		
		return $retval;
	}
    
    /**
     * 
     * @param type $studentCriteria
     * @param type $unit
     * @param type $criteriaName
     * @param type $comments
     * @param type $studentComments
     * @param type $possibleValues
     * @param type $studentValueID
     * @param type $extraCellClass
     * @param type $firstLast
     * @param type $user
     * @return string
     */
    protected function advanced_edit_grid($studentCriteria, $unit, 
	$studentComments, $possibleValues, $studentValueObj, 
            $extraCellClass, $firstLast)
	{
        global $CFG, $DB;
		$retval = '';
		//advanced mode allows
		//drop down options and comments
		//get all of the possible values that can be selected for the 
		//criteria
		//this td is used as the hover over tooltip.
		$extraClass = $extraCellClass;
		if($firstLast == -1)
		{
			//this means its the last column we are dealing with.
			//this negates their being two last columns which would draw
			//the extra border
			$extraClass = '_'.$extraCellClass;	
		}
		$extraClassEnd = $extraCellClass;
		if($firstLast == 1)
		{
			//this means its the first column we are dealing with.
			//this negates their being two first columns which would draw
			//the extra border
			$extraClassEnd = '_'.$extraCellClass;	
		}
		//$retval .= "<td class='$extraClass criteriaValue r".$unit->get_id()." c$criteriaName'>";								
		$retval .= "<span class='stuValue' id='cID_".$studentCriteria->get_id().
                "_uID_".$unit->get_id()."_SID_".$this->studentID."_QID_".
                $this->id."'>";
        $retval .= "<select class='criteriaValueSelect' id='cID_".
                $studentCriteria->get_id().
                "_uID_".$unit->get_id()."_SID_".$this->studentID.
                "_QID_".$this->id."' name='cID_".$studentCriteria->get_id().
                "'><option value='-1'></option>";
		if($possibleValues)
		{
			foreach($possibleValues AS $value)
			{
				//output each option
				//title used for on hover
                $valueShort = $value->shortvalue;
                if(isset($value->customshortvalue) && trim($value->customshortvalue) != '')
                {
                    $valueShort = $value->customvalue;
                }
                $valueLong = $value->value;
                if(isset($value->customevalue) && trim($value->customshortvalue) != '')
                {
                    $valueLong = $value->customevalue;
                }
                $selected = '';
				if($studentValueObj->get_id() == $value->id)
				{
                    $selected = 'selected';
				}
                $retval .= "<option $selected value = '$value->id' title='$valueLong'>".
                        "$valueShort - $valueLong</option>";
			}
		}
		$retval .= "</select></span>&nbsp;";
        
        $student = $this->student;
		        
        // Change this so each thing has its own attribute, wil be easier
        $commentImgID = "cmtCell_cID_".$studentCriteria->get_id()."_uID_".$unit->get_id()."_SID_".$this->studentID.
                        "_QID_".$this->id;
        
        $username = htmlentities( $student->username, ENT_QUOTES );
        $fullname = htmlentities( fullname($student), ENT_QUOTES );
        $unitname = htmlentities( $unit->get_name(), ENT_QUOTES);
        $critname = htmlentities($studentCriteria->get_name(), ENT_QUOTES);        
                                
		if(!is_null($studentComments) && $studentComments != '')
		{ 
			$retval .= "<img id='{$commentImgID}' grid='student' username='{$username}' fullname='{$fullname}' unitname='{$unitname}' critname='{$critname}' class='editComments' title='Click to Edit Comments' ".
                    "alt='Click to Edit Comments' src='$CFG->wwwroot/blocks/bcgt/plugins/bcgtbtec/pix/grid_symbols/comments.jpg'>";
            $retval .= "<div class='tooltipContent'>".nl2br( htmlentities($studentComments, ENT_QUOTES) )."</div>";
        }
		else
		{
            $retval .= "<img id='{$commentImgID}' grid='student' username='{$username}' fullname='{$fullname}' unitname='{$unitname}' critname='{$critname}' class='addComments' title='Click to Add Comments' ".
                    "alt='Click to Edit Comments' src='$CFG->wwwroot/blocks/bcgt/plugins/bcgtbtec/pix/grid_symbols/plus.png'>";
        }
        
        
		return $retval;
	}
	
	protected function simple_edit_grid($studentCriteria, $unit, 
	$studentValueObj, $comments )
	{
		$retval = "";
		$retval .= "<span class='stuValue' id='cID_".$studentCriteria->get_id().
                "_uID_".$unit->get_id()."_SID_".$this->studentID."_QID_".$this->id.
                "'><input type='checkbox'".
                "class='criteriaValueMet criteriaCheck' name='cID_".$studentCriteria->get_id()."'".
                "id='cID_".$studentCriteria->get_id()."_uID_".$unit->get_id()."_SID_".$this->studentID."_QID_".$this->id."'";
		if($studentValueObj->get_short_value() == 'A')
		{
			$retval .= "checked='checked'";
		}
		$retval .= "/></span>";
        
        if (!is_null($comments) && $comments != ''){
            $retval .= "<div class='tooltipContent'>".nl2br( htmlentities($comments, ENT_QUOTES) )."</div>";
        }
        
		return $retval;
	}

	protected function advanced_not_edit_grid($studentCriteria, $studentValueObj, 
            $unit, $comments)
	{
		$retval = '';		
		$class = $studentValueObj->get_short_value();
        $shortValue = $studentValueObj->get_short_value();
        if($studentValueObj->get_custom_short_value())
        {
            $shortValue = $studentValueObj->get_custom_short_value();
        }
		$retval .= "<span id='stCID_".$studentCriteria->get_id()."_UID_".
                $unit->get_id()."_SID_".$this->studentID."_QID_".$this->id.
                "' class='stuValue stuValueNonEdit $class' title=''>".$shortValue."</span>";
        if (!is_null($comments) && $comments != ''){
            $retval .= "<div class='tooltipContent'>".nl2br( htmlentities($comments, ENT_QUOTES) )."</div>";
        }
		return $retval;
	}
	
    /**
     * 
     * @global type $CFG
     * @param type $studentCriteria
     * @param type $studentValueObj
     * @param type $unit
     * @param type $comments
     * @return string
     */
	protected function simple_not_edit_grid($studentCriteria, $studentValueObj, 
            $unit, $comments)
	{
        $studentFlag = $studentCriteria->get_student_flag();
		global $CFG;
		$retval = '';
        $flag = '';
        if(isset($this->studentFlag))
        {
            $flag = $this->studentFlag;
        }
		//show all of the symbols for the student
        $imageObj = BTECQualification::get_simple_grid_images($studentValueObj, $studentFlag, $flag);
		$image = $imageObj->image;
		$class = $imageObj->class;
		$retval .= "<span id='stCID_".$studentCriteria->get_id()."_UID_".
                $unit->get_id()."_SID_".$this->studentID."_QID_".
                $this->id."' class='stuValue stuValueNonEdit $class' title=''><img src='".
                $CFG->wwwroot."/blocks/bcgt/plugins/bcgtbtec$image'/></span>";
        if (!is_null($comments) && $comments != ''){
            $retval .= "<div class='tooltipContent'>".nl2br( htmlentities($comments, ENT_QUOTES) )."</div>";
        }
		return $retval;
	}
	
	/**
	 * THIS WHOLE FUNCTION AND FUNCTIONALITY REALLY SHOULD BE IN THE VALUE CLASS!!!!
	 * WHAT WAS I THINKING? SHOULD BE SUB CLASS VALUES SUCH AS BTECValue
	 * @param unknown_type $studentValue
	 * @param unknown_type $studentCriteriaMet
	 */
	public static function get_simple_grid_images($studentValueObj, 
            $studentFlag = '', $flag = '')
	{
		$obj = new stdClass;
        $class = 'stuValue'.$studentValueObj->get_short_value();
        if($studentFlag == BTECCriteria::LATE && $flag == 'L')
        {
            //then lets get the late
            $image = $studentValueObj->get_core_image_late();
            if($studentValueObj->get_custom_image_late())
            {
                $image = $studentValueObj->get_custom_image_late();
            }
        }
        else
        {
            $image = $studentValueObj->get_core_image();
            if($studentValueObj->get_custom_image())
            {
                $image = $studentValueObj->get_custom_image();
            }
        }
		$obj->image = $image;
		$obj->class = $class;
		
		return $obj;
	}
    
    public static function edit_unit_award($unit, $rank, $award, $unitAwards = null)
	{
		$retval = "";
        $retval .= "<select class='unitAward' id='uAw_".$unit->get_id()."' name='unitAwardAPL_".$unit->get_id()."'>";        
		$retval .= "<option value='-1'>NA</option>";
		if($unitAwards)
		{
			foreach($unitAwards AS $possAward)
			{
				$selected = '';
				if($possAward->award == $award)
				{
					$selected = 'selected';
				}
				$retval .= "<option $selected value='$possAward->id'>$possAward->award</option>";
			}
		}
		$retval .= "</select>";
		return $retval;
	}
    
    protected function get_grid_header($totalCredits, $studentView, $criteriaNames, $grid, $subCriteriaArray = false)
	{
        $editing = false;
        $advancedMode = false;
        if($grid == 'es' || $grid == 'ea')
        {
            $editing = true;
        }
        if($grid == 'a' || $grid = 'ea')
        {
            $advancedMode = true;
        }
        global $printGrid;
		$headerObj = new stdClass();
		$header = '';
		//extra one for projects
		$header .= "<thead><tr><th></th>";
                if($advancedMode && $editing)
                {
                    $header .= "<th class='unitComment'></th>";
                }
                elseif(!($editing && $advancedMode))
                {
                    $header .= "<th></th>";
                }
                                
                $header .= "<th>Unit (Total Credits: $totalCredits)</th>";
                $totalCellCount = 3;
		if($studentView)
		{//if its not student view then we are looking at just
			//the qual in general rather than a student.
			$header .= "<th>Award</th>";
            $totalCellCount++;
            // If qual has % completions enabled
            if($this->has_percentage_completions() && !$printGrid && $studentView){
                $header .= "<th>% Complete</th>";
                $totalCellCount++;
            }
		}	  
		$headerObj = BTECQualification::get_criteria_headers($criteriaNames, $subCriteriaArray, 
                $advancedMode, $editing, $totalCellCount);
		$subHeader = $headerObj->subHeader;
		$header .= $subHeader;
		$header .= "</tr></thead>";
		$headerObj->header = $header;
		return $headerObj;
	}
    
    public static function get_criteria_headers($criteriaNames, $subCriteriaArray, 
            $advancedMode, $editing, $totalCellCount = 0)
	{		
		$headerObj = new stdClass();
		
		$subHeader = "";
		$previousLetter = '';
		$criteriaCountArray = array();
		$subCriteriaNo = array();
		if($criteriaNames)
		{
			$criteriaCount = 0;
			foreach($criteriaNames AS $criteriaName)
			{
				//for each criteria create the heading. 
				$criteriaCount++;
				$letter = substr($criteriaName, 0, 1);
				if($previousLetter != '' && $previousLetter != $letter)
				{
					//if the criteria letter changes (i.e. P to M) then lets 
					//create a divider. 
					//we also need to know how many criteria we have before each blank space
					//this is then used later. 
					$criteriaCountArray[] = $criteriaCount;
					$subHeader .= "<th class='divider'></th>";
                    $totalCellCount++;
				}
				$previousLetter = $letter;
				if($subCriteriaArray)
				{
					$subCriterias = $subCriteriaArray[$criteriaName];
				}
				$subHeader .= "<th class='criteriaName c$criteriaName'><span class='criteriaName";
				if($subCriteriaArray && $subCriterias)
				{
					$subHeader .= " hasSubCriteria' id='subCriteria_$criteriaName'>";
                    $subHeader .= "$criteriaName</span>";
                    $subHeader .= " s";
                    $subHeader .= "</th>";
                    
                }
				else
				{
					$subHeader .= "'>$criteriaName</span></th>";
				}
                $totalCellCount++;
				if($advancedMode && $editing)
				{
					//if its advanced and editing then we have the extra 
					//cell required for the add/edit comments. 
					$subHeader .= "<th class='blankHeader'></th>";
                    $totalCellCount++;
				}
				$subHeaderClass = 'subCriteria';
				$subCriteriaCount = 0;
				if($subCriteriaArray && $subCriterias)
				{
					foreach($subCriterias AS $subCriteria)
					{
						$criteriaCount++;
						$subCriteriaCount++;
						$subHeader .= "<th class='$subHeaderClass subCriteria_$criteriaName'>".$subCriteria."</th>";
						$totalCellCount++;
                        if($advancedMode && $editing)
						{
							//if its advanced and editing then we have the extra 
							//cell required for the add/edit comments. 
							$subHeader .= "<th class='blankHeader $subHeaderClass subCriteria_$criteriaName'></th>";
                            $totalCellCount++;
                        }
					}
					$subCriteriaNo[$criteriaName] = $subCriteriaCount;
				}
				
			}
		}
		$headerObj->subHeader = $subHeader;
		$headerObj->criteriaCountArray = $criteriaCountArray;
        $headerObj->totalCellCount = $totalCellCount;
		if($subCriteriaArray)
		{
			$headerObj->subCriteriaNo = $subCriteriaNo;	
		}
		return $headerObj;
	}
    
    /**
	 * Gets the criteria names that are used at least once in the units of the qualification. 
	 */
	function get_used_criteria_names()
	{
		//checks all units and see's if the criteria name is used. 
		$usedCriteriaNames = array();
        foreach($this->units AS $unit)
        {
            $unitCriteriaNames = $unit->get_criteria_names();
            $usedCriteriaNames = array_merge($unitCriteriaNames, $usedCriteriaNames);
        }
        $this->usedCriteriaNames = $usedCriteriaNames;
		return $usedCriteriaNames;
        
	}
	
	private function get_used_sub_criteria_names($criteriaNames)
	{
        global $CFG;
        require_once($CFG->dirroot.'/blocks/bcgt/classes/sorters/CriteriaSorter.class.php');
        $criteriaSorter = new CriteriaSorter();                
		$units = $this->units;
		$subCriteriaArray = array();
		foreach($units AS $unit)
		{
			foreach($criteriaNames AS $criteriaName)
			{
				$criteria = $unit->get_single_criteria(-1, $criteriaName);
				if($criteria)
				{
					$subCriterias = $criteria->get_sub_criteria();
					$subCriteriaNames = array();
					if($subCriterias)
					{
                        usort($subCriterias, array($criteriaSorter, "ComparisonDelegateByName"));
						foreach($subCriterias AS $subCriteria)
						{
							$subCriteriaNames[$subCriteria->get_name()] = $subCriteria->get_name();
						}
					}
					if(array_key_exists($criteriaName, $subCriteriaArray))
					{
						$subCriteriaArray[$criteriaName] = array_merge($subCriteriaArray[$criteriaName],$subCriteriaNames);	
					}
					else
					{
						$subCriteriaArray[$criteriaName] = $subCriteriaNames;
					}
				}
			}	
		}

		//we need to sort the sub criteria on the off chance that they contain some missing criteria. //skipped criteria
		$arrayKeys = array_keys($subCriteriaArray);
		foreach($arrayKeys AS $key)
		{
			$subArray = $subCriteriaArray[$key];
			
			usort($subArray, array($criteriaSorter, "ComparisonDelegateByArrayName"));
            $subCriteriaArray[$key] = $subArray;
		}
		return $subCriteriaArray;
	}
    
    /**
	 * This will build up the key for the Grid used in student view
	 * and single view. 
	 * SHOULD be a static function to the UNIT view can get to it
	 * At the moment we have duplicate calls. 
	 */
	public static function get_grid_key($string = true)
	{
        global $CFG; 
        $file = $CFG->wwwroot.'/blocks/bcgt/plugins/bcgtbtec';
        if($string)
        {
            $retval = '';
        }
        else
        {
            $retval = array();
        }
        
        $core = '<span class="keyValue"><img class="keyImage"';
        $core .= 'src="'.$CFG->wwwroot.'/blocks/bcgt/plugins/bcgtbtec/pix/'.
                'grid_symbols/commentsSimple.jpg"/> = Comments (Hover to view)'.
                '</span>';
        $warn = '';
        //this now needs to get them from the database!
        $possibleValues = BTECQualification::get_possible_values(BTECQualification::ID, true);
        if($possibleValues)
        {
            foreach($possibleValues AS $possibleValue)
            {
                $value = '<span class="keyValue"><img class="keyImage"';
                if(isset($possibleValue->customimg) && $possibleValue->customimg != '')
                {
                    $icon = $possibleValue->customimg;
                }
                else
                {
                    $icon = $possibleValue->coreimg;
                }
                if(isset($possibleValue->customvalue) && $possibleValue->customvalue != '')
                {
                    $desc = $possibleValue->customvalue;
                }
                else
                {
                    $desc = $possibleValue->value;
                }
                $value .= ' src="'.$file.$icon.'"/> = '.$desc.'</span>';
                if($string)
                {
                    $retval .= $value;
                }
                else
                {
                    $retval[] = $value;
                }
                if($possibleValue->shortvalue == 'A')
                {
                    
                    $warn = "<p><span>(Only $desc will be used towards Unit Award)</span> </p>";
                }
            }
        }      
        if($string)
        {
            $retval .= $warn;
        }
        return $retval;
        
	}
    
    /**
     * This processes the edit_single_student_units section.
     * IT is called AFTER load_student_information
     * IT is called from within the students qual object
     */
    protected function process_edit_single_students_units_page()
    {
        $units = $this->get_units();
        foreach($units AS $unit)
        {
            //get the check boxes
            //name is in the format of $name='s'.$student->id.'U'.$unit->get_id().'Q'.$this->id;
            $fieldToCheck = 's'.$this->studentID.'U'.$unit->get_id().'Q'.$this->id;
            $this->process_edit_students_units($unit, $fieldToCheck, $this->studentID);
        }
    }
    
    /**
     * For the unit passes in it checks to see if the 'field' has been checked or
     * not checked and updates the database
     * Basically the check boxes will be to denote of the student is doing the
     * unit or not. 
     * @param type $unit
     * @param type $fieldToCheck
     * @param type $studentID
     */
    protected function process_edit_students_units($unit, $fieldToCheck, $studentID)
    {
        if(isset($_POST[$fieldToCheck]))
        {
            //so its been checked now. was it before?
            if(!$unit->is_student_doing() && $unit->is_student_doing() != 'Yes')
            {
                $unit->set_is_student_doing(true);
                $unit->insert_student_on_unit($this->id, $studentID);
            }
        }
        else
        {
            //so it isnt checked/ Was it before?
            if($unit->is_student_doing() || $unit->is_student_doing() == 'Yes')
            {
                $unit->set_is_student_doing(false);
                $unit->delete_student_on_unit_no_id($studentID, $this->id);
            } 
        }
    }
    
    public function get_edit_student_page_init_call()
    {
        global $PAGE;
        $jsModule = array(
            'name'     => 'mod_bcgtbtec',
            'fullpath' => '/blocks/bcgt/plugins/bcgtbtec/js/bcgtbtec.js',
            'requires' => array('base', 'io', 'node', 'json', 'event', 'button')
        );
        $PAGE->requires->js_init_call('M.mod_bcgtbtec.initstudunits', null, true, $jsModule);
//        $PAGE->requires->js('/blocks/bcgt/js/block_bcgt_functions.js');
//        $PAGE->requires->js('/blocks/bcgt/js/jquery.dataTables.js');
//        $PAGE->requires->js('/blocks/bcgt/js/FixedColumns.js');
//        $PAGE->requires->js('/blocks/bcgt/js/FixedHeader.js'); 
    }
    
    /**
     * Multiple is denoting if this will appear multiple times on a page
     * @global type $OUTPUT
     * @global type $DB
     * @global type $PAGE
     * @global type $CFG
     * @param type $multiple
     * @return string
     */
    public function get_edit_students_units_page($courseID = -1, $multiple = false, $count = 1, $action = 'q')
    {
        global $OUTPUT, $DB, $PAGE, $CFG;
        $sAID = optional_param('sAID', -1, PARAM_INT);
        $heading = $this->get_type().''. 
        ' '.$this->get_level()->get_level().''. 
        ' '.$this->get_subType()->get_subType();
        $heading .= ' '.$this->get_name().'<br />';
        $heading .= ' ('.get_string('bteccredits','block_bcgt').': '.$this->get_credits().')';
        if(!$multiple)
        {
            $jsModule = array(
                'name'     => 'mod_bcgtbtec',
                'fullpath' => '/blocks/bcgt/plugins/bcgtbtec/js/bcgtbtec.js',
                'requires' => array('base', 'io', 'node', 'json', 'event', 'button')
            );
            $PAGE->requires->js_init_call('M.mod_bcgtbtec.initstudunits', null, true, $jsModule);
        }
        $studentRole = $DB->get_record_sql('SELECT id FROM {role} WHERE shortname = ?', array('student'));
        $units = $this->get_units();
        $students = $this->get_users($studentRole->id, '', 'lastname ASC', $courseID);
        $out = html_writer::tag('h3', $heading, 
            array('class'=>'subformheading'));  
		$out .= '<input type="hidden" id="qID" name="qID" value="'.$this->id.'"/>';
        $out .= '<input type="hidden" id="a" name="a" value="'.$action.'"/>';
        $out .= '<input type="hidden" id="cID" name="cID" value="'.$courseID.'"/>';
        $out .= '<input type="submit" id="all'.$this->id.'" class="all" name="all" value="Select All">';
        $out .= '<input type="submit" id="none'.$this->id.'" class="none" name="none" value="Deselect All">';
        if(!$multiple)
        {
            $out .= '<input type="submit" name="save'.$this->id.'" value="Save">';
        }
        $out .= '<p class="totalPossibleCredits">Total Possible Unit Credits:'.$this->get_current_total_credits().'</p>';
        //TODO put this on the QUALIFICATION so it can be loaded through AJAX???
        if($units || $students)
        {
            $out .= '<table id="btecStudentUnits'.$count.'" class="btecStudentsUnitsTable" align="center">';
            $out .= '<thead><tr><th class="rowOptionsCol" rowspan="1"></th><th class="picCol" rowspan="1">';
            $out .= '</th><th class="usernameCol" rowspan="1">'.get_string('username').'</th>';
            $out .= '<th class="nameCol" rowspan="1">'.get_string('name').'</th>';
            $out .= '<th class="creditsCol" rowspan="1">';
            $out .= get_string('bteccredits', 'block_bcgt');
            $out .= '</th>';
            $out .= '<th rowspan="1" class="rowUserOptionCol"></th>';
            $row = '';
            $lowRow = '<th></th><th></th><th></th><th></th><th></th><th></th>';
            foreach($units AS $unit)
            {
                $row .= '<th>'.$unit->get_uniqueID().' : '.$unit->get_name().
                        ' : '.$unit->get_credits().' '.get_string('bteccredits', 'block_bcgt').
                        '</th>';
                $lowRow .= '<th><a href="edit_students_units.php?qID='.$this->id.'&uID='.$unit->get_id().'" title="Select all Students for this Unit">'.
                        '<img src="'.$CFG->wwwroot.'/blocks/bcgt/images/arrowdown.jpg"'. 
                        'width="25" height="25" class="unitsColumn" id="q'.$this->id.'u'.$unit->get_id().'"/>'.
                        '</a></th>';
            }
            
            $out .= $row.'</tr><tr>'.$lowRow.'</tr>';
            $out .= '</thead><tbody>';
            $forceChecked = false;
            $forceUnChecked = false;
            if(isset($_POST['none']))
            {
                $forceUnChecked = true;
            }
            elseif(isset($_POST['all']))
            {
                $forceChecked = true;
            }
            if(!isset($this->usersstudent))
            {
                //have the users been loaded before?
                //if not it will load up an array that contains
                //a QUAL object for each student
                $loadParams = new stdClass();
                $loadParams->loadLevel = Qualification::LOADLEVELUNITS;
                $loadParams->loadAddUnits = false;
                //load the users and load their qual objects
                $this->load_users('student', true, 
                        $loadParams, $courseID);
            }
            if(isset($this->usersstudent))
            {
                foreach($this->usersstudent AS $student)
                {
                    $studentQual = $this->usersQualsstudent[$student->id];
                    if($studentQual)
                    {
                        if($forceChecked)
                        {
                            //are we forcing the student to be added to all units?
                            $studentQual->add_student_to_all_units();
                        }
                        elseif($forceUnChecked)
                        {
                            //are we taking the student from all units?
                            $studentQual->remove_student_from_all_units();
                        }
                        //GETS all of the units, not just the students units. 
                        //but has in them if student is doing it or not
                        $studentsUnits = $studentQual->get_units();
                    }
                    $out .= '<tr>';
                    $out .= '<td class="rowOptionsCol">'.
                            '<a href="edit_students_units.php?qID='.$this->id.'&sAID='.
                            $student->id.'" id="chq'.$this->id.'s'.$student->id.'" '.
                            'title="Copy this student selection to all in this grid">'.
                            '<img src="'.$CFG->wwwroot.'/blocks/bcgt/images/infinity.jpg"'. 
                            'width="25" height="25" class="studentAll" id="chq'.$this->id.'s'.$student->id.'"/>'.
                            '</td>';
//                    $out .= '<td class="picCol">'.$OUTPUT->user_picture($student, array(1)).'</td>';
                    $out .= '<td></td>';
                    $out .= '<td class="usernameCol">'.$student->username.'</td>';
                    $out .= '<td class="nameCol">'.$student->firstname.' '.$student->lastname.'</td>';
                    $out .= '<td class="creditsCol">';
                    $out .= $studentQual->get_students_total_credits();
                    $out .= '</td>';
                    $out .= '<td class="rowUserOptionCol"><a href="edit_students_units.php?qID='.$this->id.'&sID='.$student->id.'" title="Select all Units for this Student">'.
                            '<img src="'.$CFG->wwwroot.'/blocks/bcgt/images/arrowright.jpg"'. 
                            'width="25" height="25" class="studentRow" id="q'.$this->id.'s'.$student->id.'"/>'.
                            '</a></td>';
                    foreach($studentsUnits AS $unit)
                    {
                        //we need to check if its actually on the qual above though!
                        //i .e. it may have been removed from the qual!
                        $checked = '';
                        if($forceUnChecked)
                        {
                            $checked = '';
                        }
                        elseif($forceChecked || ($unit->is_student_doing() || $unit->is_student_doing() == 'Yes'))
                        {
                            $checked = 'checked="checked"';
                        }
                        $name='q'.$this->id.'S'.$student->id.'U'.$unit->get_id().'';
                        $out .= '<td><input id="chs'.$student->id.'q'.$this->id.'u'.$unit->get_id().'" class="eSU'.$this->id.' chq'.$this->id.'s'.$student->id.' chq'.$this->id.'u'.$unit->get_id().'" type="checkbox" '.$checked.' name="'.$name.'"/></td>';
                    }
                    $out .= '</tr>';
                }
                $out .= '</tbody></table>'; 
            }
            else
            {
                $out .= '</tbody></table>';
                $out .= '<p>This Qualification has no Students attached</p>';
            }
        }
        else
        {
            $out .= '<p>There are currently no Students or Units on this Qualification</p>';
        }
        
        return $out;
    }
    
    private function get_students_units_data()
    {
        $sql = "SELECT distinct(userunit.id) as id, user.id as userid, user.username, 
            user.firstname, user.lastname, userqual.bcgtqualificationid, unit.id as unitid, 
            unit.name as unitname, typeaward.award 
            FROM {user} user
            JOIN {block_bcgt_user_unit} userunit ON userunit.userid = user.id 
            JOIN {block_bcgt_user_qual} userqual ON userqual.userid = user.id 
            AND userqual.bcgtqualificationid = userunit.bcgtqualificationid 
            JOIN {block_bcgt_unit} unit ON unit.id = userunit.bcgtunitid
            WHERE userqual.bcgtqualificationid = ? AND userunit.bcgtqualificationid = ? 
            ORDER BY user.lastname ASC, unit.id ASC";
    }
    
    public function get_edit_single_student_units($currentCount)
    {
        global $CFG;
        //are we saving this one?
        //All or just this one still means this one
        if(isset($_POST['saveAll']) || 
                (isset($_POST['save'.$this->studentID.'q'.$this->id.''])))
        {
            $this->process_edit_single_students_units_page();
        }
        $retval = '';
        $retval .= '<h4 class="singleStudentUnitsHeader">'.$this->get_display_name().'</h4>';
        //id="singleStudentUnits'.$currentCount.'"
        $retval .= '<table id="singleStudentUnits'.$currentCount.'" class="singleStudentUnits" align="center"><thead>'.
                '<tr><th>Credits</th><th></th>';
        $units = $this->get_units();
        if($units)
        {
            foreach($units AS $unit)
            {
                $retval .= '<th>'.$unit->get_uniqueID().' : '.$unit->get_name().
                        ' : '.$unit->get_credits().' '.get_string('bteccredits', 'block_bcgt').
                        '</th>';
            }
        }       
        $retval .= '</tr></thead><tbody><tr>';
        $retval .= '<td>'.$this->get_students_total_credits().'</td>';
        $retval .= '<td class="singleStudentUnitsSelAll"><a href="edit_students_units.php?qID='.$this->id.''.
            '&sID='.$this->studentID.'" title="Select all Units for this Student">'.
            '<img src="'.$CFG->wwwroot.'/blocks/bcgt/images/arrowright.jpg"'. 
            'width="25" height="25" class="studentRow" id="s'.$this->studentID.'q'.$this->id.'"/>'.
            '</a></td>';
        foreach($units AS $unit)
        {
            $checked = '';
            if($unit->is_student_doing() || $unit->is_student_doing() == 'Yes')
            {
                $checked = 'checked="checked"';
            }
            $name='s'.$this->studentID.'U'.$unit->get_id().'Q'.$this->id;
            $retval .= '<td><input id="chs'.$this->studentID.'q'.$this->id.'u'.$unit->get_id().
                    '" class="eSU chs'.$this->studentID.'q'.$this->id.' chu'.$unit->get_id().
                    '" type="checkbox" '.$checked.' name="'.$name.'"/></td>';
        }
        $retval .= '</tr></tbody></table>';
        $retval .= '<input type="submit" name="save'.$this->studentID.'q'.$this->id.'" value="Save"/>';
        
        return $retval;
    }
    
    /**
     * 
     * @param type $studentView
     * @return type
     * Gets the total credits for the student 
     */
    public function get_students_total_credits()
	{
		$totalCredits = 0;
		foreach($this->units AS $unit)
		{
			if($unit->is_student_doing() || $unit->is_student_doing() == 'Yes')
			{
				$totalCredits = $totalCredits + $unit->get_credits();
			}
		}
		return $totalCredits;
	}
    
    /**
	 * Does this qual type have a final award that is given to the student
	 */
	public function has_final_grade()
	{
		return true;
	}
    
    /**
	 * Gets the final grade from the database
	 */
	public function retrieve_student_award()
	{
		$awards = $this->get_students_qual_award();
		if($awards)
		{
            $retval = new stdClass();
            foreach($awards AS $award)
            {
                $params = new stdClass();
                $params->award = $award->targetgrade;
                $params->type = $award->type;
                $params->ucasPoints = $award->ucaspoints;
                $params->unitsScoreUpper = $award->unitsscoreupper;
                $params->unitsScoreLower = $award->unitsscorelower;
                $qualAward = new QualificationAward($award->breakdownid, $params);
                $retval->{$award->type} = $qualAward;
            }
            return $retval;
		}
		return false;
	}
    
    /**
     * Settings: 
     * Show Different Award Options
     * Ability to turn on and off different grid options
     * Ability to select grid symbols. 
     * @return string
     */
    public function get_qual_settings_page()
    {
        
        
        
        return $retval;
    }
    
    /**
	 * Calculate the final award and if its all units
	 * have been awarded then its final
	 */
	public function calculate_final_grade()
	{
		return $this->calculate_qual_award(true);
	}
	
	/**
	 * Calculate the predicted award and if its all units
	 * have been awarded then its final else predicted
	 */
	public function calculate_predicted_grade()
	{
		return $this->calculate_qual_award(false);
	}
    
    public function get_edit_single_student_units_init_call()
    {
        global $PAGE;
        $jsModule = array(
            'name'     => 'mod_bcgtbtec',
            'fullpath' => '/blocks/bcgt/plugins/bcgtbtec/js/bcgtbtec.js',
            'requires' => array('base', 'io', 'node', 'json', 'event', 'button')
        );
        $PAGE->requires->js_init_call('M.mod_bcgtbtec.initsinglestudunits', null, true, $jsModule);
    }
    
    protected function calculate_actual_qual_award($failIfPredicted = false, $pointsPerXCredits = 1)
	{
        $noUnitsAwarded = 0;
		$countAwards = 0;
		$unitPoints = 0;
		$warningCount = 0;
		$totalCredit = 0;
		$creditsAward = 0;
        $unitPointsNoMin = 0;
        $unitPointsNoMax = 0;
        $unitPointsAtMin = 0;
        $unitPointsAtMax = 0;
        //what are the points that each unit can be worth if it was set to the minimum
        //possible grade?
        $unitPointsAtMinRecord = $this->get_min_award_points($this->level->get_id());
        //what are the points that each unit can be worth if it was set to the max
        //possible grade?
        $unitPointsAtMaxRecord = $this->get_max_award_points($this->level->get_id());
        if($unitPointsAtMinRecord)
        {
            $unitPointsAtMin = $unitPointsAtMinRecord->points;
        }
        if($unitPointsAtMaxRecord)
        {
            $unitPointsAtMax = $unitPointsAtMaxRecord->points;
        }
		//Get all of the units that have an award
		//add up all of the points that these awards make
		//count them
		foreach($this->units AS $unit)
		{
			//we want to only count the units a student is 
			//actually doing on the qual
			if($unit->is_student_doing())
			{
				//credits for the unit
				$unitCredit = $unit->get_credits();
				if($unitCredit == null || $unitCredit == 0)
				{
					$unitCredit = 10;
					$warningCount++;
				}
				$totalCredit = $totalCredit + $unitCredit;
				$unitAward = $unit->get_user_award();
				if($unitAward != null && $unitAward->get_id() != '' && $unitAward->get_id() != null && $unitAward->get_id() > 0)
				{
					//does the student have a unit award?
					$creditsAward = $creditsAward + $unitCredit;
					$countAwards++;
					//then the unit has an award
					//get the points that the unit with this award at this level is worth
					$pointsRecord = $this->get_unit_points($unitAward->get_id(), $this->level->get_id());
					if($pointsRecord)
					{
                        $noUnitsAwarded++;
						//lets get the unitPoints.
						//we need to take into consideration the $pointsPerXCredits
						//so divide the unitCredits by points per X credits before we multiply 
						$unitPoints = $unitPoints + ($pointsRecord->points * ($unitCredit/$pointsPerXCredits));
					}
				}
				elseif($failIfPredicted)
				{
					return false;
				}
                else {
                    //we dont have an award. 
                    //so lets add it to those without an award
                    $unitPointsNoMin = $unitPointsNoMin + ($unitPointsAtMin * ($unitCredit/$pointsPerXCredits));
                    $unitPointsNoMax = $unitPointsNoMax + ($unitPointsAtMax * ($unitCredit/$pointsPerXCredits));
                }
			}
		}
		//At this stage we have:
		//UnitPoints = total unitpoints for all units WITH AN AWARD
		//creditsAward = total credits for all units WITH AN AWARD
		//totalCredit = total credits for all units STUDENT IS DOING
        //creditsNoAward = total credits for all units with NO award
        //unitPointsNoMin = total unitpoints for all units with NO award AT PASS level
		//unitPointsNoMax = total unitpoints for all units with NO award AT DISS level
		$type = 'Predicted';
		$predicted = true;
		if($creditsAward == $totalCredit)
		{
			$type = 'Final';
			$predicted = false;
		}
		$averagePoints = 0;
		if($countAwards != 0)
		{
			//this is the average points per credit that the student has an award for
            $averagePoints = $unitPoints/($creditsAward/$pointsPerXCredits);
		}	
        
		//count number of actual units
		//predicted points score = average*totalcredit
		$overallPoints = $averagePoints * $totalCredit;	
        $overallMinPoint = $unitPoints + $unitPointsNoMin;
        $overallMaxPoint = $unitPoints + $unitPointsNoMax;
        
		//Try and get the final award (may not do if we dont have enough
		//points)
        $retval = new stdClass();
        $minUnitAwards = get_config('bcgt', 'btecunitspredgrade');
        $awardRecord = null;
        $retval->averageAward = null;
        if($noUnitsAwarded >= $minUnitAwards)
        {
            $awardRecord = $this->get_final_grade_by_points($overallPoints);
            if($awardRecord)
            {
                $params = new stdClass();
                $params->award = $awardRecord->targetgrade;
                $params->type = $type;
                $params->ucasPoints = $awardRecord->ucaspoints;

                //get the qual award by those points
                $qualAward = new QualificationAward($awardRecord->id, $params);
                if($warningCount != 0)
                {	
                    $qualAward->set_warningCount($warningCount);
                    $qualAward->set_warning("$warningCount units had no credits, assumed 10 credits for calculation");
                }
                //update the students award in the DB
                $this->update_qualification_award($qualAward);
                $retval->averageAward = $qualAward;
            }
        }
		
		$minAward = $this->get_final_grade_by_points($overallMinPoint);
        if($minAward)
        {
            $params = new stdClass();
            $params->award = $minAward->targetgrade;
            $params->type = 'Min';
            $params->ucasPoints = $minAward->ucaspoints;
            
			//get the qual award by those points
			$qualMinAward = new QualificationAward($minAward->id, $params);
			if($warningCount != 0)
			{	
				$qualMinAward->set_warningCount = $warningCount;
				$qualMinAward->set_warning = "$warningCount units had no credits, assumed 10 credits for calculation";
			}
			//update the students award in the DB
			$this->update_qualification_award($qualMinAward);
			$retval->minAward = $qualMinAward; 
        }
        $maxAward = $this->get_final_grade_by_points($overallMaxPoint);
        if($maxAward)
        {
            $params = new stdClass();
            $params->award = $maxAward->targetgrade;
            $params->type = 'Max';
            $params->ucasPoints = $maxAward->ucaspoints;
            
			//get the qual award by those points
			$qualAwardMax = new QualificationAward($maxAward->id, $params);
			if($warningCount != 0)
			{	
				$qualAwardMax->set_warningCount = $warningCount;
				$qualAwardMax->set_warning = "$warningCount units had no credits, assumed 10 credits for calculation";
			}
			//update the students award in the DB
			$this->update_qualification_award($qualAwardMax);
			$retval->maxAward = $qualAwardMax; 
        }
        
        if(!$awardRecord && !$minAward && !$maxAward)
        {
            return false;
        }
        $this->studentAward = $retval;
        return $retval;
	}
    
    /**
	 * Updates the users Qualification 
	 * award in the database with the one passed in
	 * If the user doesnt have an award before then it inserts it
	 * @param unknown_type $award
	 */
	public function update_qualification_award($award)
	{    
        global $DB;
        logAction(LOG_MODULE_GRADETRACKER, LOG_ELEMENT_GRADETRACKER_QUALIFICATION, LOG_VALUE_GRADETRACKER_UPDATED_QUAL_AWARD, $this->studentID, $this->id, null, null, $award->get_id());

		$obj = new stdClass();
		$obj->bcgtqualificationid = $this->id;
		$obj->userid = $this->studentID;
		$obj->bcgtbreakdownid = $award->get_id();
		$obj->type = $award->get_type();
        $obj->warning = "";
		if($award->get_warningCount() && $award->get_warningCount() != 0)
		{
			$obj->warning = $award->get_warning();
		}
		//lets find out if the user has one inserted before?
        $awards = $this->get_students_qual_award($award->get_type());
		if($awards)
		{
            foreach($awards AS $award)
            {
                $id = $award->id;
                $obj->id = $id;
                return $DB->update_record('block_bcgt_user_award', $obj);
            }
			
		}
		else
		{
			return $DB->insert_record('block_bcgt_user_award', $obj);
		}
	}
    
    public function load_qual_criteria_student_info($studentID, $qualID)
	{
		return false;
	}
    
    /**
	 * This calculates the Qualification Award
	 * If all units have been awarded it can calculate the final grade
	 * if only some units have been awarded it MAY be able to calculate 
	 * the predicted/progress award.
	 * Each unit has credits
	 * At each Qualification award level the unit award is worth a set number of points
	 * Each units points are then credits * points
	 * We can get the total unit points for the users units and calculate an 
	 * average per credit.
	 * This can be pushed out to predict the final award by taking the average and
	 * multiplying it by the total credits of the qual
	 * This points value is then looked up in the database and an award MAY be
	 * able to get it.
	 * Its possible if they only have one PASS (lets say a 7) for them
	 * not to be able to get a full award (If a student gets passes at all
	 * units they may not get an award at all)
	 * @param unknown_type $failIfPredicted
	 */
	protected function calculate_qual_award($failIfPredicted = false)
	{		
		return $this->calculate_actual_qual_award($failIfPredicted, 1);
	}
    
    /**
	 * Used to get the type specific title vales and labels.
	 */
	public function get_type_qual_title()
	{
		//At the top of add/remove units from Qual there is certain
		//information that will need to be displayed per qual.
		return get_string('bteccredrequired','block_bcgt').' : '.$this->credits;
	}
    
    public function get_unit_list_type_fields()
	{
		//This is used for the add/remove qualifications page. Different qual
		//types may want to display different information. 
		return '<label for="credits">'.get_string('btectotalcredits','block_bcgt').' : </label>
		<input type="text" name="credits" disabled="disabled" value="'.
                $this->get_current_total_credits().'"/>
		<p class="note">'.get_string('btectotalcrednote','block_bcgt').'</p>';
	}
	
	public function get_current_total_credits()
	{
		//This gets the current total credits that are on the qualification
		//gets credits for all units on the qual. 
		$totalCredits = 0;
		foreach($this->units AS $unit)
		{
			$credits = 0;
			if($unit->get_credits())
			{
				$credits = $unit->get_credits();
			}
			$totalCredits = $totalCredits + $credits;
		}
		return $totalCredits;
	}
    
    public static function get_instance($qualID, $params, $loadParams)
    {   
        if(!$params || !isset($params->level))
        {
            $levelID = optional_param('level', -1, PARAM_INT);
            if(!$params)
            {
                $params = new stdClass();
            }
            if($levelID)
            {
                $level = new Level($levelID);
                $params->level = $level;
            } 
        }
        if(!$params || !isset($params->subtype))
        {
            $subTypeID = optional_param('subtype', -1, PARAM_INT);
            if(!$params)
            {
                $params = new stdClass();
            }
            if($subTypeID)
            {
                $subType = new SubType($subTypeID);
                $params->subType = $subType;
            }
        }

        return new BTECQualification($qualID, $params, $loadParams);
    }
    
    public static function get_pluggin_qual_class($typeID = -1, $qualID = -1, 
            $familyID = -1, $params = null, $loadParams = null)
    {
        global $CFG;
        $subTypeID = -1;
        if($params)
        {

            if($params->subtype)
            {
                $subTypeID = $params->subtype;
            }
        }
        if($subTypeID == -1)
        {
            if(isset($_REQUEST['subtype']))
            {
                $subTypeID = $_REQUEST['subtype'];
            }
        }
        $levelID = -1;
        if($params)
        {
            if($params->level)
            {
                $levelID = $params->level;
            }
        }
        if($levelID == -1)
        {
            if(isset($_REQUEST['level']))
            {
                $levelID = $_REQUEST['level'];
            }
        }
        $params = new stdClass();
        $params->level = new Level($levelID);
        $params->subType = new Subtype($subTypeID);
        switch($levelID)
        {
            case(Level::level1ID):
                require_once($CFG->dirroot.'/blocks/bcgt/plugins/bcgtbtec/classes/BTECLowerQualification.class.php');
                return new BTECLowerQualification($qualID, $params, $loadParams);
                break;
            case(level::level2ID):
            case(level::level3ID):
                require_once('BTECSubType.class.php');
                switch($subTypeID)
                {
                    case(BTECSubType::BTECFndDipID):
                        require_once($CFG->dirroot.'/blocks/bcgt/plugins/bcgtbtec/classes/BTECFoundationQualification.class.php');
                        return new BTECFoundationQualification($qualID, $params, $loadParams);
                        break;
                    default:
                        return new BTECQualification($qualID, $params, $loadParams);
                        break;
                }	
                break;
            case(Level::level4ID):	
            case(Level::level5ID):
                require_once($CFG->dirroot.'/blocks/bcgt/plugins/bcgtbtec/classes/BTECHigherQualification.class.php');
                return new BTECHigherQualification($qualID, $params, $loadParams);
                break;

            default:
                return new BTECQualification($qualID, $params, $loadParams);
                break;
        }
    }
    
    /**
	 * Used to get the credits value from the database
	 * @param $id
	 */
	protected static function retrieve_credits($qualID)
	{
		global $DB;
		$sql = "SELECT credits FROM {block_bcgt_qualification} WHERE id = ?";
		return $DB->get_record_sql($sql, array($qualID));
	}
    
    /**
	 * Gets the students qualification award from the database
	 * @return Found
	 */
	protected function get_students_qual_award($awardType = null)
	{
		global $DB;
		$sql = "SELECT useraward.id as id, useraward.type, 
            breakdown.id AS breakdownid, bcgttargetqualid, 
		targetgrade, ucaspoints, unitsscorelower, unitsscoreupper 
        FROM {block_bcgt_user_award} AS useraward 
		JOIN {block_bcgt_target_breakdown} AS breakdown ON breakdown.id = useraward.bcgtbreakdownid
		WHERE bcgtqualificationid = ?
		AND userid = ?";
        $params = array($this->id, $this->studentID);
        if($awardType)
        {
            $sql .= " AND useraward.type = ?";
            $params[] = $awardType;
        }
		return $DB->get_records_sql($sql, $params);
	}
    
    /**
	 * Gets the points the unit is worth at the Qualification Level
	 * Each unit is worth a set number of points depending on what level its
	 * at and what award its got.
	 * @param unknown_type $bcgtTypeAwardID
	 * @param unknown_type $bcgtLevelID
	 */
	protected function get_unit_points($bcgtTypeAwardID, $bcgtLevelID)
	{
		global $DB;
		$sql = "SELECT * FROM {block_bcgt_unit_points} 
            WHERE bcgtlevelid = ? AND bcgttypeawardid = ?";
		return $DB->get_record_sql($sql, array($bcgtLevelID, $bcgtTypeAwardID));
	}
    
    /**
	 * Gets the points the unit is worth at the Qualification Level
	 * Each unit is worth a set number of points depending on what level its
	 * at and what award its got.
	 * @param unknown_type $bcgtTypeAwardID
	 * @param unknown_type $bcgtLevelID
	 */
	protected function get_min_award_points($bcgtLevelID)
	{
		global $DB;
		$sql = "SELECT points.* FROM {block_bcgt_unit_points} points
            JOIN {block_bcgt_type_award} typeAward ON typeAward.id = points.bcgttypeawardid
            WHERE points.bcgtlevelid = ? AND typeAward.ranking = ?";
		return $DB->get_record_sql($sql, array($bcgtLevelID, 1));
	}

    /**
	 * Gets the points the unit is worth at the Qualification Level
	 * Each unit is worth a set number of points depending on what level its
	 * at and what award its got.
	 * @param unknown_type $bcgtTypeAwardID
	 * @param unknown_type $bcgtLevelID
	 */
	protected function get_max_award_points($bcgtLevelID)
	{
		global $DB;
		$sql = "SELECT points.* FROM {block_bcgt_unit_points} points
            JOIN {block_bcgt_type_award} typeAward ON typeAward.id = points.bcgttypeawardid
            WHERE points.bcgtlevelid = ? AND typeAward.ranking = (SELECT MAX(typeAward.ranking) FROM 
            {block_bcgt_unit_points} points JOIN {block_bcgt_type_award} typeAward ON typeAward.id = 
            points.bcgttypeawardid WHERE points.bcgtlevelid = ?) ORDER BY typeAward.ranking DESC";
		return $DB->get_record_sql($sql, array($bcgtLevelID, $bcgtLevelID), 0, 1);
	}
    
    /**
	 * Gets the final grade from the (qualification award) from the database
	 * based on the points passed down
	 * @param unknown_type $points
	 */
	protected function get_final_grade_by_points($points)
	{
		global $DB;
		$sql = "SELECT breakdown.* FROM {block_bcgt_target_breakdown} breakdown 
		JOIN {block_bcgt_target_qual} targetQual ON targetQual.id = breakdown.bcgttargetqualid 
		JOIN {block_bcgt_qualification} qual ON qual.bcgttargetqualid = targetQual.id 
		WHERE qual.id = ? AND breakdown.unitsscoreupper > ? 
            AND breakdown.unitsscorelower <= ?";
		return $DB->get_record_sql($sql, array($this->id, $points, $points));
	}
    
    /**
     * Gets the final awrad for the qualifiction by the string of that award
     * e.g. Distinction
     * @global type $CFG
     * @param type $award
     * @return type
     */
    protected function get_final_grade_by_award($award)
	{
		global $DB;
		$sql = "SELECT breakdown.* FROM {block_bcgt_target_breakdown} breakdown 
		JOIN {block_bcgt_target_qual} targetQual ON targetQual.id = breakdown.bcgttargetqualid 
		JOIN {block_bcgt_qualification} qual ON qual.bcgttargetqualid = targetQual.id 
		WHERE qual.id = ? AND breakdown.targetgrade = ?";
		return $DB->get_record_sql($sql, array($this->id, $award));
	}
    
    /**
     * 
     * @global type $CFG
     * @global type $DB
     * @return boolean
     */
    protected function get_default_credits()
    {
        global $CFG;
        require_once($CFG->dirroot.'/blocks/bcgt/plugins/bcgtbtec/classes/BTECSubType.class.php');
        global $DB;
        $sql = "SELECT * FROM {block_bcgt_target_qual_att} 
            WHERE bcgttargetqualid = ? AND name = ?";
        $params = array($this->bcgtTargetQualID, BTECSubType::DEFAULTNUMBEROFCREDITSNAME);
        $record = $DB->get_record_sql($sql, $params);
        if($record)
        {
            return $record->value;
        }
        return false;
    }
    
    /**
     * 
     * @global type $DB
     * @param type $type
     * @return type
     */
    protected function get_default_award($type)
    {
        global $DB;
        $sql = "SELECT * FROM {block_bcgt_target_breakdown} WHERE bcgttargetqualid = ? ORDER BY ";
        if($type == 'Min')
        {
            $sql .= "unitsscorelower ASC";
        }
        elseif($type == 'Max')
        {
            $sql .= "unitsscoreupper DESC";
        }
        return $DB->get_records_sql($sql, array($this->bcgtTargetQualID),0, 1);
    }
    
    
    public function print_grid(){
        
        global $CFG;
        
        echo "<!doctype html><html><head>";
        echo "<link rel='stylesheet' type='text/css' href='{$CFG->wwwroot}/blocks/bcgt/print.css'>";
        echo "</head><body>";
                
        echo "<div class='c'>";
            echo "<h1>{$this->get_display_name()}</h1>";
            echo "<h2>".fullname($this->student)." ({$this->student->username})</h2>";

            echo "<br>";
            
            // Key
            echo "<div id='key'>";
                echo BTECQualification::get_grid_key();
            echo "</div>";
            
            echo "<br><br>";
            
            echo "<table id='printGridTable'>";
            
            // Header
            
            //we will reuse the header at the bottom of the table.
            $totalCredits = $this->get_students_total_credits(true);
            //for all of the units on this qual, lets check which crieria names
            //have actually been used. i.e. dont show P17 if no unit has a p17
            $criteriaNames = $this->get_used_criteria_names();

            // Can't sort by ordernum here because could be different between units, can only do this on unit grid
            require_once($CFG->dirroot.'/blocks/bcgt/classes/sorters/CriteriaSorter.class.php');
            $criteriaSorter = new CriteriaSorter();
            usort($criteriaNames, array($criteriaSorter, "ComparisonDelegateByArrayNameLetters"));


            $headerObj = $this->get_grid_header($totalCredits, true, $criteriaNames, 'student', false, true);
            $criteriaCountArray = $headerObj->criteriaCountArray;
            $this->criteriaCount = $criteriaCountArray;
            
            echo $headerObj->header;
            
            $subCriteriaArray = null;
            if ($this->has_sub_criteria()){
                $subCriteriaArray = $this->get_used_sub_criteria_names($criteriaNames);
            }
            
            // Units & Grades
            $units = $this->units;
            $unitSorter = new UnitSorter();
            usort($units, array($unitSorter, "ComparisonDelegateByType"));
            
            $rowCount = 0;
            
            foreach($units AS $unit)
            {

                $loadParams = new stdClass();
                $loadParams->loadLevel = Qualification::LOADLEVELALL;
                $loadParams->loadAward = true;
                $unit->load_student_information($this->student->id, $this->id, $loadParams);
                
                if($unit->is_student_doing())
                {	

                    echo "<tr>";
                    
                    //get the users award from the unit
                    $unitAward = $unit->get_user_award();   
                    $award = '';
                    if($unitAward)
                    {
                        $rank = $unitAward->get_rank();
                        $award = $unitAward->get_award();
                    }	

                    $extraClass = '';
                    if($rowCount == 1)
                    {
                        $extraClass = 'firstRow';
                    }
                    elseif($rowCount == count($units))
                    {
                        $extraClass = 'lastRow';
                    }

                    // Unit Comment
                    //$getComments = $unit->get_comments();

//                    if(!empty($getComments)){
//                        $retval .= "<img src='{$CFG->wwwroot}/blocks/bcgt/plugins/bcgtbtec/pix/grid_symbols/comment-icon.png' class='showCommentsUnit' />";
//                        $retval .= "<div class='tooltipContent'>".nl2br( htmlspecialchars($getComments, ENT_QUOTES) )."</div>";
//                    }
                    
                    echo "<td></td><td></td>";
                                        
                    echo "<td>{$unit->get_name()}<br><small>(".$unit->get_credits()." Credits)</small></td>";

                    
                    echo "<td>{$award}</td>";

                    $criteriaCount = 0;
                    $previousLetter = '';
                    
                    if($criteriaNames)
                    {
                        //if we have found the used criteria names. 
                        foreach($criteriaNames AS $criteriaName)
                        {	
                            
                            
                            $letter = substr($criteriaName, 0, 1);
                            if($previousLetter != '' && $previousLetter != $letter)
                            {
                                echo "<td class='divider'></td>";
                            }
                            $previousLetter = $letter;
                            
                            $criteriaCount++;
                            
                            $studentCriteria = $unit->get_single_criteria(-1, $criteriaName);
                            if($studentCriteria)
                            {	
                                echo "<td>". $this->set_up_criteria_grid($studentCriteria, '', 
                                        null, false, false, false, $unit, 0, $this->student) . "</td>";
                            }
                            else 
                            {         
                                echo "<td class='grid_cell_blank'></td>";
                            }
                        
                            
                            if($this->has_sub_criteria())
                            {
                                //Get the used Sub Criteria Names from the heading for this criteriaName
                                //for example get the p1.1, P1.2 ect for the P1
                                if(array_key_exists($criteriaName, $subCriteriaArray))
                                {
                                    $criteriaSubCriteriasUsed = $subCriteriaArray[$criteriaName];
                                    //Lets see if this Criteria has the subcriteria that matches the heading
                                    $cellCount = count($criteriaSubCriteriasUsed);
                                    $i = 0;
                                    foreach($criteriaSubCriteriasUsed AS $subCriteriaUsed)
                                    {
                                        $firstLast = 0;
                                        $i++;
                                        $extraClass = '';
                                        if($i == 1)
                                        {
                                            $extraClass = 'startSubCrit';
                                            if(count($criteriaSubCriteriasUsed) == 1)
                                            {
                                                $extraClass .= " endSubCrit";
                                            }
                                            $firstLast = 1;
                                        }
                                        elseif($i == $cellCount)
                                        {
                                            $extraClass = 'endSubCrit';
                                            $firstLast = -1;
                                        }
                                        $criteriaCount++;
                                        $actualSubCriteria = $studentCriteria->get_single_criteria(-1, $subCriteriaUsed);
                                        if($actualSubCriteria)
                                        {
                                            //then create the grid
                                            echo "<td>".$this->set_up_criteria_grid($actualSubCriteria, 
                                                    $extraClass.' subCriteria subCriteria_'.$criteriaName, 
                                                    null, false, false, 
                                                    true, $unit, $firstLast, $this->student)."</td>";
                                        }
                                        else
                                        {
                                            echo "<td class='grid_cell_blank'></td>";
                                        }//end else not actualSubCriteria
                                    }//end loop sub Criteria
                                }
                            
                            }
                            
                            //if its the student view then lets print
                            //out the students unformation
                            
                        }
                            
                    }

                    echo "</tr>";
                    
                }

            }

            
            
            
            echo "</table>";
            echo "</div>";
            
            //echo "<br class='page_break'>";
            
            // Comments and stuff
            // TODO at some point
            
        
        echo "</body></html>";
        
    }
    
//    protected function load_target_grades($orderBY = '')
//    {
//        global $DB;
//		$sql = "SELECT * FROM {block_bcgt_target_breakdown} 
//		WHERE bcgttargetqualid = ? ORDER BY ";
//        if($orderBY == '')
//        {
//            $sql .= 'ranking ASC';
//        }
//        else
//        {
//            $sql .= $orderBY;
//        }
//        $params = array($this->bcgtTargetQualID);
//		$targetGrades = $DB->get_records_sql($sql, $params);
//        foreach($targetGrades AS $targetGrade)
//        {
//            $targetGrade->grade = $targetGrade->targetgrade;
//        }
//        $this->targetGrades = $targetGrades;
//    }
    
    
}

?>
