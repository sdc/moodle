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
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/Level.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/SubType.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/Unit.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/Criteria.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/Task.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/Grade.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/Pathway.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/PathwayType.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/Project.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/Breakdown.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/TargetGrade.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/UserCourseTarget.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/UserPriorLearning.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/EntryQual.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/EntryGrade.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/QualWeighting.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/Import.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/UserDataImport.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/Subject.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/Range.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/Reporting.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/UnitTests.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/classes/core/UserCalculations.class.php');
require_once($CFG->dirroot.'/blocks/bcgt/bclib.php');

define('BCGT_NUMBER_CORE_DASH_TABS', 11);
define('BCGT_COURSE_TAB_NUMBER', 2);
define('BCGT_ADMIN_TAB_NUMBER', 9);

/**
 * 
 * @global type $PAGE
 * @param type $uI
 * @param type $simpleLoad - This is for loading it up within other ajax stuff, like the elbp
 */
function load_javascript($uI = false, $simpleLoad = false)
{
    global $CFG, $PAGE;
    
    $output = "";
        
    if(!get_config('bcgt', 'themejquery'))
    {
        if ($simpleLoad){
            $output .= "<script type='text/javascript' src='{$CFG->wwwroot}/blocks/bcgt/js/jquery-1.9.1.js'></script>";
        } else {
            $PAGE->requires->js( new moodle_url('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js') );
            #$PAGE->requires->js('/blocks/bcgt/js/jquery-1.9.1.js');
        }
    }
    else
    {
        $location = get_config('bcgt', 'themejqueryloc');
        if ($simpleLoad){
            $output .= "<script type='text/javascript' src='{$CFG->wwwroot}/{$location}'></script>";
        } else {
            $PAGE->requires->js(''.$location.'');
        }
    }
    
    if($uI)
    {
        if ($simpleLoad){
            $output .= "<script type='text/javascript' src='{$CFG->wwwroot}/blocks/bcgt/js/jquery-ui.js'></script>";
        } else {
            $PAGE->requires->js( new moodle_url('http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/jquery-ui.min.js') );
            #$PAGE->requires->js('/blocks/bcgt/js/jquery-ui.js');
        }
    }
    
    if ($simpleLoad){
        $output .= "<script type='text/javascript' src='{$CFG->wwwroot}/blocks/bcgt/js/block_bcgt_functions.js'></script>";
        $output .= "<script type='text/javascript' src='{$CFG->wwwroot}/blocks/bcgt/js/jquery.dataTables.js'></script>";
        $output .= "<script type='text/javascript' src='{$CFG->wwwroot}/blocks/bcgt/js/FixedColumns.stable.js'></script>";
        $output .= "<script type='text/javascript' src='{$CFG->wwwroot}/blocks/bcgt/js/FixedHeader.js'></script>";
        $output .= "<script type='text/javascript' src='{$CFG->wwwroot}/blocks/bcgt/js/jquery.ui.touch-punch.min.js'></script>";
        $output .= "<script type='text/javascript' src='{$CFG->wwwroot}/blocks/bcgt/js/jquery.doubleScroll.js'></script>";
        $output .= "<script type='text/javascript' src='{$CFG->wwwroot}/blocks/bcgt/js/jquery.fixedheadertable.min.js'></script>";
    } else {
        $PAGE->requires->js('/blocks/bcgt/js/block_bcgt_functions.js');
        $PAGE->requires->js('/blocks/bcgt/js/jquery.dataTables.js');
        $PAGE->requires->js('/blocks/bcgt/js/FixedColumns.stable.js');
        $PAGE->requires->js('/blocks/bcgt/js/FixedHeader.js'); 
        $PAGE->requires->js('/blocks/bcgt/js/jquery.ui.touch-punch.min.js');
        $PAGE->requires->js('/blocks/bcgt/js/jquery.doubleScroll.js');
        $PAGE->requires->js('/blocks/bcgt/js/jquery.fixedheadertable.min.js');
    }
    
    return $output;
    
}


function load_css($ui = false, $simple = false)
{
    
    global $CFG, $PAGE;
    
    $output = "";
    
    if ($simple)
    {
        $output .= "<link rel='stylesheet' type='text/css' href='{$CFG->wwwroot}/blocks/bcgt/js/jquery.fixedtableheader.defaulttheme.css' />";
        $output .= "<link rel='stylesheet' type='text/css' href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/redmond/jquery-ui.min.css' />";
    }
    else
    {
        $PAGE->requires->css('/blocks/bcgt/js/jquery.fixedtableheader.defaulttheme.css');
        $PAGE->requires->css( new moodle_url('http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/redmond/jquery-ui.min.css') );
    }
    
    return $output;
    
}


function get_qualification_type_families_used($familyID = -1, $excludeBespoke = false)
{
	global $DB;
	$sql = "SELECT distinct(family.id), family.family
    FROM {block_bcgt_type_family} AS family";
//	WHERE type.id IN (SELECT distinct(bcgttypeid) FROM {block_bcgt_target_qual})";
    $params = array();
    if($familyID != -1 || $excludeBespoke)
    {
        $sql .= " WHERE ";
    }
    $and = false;
	if($familyID != -1)
	{
		$sql .= " family.id = ?";
        $and = true;
        $params[] = $familyID;
	}
    if($excludeBespoke)
    {
        if($and)
        {
            $sql .= ' AND ';
        }
        $sql .= "family.family <> ?";
        $params[] = 'Bespoke';
        $and = true;
    }
	return $DB->get_records_sql($sql, $params);
}

/**
 * 
 * @global type $DB
 * @param type $id
 * @param type $sortOrder
 * @param type $ascDesc
 * @param type $typeID
 * @param type $levelID
 * @param type $subtypeID
 * @return type
 */
function get_qualification_targets($id = -1, $sortOrder = '', 
        $typeID = -1, $levelID = -1, $subtypeID = -1, $excludingBespoke = false)
{
    global $DB;
	$sql = "SELECT qual.id, level.id AS levelid, level.trackinglevel, type.id AS typeid, 
	type.type, subtype.id AS subtypeid, subtype.subtype, qual.id as bcgttargetqualid, countquals, family.family 
	FROM {block_bcgt_target_qual} qual 
	JOIN {block_bcgt_level} level ON level.id = qual.bcgtlevelid
	JOIN {block_bcgt_type} type ON type.id = qual.bcgttypeid
	JOIN {block_bcgt_subtype} subtype ON subtype.id = qual.bcgtsubtypeid
    JOIN {block_bcgt_type_family} family ON family.id = type.bcgttypefamilyid 
	LEFT OUTER JOIN 
	(
		SELECT bcgttargetqualid, count(bcgttargetqualid) AS countquals FROM {block_bcgt_qualification} 
		GROUP BY bcgttargetqualid
	) qualification ON qualification.bcgttargetqualid = qual.id 
	";
    $params = array();
	if($id != -1 || $typeID != -1 || $levelID != -1 || $subtypeID != -1 || $excludingBespoke)
	{
		$and = false;
		$sql .= " WHERE"; 
		if($id != -1)
		{
			$sql .= " qual.id = ?";
            $params[] = $id;
			$and = true;	
		}
		if($typeID != -1)
		{
			if($and)
			{
				$sql .= " AND";
			}
			$sql .= " type.id = ?";
            $params[] = $typeID;
			$and = true;
		}
		if($subtypeID != -1)
		{
			if($and)
			{
				$sql .= " AND";
			}
			$sql .= " subtype.id = ?";
            $params[] = $subtypeID;
			$and = true; 
		}
		if($levelID != -1)
		{
			if($and)
			{
				$sql .= " AND";
			}
			$sql .= " level.id = ?";
            $params[] = $levelID;
			$and = true;
		}
        if($excludingBespoke)
        {
            if($and)
            {
                $sql .= " AND ";
            }
            $sql .= " family.family <> ?";
            $params[] = 'Bespoke';
            $and = true;
        }
	}
	if($sortOrder != '')
	{
		$sql .= " ORDER BY $sortOrder";
	}
        	
	if($id != -1)
	{
		return $DB->get_record_sql($sql, $params);
	}
	return $DB->get_records_sql($sql, $params);
}

/**
 * 
 * @global type $DB
 * @param type $id
 * @return type
 */
function get_qualification_breakdown_by_targetQual($id)
{
	global $DB;
	$sql = "SELECT * FROM {block_bcgt_target_breakdown}  
	WHERE bcgttargetqualid = $id ORDER BY ranking DESC, unitsscoreupper DESC, ucaspoints DESC";

	return $DB->get_records_sql($sql);
}

/**
 * 
 * @global type $DB
 * @param type $id
 * @return type
 */
function get_qualification_grades_by_targetQual($id)
{
    global $DB;
	$sql = "SELECT * FROM {block_bcgt_target_grades}  
	WHERE bcgttargetqualid = $id ORDER BY ranking DESC, upperscore DESC, ucaspoints DESC";

	return $DB->get_records_sql($sql);
}

/**
 * Gets the database units for a qual
 * @global type $DB
 * @param type $qualID
 * @return type
 */
function get_qualification_units($qualID)
{
    global $DB;
    $sql = "SELECT unit.* FROM {block_bcgt_unit} unit 
        JOIN {block_bcgt_qual_units} qualunits ON qualunits.bcgtunitid = unit.id 
        WHERE qualunits.bcgtqualificationid = ?";
    return $DB->get_records_sql($sql, array($qualID));
}

/**
 * This gets the levels that have been added in
 * block_bcgt_target_qual for the the type passed
 * @param $typeID
 */
function get_level_from_type($typeID = -1, $familyID = -1, $subTypeID = -1)
{
	global $DB;
	$sql = "SELECT DISTINCT(level.id), level.trackinglevel FROM {block_bcgt_level} AS level 
	JOIN {block_bcgt_target_qual} AS targetqual ON targetqual.bcgtlevelid = level.id
	JOIN {block_bcgt_type} AS type ON type.id = targetqual.bcgttypeid";
	$params = array();
	if($typeID != -1 || $familyID != -1 || $subTypeID != -1)
	{
		$sql .= " WHERE"; 
	}
	$andUsed = false;
	if($typeID != -1)
	{
		$sql .= " targetqual.bcgttypeid = ?";
        $params[] = $typeID;
		$andUsed = true;
	}
	elseif($familyID != -1)
	{
		$sql .= " type.bcgttypefamilyid = ?";
        $params[] = $familyID;
		$andUsed = true;
	}
	if($subTypeID != -1)
	{
		if($andUsed)
		{
			$sql .= " AND";
		}
		$sql .= " targetqual.bcgtsubtypeid = ?";
        $params[] = $subTypeID;
	}
    $sql .= " ORDER BY level.trackinglevel ASC";
	$levels = $DB->get_records_sql($sql, $params);
	
	$levelsArray = array();
	if($levels)
	{
		if(count($levels) == 1)
		{
			$level = end($levels);
			$levelObj = new Level($level->id, $level->trackinglevel);
			$levelsArray[] = $levelObj;
		}
		else
		{
			foreach($levels AS $level)
			{
				$levelObj = new Level($level->id, $level->trackinglevel);	
				$levelsArray[$level->id] = $levelObj;
			}
		}
	}
	return $levelsArray;
}

/**
 * This gets the subtypes that have been added in
 * block_bcgt_target_qual for the the type passed in and the level passed in
 * @param $typeID
 */
function get_subtype_from_type($typeID = -1, $levelID = -1, $familyID = -1)
{
	global $DB;
	$sql = "SELECT DISTINCT(subtype.id), subtype.subtype FROM {block_bcgt_subtype} AS subtype 
	JOIN {block_bcgt_target_qual} AS targetqual ON targetqual.bcgtsubtypeid = subtype.id
	JOIN {block_bcgt_type} AS type ON type.id = targetqual.bcgttypeid";
    $params = array();
	if($typeID != -1 || $levelID != -1 || $familyID != -1)
	{
		$sql .= " WHERE";
	}
	$andUsed = false;
	if($typeID != -1)
	{
		$sql .= " targetqual.bcgttypeid = ?";
		$andUsed = true;
        $params[] = $typeID;
	}
	elseif($familyID != -1)
	{
		$sql .= " type.bcgttypefamilyid = ?";
		$andUsed = true;
        $params[] = $familyID;
	}
	if($levelID != -1)
	{
		if($andUsed)
		{
			$sql .= " AND";
		}
		$sql .= " targetqual.bcgtlevelid = ?";
        $params[] = $levelID;
	}
    $sql .= " ORDER BY subtype.subtype ASC";
	$subTypes = $DB->get_records_sql($sql, $params);
	
	$subTypesArray = array();
	if($subTypes)
	{
		if(count($subTypes) == 1)
		{
			$subType = end($subTypes);
			$subTypeObj = new SubType($subType->id, $subType->subtype);
			$subTypesArray[] = $subTypeObj;
		}
		else
		{
			foreach($subTypes AS $subType)
			{
				$subTypeObj = new SubType($subType->id, $subType->subtype);	
				$subTypesArray[$subType->id] = $subTypeObj;
			}
		}
	}
	return $subTypesArray;
}

function get_pathway_from_type($familyID)
{
    
    global $DB;
    
    $sql = "SELECT p.*
            FROM {block_bcgt_pathway_dep} p
            WHERE p.bcgttypefamilyid = ?";
    
    $params = array($familyID);
    
    $records = $DB->get_records_sql($sql, $params);
    $results = array();
    if ($records)
    {
        foreach($records as $record)
        {
            $results[$record->id] = $record->pathway;
        }
    }
    
    return $results;
    
}

function get_pathway_types_from_pathway($pathwayID){
    
    global $DB;
    
    $sql = "SELECT t.*, dt.id as dtid
            FROM {block_bcgt_pathway_type} t
            INNER JOIN {block_bcgt_pathway_dep_type} dt ON dt.bcgtpathwaytypeid = t.id
            WHERE dt.bcgtpathwaydepid = ?";
    $records = $DB->get_records_sql($sql, array($pathwayID));
   
    $results = array();
    if ($records)
    {
        foreach($records as $record)
        {
            $results[$record->dtid] = $record->pathwaytype;
        }
    }
    
    return $results;
    
}

function get_pathway_subtypes_from_type($pathwayTypeID){
    
    global $DB;
    
    $sql = "SELECT s.*
            FROM {block_bcgt_subtype} s
            INNER JOIN {block_bcgt_pathway_subtype} ps ON ps.bcgtsubtypeid = s.id
            WHERE ps.bcgtpathwaydeptypeid = ?";
    $records = $DB->get_records_sql($sql, array($pathwayTypeID));
   
    $results = array();
    if ($records)
    {
        foreach($records as $record)
        {
            $results[$record->id] = $record->subtype;
        }
    }
    
    return $results;
    
}

function get_pathway_dep_type_from_both($pathway, $type)
{
    global $DB;

    $record = $DB->get_record("block_bcgt_pathway_dep_type", array("bcgtpathwaydepid" => $pathway, "bcgtpathwaytypeid" => $type));
    return ($record) ? $record->id : null;
}

