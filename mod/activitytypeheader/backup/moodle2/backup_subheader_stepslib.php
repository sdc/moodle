<?php
/**
 * Define all the backup steps that will be used by the backup_activitytypeheader_activity_task
 *
 * @package   mod_activitytypeheader
 * @category  backup
 * @copyright 2015 Blake Kidney
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Define the complete activitytypeheader structure for backup, with file and id annotations
 *
 * @package   mod_activitytypeheader
 * @category  backup
 * @copyright 2015 Blake Kidney
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_activitytypeheader_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the backup structure of the module
     *
     * @return backup_nested_element
     */
    protected function define_structure() {

        // Get know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define the root element describing the activitytypeheader instance.
        $activitytypeheader = new backup_nested_element('activitytypeheader', array('id'), array(
            'activitytypeheader', 'timemodified'));

        // If we had more elements, we would build the tree here.

        // Define data sources.
        $activitytypeheader->set_source_table('activitytypeheader', array('id' => backup::VAR_ACTIVITYID));

        // If we were referring to other tables, we would annotate the relation
        // with the element's annotate_ids() method.

        // Define file annotations (we do not use itemid in this example).
        //$activitytypeheader->annotate_files('mod_activitytypeheader', 'intro', null);

        // Return the root element (activitytypeheader), wrapped into standard activity structure.
        return $this->prepare_activity_structure($activitytypeheader);
    }
}
