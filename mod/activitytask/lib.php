<?php
/**
 * Library of interface functions and constants for module activitytask
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 *
 * All the activitytask specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_activitytask
 * @copyright  2015 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/* Moodle core API */

/**
 * List of features supported in activitytask module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function activitytask_supports($feature) {
    switch($feature) {
		case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;  //Type of module
		//yes
        case FEATURE_BACKUP_MOODLE2:          return true;		//True if module supports backup/restore of moodle2 format
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;		//True if module can support completion 'on view'
        case FEATURE_COMPLETION_HAS_RULES: 	  return true;		//True if module has custom completion rule
		//no
        case FEATURE_NO_VIEW_LINK:            return false;		//True if module has no 'view' page
        case FEATURE_MOD_INTRO:               return false;		//True if module supports intro editor
		case FEATURE_IDNUMBER:                return false;		//True if module supports outcomes	
        case FEATURE_GROUPS:                  return false;		//True if module supports groups
        case FEATURE_GROUPINGS:               return false;		//True if module supports groupings
        case FEATURE_GRADE_HAS_GRADE:         return false;		//True if module can provide a grade
        case FEATURE_GRADE_OUTCOMES:          return false;		//True if module supports outcomes
        case FEATURE_SHOW_DESCRIPTION:        return false;		//True if module can show description on course main page

        default: return null;
    }
}
/**
 * Saves a new instance of the activitytask into the database.
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $activitytask Submitted data from the form in mod_form.php
 * @param mod_activitytask_mod_form $mform The form instance itself (if needed)
 * @return int The id of the newly inserted activitytask record
 */
function activitytask_add_instance($activitytask, $mform = null) {
    global $DB;
	
	/*
	 * The intro format can be the following:
	 * define('FORMAT_MOODLE',   '0');   // Does all sorts of transformations and filtering
	 * define('FORMAT_HTML',     '1');   // Plain HTML (with some tags stripped)
	 * define('FORMAT_PLAIN',    '2');   // Plain text (even tags are printed in full)
	 * define('FORMAT_WIKI',     '3');   // Wiki-formatted text
	 * define('FORMAT_MARKDOWN', '4');   // Markdown-formatted text http://daringfireball.net/projects/markdown/
	 * @see: /lib/weblib.php
	 */
	$activitytask->intro = (trim(strip_tags($activitytask->details['text']))) ? $activitytask->details['text'] : '';
	$activitytask->introformat = 0;
	$activitytask->timecreated = time();
    $activitytask->timemodified = time();

    return $DB->insert_record('activitytask', $activitytask);
}

/**
 * Updates an instance of the activitytask in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $activitytask An object from the form in mod_form.php
 * @param mod_activitytask_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function activitytask_update_instance($activitytask, $mform = null) {
    global $DB;
	
	$activitytask->intro = (trim(strip_tags($activitytask->details['text']))) ? $activitytask->details['text'] : '';
	$activitytask->introformat = 0;
    $activitytask->timemodified = time();
    $activitytask->id = $activitytask->instance;

    return $DB->update_record('activitytask', $activitytask);
}

/**
 * Removes an instance of the activitytask from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function activitytask_delete_instance($id) {
    global $DB;

    if(!$activitytask = $DB->get_record('activitytask', array('id' => $id))) {
        return false;
    }

    if(!$DB->delete_records('activitytask', array('id' => $activitytask->id))) {
		return false;
	}

    return true;
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 *
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function activitytask_reset_userdata($data) {
    return array();
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function activitytask_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}
/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @param stdClass $coursemodule
 * @return cached_cm_info info
 */
function activitytask_get_coursemodule_info($coursemodule) {
	global $DB;
	
    $context = context_module::instance($coursemodule->id);
		
    if(!$activitytask = $DB->get_record('activitytask', array('id' => $coursemodule->instance), 'name,intro')) {
        return NULL;
    }
    $info = new cached_cm_info();
	$info->name = $activitytask->name;
	$info->customdata = (trim(strip_tags($activitytask->intro))) ? '1' : '0';
	
    return $info;
}


