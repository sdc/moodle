<?php
/**
 * Definition of log events
 *
 * @package    mod_activitytask
 * @category   log
 * @copyright  2015 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$logs = array(
    array('module'=>'activitytask', 'action'=>'add', 'mtable'=>'activitytask', 'field'=>'name'),
    array('module'=>'activitytask', 'action'=>'update', 'mtable'=>'activitytask', 'field'=>'name'),
);