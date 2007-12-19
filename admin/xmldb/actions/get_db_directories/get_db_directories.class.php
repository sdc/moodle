<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// This class will check all the db directories existing under the
/// current Moodle installation, sending them to the SESSION->dbdirs array

class get_db_directories extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();
    /// Set own core attributes
        $this->can_subaction = ACTION_NONE;
        //$this->can_subaction = ACTION_HAVE_SUBACTIONS;

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
        /// 'key' => 'module',
        ));
    }

    /**
     * Invoke method, every class will have its own
     * returns true/false on completion, setting both
     * errormsg and output as necessary
     */
    function invoke() {
        parent::invoke();

        $result = true;

    /// Set own core attributes
        $this->does_generate = ACTION_NONE;
        //$this->does_generate = ACTION_GENERATE_HTML;

    /// These are always here
        global $CFG, $XMLDB;

    /// Do the job, setting $result as needed

    /// Lets go to add all the db directories available inside Moodle
    /// Create the array if it doesn't exists
        if (!isset($XMLDB->dbdirs)) {
            $XMLDB->dbdirs = array();
        }
    /// First, the main one (lib/db)
        $dbdir = new stdClass;
        $dbdir->path = $CFG->libdir . '/db';
        if (!isset($XMLDB->dbdirs[$dbdir->path])) {
            $XMLDB->dbdirs[$dbdir->path] = $dbdir;
        }
        $XMLDB->dbdirs[$dbdir->path]->path_exists = file_exists($dbdir->path);  //Update status

    /// Now, activity modules (mod/xxx/db)
        if ($plugins = get_list_of_plugins('mod')) {
            foreach ($plugins as $plugin) {
                $dbdir = new stdClass;
                $dbdir->path = $CFG->dirroot . '/mod/' . $plugin . '/db';
                if (!isset($XMLDB->dbdirs[$dbdir->path])) {
                    $XMLDB->dbdirs[$dbdir->path] = $dbdir;
                }
                $XMLDB->dbdirs[$dbdir->path]->path_exists = file_exists($dbdir->path);  //Update status
            }
        }

    /// Now, assignment submodules (mod/assignment/type/xxx/db)
        if ($plugins = get_list_of_plugins('mod/assignment/type')) {
            foreach ($plugins as $plugin) {
                $dbdir = new stdClass;
                $dbdir->path = $CFG->dirroot . '/mod/assignment/type/' . $plugin . '/db';
                if (!isset($XMLDB->dbdirs[$dbdir->path])) {
                    $XMLDB->dbdirs[$dbdir->path] = $dbdir;
                }
                $XMLDB->dbdirs[$dbdir->path]->path_exists = file_exists($dbdir->path);  //Update status
            }
        }

    /// Now, question types (question/type/xxx/db)
        if ($plugins = get_list_of_plugins('question/type')) {
            foreach ($plugins as $plugin) {
                $dbdir = new stdClass;
                $dbdir->path = $CFG->dirroot . '/question/type/' . $plugin . '/db';
                if (!isset($XMLDB->dbdirs[$dbdir->path])) {
                    $XMLDB->dbdirs[$dbdir->path] = $dbdir;
                }
                $XMLDB->dbdirs[$dbdir->path]->path_exists = file_exists($dbdir->path);  //Update status
            }
        }

    /// Now, backup/restore stuff (backup/db)
        $dbdir = new stdClass;
        $dbdir->path = $CFG->dirroot . '/backup/db';
        if (!isset($XMLDB->dbdirs[$dbdir->path])) {
            $XMLDB->dbdirs[$dbdir->path] = $dbdir;
        }
        $XMLDB->dbdirs[$dbdir->path]->path_exists = file_exists($dbdir->path);  //Update status

    /// Now, block system stuff (blocks/db)
        $dbdir = new stdClass;
        $dbdir->path = $CFG->dirroot . '/blocks/db';
        if (!isset($XMLDB->dbdirs[$dbdir->path])) {
            $XMLDB->dbdirs[$dbdir->path] = $dbdir;
        }
        $XMLDB->dbdirs[$dbdir->path]->path_exists = file_exists($dbdir->path);  //Update status

    /// Now, blocks (blocks/xxx/db)
        if ($plugins = get_list_of_plugins('blocks', 'db')) {
            foreach ($plugins as $plugin) {
                $dbdir = new stdClass;
                $dbdir->path = $CFG->dirroot . '/blocks/' . $plugin . '/db';
                if (!isset($XMLDB->dbdirs[$dbdir->path])) {
                    $XMLDB->dbdirs[$dbdir->path] = $dbdir;
                }
                $XMLDB->dbdirs[$dbdir->path]->path_exists = file_exists($dbdir->path);  //Update status
            }
        }

    /// Now, course formats (course/format/xxx/db)
        if ($plugins = get_list_of_plugins('course/format', 'db')) {
            foreach ($plugins as $plugin) {
                $dbdir = new stdClass;
                $dbdir->path = $CFG->dirroot . '/course/format/' . $plugin . '/db';
                if (!isset($XMLDB->dbdirs[$dbdir->path])) {
                    $XMLDB->dbdirs[$dbdir->path] = $dbdir;
                }
                $XMLDB->dbdirs[$dbdir->path]->path_exists = file_exists($dbdir->path);  //Update status
            }
        }

    /// Now, enrolment plugins (enrol/xxx/db)
        if ($plugins = get_list_of_plugins('enrol', 'db')) {
            foreach ($plugins as $plugin) {
                $dbdir = new stdClass;
                $dbdir->path = $CFG->dirroot . '/enrol/' . $plugin . '/db';
                if (!isset($XMLDB->dbdirs[$dbdir->path])) {
                    $XMLDB->dbdirs[$dbdir->path] = $dbdir;
                }
                $XMLDB->dbdirs[$dbdir->path]->path_exists = file_exists($dbdir->path);  //Update status
            }
        }

    /// Local database changes, if the local folder exists.
        if (file_exists($CFG->dirroot . '/local')) {
            $dbdir = new stdClass;
            $dbdir->path = $CFG->dirroot . '/local/db';
            if (!isset($XMLDB->dbdirs[$dbdir->path])) {
                $XMLDB->dbdirs[$dbdir->path] = $dbdir;
            }
            $XMLDB->dbdirs[$dbdir->path]->path_exists = file_exists($dbdir->path);  //Update status
        }

    /// Sort by key
        ksort($XMLDB->dbdirs);

    /// Return ok if arrived here
        return true;
    }
}
?>