/**
 * Called when viewing course page. Adds information to the course-module object.
 *
 * @see: /lib/modinfolib.php  class cm_info
 *
 * Allowed methods:
 * - {@link cm_info::set_after_edit_icons()}
 * - {@link cm_info::set_after_link()}
 * - {@link cm_info::set_content()}
 * - {@link cm_info::set_extra_classes()
 * 
 * @param cm_info $cm Course-module object
 */
function activitytask_cm_info_view(cm_info $cm) {
	global $USER, $PAGE, $DB;
	
	//we will create a done button for marking whether or not an item is complete
	$done = '';
	$view = '';
				
	//create a view button
	if($cm->customdata) {
		$view = '<a class="activitytask-button-view btn btn-primary"'.
		' href="'.new moodle_url('/mod/activitytask/view.php?id='.$cm->id).'"'.
		'>'.get_string('btn_label_view', 'activitytask').'</a>';
	}

	//let's see if the user has a capability to add an activity instance
	//this indicates that they are a teacher or the like and not a student
	//we don't need to show a done button if the user can edit the instance
	if(!has_capability('mod/activitytask:addinstance', context_course::instance($cm->course))) {
		
		//pull this users status for this activity
		$params = array(
			'userid' => $USER->id, 
			'activitytask' => $cm->instance
		);
		$fields = 'datedone';		
		$status = $DB->get_record('activitytask_status', $params, $fields);	
		
		//if not done, then show a button
		if(!$status || !$status->datedone) {
			//add the javascript to make it possible to update using ajax
			$PAGE->requires->js('/mod/activitytask/activitytask.js');
			$done = '<a class="activitytask-button btn btn-primary"'.
					  ' href="'.new moodle_url('/mod/activitytask/markdone.php?id='.$cm->instance).'"'.
					  ' >'.get_string('btn_label_done', 'activitytask').'</a>';
		} else {
			$dt = new DateTime($status->datedone);
			$done = '<span class="activitytask-done">('.$dt->format('M j').')</span>';		
		}
	
	}
	
	
	if(!$PAGE->user_is_editing()) {
		//if no view link is set, then the url will be blank
		if(!$cm->url) {
			//we have to add our own icon
			$cm->set_content(
				'<img role="presentation" class="iconlarge activityicon" src="'.$cm->get_icon_url().'">'.
				'<span class="instancename">'.$cm->name.
				'<span class="accesshide "> Activity Task</span>'.
				'</span>'.$view.$done
			);
		} else {
			$cm->set_after_link($view.$done);
		}
	}
	
}
/**
 * Adds information to the course-module object.
 *
 * Allowed methods:
 * - {@link cm_info::set_available()}
 * - {@link cm_info::set_name()}
 * - {@link cm_info::set_no_view_link()}
 * - {@link cm_info::set_user_visible()}
 * - {@link cm_info::set_on_click()}
 * - {@link cm_info::set_icon_url()}
 *
 * @param cm_info $cm Course-module object
 */
function activitytask_cm_info_dynamic(cm_info $cm) {	
	/*
	//we want to hide the view link if this is a student based upon whether they can add the instance or not
	if(!has_capability('mod/activitytask:addinstance', context_course::instance($cm->course))) {
		//don't show the view link as this person cannot add an instance of this module
		$cm->set_no_view_link();
	}
	//*/
}
/**
 * Obtains the automatic completion state for this module based on any conditions
 * in assign settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 */
function activitytask_get_completion_state($course, $cm, $userid, $type) {
    global $CFG, $DB;

	//obtain the activitytask instance
    $activitytask = $DB->get_record('activitytask', array('id' => $cm->instance), '*', MUST_EXIST);
	
    // If completion option is enabled, evaluate it and return true/false.
    if($activitytask->completiondone) {
		
		$params = array(
			'userid' => $userid, 
			'activitytask' => $cm->instance
		);	
		$status = $DB->get_record('activitytask_status', $params, 'id', IGNORE_MISSING);	
        return ($status);
    
	} else {
        // Completion option is not enabled so just return $type.
        return $type;
    }
}

