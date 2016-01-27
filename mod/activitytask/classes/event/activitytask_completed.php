<?php
/**
 * Defines the view event.
 *
 * @package    mod_activitytask
 * @copyright  2015 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_activitytask\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_activitytask instance completed event class
 *
 * @package    mod_activitytask
 * @copyright  2015 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activitytask_completed extends \core\event\base {

    /**
     * Create event from activitytask_completion record.
     * @param \stdClass $status
     * @return activitytask_completion
     */
    public static function create_from_task(\stdClass $status, \stdClass $activitytask, $userid) {
        $event = self::create(
            array(
                'objectid' => $status->id,
                'context' => \context_course::instance($activitytask->course),
                'courseid' => $activitytask->course,
				'relateduserid' => $userid,
				'other' => array(
					'activitytaskid' => $activitytask->id
				)
            )
        );
        $event->add_record_snapshot('activitytask_status', $status);
        return $event;
    }

    /**
     * Initialise required event data properties.
     */
    protected function init() {
        $this->data['objecttable'] = 'activitytask_status';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('activitytaskdone', 'mod_activitytask');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->relateduserid' has completed ".
			   "the activity task status with id '$this->objectid' ".
			   "and an activity task id with '$this->other['activitytaskid']'.";
    }

    /**
     * Returns Moodle URL where the event can be observed afterwards. 
	 * Can be null, if no valid location is present. 
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/activitytask/index.php', array('id' => $this->activitytaskid));
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if(!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }
		
		if(!isset($this->other['activitytaskid'])) {
            throw new \coding_exception('The \'activitytaskid\' must be set.');
        }
		
    }
}
