<?php
/**
 * Define all the restore steps that will be used by the restore_activitytypeheader_activity_task
 *
 * @package   mod_activitytypeheader
 * @category  backup
 * @copyright 2015 Blake Kidney
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Structure step to restore one activitytypeheader activity
 *
 * @package   mod_activitytypeheader
 * @category  backup
 * @copyright 2015 Blake Kidney
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_activitytypeheader_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines structure of path elements to be processed during the restore
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('activitytypeheader', '/activity/activitytypeheader');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the given restore path element data
     *
     * @param array $data parsed element data
     */
    protected function process_activitytypeheader($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        if (empty($data->timecreated)) {
            $data->timecreated = time();
        }

        if (empty($data->timemodified)) {
            $data->timemodified = time();
        }

        // Create the activitytypeheader instance.
        $newitemid = $DB->insert_record('activitytypeheader', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Post-execution actions
     */
    protected function after_execute() {
        
    }
}
