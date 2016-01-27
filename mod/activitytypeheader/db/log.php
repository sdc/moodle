<?php
/**
 * Definition of log events
 *
 * @package    mod_activitytypeheader
 * @category   log
 * @copyright  2015 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$logs = array(
    array('module'=>'activitytypeheader', 'action'=>'add', 'mtable'=>'activitytypeheader', 'field'=>'name'),
    array('module'=>'activitytypeheader', 'action'=>'update', 'mtable'=>'activitytypeheader', 'field'=>'name'),
);