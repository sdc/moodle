<?php
/**
 * Defines backup_activitytask_activity_task class
 *
 * @package   mod_activitytask
 * @category  backup
 * @copyright 2015 Blake Kidney
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/activitytask/backup/moodle2/backup_activitytask_stepslib.php');

/**
 * Provides the steps to perform one complete backup of the activitytask instance
 *
 * @package   mod_activitytask
 * @category  backup
 * @copyright 2015 Blake Kidney
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_activitytask_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the activitytask.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_activitytask_activity_structure_step('activitytask_structure', 'activitytask.xml'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {
        return $content;
    }
}