function get_pathway_and_type_from_dep_type($pathway){
    global $DB;
    return $DB->get_record_sql("SELECT dt.id, p.id as pathway, t.id as type
                                    FROM {block_bcgt_pathway_dep_type} dt
                                    INNER JOIN {block_bcgt_pathway_dep} p ON p.id = dt.bcgtpathwaydepid
                                    INNER JOIN {block_bcgt_pathway_type} t ON t.id = dt.bcgtpathwaytypeid
                                    WHERE dt.id = ?", array($pathway));
}

/**
 * Returns the qualification level as specfied by the levelID. 
 * If level id is -1 (or not passed in) then all qualification level are returned
 * @param $levelID
 */
function get_qualification_level($levelID = -1)
{
	global $DB;
	$sql = "SELECT * FROM {block_bcgt_level} AS level";
	if($levelID != -1)
	{
		$sql .= " WHERE level.id = ?";	
	}
	
	$levels = $DB->get_records_sql($sql, array($levelID));
	$levelsArray = array();
	if($levels)
	{
		if(count($levels) == 1)
		{
			$level = end($levels);
			$levelObj = new Level($level->id, $level->trackinglevel);
			return $levelObj;
		}
		foreach($levels AS $level)
		{
			$levelObj = new Level($level->id, $level->trackinglevel);
			$levelsArray[] = $levelObj;
		}
	}
	return $levelsArray;
}

/**
 * Returns the qualification subtype as specfied by the subtypeID. 
 * If subtype id is -1 (or not passed in) then all qualification subtypes are returned
 * @param $subtypeID
 */
function get_qualification_subtype($subtypeID = -1)
{
	global $DB;
	$sql = "SELECT * FROM {block_bcgt_subtype} AS subtype";
	if($subtypeID != -1)
	{
		$sql .= " WHERE subtype.id = ?";	
	}
	
	$subTypes = $DB->get_records_sql($sql, array($subtypeID));
	
	$subTypesArray = array();
	if($subTypes)
	{
		if(count($subTypes) == 1)
		{
			$subType = end($subTypes);
			$subTypeObj = new SubType($subType->id, $subType->subtype);
			return $subTypeObj;
		}
		foreach($subTypes AS $subType)
		{
			$subTypeObj = new SubType($subType->id, $subType->subtype);	
			$subTypesArray[$subType->id] = $subTypeObj;
		}
	}
	return $subTypesArray;
}

/**
 * 
 * @global type $DB
 * @param type $typeID
 * @param type $levelID
 * @param type $subTypeID
 * @param type $search
 * @param type $familyID
 * @param type $notIN
 * @param type $courseID
 * @param type $onCourse
 * @param type $hasStudents
 * @param type $excludeFamilies is an array of string family names
 * @return type
 */
function search_qualification($typeID = -1, $levelID = -1, $subTypeID = -1, $search = '', 
        $familyID = -1, $notIN = null, $courseID = -1, $onCourse = false, $hasStudents = false, 
        $excludeFamilies = array(), $editableByUserID = -1)
{	
	$and = false;
	global $DB; 
	$sql = "
	SELECT distinct(qual.id) AS id, type.type, level.id AS levelid, level.trackinglevel, 
    subtype.subtype, subtype.subtypeshort, qual.name, qual.additionalname,  
	qualunits.countunits, coursequal.countcourse, family.family 
	FROM {block_bcgt_qualification} AS qual 
	JOIN {block_bcgt_target_qual} AS targetQual ON targetQual.id = qual.bcgttargetqualid 
	JOIN {block_bcgt_type} AS type ON type.id = targetQual.bcgttypeid 
	JOIN {block_bcgt_subtype} AS subtype ON subtype.id = targetQual.bcgtsubtypeid 
	JOIN {block_bcgt_level} AS level ON level.id = targetQual.bcgtlevelid
    JOIN {block_bcgt_type_family} AS family ON family.id = type.bcgttypefamilyid
	LEFT OUTER JOIN 
	(
	 SELECT bcgtqualificationid, COUNT(bcgtunitid) AS countunits
	 FROM {block_bcgt_qual_units}
	 group by bcgtqualificationid
	) AS qualunits ON qualunits.bcgtqualificationid = qual.id
	LEFT OUTER JOIN 
	(
	 SELECT bcgtqualificationid, COUNT(courseid) AS countcourse 
	 FROM {block_bcgt_course_qual} AS coursequal
	 GROUP BY bcgtqualificationid
	) AS coursequal ON coursequal.bcgtqualificationid = qual.id";
    if($hasStudents)
    {
        $sql .= " JOIN {block_bcgt_user_qual} userqual 
            ON userqual.bcgtqualificationid = qual.id 
            JOIN {role} role ON role.id = userqual.roleid AND role.shortname = ?";
    }
    if($editableByUserID != -1)
    {
        //then we want to check coursequal
        //course, context etc for the quals they can edit
        $sql .= " JOIN {block_bcgt_course_qual} teachcoursequal 
            JOIN {context} context ON context.instanceid = teachcoursequal.courseid 
            JOIN {role_assignments} roleass ON roleass.contextid = context.id 
            JOIN {role} role ON role.id = roleass.roleid";
    }
	$params = array();
    if($hasStudents)
    {
        $params[] = 'student';
    }
	if($typeID != -1 || $levelID != -1 || $subTypeID != -1 || $search != '' 
            || $familyID != -1 || $notIN || $courseID != -1 || count($excludeFamilies) != 0 
            || $editableByUserID != -1)
	{
		//then we are searching
		$sql .= " WHERE";
		
		if($typeID != -1)
		{
			$sql .= " type.id = ?";
			$and = true;
            $params[] = $typeID;
		}
		if($levelID != -1)
		{
			if($and)
			{
				$sql .= " AND";
			}
			$sql .= " level.id = ?";	
			$and = true;
            $params[] = $levelID;
		}
		if($subTypeID != -1)
		{
			if($and)
			{
				$sql .= ' AND';
			}
			$sql .= " subtype.id = ?";
			$and = true;
            $params[] = $subTypeID;
		}
		if($search != '')
		{
			if($and)
			{
				$sql .= ' AND';
			}
			$sql .= " qual.name LIKE ?";
			$and = true;
            $params[] = '%'.$search.'%';
		}
		if($familyID != -1)
		{
			if($and)
			{
				$sql .= ' AND';
			}
			$sql .= " type.bcgttypefamilyid = ?";
            $params[] = $familyID;
            $and = true;
		}
        if($notIN)
        {
            if($and)
            {
                $sql  .= ' AND';
            }
            $sql .= " qual.id NOT IN (";
            $count = 0;
            foreach($notIN AS $qualID)
            {
                $count++;
                if($count != 1)
                {
                    $sql .= ',';
                }
                $sql .= "?";
                $params[] = $qualID;
            }
            $sql .= ")";
            $and = true;
        }
        if($courseID != -1)
        {
            if($and)
            {
                $sql .= ' AND';
            }
            $sql .= ' qual.id';
            if($onCourse)
            {
                $sql .= ' IN';
            }
            else
            {
               $sql .= ' NOT IN'; 
            }
            $sql .= ' (SELECT bcgtqualificationid FROM {block_bcgt_course_qual} WHERE courseid = ?)';
            $params[] = $courseID;
            $and = true;
        }
        if($excludeFamilies && count($excludeFamilies) != 0)
        {
            if($and)
            {
                $sql .= ' AND';
            }
            $count = 0;
            foreach($excludeFamilies AS $family)
            {
                $count++;
                if($count != 1)
                {
                    $sql .= ' AND';
                }
                $sql .= ' family.family != ?';
                $params[] = $family;
            }
            $and = true;
        }
        if($editableByUserID != -1)
        {
            //then we want to check coursequal
            //course, context etc for the quals they can edit
            if($and)
            {
                $sql .= ' AND';
            }
            $and = true;
            $sql .= ' role.shortname = ? AND roleass.userid = ?';
            $params[] = 'editingteacher';
            $params[] = $editableByUserID;
        }
	}
	$sql .= " ORDER BY family.family ASC, level.trackinglevel ASC, subtype.subtype ASC, qual.name ASC";
    $records = $DB->get_records_sql($sql, $params);
    // Bespoke cannot be done like this, there are no types, levels, etc... to join
    // Search bespoke quals as well
    if ($familyID == 1 || $familyID == -1){
                
        $sql = "SELECT q.id, b.displaytype, b.subtype, b.level, q.name, q.additionalname, 1 as isbespoke, qualunits.countunits, coursequal.countcourse
                FROM {block_bcgt_bespoke_qual} b
                INNER JOIN {block_bcgt_qualification} q ON q.id = b.bcgtqualid
                LEFT OUTER JOIN 
                (
                    SELECT bcgtqualificationid, COUNT(bcgtunitid) AS countunits
                    FROM {block_bcgt_qual_units}
                    group by bcgtqualificationid
                ) AS qualunits ON qualunits.bcgtqualificationid = q.id
                LEFT OUTER JOIN 
                (
                    SELECT bcgtqualificationid, COUNT(courseid) AS countcourse 
                    FROM {block_bcgt_course_qual} AS coursequal
                    GROUP BY bcgtqualificationid
                ) AS coursequal ON coursequal.bcgtqualificationid = q.id
                WHERE q.name LIKE ? OR b.displaytype LIKE ? OR b.subtype LIKE ? 
                ORDER BY b.displaytype ASC, b.level ASC, b.subtype ASC, q.name ASC";
        $bespoke = $DB->get_records_sql($sql, array('%'.$search.'%', '%'.$search.'%', '%'.$search.'%'));
                        
        if ($bespoke)
        {
            foreach($bespoke as $bspk)
            {
                if (!isset($records[$bspk->id])){
                    $bspk->trackinglevel = '';
                    $bspk->family = '';
                    $records[$bspk->id] = $bspk;
                }
            }
        }
        
    }
        
    return $records;
}

function bcgt_search_system($qualID = -1, $courseID = -1, $search = '', $searchParams = '', $qualExcludes = array())
{
    global $DB;
    $sql = '';
    $params = array();
    return $DB->get_records_sql($sql, $params);
}

function bcgt_get_courses_with_quals($qualID = -1, $excludeFamilies = array(), $courseSearch = '')
{
    $and = false;
    global $DB;
    $sql = "SELECT distinct(course.id), course.* FROM {course} course 
        JOIN {block_bcgt_course_qual} coursequal ON coursequal.courseid = course.id";
    if($excludeFamilies && count($excludeFamilies) != 0)
    {
        $sql .= ' JOIN {block_bcgt_qualification} qual ON qual.id = coursequal.bcgtqualificationid 
            JOIN {block_bcgt_target_qual} targetqual ON targetqual.id = qual.bcgttargetqualid 
            JOIN {block_bcgt_type} type ON type.id = targetqual.bcgttypeid 
            JOIN {block_bcgt_type_family} family ON family.id = type.bcgttypefamilyid';
    }
    $params = array();
    if($qualID != -1 || ($excludeFamilies && count($excludeFamilies) != 0) || $courseSearch != '')
    {
        $sql .= ' WHERE';
    }
    if($qualID != -1)
    {
        $sql .= " coursequal.bcgtqualificationid = ?";
        $params[] = $qualID;
        $and = true;
    }
    if($excludeFamilies && count($excludeFamilies) != 0)
    {
        if($and)
        {
            $sql .= ' AND';
        }
        $count = 0;
        foreach($excludeFamilies AS $family)
        {
            $count++;
            if($count != 1)
            {
                $sql .= ' AND';
            }
            $sql .= ' family.family != ?';
            $params[] = $family;
        }
        $sql .= '';
        $and = true;
        
    }
    if($courseSearch != '')
    {
        if($and)
        {
            $sql .= ' AND';
        }
        $sql .= '(';
        $sql .= ' course.shortname LIKE ? OR course.shortname LIKE ?';
        $params[] = '%'.$courseSearch.'%';
        $params[] = '%'.$courseSearch.'%';
        $coursesSearches = explode(" ", $courseSearch);
        if($coursesSearches)
        {
            foreach($coursesSearches AS $search)
            {
                $sql .= ' OR course.shortname LIKE ? OR course.shortname LIKE ?';
                $params[] = '%'.$search.'%';
                $params[] = '%'.$search.'%';
            }
        }
        $sql .= ')';
        $and = true;
    }
    return $DB->get_records_sql($sql, $params);
}

/**
 * 
 * @global type $DB
 * @param type $unitTypeID
 * @param type $qualID
 * @param type $search
 * @param type $levelID
 * @param type $subTypeID
 * @param type $in
 * @param type $qualTypeID
 * @param type $uniqueID
 * @param type $name
 * @param type $unitFamilyID
 * @param type $qualFamilyID
 * @param type $unitLevelID
 * @param type $qualIDExclude
 * @param type $qualSearch
 * @return type
 */
function search_unit($unitTypeID = -1, $qualID = -1, $search = '', $levelID = -1, 
        $subTypeID = -1, $in = '', $qualTypeID = -1, 
        $uniqueID = '', $name = '', $unitFamilyID = -1, $qualFamilyID = -1, 
        $unitLevelID = -1, $qualIDExclude = -1, $qualSearch = '')
{
	$and = false;
	
	global $DB;
	$sql = "SELECT DISTINCT(unit.id), unit.*, unitLevel.trackinglevel as unitlevel, 
        unitLevel.id AS unitlevelid, unitFamily.family 
        FROM {block_bcgt_unit} AS unit 
	LEFT OUTER JOIN {block_bcgt_qual_units} AS qualUnits ON qualUnits.bcgtunitid = unit.id 
	LEFT OUTER JOIN {block_bcgt_qualification} AS qual ON qual.id = qualUnits.bcgtqualificationid 
	LEFT OUTER JOIN {block_bcgt_target_qual} AS targetQual ON targetQual.id = qual.bcgttargetqualid 
	LEFT OUTER JOIN {block_bcgt_type} AS type ON type.id = targetQual.bcgttypeid 
	LEFT OUTER JOIN {block_bcgt_subtype} AS subtype ON subtype.id = targetQual.bcgtsubtypeid 
	LEFT OUTER JOIN {block_bcgt_level} AS level ON level.id = targetQual.bcgtlevelid 
	LEFT OUTER JOIN {block_bcgt_type} AS unitType ON unitType.id = unit.bcgttypeid
	LEFT OUTER JOIN {block_bcgt_level} AS unitLevel on unitLevel.id = unit.bcgtlevelid
    LEFT OUTER JOIN {block_bcgt_type_family} AS unitFamily ON unitFamily.id = unitType.bcgttypefamilyid ";
    $params = array();
    if($unitTypeID != -1 || $qualID != -1 || $levelID != -1 || 
	$subTypeID != -1 || $search != '' || 
	$in != '' || $qualTypeID != -1 || $uniqueID != '' || 
	$name != '' || $unitFamilyID != -1 || $qualFamilyID != -1 || $unitLevelID != -1 
            || $qualIDExclude != -1 || $qualSearch != '')
	{
		//then we are searching
		$sql .= " WHERE";
		
		if($unitTypeID != -1)
		{
			$sql .= " unit.bcgttypeid = ?";
			$and = true;
            $params[] = $unitTypeID;
		}
		if($qualID != -1)
		{
			if($and)
			{
				$sql .= " AND";
			}
			$sql .= "qual.id = ?";
			$and = true;
            $params[] = $qualID;
		}
		if($levelID != -1)
		{
			if($and)
			{
				$sql .= " AND";
			}
			$sql .= " level.id = ?";	
			$and = true;
            $params[] = $levelID;
		}
		if($subTypeID != -1)
		{
			if($and)
			{
				$sql .= ' AND';
			}
			$sql .= " subtype.id = ?";
			$and = true;
            $params[] = $subTypeID;
		}
		if($search != '')
		{
			if($and)
			{
				$sql .= ' AND';
			}
			$sql .= " (unit.name LIKE ? OR unit.uniqueid LIKE ? OR unit.details LIKE ?)";
			$and = true;
            $params[] = '%'.$search.'%';
            $params[] = '%'.$search.'%';
            $params[] = '%'.$search.'%';
		}	
		if($in != '')
		{
			if($and)
			{
				$sql .= ' AND';
			}
			$sql .= " unit.id NOT IN (";
            $inSplit = explode(',', $in);
            $count = 0;
            foreach($inSplit AS $split)
            {
                $count++;
                if($count != 1)
                {
                    $sql .= ',';
                }
                $sql .= '?';
                $params[] = $split;
            }
			$and = true;
            $sql .= ')';
		}
		if($qualTypeID != -1)
		{
			if($and)
			{
				$sql .= ' AND';
			}
			$sql .= " type.id = ?";
			$and = true;
            $params[] = $qualTypeID;
		}
		if($uniqueID != '')
		{
			if($and)
			{
				$sql .= ' AND';
			}
			$sql .= " unit.uniqueid = ?";
			$and = true;
            $params[] = $uniqueID;
		}
		if($name != '')
		{
			if($and)
			{
				$sql .= ' AND';
			}
			$sql .= " units.name = ?";
			$and = true;
            $params[] = $name;
		}
		if($qualFamilyID != -1)
		{
			if($and)
			{
				$sql .= ' AND';
			}
			$sql .= " type.bcgttypefamilyid = ?";
			$and = true;
            $params[] = $qualFamilyID;
		}
		if($unitFamilyID != -1)
		{
			if($and)
			{
				$sql .= ' AND';
			}
			$sql .= " unitType.bcgttypefamilyid = ?";
			$and = true;
            $params[] = $unitFamilyID;
		}
		if($unitLevelID != -1)
		{
			if($and)
			{
				$sql .= ' AND';
			}
			$sql .= " unit.bcgtlevel = ?";
			$and = true;
            $params[] = $unitLevelID;
		}
        if($qualIDExclude != -1)
        {
            if($and)
            {
                $sql .= ' AND';
            }
            $sql .= " unit.id NOT IN (SELECT bcgtunitid 
                FROM {block_bcgt_qual_units} WHERE bcgtqualificationid = ?)";
                $params[] = $qualIDExclude;
        }
        if($qualSearch != '')
        {
            if($and)
			{
				$sql .= ' AND';
			}
			$sql .= " qual.name LIKE ?";
			$and = true;
            $params[] = '%'.$qualSearch.'%';
        }
	}
    
	$results = $DB->get_records_sql($sql, $params, 0 , 50);
    
    // Search bespoke quals as well
    if ($unitFamilyID == 1 || $unitFamilyID <= 0){
                
        $sql = "SELECT u.*, b.displaytype, b.level, 1 as isbespoke
                FROM {block_bcgt_bespoke_unit} b
                INNER JOIN {block_bcgt_unit} u ON u.id = b.bcgtunitid
                WHERE u.name LIKE ? OR b.displaytype LIKE ?
                ORDER BY b.displaytype ASC, b.level ASC, u.name ASC";
        $bespoke = $DB->get_records_sql($sql, array('%'.$search.'%', '%'.$search.'%'));
                        
        if ($bespoke)
        {
            foreach($bespoke as $bspk)
            {
                if (!isset($results[$bspk->id])){
                    $results[$bspk->id] = $bspk;
                }
            }
        }
        
    }    
    
    
    
    return $results;
}

