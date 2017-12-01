<?php
/**
 * Define all the backup steps that will be used by the backup_activitytask_activity_task
 *
 * @package   mod_activitytask
 * @category  backup
 * @copyright 2015 Blake Kidney
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Define the complete activitytask structure for backup, with file and id annotations
 *
 * @package   mod_activitytask
 * @category  backup
 * @copyright 2015 Blake Kidney
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_activitytask_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the backup structure of the module
     *
     * @return backup_nested_element
     */
    protected function define_structure() {

        // Get know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define the root element describing the activitytask instance.
        $activitytask = new backup_nested_element('activitytask', array('id'), array(
            'name', 'duedate', 'intro', 'introformat', 'completiondone', 'timemodified'));

        // If we had more elements, we would build the tree here.

        // Define data sources.
        $activitytask->set_source_table('activitytask', array('id' => backup::VAR_ACTIVITYID));

        // If we were referring to other tables, we would annotate the relation
        // with the element's annotate_ids() method.

        // Define file annotations (we do not use itemid in this example).
        $activitytask->annotate_files('mod_activitytask', 'intro', null);

        // Return the root element (activitytask), wrapped into standard activity structure.
        return $this->prepare_activity_structure($activitytask);
    }
}
