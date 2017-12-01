<?php
/**
 * Defines the view event.
 *
 * @package    mod_activitytypeheader
 * @copyright  2015 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_activitytypeheader\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_activitytypeheader instance list viewed event class
 *
 * If the view mode needs to be stored as well, you may need to
 * override methods get_url() and get_legacy_log_data(), too.
 *
 * @package    mod_activitytypeheader
 * @copyright  2015 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_viewed extends \core\event\course_module_viewed {

    /**
     * Initialize the event
     */
    protected function init() {
        $this->data['objecttable'] = 'activitytypeheader';
        parent::init();
    }
}