function get_users_not_on_qual($qualID, $roleID, $search = '')
{
    global $DB;
    $sql = "SELECT user.* FROM {user} user
        WHERE user.id NOT IN 
        (SELECT userid FROM {block_bcgt_user_qual} WHERE roleid = ? 
        AND bcgtqualificationid = ?)";
    $params = array($roleID,$qualID);
    if($search != '')
    {
        $sql .= " AND (user.firstname LIKE ? OR user.lastname LIKE ? 
            OR user.email LIKE ? OR user.username LIKE ?)";
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
    }
    return $DB->get_records_sql($sql, $params, 0, 100);
}

/**
 * Seraches for a user
 * @global type $DB
 * @param type $search
 * @return type
 */
function get_users_bcgt($search = '')
{
    global $DB;
    $sql = "SELECT user.* FROM {user} user";
    $params = array();
    if($search != '')
    {
        $sql .= " WHERE (user.firstname LIKE ? OR user.lastname LIKE ? 
            OR user.email LIKE ? OR user.username LIKE ?)";
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
    }
    return $DB->get_records_sql($sql, $params, 0, 100);
}

/**
 * Find the users in the database that are not assigned to the user passed in. 
 * @global type $DB
 * @param type $roleID
 * @param type $userID
 * @param type $search
 * @return type
 */
function get_users_non_users($roleID, $userID, $search = '')
{
    global $DB;
    $sql = 'SELECT user.* FROM {user} user
        WHERE user.id NOT IN (SELECT user2.id FROM {user} user2
        JOIN {block_bcgt_user_assign} assign ON assign.assigneeuserid = user2.id 
        WHERE assign.roleid = ? AND assign.userid = ?) AND user.id <> ? ';
    $params = array($roleID, $userID, $userID);
    if($search != '')
    {
        $sql .= ' AND (user.lastname LIKE ? 
            OR user.firstname LIKE ? OR user.username LIKE ? 
            OR user.username LIKE ?)';
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
    }
    return $DB->get_records_sql($sql, $params);
}

function add_users_users($userIDs, $roleID, $userID)
{
    global $DB;
    foreach($userIDs AS $idAdd)
    {
        $stdObj = new stdClass();
        $stdObj->userid = $userID;
        $stdObj->roleid = $roleID;
        $stdObj->assigneeuserid = $idAdd;
        $DB->insert_record('block_bcgt_user_assign', $stdObj);
    }
}

function remove_users_users($userIDs, $roleID, $userID)
{
    global $DB;
    foreach($userIDs AS $idRemove)
    {
        $DB->delete_records('block_bcgt_user_assign', array('roleid'=>$roleID, 
            'userid'=>$userID, 'assigneeuserid'=>$idRemove));
    }
}

/**
 * Finds the users in the database that have been assigned under the role
 * to the user passed in
 * @global type $DB
 * @param type $roleID
 * @param type $userID
 * @param type $search
 * @return type
 */
function get_users_users($roleID, $userID, $search = '')
{
    global $DB;
    $sql = 'SELECT user.* FROM {user} user 
        JOIN {block_bcgt_user_assign} assign ON assign.assigneeuserid = user.id 
        WHERE assign.roleid = ? AND assign.userid = ?';
    $params = array($roleID, $userID);
    if($search != '')
    {
        $sql .= ' AND (user.lastname LIKE ? 
            OR user.firstname LIKE ? OR user.username LIKE ? 
            OR user.username LIKE ?)';
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
    }
    return $DB->get_records_sql($sql, $params);

    
}

/**
 * Find the role from the database and then gets the quals for that user.  
 * @global type $DB
 * @param type $userID
 * @param type $role
 * @param string $searc
 */
function get_role_quals($userID, $role, $search = '', $familyID = -1)
{
    global $DB;
    $sql = "SELECT id FROM {role} WHERE ";
    $params = array();
    if(is_array($role))
    {
        $count = 0;
        //then we split it.
        foreach($role AS $r)
        {
            $count++;
            if($count != 1)
            {
                $sql .= ' OR';
            }
            $sql .= ' shortname = ?';
            $params[] = $r;
        }   
    }
    else
    {
        $sql .= " shortname = ?";
        $params[] = $role;
    }
    $roleDB = $DB->get_records_sql($sql, $params);
    if($roleDB)
    {
        $roles = array();
        foreach($roleDB AS $role)
        {
            $roles[] = $role->id;
        }
        return get_users_quals($userID, $roles, $search, $familyID);
    } 
    return false;
}

function bcgt_get_role($role)
{
    global $DB;
    return $DB->get_record_sql('SELECT id FROM {role} WHERE shortname = ?', array($role));
}

/**
 * Gets the users quals. 
 * @global type $DB
 * @param type $userID
 * @param type $roleID
 * @param type $search
 * @return type
 */
function get_users_quals($userID, $roleID = -1, $search = '', $familyID = -1, $courseID = -1, $excludeFamilies = array())
{
    global $DB;
    $sql = "SELECT distinct(qual.id), qual.*, family.family, 
        level.trackinglevel, subtype.subtype, level.id as levelid, subtype.subtypeshort, type.type, targetQual.id as bcgttargetqualid 
        FROM {block_bcgt_user_qual} userQual 
        JOIN {block_bcgt_qualification} qual ON qual.id = userQual.bcgtqualificationid
        JOIN {block_bcgt_target_qual} targetQual ON targetQual.id = qual.bcgttargetqualid
        JOIN {block_bcgt_type} type ON type.id = targetQual.bcgttypeid
        JOIN {block_bcgt_type_family} family ON family.id = type.bcgttypefamilyid
        JOIN {block_bcgt_level} level ON level.id = targetQual.bcgtlevelid 
        JOIN {block_bcgt_subtype} subtype ON subtype.id = targetQual.bcgtsubtypeid";
        if($courseID != -1)
        {
            $sql .= " JOIN {block_bcgt_course_qual} coursequal ON coursequal.bcgtqualificationid = qual.id";
        }
        $sql .= " WHERE userQual.userid = ?";
        $params = array($userID);
        if($roleID != -1)
        {
            $sql .= ' AND (';
            if(is_array($roleID))
            {
                $count = 0;
                //then we split it.
                foreach($roleID AS $role)
                {
                    $count++;
                    if($count != 1)
                    {
                        $sql .= ' OR';
                    }
                    $sql .= ' userQual.roleid = ?';
                    $params[] = $role;
                }   
            }
            else
            {
                $sql .= " userQual.roleid = ?";
                $params[] = $roleID;
            }
            $sql .= ')';
        }
        if($search != '')
        {
            $sql .= ' AND qual.name LIKE ?';
            $params[] = '%'.$search.'%';
        }
        if($familyID != -1)
        {
            $sql .= ' AND family.id = ?';
            $params[] = $familyID;
        }
        if($courseID != -1)
        {
            $sql .= ' AND coursequal.courseid = ?';
            $params[] = $courseID;
        }
        if($excludeFamilies && count($excludeFamilies) != 0)
        {
            $sql .= ' AND';
            $count = 0;
            foreach($excludeFamilies AS $family)
            {
                $count++;
                if($count != 1)
                {
                    $sql .= ' AND';
                }
                $sql .= ' family.family != ?';
                $params[] = $family;
            }
            $sql .= '';
            $and = true;
        }
        $sql .= ' ORDER BY family.family DESC, subtype.subtype ASC, qual.name ASC';
    $records = $DB->get_records_sql($sql, $params);
    // Bespoke cannot be done like this, there are no types, levels, etc... to join
    // Search bespoke quals as well
                
    if ($familyID == 1 || $familyID < 0)
    {
    
        $sql = "SELECT q.*, b.displaytype, b.subtype, b.level, 1 as isbespoke
                FROM {block_bcgt_bespoke_qual} b
                INNER JOIN {block_bcgt_qualification} q ON q.id = b.bcgtqualid
                INNER JOIN {block_bcgt_user_qual} userQual ON userQual.bcgtqualificationid = q.id
                LEFT OUTER JOIN 
                (
                    SELECT bcgtqualificationid, COUNT(bcgtunitid) AS countunits
                    FROM {block_bcgt_qual_units}
                    group by bcgtqualificationid
                ) AS qualunits ON qualunits.bcgtqualificationid = q.id
                LEFT OUTER JOIN 
                (
                    SELECT bcgtqualificationid, COUNT(courseid) AS countcourse 
                    FROM {block_bcgt_course_qual} AS coursequal
                    GROUP BY bcgtqualificationid
                ) AS coursequal ON coursequal.bcgtqualificationid = q.id
                WHERE userQual.userid = ?";
        
                $params = array($userID);
        
                if($roleID != -1)
                {
                    $sql .= ' AND (';
                    if(is_array($roleID))
                    {
                        $count = 0;
                        //then we split it.
                        foreach($roleID AS $role)
                        {
                            $count++;
                            if($count != 1)
                            {
                                $sql .= ' OR';
                            }
                            $sql .= ' userQual.roleid = ?';
                            $params[] = $role;
                        }   
                    }
                    else
                    {
                        $sql .= " userQual.roleid = ?";
                        $params[] = $roleID;
                    }
                    $sql .= ')';
                }
                if($search != '')
                {
                    $sql .= ' AND q.name LIKE ?';
                    $params[] = '%'.$search.'%';
                }
//                if($familyID != -1)
//                {
//                    $sql .= ' AND family.id = ?';
//                    $params[] = $familyID;
//                }
        
        $bespoke = $DB->get_records_sql($sql, $params);
                        
        if ($bespoke)
        {
            foreach($bespoke as $bspk)
            {
                if (!isset($records[$bspk->id])){
                    $records[$bspk->id] = $bspk;
                }
            }
        }
        
    }
        
    
    
    return $records;
}

function bcgt_get_users_courses($userID, $roleID, $hasQual = false, $qualID = -1, $excludeFamilies = array())
{
    global $DB;
    $sql = "SELECT distinct(course.id), course.* FROM {course} course
         JOIN {context} context ON context.instanceid = course.id
            JOIN {role_assignments} roleass ON roleass.contextid = context.id 
            JOIN {user} user ON user.id = roleass.userid 
            JOIN {role} role ON role.id = roleass.roleid";
    if($hasQual || $qualID != -1 || ($excludeFamilies && count($excludeFamilies) != 0))
    {
        $sql .= " JOIN {block_bcgt_course_qual} coursequal ON coursequal.courseid = course.id";
        if($excludeFamilies && count($excludeFamilies) != 0)
        {
            $sql .= ' JOIN {block_bcgt_qualification} qual ON qual.id = coursequal.bcgtqualificationid 
                JOIN {block_bcgt_target_qual} targetqual ON targetqual.id = qual.bcgttargetqualid 
                JOIN {block_bcgt_type} type ON type.id = targetqual.bcgttypeid 
                JOIN {block_bcgt_type_family} family ON family.id = type.bcgttypefamilyid';
        }
    }
    $params = array($userID, $roleID);
    $sql .= " WHERE user.id = ? AND role.id = ?";
    if($qualID != -1)
    {
        $sql .= " AND coursequal.bcgtqualificationid = ?";
        $params[] = $qualID;
    }
    if($excludeFamilies && count($excludeFamilies) != 0)
    {
        foreach($excludeFamilies AS $family)
        {
            $sql .= ' AND family.family != ?';
            $params[] = $family;
        }
    }
    return $DB->get_records_sql($sql, $params);
}

