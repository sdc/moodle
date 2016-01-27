<?php
/**
 * Library of interface functions and constants for module activitytypeheader
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 *
 * All the activitytypeheader specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_activitytypeheader
 * @copyright  2015 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/* Moodle core API */

/**
 * List of features supported in activitytypeheader module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function activitytypeheader_supports($feature) {
    switch($feature) {
		case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;  //Type of module
		//yes
        case FEATURE_BACKUP_MOODLE2:          return true;		//True if module supports backup/restore of moodle2 format
		//no
        case FEATURE_NO_VIEW_LINK:            return false;		//True if module has no 'view' page
        case FEATURE_MOD_INTRO:               return false;		//True if module supports intro editor
		case FEATURE_IDNUMBER:                return false;		//True if module supports outcomes	
        case FEATURE_GROUPS:                  return false;		//True if module supports groups
        case FEATURE_GROUPINGS:               return false;		//True if module supports groupings
        case FEATURE_COMPLETION_TRACKS_VIEWS: return false;		//True if module can support completion 'on view'
        case FEATURE_GRADE_HAS_GRADE:         return false;		//True if module can provide a grade
        case FEATURE_GRADE_OUTCOMES:          return false;		//True if module supports outcomes
        case FEATURE_SHOW_DESCRIPTION:        return false;		//True if module can show description on course main page

        default: return null;
    }
}
/**
 * Saves a new instance of the activitytypeheader into the database.
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $activitytypeheader Submitted data from the form in mod_form.php
 * @param mod_activitytypeheader_mod_form $mform The form instance itself (if needed)
 * @return int The id of the newly inserted activitytypeheader record
 */
function activitytypeheader_add_instance($activitytypeheader, $mform = null) {
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
	$activitytypeheader->intro = $activitytypeheader->name;
	$activitytypeheader->introformat = 0;
	$activitytypeheader->timecreated = time();
    $activitytypeheader->timemodified = time();

    return $DB->insert_record('activitytypeheader', $activitytypeheader);
}

/**
 * Updates an instance of the activitytypeheader in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $activitytypeheader An object from the form in mod_form.php
 * @param mod_activitytypeheader_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function activitytypeheader_update_instance($activitytypeheader, $mform = null) {
    global $DB;
	
	$activitytypeheader->intro = $activitytypeheader->name;
	$activitytypeheader->introformat = 0;
    $activitytypeheader->timemodified = time();
    $activitytypeheader->id = $activitytypeheader->instance;

    return $DB->update_record('activitytypeheader', $activitytypeheader);
}

/**
 * Removes an instance of the activitytypeheader from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function activitytypeheader_delete_instance($id) {
    global $DB;

    if(!$activitytypeheader = $DB->get_record('activitytypeheader', array('id' => $id))) {
        return false;
    }

    if(!$DB->delete_records('activitytypeheader', array('id' => $activitytypeheader->id))) {
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
function activitytypeheader_reset_userdata($data) {
    return array();
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function activitytypeheader_get_extra_capabilities() {
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
/*
function activitytypeheader_get_coursemodule_info($coursemodule) {
	global $DB;
	
    $context = context_module::instance($coursemodule->id);
		
    if(!$activitytypeheader = $DB->get_record('activitytypeheader', array('id' => $coursemodule->instance), 'name')) {
        return NULL;
    }
    $info = new cached_cm_info();
	$info->name = $activitytypeheader->name;
	
    return $info;
}
//*/

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
function activitytypeheader_cm_info_view(cm_info $cm) {
	global $PAGE;
	if(!$PAGE->user_is_editing()) {
		$cm->set_content('<div class="atheader">'.$cm->name.'</div>');
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
function activitytypeheader_cm_info_dynamic(cm_info $cm) {
	global $PAGE;
	//We want to keep the editing capability so that we can have the inline editing button
	if(!$PAGE->user_is_editing()) {
		//don't show the link if we are not editing
		$cm->set_no_view_link();
	} else {
		//hide the icon
		$cm->set_icon_url(new moodle_url(''));
	}
}
