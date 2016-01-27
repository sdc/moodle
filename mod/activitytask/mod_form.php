<?php

/**
 * The main activitytask configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_activitytask
 * @copyright  2015 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form
 *
 * @package    mod_activitytask
 * @copyright  2015 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_activitytask_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
	 * @see: https://docs.moodle.org/dev/lib/formslib.php_Form_Definition
     */
    public function definition() {

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement(
			'text',										//type of element 
			'name', 									//name of the element
			get_string('activitytaskname', 'activitytask'), 	//label
			array('size' => '64')						//html attributes
		);
        $mform->setType('name', PARAM_TEXT);
        /*
		 * $mform->addRule($element, $message, $type, $format, $validation, $reset, $force)
		 * @param    string     $element       Form element name
		 * @param    string     $message       Message to display for invalid data
		 * @param    string     $type          Rule type, use getRegisteredRules() to get types
		 * @param    string     $format        (optional)Required for extra rule data
		 * @param    string     $validation    (optional)Where to perform validation: "server", "client"
		 * @param    boolean    $reset         Client-side validation: reset the form element to its original value if there is an error?
		 * @param    boolean    $force         Force the rule to be applied, even if the target form element does not exist
		*/
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 500), 'maxlength', 500, 'client');
        
		/**
		 * @param 	$elementname 	The name of the form element to add the help button for
		 * @param 	$identifier 	The identifier for the help string and its title (see below)
		 * @param 	$component 		The component name to look for the help string in
		 */
		//$mform->addHelpButton('name', 'activitytaskname', 'activitytask');
		
		// Add the intro editor box
		// @see /course/moodleform_mod.php
		//$this->standard_intro_elements(get_string('introlabel', 'activitytask'));
		
		// Add the box for the details of the activity task
		$mform->addElement('editor', 'details', get_string('details', 'activitytask'), array('rows' => 10), 
							array('maxfiles' => EDITOR_UNLIMITED_FILES, 'noclean' => true, 'context' => $this->context, 'subdirs' => true));
		$mform->setType('details', PARAM_RAW);
		$mform->addHelpButton('details', 'details', 'activitytask');
		
		// Adding the date due field
		$name = get_string('duedate', 'activitytask');
        $mform->addElement('date_time_selector', 'duedate', $name, array('optional'=>true));
        $mform->addHelpButton('duedate', 'duedate', 'activitytask');
		
        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }
	/**
     * Load in existing data as form defaults. Usually new entry defaults are stored directly in
     * form definition (new entry form); this function is used to load in data where values
     * already exist and data is being edited (edit entry form).
	 *
	 * @see: /course/moodleform.php
     */
	function set_data($default_values) {
		if(is_object($default_values)) {
            $default_values = (array)$default_values;
        }
		$default_values['details']['text'] = $default_values['intro'];		
		parent::set_data($default_values);
	}

    /**
     * Add any custom completion rules to the form.
     *
     * @return array Contains the names of the added form elements
     */
    public function add_completion_rules() {
        $mform =& $this->_form;

        $mform->addElement('checkbox', 'completiondone', '', get_string('completiondone', 'activitytask'));
        return array('completiondone');
    }

    /**
     * Determines if completion is enabled for this module.
     *
     * @param array $data
     * @return bool
     */
    public function completion_rule_enabled($data) {
        return !empty($data['completiondone']);
    }
}