function bcgt_get_users_assessments($userID, $roleID, $search)
{
    global $DB;
    $sql = "SELECT distinct(project.id), project.* FROM {block_bcgt_project} project 
        JOIN {block_bcgt_activity_refs} activityrefs ON activityrefs.bcgtprojectid = project.id 
        JOIN {block_bcgt_user_qual} userqual ON userqual.bcgtqualificationid = activityrefs.bcgtqualificationid 
        WHERE userqual.roleid = ? AND userqual.userid = ?";
    $params = array($roleID, $userID);
    if($search != '')
    {
        $sql .= ' AND (project.name LIKE ?';
        $params[] = '%'.$search.'%';
        $searchSplit = explode(' ', $search);
        if($searchSplit)
        {
            foreach($searchSplit AS $split)
            {
                $sql .= ' OR project.name LIKE ?';
                $params[] = '%'.$split.'%';
            }
        }
        $sql .= ')';
    }
    return $DB->get_records_sql($sql, $params);
}

function bcgt_get_users_units($userID, $roleID, $search)
{
    global $DB;
    $sql = "SELECT distinct(unit.id), unit.* FROM {block_bcgt_unit} unit 
        JOIN {block_bcgt_qual_units} qualunits ON qualunits.bcgtunitid = unit.id 
        JOIN {block_bcgt_user_qual} userqual ON userqual.bcgtqualificationid = qualunits.bcgtqualificationid 
        WHERE userqual.roleid = ? AND userqual.userid = ?";
    $params = array($roleID, $userID);
    if($search != '')
    {
        $sql .= ' AND (unit.name LIKE ? OR unit.uniqueid LIKE ?';
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
        $searchSplit = explode(' ', $search);
        if($searchSplit)
        {
            foreach($searchSplit AS $split)
            {
                $sql .= ' OR unit.name LIKE ? OR unit.uniqueid LIKE ?';
                $params[] = '%'.$split.'%';
                $params[] = '%'.$split.'%';
            }
        }
        $sql .= ')';
    }
    return $DB->get_records_sql($sql, $params);
}

function bcgt_get_users_users($userID, $theirRoleID, $userRoleID, $search = '')
{
    global $DB;
    $sql = "SELECT distinct(user.id), user.* FROM {user} user
        JOIN {role_assignments} roleass ON roleass.userid = user.id 
        JOIN {context} context ON context.id = roleass.contextid 
        JOIN {course} course ON course.id = context.instanceid 
        JOIN {block_bcgt_user_qual} userqual ON userqual.userid = user.id
        JOIN {block_bcgt_course_qual} coursequal ON coursequal.bcgtqualificationid = userqual.bcgtqualificationid
        JOIN {block_bcgt_user_qual} userqualteach ON userqualteach.bcgtqualificationid = coursequal.bcgtqualificationid
        WHERE userqual.roleid = ? AND userqualteach.userid = ? AND";
    $params = array($userRoleID, $userID);
    if(is_array($theirRoleID))
    {
        $count = 0;
        $sql .= ' (';
        foreach($theirRoleID AS $roleID)
        {
            $count++;
            $sql .= ' userqualteach.roleid = ?';
            if($count != count($theirRoleID))
            {
                $sql .= ' OR';
            }
            $params[] = $roleID;
        }
        $sql .= ')';
    }
    else
    {
        $params[] = $theirRoleID;
        $sql .= ' userqualteach.roleid = ?';
    }
    
    
    if($search != '')
    {
        $sql .= " AND (user.firstname LIKE ? OR user.lastname LIKE ? 
                OR user.email LIKE ? OR user.username LIKE ? ";
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
        $searchSplit = explode(' ', $search);
        if($searchSplit)
        {
            foreach($searchSplit AS $split)
            {
                $sql .= ' OR user.firstname LIKE ? OR user.lastname LIKE ? 
            OR user.email LIKE ? OR user.username LIKE ? ';
                $params[] = '%'.$split.'%';
                $params[] = '%'.$split.'%';
                $params[] = '%'.$split.'%';
                $params[] = '%'.$split.'%';
            }
        }
        $sql .= ')';
    }
    $sql .= 'ORDER BY user.lastname ASC';
    return $DB->get_records_sql($sql, $params);
}

function add_qual_user($qualIDs, $roleID, $userID, $role = 'student')
{
    $loadParams = new stdClass();
    $loadParams->loadLevel = Qualification::LOADLEVELUNITS;
    global $DB;
    foreach($qualIDs AS $qualID)
    {
        if(!Qualification::check_user_on_qual($userID, $roleID, $qualID))
        {
            $userQual = new stdClass();
            $userQual->bcgtqualificationid = $qualID;
            $userQual->userid = $userID;
            $userQual->roleid = $roleID;
            $DB->insert_record('block_bcgt_user_qual', $userQual);
            
            if($role == 'student')
            {
                $qualification = Qualification::get_qualification_class_id($qualID, $loadParams);
                if($qualification)
                {
                    $qualification->add_single_student_units($userID);
                }
            }
        }
    }
}

function remove_qual_user($qualIDs, $roleID, $userID, $role = 'student')
{
    $loadParams = new stdClass();
    $loadParams->loadLevel = Qualification::LOADLEVELUNITS;
    global $DB;
    foreach($qualIDs AS $qualID)
    {
        if($role == 'student')
        {
            $qualification = Qualification::get_qualification_class_id($qualID, $loadParams);
            if($qualification)
            {
                $qualification->remove_single_students_units($userID);
            }
        }
        $DB->delete_records('block_bcgt_user_qual', array('bcgtqualificationid'=>$qualID, 
        'roleid'=>$roleID, 'userid'=>$userID));
    }
}

/**
 * 
 * @global type $DB
 * @param type $categoryID
 * @param type $search
 * @param type $studentSearch
 * @param type $teacherSearch
 * @return type
 */
function search_courses($categoryID = -1, $search = '', $studentSearch = '', $teacherSearch = '', $sort = '')
{
    global $DB;
    $sql = "SELECT distinct(course.id), course.* FROM {course} course";
    if($studentSearch != '')
    {
        $sql .= " JOIN {context} scontext ON scontext.instanceid = course.id
            JOIN {role_assignments} sroleass ON sroleass.contextid = scontext.id 
            JOIN {user} suser ON suser.id = sroleass.userid 
            JOIN {role} srole ON srole.id = sroleass.roleid";
    }
    if($teacherSearch != '')
    {
        $sql .= " JOIN {context} tcontext ON tcontext.instanceid = course.id
            JOIN {role_assignments} troleass ON troleass.contextid = tcontext.id 
            JOIN {user} tuser ON tuser.id = troleass.userid 
            JOIN {role} trole ON trole.id = troleass.roleid"; 
    }
    $params = array();
    if($categoryID != -1 || $search != '' || $studentSearch != '' || $teacherSearch != '')
    {
        $and = false;
        $sql .= " WHERE";
        if($categoryID != -1)
        {
            $sql .= " course.category = ?";
            $and = true;
            $params[] = $categoryID;
        }
        if($search != '')
        {
            if($and)
            {
                $sql .= " AND";
            }
            $and = true;
            $sql .= " (course.fullname LIKE ? OR course.shortname LIKE ? OR course.idnumber LIKE ?)";
            $params[] = '%'.$search.'%';
            $params[] = '%'.$search.'%';
            $params[] = '%'.$search.'%';
        }
        if($studentSearch != '')
        {
            if($and)
            {
                $sql .= " AND";
            }
            $and = true;
            $sql .= " srole.shortname = ? AND (suser.username LIKE ? OR suser.firstname LIKE ? OR suser.lastname LIKE ?)";
            $params[] = 'student';
            $params[] = '%'.$studentSearch.'%';
            $params[] = '%'.$studentSearch.'%';
            $params[] = '%'.$studentSearch.'%';
            //JOIN ON users and enrollments where student...
        }
        if($teacherSearch != '')
        {
            //JOIN ON usres and enrollments where teacher ...
            if($and)
            {
                $sql .= " AND";
            }
            $and = true;
            $sql .= " trole.shortname LIKE ? AND (tuser.username LIKE ? OR tuser.firstname LIKE ? OR tuser.lastname LIKE ?)";
            $params[] = '%teacher%';
            $params[] = '%'.$teacherSearch.'%';
            $params[] = '%'.$teacherSearch.'%';
            $params[] = '%'.$teacherSearch.'%';
        }
    }
    if($sort != '')
    {
        $sql .= ' ORDER BY '.$sort;
    }
    return $DB->get_records_sql($sql, $params, null, 100);
    
    //Include child courses???
}

/**
 * 
 * @global type $DB
 * @param type $courseID
 * @return type
 */
function bcgt_get_course_students($courseID)
{
    global $DB;
    $sql = "SELECT ra.id as id , user.id as userid, user.username, user.firstname, user.lastname, user.picture, 
        user.imagealt, user.email, 
course.id as courseid, course.shortname as courseshortname, 'direct' as enrolment 
FROM {user} user 
JOIN {role_assignments} ra ON ra.userid = user.id
JOIN {context} c ON c.id = ra.contextid
JOIN {role} r ON r.id = ra.roleid
JOIN {course} course ON course.id = c.instanceid
WHERE course.id = ? AND r.shortname = ?  AND ra.component = ? 
UNION
SELECT ra.id as id , user.id as userid, user.username, user.firstname, user.lastname, 
user.picture, user.imagealt, user.email, childcourse.id as courseid, 
childcourse.shortname as courseshortname, 'child' as enrolment 
FROM mdl_user user 
JOIN mdl_role_assignments ra ON ra.userid = user.id 
JOIN mdl_context c ON c.id = ra.contextid 
JOIN mdl_role r ON r.id = ra.roleid 
JOIN mdl_course childcourse ON childcourse .id = c.instanceid 
LEFT OUTER JOIN mdl_enrol e ON e.customint1 = childcourse .id 
JOIN mdl_course course ON course.id = e.courseid 
WHERE course.id = ? AND r.shortname = ? 
ORDER BY enrolment DESC, courseid ASC, lastname ASC";
    return $DB->get_records_sql($sql, array($courseID, 'student', '', $courseID, 'student'));
}

/**
 * Gets all of the qualifications that are on a course
 * @global type $DB
 * @param type $courseID
 * @return type
 */
function bcgt_get_course_quals($courseID, $familyID = -1, $qualID = -1, $excludeFamilies = array(), $search = '')
{
    global $DB;
    $sql = "SELECT qual.id, family.family, level.trackinglevel, subtype.subtype, 
        qual.name, qual.additionalname FROM {block_bcgt_course_qual} coursequal 
        JOIN {block_bcgt_qualification} qual ON qual.id = coursequal.bcgtqualificationid 
        JOIN {block_bcgt_target_qual} targetqual ON targetqual.id = qual.bcgttargetqualid 
        JOIN {block_bcgt_type} type ON type.id = targetqual.bcgttypeid 
        JOIN {block_bcgt_type_family} family ON family.id = type.bcgttypefamilyid 
        JOIN {block_bcgt_level} level ON level.id = targetqual.bcgtlevelid 
        JOIN {block_bcgt_subtype} subtype ON subtype.id = targetqual.bcgtsubtypeid 
        WHERE coursequal.courseid = ?
        ";

    $params = array($courseID);
    if($familyID != -1)
    {
        $sql .= ' AND type.bcgttypefamilyid = ?';
        $params[] = $familyID;
    }
    if($qualID != -1)
    {
        $sql .= ' AND qual.id = ?';
        $params[] = $qualID;
    }
    if($excludeFamilies && count($excludeFamilies) != 0)
    {
        $sql .= ' AND';
        $count = 0;
        foreach($excludeFamilies AS $family)
        {
            $count++;
            if($count != 1)
            {
                $sql .= ' AND';
            }
            $sql .= ' family.family != ?';
            $params[] = $family;
        }
        $sql .= '';
    }
    if($search != '')
    {
        $sql .= ' AND (qual.name LIKE ?';
        $params[] = '%'.$search.'%';
        $seachSplit = explode(' ', $search);
        if($seachSplit)
        {
            foreach($seachSplit AS $split)
            {
                $sql .= ' OR qual.name LIKE ?';
                $params[] = '%'.$split.'%';
            }
        }
        $sql .= ')';
    }
    $records = $DB->get_records_sql($sql, $params);
    
    
    // Bespoke check
    if ($familyID == 1 || $familyID < 0){
        
        $bespoke = $DB->get_records_sql("SELECT q.id, q.name, b.displaytype, b.level, b.subtype, 1 as isbespoke
                                        FROM {block_bcgt_course_qual} cq
                                        INNER JOIN {block_bcgt_qualification} q ON q.id = cq.bcgtqualificationid
                                        INNER JOIN {block_bcgt_bespoke_qual} b ON b.bcgtqualid = q.id
                                        WHERE cq.courseid = ?", array($courseID));
        //Also get any bespoke records that are on this course. 
        if ($bespoke)
        {
            foreach($bespoke as $record)
            {
                if (!isset($records[$record->id])){
                    $records[$record->id] = $record;
                }
            }
        }
    
    }
    
    return $records;
}


/**
 * 
 * @global type $DB
 * @param type $courseID
 * @return type
 */
function bcgt_get_course_units($courseID, $familyID = -1)
{
    global $DB;
    $sql = "SELECT unit.* FROM {block_bcgt_course_qual} coursequal 
        JOIN {block_bcgt_qualification} qual ON qual.id = coursequal.bcgtqualificationid 
        JOIN {block_bcgt_target_qual} targetqual ON targetqual.id = qual.bcgttargetqualid 
        JOIN {block_bcgt_type} type ON type.id = targetqual.bcgttypeid 
        JOIN {block_bcgt_type_family} family ON family.id = type.bcgttypefamilyid 
        JOIN {block_bcgt_level} level ON level.id = targetqual.bcgtlevelid 
        JOIN {block_bcgt_subtype} subtype ON subtype.id = targetqual.bcgtsubtypeid 
        JOIN {block_bcgt_qual_units} qualunits ON qualunits.bcgtqualificationid = qual.id
        JOIN {block_bcgt_unit} unit ON unit.id = qualunits.bcgtunitid 
        WHERE coursequal.courseid = ?
        ";
    $params = array($courseID);
    if($familyID != -1)
    {
        $sql .= ' AND type.bcgttypefamilyid = ?';
        $params[] = $familyID;
    }
    return $DB->get_records_sql($sql, $params);
}


function bcgt_unit_activities($courseID, $unitID, $moduleName = 'assign', 
        $orderBy = '', $extraField = '')
{
    global $DB;
    $sql = "SELECT distinct(cm.id), cm.*, m.id as mid, m.name as modname $extraField 
        FROM {block_bcgt_activity_refs} activity 
        JOIN {course_modules} cm ON cm.id = activity.coursemoduleid
        JOIN {modules} md ON md.id = cm.module 
        JOIN {".$moduleName."} m ON m.id = cm.instance 
        WHERE cm.course = ? AND md.name = ? AND activity.bcgtunitid = ?
        ";
        $params = array($courseID, $moduleName, $unitID);
    return $DB->get_records_sql($sql, $params);
}

function get_grid_menu($studentID, $unitID, $qualID = -1, $courseID = -1)
{
    global $CFG, $COURSE, $qualID, $studentID;

    if($courseID != -1)
    {
        $context = context_course::instance($courseID);
    }
    else
    {
        $context = context_course::instance($COURSE->id);
    }
    
    //KD-debug
    //echo '<br />qualID=';
    //print_r($qualID);
    //echo '<br />studentID=';
    //print_r($studentID);
    //echo '<br />courseID=';
    //print_r($courseID);
    //echo '<br />COURSE->id=';
    //print_r($COURSE->id);

    // Gets cID from URL after clicking link to view grid
    // if cID not set then it is 1 for front page
    if (isset($_GET['cID'])) {
    $cID = $_GET['cID'];
    }
    else {
        $cID = 1;
    }
    $gridtype = '';
    if (isset($_GET['g'])) {
    $gridtype = $_GET['g'];
    }
    //echo '<br />cID=';
    //print_r($cID);

    //This gets the menu for the grid
    $out = '<ul class="bcgtGridMenuList">';
    if(has_capability('block/bcgt:viewclassgrids', $context) || 
            has_capability('block/bcgt:manageactivitylinks', $context) || 
            has_capability('block/bcgt:viewdashboard', $context))
    {
        // CORE MENU
        $out .= '<li class="bcgtHeadLink"><a href="#">Core &darr;</a>';
        
            $out .= '<ul class="bcgtDroppy">';
            
                if(has_capability('block/bcgt:viewclassgrids', $context))
                {
                    $out .= '<li><a href="'.$CFG->wwwroot.'/blocks/bcgt/forms/grid_select.php?g=s&cID='.$courseID.'">Student Grids</a></li>';
                    $out .= '<li><a href="'.$CFG->wwwroot.'/blocks/bcgt/forms/grid_select.php?g=u&cID='.$courseID.'">Unit Grids</a></li>';        
                    $out .= '<li><a href="'.$CFG->wwwroot.'/blocks/bcgt/forms/grid_select.php?g=c&cID='.$courseID.'">Class Grids</a></li>';
                }
                if(has_capability('block/bcgt:manageactivitylinks', $context))
                {
                    //$out .= '<li><a href="'.$CFG->wwwroot.'/blocks/bcgt/forms/activities.php?tab=act&cID='.$courseID.'">Assessments</a></li>';
                    if($cID!=1 && $gridtype!='c'){
                    $out .= '<li><a href="'.$CFG->wwwroot.'/blocks/bcgt/forms/activities.php?tab=act&cID='.$cID.'">Assessments</a></li>';
                    }
                    else { 
                        // do nothing as we're not viewing this via a particular course, and a qual might be on multiple courses
                        // grid type is to avoid it showing on class at the moment
                    }
                }
                if(has_capability('block/bcgt:viewdashboard', $context))
                {
                    $out .= '<li><a href="'.$CFG->wwwroot.'/blocks/bcgt/forms/my_dashboard.php">My Dashboard</a></li>';
                }                
            $out .= '</ul>';
        $out .= '</li>';
    }
    // CONTEXT MENU
    if(has_capability('block/bcgt:viewclassgrids', $context))
    {
    $out .= '<li class="bcgtHeadLink"><a href="#">Context &darr;</a>';

        $out .= '<ul class="bcgtDroppy">';            
        if(has_capability('block/bcgt:editqual', $context))
        {
            $out .= '<li><a href="'.$CFG->wwwroot.'/blocks/bcgt/forms/edit_qual.php?qID='.$qualID.'">Edit Qualification</a></li>'; 
        }
        if(has_capability('block/bcgt:editunit', $context))
        {
            $out .= '<li><a href="'.$CFG->wwwroot.'/blocks/bcgt/forms/edit_qual_units.php?qID='.$qualID.'">Edit Qual. Units</a></li>';
        }
        if(has_capability('block/bcgt:editstudentunits', $context))
        {
            $out .= '<li><a href="'.$CFG->wwwroot.'/blocks/bcgt/forms/edit_students_units.php?qID='.$qualID.'">Edit Students\' Units</a></li>';
        }

        if(has_capability('block/bcgt:editstudentunits', $context))
        {
            $out .= '<li><a href="'.$CFG->wwwroot.'/blocks/bcgt/forms/edit_students_units.php?a=s&sID='.$studentID.'">Edit Individual\'s Units</a></li>';
        }

        $out .= '</ul>';
    $out .= '</li>';
    }        
        
    // Grid menu
    // TEMPORARY until print & download done - Hiding this menu if not student
    // Which grid are we on?
    if($studentID && (has_capability('block/bcgt:printstudentgrid', $context))) {
    $out .= "<li class='bcgtHeadLink'><a href='#'>Grid &darr;</a>";
        $out .= "<ul class='bcgtDroppy'>";

            if(has_capability('block/bcgt:printstudentgrid', $context))
            {
                // Which grid are we on?
                if($studentID) {
                    $out .= '<li><a href="'.$CFG->wwwroot.'/blocks/bcgt/grids/print_grid.php?sID='.$studentID.'&qID='.$qualID.'" target="_blank">Print Grid</a></li>';
                }
                elseif ($unitID) {
                    //$out .= '<li><a href="'.$CFG->wwwroot.'/blocks/bcgt/grids/print_grid.php?uID='.$unitID.'&qID='.$qualID.'" target="_blank">Print Grid</a></li>';
                }
                else {
                    // do nuttin
                }                    
            }
            // Downloading not ready 09.08.13
            //if(has_capability('block/bcgt:downloadstudentgrid', $context))
            //{
                //$out .= '<li><a href="'.$CFG->wwwroot.'/blocks/bcgt/grids/download_grid.php?sID='.$studentID.'&qID='.$qualID.'"  target="_blank">Download Grid</a></li>'; 
            //}

    } // TEMPORARY fix
            
            $out .= "</ul>";
        $out .= "</li>";
        
    $out .= '</ul>';
    return $out;
}

function bcgt_get_module_from_course_mod($cmID, $moduleName = 'assign')
{
    global $DB;
    $sql = "SELECT m.* FROM {".$moduleName."} m
        JOIN {course_modules} cm ON cm.instance = m.id 
        WHERE cm.id = ?";
        $params = array($cmID);
    return $DB->get_record_sql($sql, $params);
}

/**
 * 
 * @global type $DB
 * @param type $courseID
 * @param type $role
 * @return type
 */
function bcgt_get_course_users($courseID, $role)
{
    global $DB;
    $sql = "SELECT * FROM {user} user
        JOIN {role_assignments} roleass ON roleass.userid = user.id 
        JOIN {role} role ON role.id = roleass.roleid 
        JOIN {context} context ON context.id = roleass.contextid 
        JOIN {course} course ON course.id = context.instanceid 
        WHERE course.id = ? AND role LIKE ?";
    $params = array($courseID, '%'.$role.'%');
    return $DB->get_record_sql($sql, $params);
}

/**
 * 
 * @global type $DB
 * @param type $qualID
 * @param type $userID
 * @param type $role
 * @return boolean
 */
function bcgt_get_user_on_qual($qualID, $userID, $role = 'student')
{
    global $DB;
    $role = bcgt_get_role($role);
    if($role)
    {
        $sql = "SELECT * FROM {block_bcgt_user_qual} WHERE 
            bcgttrackingqualificationid = ? AND userid = ? AND roleid = ?";
        $params = array($qualID, $userID, $role->id);
        return $DB->get_record_sql($sql, $params);
    }
    return false;
}

/**
 * 
 * @param type $courseID
 */
function bcgt_process_course_qual_users($courseID)
{
    $loadParams = new stdClass();
    $loadParams->loadLevel = Qualification::LOADLEVELUNITS;
    $currentQuals = bcgt_get_course_quals($courseID);
    $users = bcgt_get_course_students($courseID);
    $qualsArray = array();
    $role = bcgt_get_role('student');
    
    if($users)
    {
        foreach($users AS $student)
        {
            if($currentQuals)
            {
                foreach($currentQuals AS $qual)
                {
                    if(array_key_exists($qual->id, $qualsArray))
                    {
                        //then we already know about the qualification
                        $qualification = $qualsArray[$qual->id];
                    }
                    else 
                    {
                        $qualification = Qualification::get_qualification_class_id($qual->id, $loadParams);
                        $qualsArray[$qual->id] = $qualification;
                    }
                    if(isset($_POST['chq'.$qual->id.'s'.$student->userid]))
                    {
                        //the check box is set
                        //ihis will check its in the db and add it to all units
                        $qualification->add_user_to_qual($student->userid, $role->id, true);
                    }
                    else
                    {
                        //it isnt now, was it before?
                        $qualification->remove_user_from_qual($student->userid, $role->id, true);
                    }
                }
            }
        }
    }
        
}

/**
 * 
 * @param type $qual
 * @param type $long
 * @param type $seperator
 * @param type $exclusions
 * @return string
 */
function bcgt_get_qualification_display_name($qual, $long = true, $seperator = ' ', $exclusions = array())
{
        
    $retval = '';
    if(!in_array('type', $exclusions))
    {
        if (isset($qual->isbespoke)){
            $retval .= $qual->displaytype;
        } else {
            $retval .= (isset($qual->type)) ? $qual->type : $qual->family;
        }
        $retval .= $seperator;
    }
    elseif(!in_array('family', $exclusions))
    {
        if (isset($qual->isbespoke)){
            $retval .= '';
        } else {
            $retval .= (isset($qual->family)) ? $qual->family : '';
        }
        $retval .= $seperator;
    }
    if(!in_array('trackinglevel', $exclusions))
    {
        if($long)
        {
            
            if (isset($qual->isbespoke)){
                $retval .= "Level " . $qual->level;
            } else {
                $retval .= $qual->trackinglevel;
            }
            
        }
        else
        {
            
            if (isset($qual->isbespoke)){
                $retval .= "L{$qual->level}";
            } else {
                $retval .= Level::get_short_version($qual->levelid);
            }
            
        }
        $retval .= $seperator;
    }
    if(!in_array('subtype', $exclusions))
    {
        if($long)
        {
            $retval .= $qual->subtype;
        }
        else
        {
            //$retval .= $qual->subtype;
            $retval .= $qual->subtypeshort;
        }
        $retval .= $seperator;
    }
    $retval .= $qual->name;
    if(!in_array('additionalname', $exclusions))
    {
        if($qual->additionalname && $qual->additionalname != '')
        {
            $retval .= ' ('.$qual->additionalname.')';
        }
    }
    return $retval;
}

function get_student_qual_update_time($qualID, $studentID)
{
    global $DB;
    $studentRole = $DB->get_record_sql("SELECT * FROM {role} WHERE shortname = ? ", array('student'));
    
    $sql = "SELECT * FROM {block_bcgt_user_qual} WHERE userid = ? AND bcgtqualificationid = ? AND roleid = ?";
    $params = array($studentID, $qualID, $studentRole->id);
    $record = $DB->get_record_sql($sql, $params);
    if($record)
    {
        return $record->lastupdatedtime;
    }
    return false;
}

/**
 * Finds the unittype, find the location of the unit class and loads it
 * @param type $unitID
 */
function load_unit_class($unitID)
{
    global $DB, $CFG;
    $sql = "SELECT * FROM {block_bcgt_type_family} family 
        JOIN {block_bcgt_type} type ON type.bcgttypefamilyid = family.id 
        JOIN {block_bcgt_unit} unit ON unit.bcgttypeid = type.id 
        WHERE unit.id = ?";
    $record = $DB->get_record_sql($sql, array($unitID));
    if($record)
    {
        require_once($CFG->dirroot.$record->classfolderlocation."/".$record->family."Unit.class.php");
    }
}

/**
 * Finds the unittype, find the location of the unit class and loads it
 * @param type $unitID
 */
function load_qual_class($qualID)
{
    global $DB, $CFG;
    $sql = "SELECT * FROM {block_bcgt_type_family} family 
        JOIN {block_bcgt_type} type ON type.bcgttypefamilyid = family.id 
        JOIN {block_bcgt_target_qual} targetqual ON targetqual.bcgttypeid = type.id 
        JOIN (block_bcgt_qualification) qual ON qual.bcgttargetqualid = targetqual.id
        WHERE qual.id = ?";
    $record = $DB->get_record_sql($sql, array($qualID));
    if($record)
    {
        require_once($CFG->dirroot.$record->classfolderlocation."/".$record->family."Qualification.class.php");
    }
}

function get_users_on_unit_qual($unitID, $qualID)
{
    global $DB;
    $sql = "SELECT * FROM {block_bcgt_user_unit} userunit 
        JOIN {user} user ON user.id = userunit.userid 
        WHERE userunit.bcgtunitid = ? AND userunit.bcgtqualificationid = ? AND user.deleted != 1 
        ORDER BY user.lastname, user.firstname, user.username";
    return $DB->get_records_sql($sql, array($unitID, $qualID));
}

/**
 * Checks if the user is a student on a tracking sheet,
 * @param type $userID
 * @return boolean
 */
function does_user_have_tracking_sheets($userID)
{
    global $DB;
    $studentRole = $DB->get_record_sql('SELECT * FROM {role} WHERE shortname = ? ', array('student'));
    $trackingSheets = get_users_quals($userID, $studentRole->id);
    if($trackingSheets)
    {
        return true;
    }
    return false;
}

function install_plugin($pluginName)
{
    global $CFG;
    //check that the plugin hasnt already been installed
        //instantiate it
            //call its install function.
    //require_once the class
    require_once($CFG->dirroot.'/blocks/bcgt/plugins/'.$pluginName.'/'.$pluginName.'.class.php');
    $plugin = $pluginName::get_instance();
    if($plugin)
    {
        $plugin->install();
    }
}

//function find_new_plugins($install = false)
//{
//    //needs to check a database table that holds the plugins. 
//}

function is_plugin_installed($name)
{
    global $DB;
    $sql = "SELECT * FROM {block_bcgt_plugins} WHERE name = ?";
    return $DB->get_record_sql($sql, array($name));
}

function update_session_qual($studentID, $qualID, $qualification, $unit = null)
{
        
    $sessionQuals = isset($_SESSION['session_stu_quals'])? unserialize(urldecode($_SESSION['session_stu_quals'])) : array();
        
    $qualArray = array();
    if(array_key_exists($studentID, $sessionQuals))
    {
        $qualArray = $sessionQuals[$studentID];
    }
    if(array_key_exists($qualID, $qualArray))
    {
        $qualObject = $qualArray[$qualID];
    }
    else 
    {
        $qualObject = new stdClass();
    }

    if (!is_null($unit)){
        
        $qualUnits = $qualification->get_units();
        if (isset($qualUnits[$unit->get_id()])){
            $qualUnits[$unit->get_id()] = $unit;
            $qualification->set_units($qualUnits);
        }
        
    }
        
    $qualObject->qualification = $qualification;
    $qualArray[$qualID] = $qualObject;
    $sessionQuals[$studentID] = $qualArray;
    $_SESSION['session_stu_quals'] = urlencode(serialize($sessionQuals));
    
}

function update_session_unit($studentID, $unitID, $unit, $qualID){
    
    global $DB;
    
    $sessionUnits = isset($_SESSION['session_unit'])? unserialize(urldecode($_SESSION['session_unit'])) : array();
    
//    
//    $studentUnit = false;
//    
//    if(array_key_exists($unitID, $sessionUnits))
//    {
//        $unitObject = $sessionUnits[$unitID];
//        $qualArray = $unitObject->qualArray;
//        if(array_key_exists($qualID, $qualArray))
//        {
//            //what happens if a student has been added since?
//
//            //then this will return an array of students unit objects
//            //for this qualid for this unit.
//            $studentsArray = $qualArray[$qualID];
//            
//            if (array_key_exists($studentID, $studentsArray))
//            {
//                $studentUnit = $studentsArray[$studentID]->unit;
//            }
//            
//            //studentsArray[] is an object with two properties. The Unit Object with stu
//            //loaded and a few of the students information.
//        }    
//    }
//    
//    // Load from db
//    if (!$studentUnit)
//    {
//        $loadParams = new stdClass();
//        $loadParams->loadLevel = Qualification::LOADLEVELALL;
//        $loadParams->loadAward = true;
//        $studentUnit = Unit::get_unit_class_id($unitID, $loadParams);
//        $studentUnit->load_student_information($studentID, $qualID, $loadParams);
//        $studentUnit = $studentUnit;
//    }
    
    
    
    if(array_key_exists($unitID, $sessionUnits))
    {
        $unitObject = $sessionUnits[$unitID];
        $qualArray = $unitObject->qualArray;
    }
    else
    {
        //it hasnt been loaded into the session before! (can it even get here if this is the case?)
        //then we need to add it
        $loadParams = new stdClass();
        $loadParams->loadLevel = Qualification::LOADLEVELALL;
        $loadParams->loadAward = true;
        
        $unitObject = new stdClass();
        $unitObject->unit = Unit::get_unit_class_id($unitID, $loadParams);
        $qualArray = array();
    }
    
    
    if(array_key_exists($qualID, $qualArray))
    {
        $studentArray = $qualArray[$qualID];
    }
    else
    {
        $studentArray = array();
    }
    
    if(array_key_exists($studentID, $studentArray))
    {
        $studentObject = $studentArray[$studentID];
    }
    else
    {
        $studentObject = $DB->get_record_sql("SELECT * FROM {user} WHERE id = ?", array($studentID));
    }
    $studentObject->unit = $unit;    
    $studentArray[$studentID] = $studentObject;
    $qualArray[$qualID] = $studentArray;
    $unitObject->qualArray = $qualArray;
    $sessionUnits[$unitID] = $unitObject;
    $_SESSION['session_unit'] = urlencode(serialize($sessionUnits));    

    
}

function get_student_qual_from_session($qualID, $studentID)
{
    $sessionQuals = isset($_SESSION['session_stu_quals'])? 
    unserialize(urldecode($_SESSION['session_stu_quals'])) : array(); 

    $qualObject = new stdClass();
    $qualification = null;
    //this will be an array of studentID => qualarray->qual object->qual
    //does the qual exist already for this student?
    if(array_key_exists($studentID, $sessionQuals))
    {
        //the sessionsQuals[studentID] is an array of qualid =>object
        //where object has qualification and session start
        $studentQualArray = $sessionQuals[$studentID];
        if(array_key_exists($qualID, $studentQualArray))
        {
            $qualObject = $studentQualArray[$qualID];
            if(isset($qualObject->sessionStartTime))
            {
                $sessionStartTime = $qualObject->sessionStartTime;
            }
            $qualification = $qualObject->qualification;
        }
        else
        {
            $qualObject->sessionStartTime = time();
            $studentQualArray[$qualID] = $qualObject;
            $sessionQuals[$studentID] = $sessionQuals[$studentID];
        }
    }
    else
    {
        $qualObject->sessionStartTime = time();
        $qualArray = array();
        $qualArray[$qualID] = $qualObject;
        $sessionQuals[$studentID] = $qualArray;
    }       

    if(!$qualification)
    {
        $loadParams = new stdClass();
        $loadParams->loadLevel = Qualification::LOADLEVELALL;
        $loadParams->loadAward = true;
        $loadParams->loadTargets = true;
        $qualification = Qualification::get_qualification_class_id($qualID, $loadParams);
        $qualification->load_student_information($studentID, $loadParams);
        
        //we need to put this into the session!
        update_session_qual($studentID, $qualID, $qualification);
    }

    return $qualification;
}

function get_plugin_name($familyID)
{
    global $DB;
    $sql = "SELECT * FROM {block_bcgt_type_family} WHERE id = ?";
    $class = $DB->get_record_sql($sql, array($familyID));
    if($class)
    {
        return $class->pluginname;
    }
    return false;
}

function get_course_qual_families($courseID)
{
    global $DB;
    $sql = "SELECT distinct(family.id), type.*, family.* FROM {block_bcgt_type} type
        JOIN {block_bcgt_target_qual} targetqual ON targetqual.bcgttypeid = type.id 
        JOIN {block_bcgt_qualification} qual ON qual.bcgttargetqualid = targetqual.id 
        JOIN {block_bcgt_course_qual} coursequal ON coursequal.bcgtqualificationid = qual.id 
        JOIN {block_bcgt_type_family} family ON family.id = type.bcgttypefamilyid
        WHERE coursequal.courseid = ?";
    return $DB->get_records_sql($sql, array($courseID));
}

function insert_activity_onto_unit($record)
{
    global $DB, $USER;
    $record->createdby = $USER->id;
    $record->created = time();
    $record->updatedby = $USER->id;
    $record->updated = time();
    $DB->insert_record('block_bcgt_activity_refs', $record);
}

function delete_activity_from_unit($cmID, $unitID)
{
    echo $cmID." ".$unitID;
    global $DB;
    $DB->delete_records('block_bcgt_activity_refs', array('coursemoduleid'=>$cmID, 
         'bcgtunitid'=>$unitID));
}

function delete_activity_by_qual_from_unit($cmID, $qualID, $unitID)
{
    global $DB;
    $DB->delete_records('block_bcgt_activity_refs', array('coursemoduleid'=>$cmID, 
        'bcgtqualificationid'=>$qualID, 'bcgtunitid'=>$unitID));
}

function delete_activity_by_criteria_from_unit($cmID, $criteriaID, $unitID)
{
    global $DB;
    $DB->delete_records('block_bcgt_activity_refs', array('coursemoduleid'=>$cmID, 
        'bcgtcriteriaid'=>$criteriaID, 'bcgtunitid'=>$unitID));
}

function get_activity_criteria($cmID, $quals = null, $unitID = -1)
{
    global $DB;
    $sql = "SELECT criteria.* FROM {block_bcgt_criteria} criteria 
        JOIN {block_bcgt_activity_refs} refs ON refs.bcgtcriteriaid = criteria.id 
        WHERE refs.coursemoduleid = ?";
    $params = array($cmID);
    if($quals)
    {
        $count = 1;
        $sql .= 'AND refs.bcgtqualificationid IN (';
        foreach($quals AS $qual)
        {
            if($count != 1)
            {
                $sql .= ',';
            }
            $sql .= '?';
            $params[] = $qual->id;
            $count++;
        }
        $sql .= ')';
    }
    if($unitID != -1)
    {
        $sql .= ' AND refs.bcgtunitid = ?';
        $params[] = $unitID;
    }
    return $DB->get_records_sql($sql, $params);
}

/**
 * This gets the quals that were selected
 * for an activity and for a specific unit if given
 * @global type $DB
 * @param type $cmID
 * @param type $unitID
 * @return type
 */
function get_activity_quals($cmID, $unitID = -1)
{
    global $DB;
    $sql = "SELECT distinct(qual.id), qual.* FROM {block_bcgt_qualification} qual 
        JOIN {block_bcgt_activity_refs} refs ON refs.bcgtqualificationid = qual.id 
        WHERE refs.coursemoduleid = ?";
    $params = array($cmID);
    if($unitID != -1)
    {
        $sql .= ' AND refs.bcgtunitid = ?';
        $params[] = $unitID;
    }
    return $DB->get_records_sql($sql, $params);
}

function get_activity_units($cmID)
{
    global $DB;
    $sql = "SELECT distinct(unit.id), unit.*
        FROM {block_bcgt_unit} unit
        JOIN {block_bcgt_activity_refs} refs ON refs.bcgtunitid = unit.id 
        WHERE refs.coursemoduleid = ?";
    return $DB->get_records_sql($sql, array($cmID));
}

function get_activity_units_criteria($cmID, $unitID)
{
    global $DB;
    $sql = "SELECT distinct(criteria.id), criteria.*
        FROM {block_bcgt_criteria} criteria
        JOIN {block_bcgt_activity_refs} refs ON refs.bcgtcriteriaid = criteria.id 
        WHERE refs.coursemoduleid = ? AND refs.bcgtunitid = ?";
    return $DB->get_records_sql($sql, array($cmID, $unitID));
}

function bcgt_html($str, $nl2br = false){
    if ($nl2br) return nl2br( htmlspecialchars($str, ENT_QUOTES) );
    else return htmlspecialchars($str, ENT_QUOTES);
}



/**
 * Handle enrolments onto a course
 * Add the student to relevant quals & units if they're not already on them
 * @param type $eventData
 * @return true - This has to return true or it will mark it as different in the db and never run it again...
 */
function event_handle_user_enrolled($eventData){
    echo "Event Handle User Enrolled in <br />";
    global $DB;
    
    // Check if we have this setting enabled
    $setting = get_config('bcgt', 'autoenrolusers');
    if ($setting != '1') return true;
    
    
        
    $userID = $eventData->userid;
    $courseID = $eventData->courseid;
        
    $quals = $DB->get_records("block_bcgt_course_qual", array("courseid" => $courseID));
    if (!$quals) return true;
        
    // Enrol id is wrong, it brings down the default role not the actual role_assignment
    $context = $DB->get_record("context", array("contextlevel" => CONTEXT_COURSE, "instanceid" => $courseID));
    if (!$context) return true; # Has to eb true or the event won't ever work again...
    
    $ra = $DB->get_record("role_assignments", array("contextid" => $context->id, "userid" => $userID));
    if (!$ra) return true;
        
    $roleID = $ra->roleid;
    $qualIDs = array();
    
    foreach($quals as $qual){
        $qualIDs[] = $qual->bcgtqualificationid;
    }
    echo count($quals)." Quals to be enrolled on Course".$courseID."<br />";   
    $role = ($roleID == 5) ? 'student' : 'teacher';
    
    add_qual_user($qualIDs, $roleID, $userID, $role);
    mtrace("added userid {$userID} to qualids " . implode(",", $qualIDs));
    return true;
    
    
}


function event_handle_user_unenrolled($eventData){
    
    global $DB;
        
    // Check if we have this setting enabled
    $setting = get_config('bcgt', 'autounenrolusers');
    if ($setting != '1') return true;
    
    $userID = $eventData->userid;
    $courseID = $eventData->courseid;
    
    $quals = $DB->get_records("block_bcgt_course_qual", array("courseid" => $courseID));
    if (!$quals) return true;
    
    $qualIDs = array();
    
    foreach($quals as $qual){
        $qualIDs[] = $qual->bcgtqualificationid;
    }
        
    
    // The event data doesn't give us the correct roleid because it's shit so we'll just have to remove all records for the
    // user on the qual
    $roles = $DB->get_records("role");
    foreach($roles as $role){
        remove_qual_user($qualIDs, $role->id, $userID);
    }
    
    return true;
    
}

/**
 * Get a dsitcint list of all quals user is on and has ever been on
 * @param type $userID
 */
function bcgt_get_all_users_quals($userID){
    
    global $DB;
    
    $sql = "SELECT DISTINCT uq.bcgtqualificationid,
                CASE
                    WHEN q.id IS NULL THEN 'F'
                    WHEN q.id IS NOT NULL THEN 'T'
                END as qualExists
            FROM {block_bcgt_user_qual} uq
            LEFT JOIN {block_bcgt_qualification} q ON q.id = uq.bcgtqualificationid
            WHERE uq.userid = ?

            UNION

            SELECT DISTINCT uqh.bcgtqualificationid,
                CASE
                    WHEN q.id IS NULL THEN 'F'
                    WHEN q.id IS NOT NULL THEN 'T'
                END as qualExists
            FROM {block_bcgt_user_qual_his} uqh
            LEFT JOIN {block_bcgt_qualification} q ON q.id = uqh.bcgtqualificationid
            WHERE uqh.userid = ?";
    
    $params = array($userID, $userID);
    
    // This gives us the qual ids and whether ot not they are still in qual or in qual_history
    $records = $DB->get_records_sql($sql, $params);
    
    // Now let's get the info about the qual - name, level, type, etc...
    $quals = array();
        
    if ($records)
    {
        foreach($records as $record)
        {
            
            // If it exists get from qual, else get from qual_history
            if ($record->qualexists == 'T')
            {
                $sql = "SELECT DISTINCT q.id, t.type, q.name, l.trackinglevel, s.subtype
                    FROM {block_bcgt_qualification} q
                    INNER JOIN {block_bcgt_target_qual} tq ON tq.id = q.bcgttargetqualid
                    INNER JOIN {block_bcgt_type} t ON t.id = tq.bcgttypeid
                    INNER JOIN {block_bcgt_level} l ON l.id = tq.bcgtlevelid
                    INNER JOIN {block_bcgt_subtype} s ON s.id = tq.bcgtsubtypeid
                    WHERE q.id = ?";            }
            else
            {
                $sql = "SELECT DISTINCT qh.bcgtqualificationid as id, t.type, qh.name, l.trackinglevel, s.subtype
                    FROM {block_bcgt_qualification_his} qh
                    INNER JOIN {block_bcgt_target_qual} tq ON tq.id = qh.bcgttargetqualid
                    INNER JOIN {block_bcgt_type} t ON t.id = tq.bcgttypeid
                    INNER JOIN {block_bcgt_level} l ON l.id = tq.bcgtlevelid
                    INNER JOIN {block_bcgt_subtype} s ON s.id = tq.bcgtsubtypeid
                    WHERE qh.bcgtqualificationid = ?";  
            }
            
            $info = $DB->get_record_sql($sql, array($record->bcgtqualificationid));
            if ($info)
            {
                $quals[$info->id] = $info;
            }
            
            
        }
    }
    
        
    return $quals;
    
}

function bcgt_get_qualification_family_ID($family)
{
    global $DB;
    $sql = "SELECT * FROM {block_bcgt_type_family} WHERE family = ?";
    $record = $DB->get_record_sql($sql, array($family));
    if($record)
    {
        return $record->id;
    }
    return -1;
}    

function get_unit_name_by_id($unitID)
{
	global $DB;
    $record = $DB->get_record("block_bcgt_unit", array("id" => $unitID));
	return ($record) ? $record->name : false;
}

function get_criteria_by_id($id)
{
    global $DB;
    $record = $DB->get_record("block_bcgt_criteria", array("id" => $id));
    return $record;
}

function bcgt_get_users_column_headings($rowSpan = 1, $returnArray = false)
{
    $retval = '';
    if($returnArray)
    {
        $retval = array();
    }
    //picture,username,name,firstname,lastname,email
    $columns = array('picture', 'username','name');
    //need to get the global config record

    $configColumns = get_config('bcgt','btecgridcolumns');
    if($configColumns)
    {
        $columns = explode(",", $configColumns);
    }
    foreach($columns AS $column)
    { 
        $out = '<th rowspan="'.$rowSpan.'">';
        $out .= get_string(trim($column), 'block_bcgt');
        $out .= '</th>';
        if($returnArray)
        {
            $returnArray[] = $out;
        }
        else
        {
            $retval .=  $out;
        }
        
    }
    return $retval;
}

function bcgt_get_users_columns($user, $qualID = -1)
{
    global $OUTPUT, $CFG;
    $out = '';
    //picture,username,name,firstname,lastname,email
    $columns = array('picture', 'username','name');
    //need to get the global config record
    $configColumns = get_config('bcgt','btecgridcolumns');
    if($configColumns)
    {
        $columns = explode(",", $configColumns);
    }
    foreach($columns AS $column)
    {
        $out .= '<td>';
        if($qualID != -1)
        {
            //then we are showing a link
            $out .= '<a href="'.$CFG->wwwroot.'/blocks/bcgt/grids/student_grid.php?sID='.$user->id.'&qID='.$qualID.'">';
        }
        switch(trim($column))
        {
            case("picture"):
                $out .= $OUTPUT->user_picture($user, array(1));
                break;
            case("username"):
                $out .= $user->username;
                break;
            case("name"):
                $out .= $user->firstname."<br />".$user->lastname;
                break;
            case("firstname"):
                $out .= $user->firstname;
                break;
            case("lastname"):
                $out .= $user->lastname;
                break;
            case("email"):
                $out .= $user->email;
                break;
        }
        if($qualID != -1)
        {
            //then we are showing a link
            $out .= '</a>';
        }
        $out .= '</td>';
    }
    return $out;
}

function bcgt_display_qual_grid_select($qualID, $courseID, $search)
{
    global $CFG, $DB, $COURSE;
    if($courseID == -1)
    {
        $courseID = $COURSE->id;
    }
    $context = context_course::instance($courseID);
    $out = '';
    // echo $cID;
    $role = $DB->get_record_select('role', 'shortname = ?', array('student'));
    //get all of the students on this qual
    $qualification = Qualification::get_qualification_class_id($qualID);
    if($qualification)
    {
        $canEdit = false;
        if(has_capability('block/bcgt:editstudentgrid', $context))
        {	
            $canEdit = true;
        }
        $advancedMode = false;
        if($qualification->has_advanced_mode())
        {
            $advancedMode = true;
        }
        $out .= '<div>';
        $out .= '<h3>'.$qualification->get_display_name().'</h3>';
        $users = $qualification->get_users($role->id, $search);
        $out .= '<table class="qualificationUsers">';
        $out .= '<thead>';
        $out .= bcgt_get_users_column_headings();
        $out .= '<th>'.get_string('viewsimple', 'block_bcgt').'</th>';
        if($canEdit)
        {
            $out .= '<th>'.get_string('editsimple', 'block_bcgt').'</th>';
        }
        if($advancedMode)
        {
            $out .= '<th>'.get_string('viewadvanced', 'block_bcgt').'</th>';
            if($canEdit)
            {
                $out .= '<th>'.get_string('editadvanced', 'block_bcgt').'</th>';
            }
        }
        if(get_config('bcgt', 'alevelusefa'))
        {
            //then we are using formal assessments
            $out .= '<th>'.get_string('formalassessments', 'block_bcgt').'</th>';
        }
        $out .= '</tr></thead><tbody>';
        if($users)
        {
            $link = $CFG->wwwroot.'/blocks/bcgt/grids/student_grid.php?';
            foreach($users AS $user)
            {
                $out .= '<tr>';
                $out .= bcgt_get_users_columns($user);
                $out .= '<td><a href="'.$link.'sID='.$user->id.'&qID='.$qualID.'&g=s&cID='.$courseID.'">'.get_string('viewsimple', 'block_bcgt').'</a></td>';
                if($canEdit)
                {
                    $out .= '<td><a href="'.$link.'sID='.$user->id.'&qID='.$qualID.'&g=se&cID='.$courseID.'">'.get_string('editsimple', 'block_bcgt').'</a></td>';
                }
                if($advancedMode)
                {
                    $out .= '<td><a href="'.$link.'sID='.$user->id.'&qID='.$qualID.'&g=a&cID='.$courseID.'">'.get_string('viewadvanced', 'block_bcgt').'</a></td>';
                    if($canEdit)
                    {
                        $out .= '<td><a href="'.$link.'sID='.$user->id.'&qID='.$qualID.'&g=ae&cID='.$courseID.'">'.get_string('editadvanced', 'block_bcgt').'</a></td>';
                    }
                }
                if(get_config('bcgt', 'alevelusefa') && $qualification->has_formal_assessments() && $qualification->get_formal_assessments())
                {
                    $out .= '<td><a href="'.$CFG->wwwroot.'/blocks/bcgt/'.
                            'grids/ass_grid.php?cID='.$courseID.'&sID='.$user->id.'&qID='.$qualID.'">'.
                            get_string('formalassessments', 'block_bcgt').
                            '</a></td>';
                }
                $out .= '</tr>';
            }
        }
        $out .= '</tbody></table>';
        $out .= '</div>';
    }
    return $out;
}

function bcgt_display_student_grid_select($search, $userID = -1, $studentID = -1)
{
    $out = '';
    $courseID = optional_param('cID', -1, PARAM_INT);
    //basically get all of the users that have quals in the system and find their quals.
    global $DB, $CFG;
    $studenRole = $DB->get_record_select('role', 'shortname = ?', array('student'));
    $sql = "SELECT distinct(userqual.id), user.id AS userid, user.firstname, user.lastname, user.username, user.picture, 
        user.email, user.url, user.imagealt, userqual.bcgtqualificationid FROM {user} user
        JOIN {block_bcgt_user_qual} userqual ON userqual.userid = user.id";
    if($userID != -1)
    {
        //then we want to search for only students that this user can see.
        $sql .= " JOIN {block_bcgt_course_qual} coursequal ON coursequal.bcgtqualificationid = userqual.bcgtqualificationid 
            JOIN {course} course ON course.id = coursequal.courseid 
            JOIN {context} context ON context.instanceid = course.id 
            JOIN {role_assignments} roleass ON roleass.contextid = context.id 
            JOIN {role} role ON role.id = roleass.roleid 
            JOIN {context} teachcontext ON teachcontext.instanceid = course.id 
            JOIN {role_assignments} teachroleass ON teachroleass.contextid = teachcontext.id
            JOIN {role} teachrole ON teachrole.id = teachroleass.roleid";   
    }
    $sql .= " WHERE userqual.roleid = ?";
    $params = array($studenRole->id);
    if($search != '')
    {
        $sql .= " AND (user.firstname LIKE ? OR user.lastname LIKE ? 
                OR user.email LIKE ? OR user.username LIKE ? ";
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
        $searchSplit = explode(' ', $search);
        if($searchSplit)
        {
            foreach($searchSplit AS $split)
            {
                $sql .= ' OR user.firstname LIKE ? OR user.lastname LIKE ? 
            OR user.email LIKE ? OR user.username LIKE ? ';
                $params[] = '%'.$split.'%';
                $params[] = '%'.$split.'%';
                $params[] = '%'.$split.'%';
                $params[] = '%'.$split.'%';
            }
        }
        $sql .= ')';
    }
    if($userID != -1)
    {
        $sql .= ' AND role.shortname = ? AND teachrole.shortname = ? AND teachroleass.userid = ?';
        $params[] = 'student';
        $params[] = 'editingteacher';
        $params[] = $userID;
    }
    if($studentID != -1)
    {
        $sql .= ' AND user.id = ?';
        $params[] = $studentID;
    }
    $sql .= ' ORDER BY lastname ASC';
    $users = $DB->get_records_sql($sql, $params);
    if($users)
    {
        $out = '<table>';
        $out .= '<tr>';
        $out .= bcgt_get_users_column_headings();
        $out .= '<th>'.get_string('quals', 'block_bcgt').'</th>';
        $out .= '</tr>';
        $out .= '<tr>';
        $lastUserID = -1;
        $link = $CFG->wwwroot.'/blocks/bcgt/grids/student_grid.php?g=s';
        foreach($users AS $user)
        {
            $userID = $user->userid;
            //we need the is of user to the userid for the
            //images. 
            $user->id = $user->userid;
            if($lastUserID != $userID)
            {
                $lastUserID = $userID;
                $out .= '</tr>';
                $out .= '<tr>';
                $out .= bcgt_get_users_columns($user);
                $qualification = Qualification::get_qualification_class_id($user->bcgtqualificationid);
                if($qualification)
                {
                    $out .= '<td><a href="'.$link.'&sID='.$userID.'&qID='.$user->bcgtqualificationid.'&cID='.$courseID.'">'.$qualification->get_display_name().'</a></td>';
                }
                //then we are on a new user
            }
            $qualification = Qualification::get_qualification_class_id($user->bcgtqualificationid);
            if($qualification)
            {
                $out .= '<td><a href="'.$link.'&sID='.$userID.'&qID='.$user->bcgtqualificationid.'&cID='.$courseID.'">'.$qualification->get_display_name().'</a></td>';
            }
        }
        $out .= '</tr>';
        $out .= '</table>';
    }
    else
    {
        $out .= '<p>'.get_string('noqualsuser', 'block_bcgt').'</p>';
    }
    return $out;
}

function bcgt_display_unit_grid_select_search($search, $familesExcluded = array(), $userID = -1, $unitID = -1)
{
    //get units
    //get the quals they are on
    $out = '';
    $courseID = optional_param('cID', -1, PARAM_INT);
    global $DB, $CFG;
    $params = array();
    $sql = "SELECT distinct(qualunits.id), qualunits.bcgtunitid, qualunits.bcgtqualificationid, unit.name, unit.uniqueid 
        FROM {block_bcgt_unit} unit JOIN {block_bcgt_qual_units} qualunits 
        ON unit.id = qualunits.bcgtunitid 
        JOIN {block_bcgt_course_qual} coursequal ON coursequal.bcgtqualificationid = qualunits.bcgtqualificationid";
    if($familesExcluded && count($familesExcluded) != 0)
    {
        $sql .= " JOIN {block_bcgt_qualification} qual ON qual.id = qualunits.bcgtqualificationid 
            JOIN {block_bcgt_target_qual} targetqual ON targetqual.id = qual.bcgttargetqualid 
            JOIN {block_bcgt_type} type ON type.id = targetqual.bcgttypeid 
            JOIN {block_bcgt_type_family} family ON family.id = type.bcgttypefamilyid";
    }
    if($userID != -1)
    {
        //then we need to find the courses that this user can see
        //then find the quals that are on this
        $sql .= ' JOIN {course} course ON course.id = coursequal.courseid 
            JOIN {context} context ON context.instanceid = course.id 
            JOIN {role_assignments} roleass ON roleass.contextid = context.id 
            JOIN {role} role ON role.id = roleass.roleid';
    }
    $sql .= " WHERE (unit.name LIKE ? OR unit.uniqueid LIKE ?";
        $params[] = '%'.$search.'%';
        $params[] = '%'.$search.'%';
        $searchSplit = explode(' ', $search);
        if($searchSplit)
        {
            foreach($searchSplit AS $split)
            {
                $sql .= ' OR unit.name LIKE ? OR unit.uniqueid LIKE ?';
                $params[] = '%'.$split.'%';
                $params[] = '%'.$split.'%';
            }
        }
        $sql .= ')';
    if($familesExcluded && count($familesExcluded) != 0)
    {
        $sql .= ' AND (';
        $count = 0;
        foreach($familesExcluded AS $family)
        {
            $count++;
            if($count != 1)
            {
                $sql .= ' AND';
            }
            $sql .= 'family.family != ?';
        }
        $params[] = $family;
        $sql .= ')';
    }
    if($userID != -1)
    {
        $sql .= ' AND role.shortname = ? AND roleass.userid = ?';
        $params[] = 'editingteacher';
        $params[] = $userID;
    }
    if($unitID != -1)
    {
        $sql .= ' AND unit.id = ?';
        $params[] = $unitID;
    }
    $units = $DB->get_records_sql($sql, $params);
    if($units)
    {
        $out = '<table>';
        $out .= '<tr>';
        $out .= '<th>'.get_string('uniqueid', 'block_bcgt').'</th>';
        $out .= '<th>'.get_string('name', 'block_bcgt').'</th>';
        $out .= '<th>'.get_string('quals', 'block_bcgt').'</th>';
        $out .= '</tr>';
        $out .= '<tr>';
        $lastUnitID = -1;
        $link = $CFG->wwwroot.'/blocks/bcgt/grids/unit_grid.php?g=s';
        foreach($units AS $unit)
        {
            $unitID = $unit->bcgtunitid;
            if($lastUnitID != $unitID)
            {
                $lastUnitID = $unitID;
                $out .= '</tr>';
                $out .= '<tr>';
                $out .= '<td>'.$unit->uniqueid.'</td>';
                $out .= '<td>'.$unit->name.'</td>';
                //then we are on a new user
            }
            $qualification = Qualification::get_qualification_class_id($unit->bcgtqualificationid);
            if($qualification)
            {
                $out .= '<td><a href="'.$link.'&uID='.$unitID.'&qID='.$unit->bcgtqualificationid.'&cID='.$courseID.'">'.$qualification->get_display_name(false).'</a></td>';
            }
        }
        $out .= '</tr>';
        $out .= '</table>';
    }
    return $out;
}

function bcgt_display_unit_grid_select($qualID, $courseID, $search)
{
    global $DB, $CFG;
    $out = '';
    //get all of the students on this qual
    $loadParams = new stdClass();
    $loadParams->loadLevel = Qualification::LOADLEVELUNITS;
    $qualification = Qualification::get_qualification_class_id($qualID, $loadParams);
    if($qualification)
    {
        $units = $qualification->get_units();
        $out .= '<table class="qualificationUsers">';
        $out .= '<thead><tr><th>'.get_string('uniqueid', 'block_bcgt').'</th><th>'.get_string('name', 'block_bcgt').'</th>'.
                '<th>'.get_string('viewsimple', 'block_bcgt').'</th><th>'.get_string('editsimple', 'block_bcgt').'</th></tr></thead><tbody>';
        if($units)
        {
            $link = $CFG->wwwroot.'/blocks/bcgt/grids/unit_grid.php?';
            foreach($units AS $unit)
            {
                $out .= '<tr>';
                $out .= '<td>'.$unit->get_uniqueID().'</td>';
                $out .= '<td>'.$unit->get_name().'</td>';
                $out .= '<td><a href="'.$link.'uID='.$unit->get_id().'&qID='.$qualID.'&g=s&cID='.$courseID.'">'.get_string('viewsimple', 'block_bcgt').'</a></td>';
                $out .= '<td><a href="'.$link.'uID='.$unit->get_id().'&qID='.$qualID.'&g=se&cID='.$courseID.'">'.get_string('editsimple', 'block_bcgt').'</a></td>';
                $out .= '</tr>';
            }
        }
        $out .= '</tbody></table>';
    }
    return $out;
}

function bcgt_display_class_grid_select_search($cID, $search, $qualExcludes, $userID = -1)
{
    global $COURSE;
    if($cID == -1)
    {
        $cID = $COURSE->id;
    }
    $context = context_course::instance($cID);
    $out = '';
    //get all of the students on this qual
    $quals = search_qualification(-1, -1, -1, $search, 
        -1, null, -1, false, false, 
        $qualExcludes, $userID);
    if($quals)
    {
        $canEdit = false;
        if(has_capability('block/bcgt:editstudentgrid', $context))
        {	
            $canEdit = true;
        }
        $out .= '<table class="qualificationClass">';
        $out .= class_qual_select_grid($quals, $cID, $canEdit);
        $out .= '</table>';
    }
    return $out;
}

function bcgt_display_assessment_grid_select_search($search, $userID = -1, $assID = -1)
{
    $retval = '';
    //so, lets either get all of the assessments in the system
    //and then a drop down of all of the quals this is on
    //or get the formal assessments that just we can see: through coursequal, context etc
    //and the quals drop down will be our quals. 
    global $DB;
    $sql = "SELECT distinct(project.id), project.* FROM {block_bcgt_project} project 
        JOIN {block_bcgt_activity_refs} refs ON refs.bcgtprojectid = project.id 
        JOIN {block_bcgt_course_qual} coursequal ON coursequal.bcgtqualificationid = refs.bcgtqualificationid";
    if($userID != -1)
    {
        $sql .= ' JOIN {context} context ON context.instanceid = coursequal.courseid 
            JOIN {role_assignments} roleass ON roleass.contextid = context.id 
            JOIN {role} role ON role.id = roleass.roleid';
    }
    $params = array();
    if($search != '' || $userID != -1 || $assID != -1)
    {
        $sql .= ' WHERE';
        $and = false;
        if($search != '')
        {
            $and = true;
            $sql .= ' project.name LIKE ?';
            $params[] = '%'.$search.'%';
            $searchSplit = explode(' ', $search);
            if($searchSplit)
            {
                foreach($searchSplit AS $split)
                {
                    $sql .= ' OR project.name LIKE ?';
                    $params[] = '%'.$split.'%';
                }
            }
        }
        if($userID != -1)
        {
            if($and)
            {
                $sql .= ' AND';
            }
            $and = true;
            $sql .= ' role.shortname = ? AND roleass.userid = ?';
            $params[] = 'editingteacher';
            $params[] = $userID;;
        }
        if($assID != -1)
        {
            if($and)
            {
                $sql .= ' AND';
            }
            $and = true;
            $sql .= ' project.id = ?';
            $params[] = $assID;
        }
    }
    
    $projects = $DB->get_records_sql($sql, $params);
    if($projects) 
    {
        $retval .= '<table>';
        $retval .= '<tr>';
        $retval .= '<th>'.get_string('assessments', 'block_bcgt').'</th><th>'.get_string('quals', 'block_bcgt').'</th>';
        $retval .= '</tr>';
        foreach($projects AS $project)
        {
            $retval .= '<tr>';
            $retval .= '<td>'.$project->targetdate.' : '.$project->name.'</td>';
            $retval .= '<td>';
            $retval .= '<select name="projectQual" id="'.$project->id.'" class="projectQualSelect">';
            $retval .= '<option value="-1">Please Select One</option>';
            $projectObj = new Project($project->id, $project);
            $quals = $projectObj->get_editable_quals($userID);
            if($quals)
            {
                foreach($quals AS $qual)
                {
                    $retval .= '<option value="'.$qual->id.'">'.bcgt_get_qualification_display_name($qual).'</option>';
                }
            }
            $retval .= '</select>';
            $retval .= '</td>';
            $retval .= '</tr>';
        }   
        $retval .= '</table>';
    }
    
    return $retval;
}

function bcgt_display_assessment_grid_select($courseID)
{
    //this wants to display the quals that are on 
    //this course and then all of the assessments that can be chosen from
    //the qual will click through to show all, the assessments will just show the
    //ass
    $retval = '';
    global $DB, $CFG;
    //so get all of the quals that are on this course
    //get all of the projects that are on these quals
    $sql = "SELECT distinct(activityrefs.id), qual.id as bcgtqualificationid, project.id as projectid, project.name, project.targetdate FROM {block_bcgt_qualification} qual 
        JOIN {block_bcgt_course_qual} coursequal ON coursequal.bcgtqualificationid = qual.id
        JOIN {block_bcgt_activity_refs} activityrefs ON activityrefs.bcgtqualificationid = qual.id
        JOIN {block_bcgt_project} project ON project.id = activityrefs.bcgtprojectid 
        WHERE coursequal.courseid = ? AND project.centrallymanaged = ? ORDER BY bcgtqualificationid ASC, project.name ASC
        ";
    $params = array($courseID, 1);
    $projects = $DB->get_records_sql($sql, $params);
    if($projects)
    {
        $retval .= '<table>';
        $retval .= '<tr>';
        $retval .= '<th>'.get_string('quals', 'block_bcgt').'</th>';
        $retval .= '<th>'.get_string('assessments', 'block_bcgt').'</th>';
        $retval .= '</tr>';
        $lastQualID = -1;
        $retval .= '<tr>';
        $qualLink = $CFG->wwwroot.'/blocks/bcgt/grids/ass_grid_class.php?cID='.$courseID.'&g=a';
        $projectLink = $CFG->wwwroot.'/blocks/bcgt/grids/ass.php?cID='.$courseID;
        foreach($projects AS $project)
        {
            if($lastQualID != $project->bcgtqualificationid)
            {
                $lastQualID = $project->bcgtqualificationid;
                //then we are on a new qual
                $retval .= '</tr>';
                $retval .= '<tr>';
                $qualification = Qualification::get_qualification_class_id($project->bcgtqualificationid);
                $retval .= '<td><a href="'.$qualLink.'&qID='.$lastQualID.'">'.$qualification->get_display_name().'</a></td>';
                $retval .= '<td><a href="'.$projectLink.'&pID='.$project->projectid.'&qID='.$lastQualID.'">'.$project->name.'</a></td>';
            }
            else {
                $retval .= '<td><a href="'.$projectLink.'&pID='.$project->projectid.'&qID='.$lastQualID.'">'.$project->name.'</a></td>';
            }
            
        }
        $retval .= '</tr>';
        $retval .= '</table>';
    }
    return $retval;
    
}

function bcgt_display_class_grid_select($courseID, $cID, $qualExcludes, $search)
{
    global $COURSE;
    if($courseID == -1)
    {
        $courseID = $COURSE->id;
    }
    $context = context_course::instance($courseID);
    $out = '';
    //get all of the students on this qual
    $quals = bcgt_get_course_quals($courseID, -1, -1, $qualExcludes, $search);
    if($quals)
    {
        $canEdit = false;
        if(has_capability('block/bcgt:editstudentgrid', $context))
        {	
            $canEdit = true;
        }
        $out .= '<table class="qualificationClass">';
        $out .= class_qual_select_grid($quals, $cID, $canEdit);
        $out .= '</table>';
    }
    return $out;
}

function class_qual_select_grid($quals, $cID, $canEdit)
{
    $out = '';
    global $CFG;
    $link = $CFG->wwwroot.'/blocks/bcgt/grids/class_grid.php?cID='.$cID;
    $out .= '<tr><th>'.get_string('qual', 'block_bcgt').'</th>';
    $out .= '<th>'.get_string('view', 'block_bcgt').'</th>';
    if($canEdit)
    {
        $out .= '<th>'.get_string('edit', 'block_bcgt').'</th>'; 
    }
    $out .= '</tr>';
    foreach($quals AS $qual)
    {
        $qualID = $qual->id;
        $loadParams = new stdClass();
        $loadParams->loadLevel = Qualification::LOADLEVELUNITS;
        $qualification = Qualification::get_qualification_class_id($qual->id, $loadParams);
        if($qualification)
        { 
            $out .= '<tr><td>'.$qualification->get_display_name(false).'</td>';
            $out .= '<td><a href="'.$link.'&qID='.$qualID.'">'.get_string('view', 'block_bcgt').'</a></td>';
            if($canEdit)
            {
                $out .= '<td><a href="'.$link.'&qID='.$qualID.'">'.get_string('edit', 'block_bcgt').'</a></td>';
            }
            $out .= '</tr>';
        }
    }
    return $out;
}

function check_target_qual_exists($familyID = -1, $typeID = -1, $subTypeID = -1, $levelID = -1)
{
    global $DB;
    $sql = "SELECT * FROM {block_bcgt_target_qual} targetqual 
        JOIN {block_bcgt_type} type ON type.id = targetqual.bcgttypeid 
        JOIN {block_bcgt_subtype} subtype ON subtype.id = targetqual.bcgtsubtypeid 
        JOIN {block_bcgt_level} level ON level.id = targetqual.bcgtlevelid 
        JOIN {block_bcgt_type_family} family ON family.id = type.bcgttypefamilyid";
    $params = array();
    if($familyID != -1 || $typeID != -1 || $subTypeID != -1 || $levelID != -1)
    {
        $sql .= " WHERE";
        $and = false;
        if($familyID != -1)
        {
            $sql .= ' family.id = ?';
            $params[] = $familyID;
            $and = true;
        }
        if($typeID != -1)
        {
            if($and)
            {
                $sql .= ' AND';
            }
            $and = true;
            $sql .= ' type.id = ?';
            $params[] = $typeID;
        }
        if($subTypeID != -1)
        {
            if($and)
            {
                $sql .= ' AND';
            }
            $and = true;
            $sql .= ' subtype.id = ?';
            $params[] = $subTypeID;
        }
        if($levelID != -1)
        {
            if($and)
            {
                $sql .= ' AND';
            }
            $sql .= ' level.id = ?';
            $params[] = $levelID;
        }
    }
    return $DB->get_records_sql($sql, $params);
}

/**
 * 
 * @global type $DB
 * @param type $quals
 * @param type $courses
 * @param type $users
 * @return type
 */
function bcgt_get_users($quals, $courses, $users)
{
    global $DB;
    $sql = "SELECT distinct(user.id) FROM {user} user 
        JOIN {block_bcgt_user_qual} userqual ON userqual.userid = user.id 
        JOIN {role_assignments} ra ON ra.userid = user.id 
        JOIN {context} con ON con.id = ra.contextid 
        JOIN {course} course ON course.id = con.instanceid 
        WHERE ";
    $params = array();
    $count = 0;
    foreach($quals AS $qual)
    {
        $count++;
        $sql .= " userqual.bcgtqualificationid = ?";
        if(count($quals) != $count)
        {
            $sql .= ' OR ';
        }
        $params[] = $qual;
    }
    $count = 0;
    foreach($courses AS $course)
    {
        $count++;
        $sql .= " course.id = ?";
        if(count($courses) != $count)
        {
            $sql .= ' OR ';
        }
        $params[] = $course;
    }
    $count = 0;
    foreach($users AS $user)
    {
        $count++;
        $sql .= " user.id = ?";
        if(count($users) != $count)
        {
            $sql .= ' OR ';
        }
        $params[] = $user;
    }
    return $DB->get_records_sql($sql, $params);
}

function bcgt_count_quals_course($courseID)
{
    global $DB;
    $sql = "SELECT count(id) FROM {block_bcgt_course_qual} WHERE courseid = ?";
    return $DB->count_records_sql($sql, array($courseID));
}


function bcgt_start_timing(){
    
   global $starttime;
   $mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $starttime = $mtime; 
    
}

function bcgt_end_timing(){
    
   global $starttime, $endtime;
   $mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $endtime = $mtime; 
   return ($endtime - $starttime); 
    
}


function bcgt_get_aspirational_target_grade($studentID, $courseID, $qualID = false){
    
    global $DB;
    
    if ($qualID){
        $record = $DB->get_record("block_bcgt_stud_course_grade", array("userid" => $studentID, "courseid" => $courseID, "qualid" => $qualID, "type" => "aspirational"));
    } else {
        $record = $DB->get_record("block_bcgt_stud_course_grade", array("userid" => $studentID, "courseid" => $courseID, "type" => "aspirational"));
    }
    if (!$record) return false;
    
    $setby = $DB->get_record("user", array("id" => $record->setbyuserid));
    
    $grade = new stdClass();
    $grade->id = $record->recordid;
    $grade->setby = $setby;
    $grade->settime = $record->settime;
    $grade->grade = false;
    
    switch($record->location)
    {
        
        case 'block_bcgt_target_breakdown':
            $obj = $DB->get_record("block_bcgt_target_breakdown", array("id" => $record->recordid));
            if ($obj)
            {
                $grade->grade = $obj->targetgrade;
            }
        break;
    
        case 'block_bcgt_target_grades':
            $obj = $DB->get_record("block_bcgt_target_grades", array("id" => $record->recordid));
            if ($obj)
            {
                $grade->grade = $obj->grade;
            }
        break;
    
        case 'block_bcgt_custom_grades':
            $obj = $DB->get_record("block_bcgt_custom_grades", array("id" => $record->recordid));
            if ($obj)
            {
                $grade->grade = $obj->grade;
            }
        break;
    
        
    }
    
    return $grade;    
    
    
}


function bcgt_flatten_by_keys($array, &$returnArray = false){
    
    if (is_array($returnArray))
    {
                
        foreach($array as $critName => $value)
        {

            $returnArray[] = $critName;
            
            if (is_array($value))
            {
                bcgt_flatten_by_keys($value, $returnArray);
            }

        }
        
        return true;
        
    }
    
    
    
    $return = array();
    
    foreach($array as $critName => $value)
    {
        
        $return[] = $critName;
        
        if (is_array($value))
        {
            bcgt_flatten_by_keys($value, $return);
        }
        
    }
    
    return $return;
    
}