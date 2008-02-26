<?php  //$Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
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
require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once '../grade_import_form.php';
require_once '../lib.php';

$id = required_param('id', PARAM_INT); // course id

if (!$course = get_record('course', 'id', $id)) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $id);
require_capability('moodle/grade:import', $context);
require_capability('gradeimport/csv:view', $context);

// sort out delimiter
$csv_encode = '/\&\#44/';
if (isset($CFG->CSV_DELIMITER)) {
    $csv_delimiter = '\\' . $CFG->CSV_DELIMITER;
    $csv_delimiter2 = $CFG->CSV_DELIMITER;

    if (isset($CFG->CSV_ENCODE)) {
        $csv_encode = '/\&\#' . $CFG->CSV_ENCODE . '/';
    }
} else {
    $csv_delimiter = "\,";
    $csv_delimiter2 = ",";
}

$strgrades = get_string('grades', 'grades');
$actionstr = get_string('csv', 'grades');
$navigation = grade_build_nav(__FILE__, $actionstr, array('courseid' => $course->id));

print_header($course->shortname.': '.get_string('grades'), $course->fullname, $navigation);
print_grade_plugin_selector($id, 'import', 'csv');

// set up import form
$mform = new grade_import_form();

// set up grade import mapping form
$header = '';
$gradeitems = array();
if ($id) {
    if ($grade_items = grade_item::fetch_all(array('courseid'=>$id))) {
        foreach ($grade_items as $grade_item) {
            // skip course type and category type
            if ($grade_item->itemtype == 'course' || $grade_item->itemtype == 'category') {
                continue;
            }

            // this was idnumber
            $gradeitems[$grade_item->id] = $grade_item->get_name();
        }
    }
}

if ($importcode = optional_param('importcode', '', PARAM_FILE)) {
    $filename = $CFG->dataroot.'/temp/gradeimport/cvs/'.$USER->id.'/'.$importcode;
    $fp = fopen($filename, "r");
    $header = split($csv_delimiter, fgets($fp,1024), PARAM_RAW);
}

$mform2 = new grade_import_mapping_form(null, array('gradeitems'=>$gradeitems, 'header'=>$header));

// if import form is submitted
if ($formdata = $mform->get_data()) {

    // Large files are likely to take their time and memory. Let PHP know
    // that we'll take longer, and that the process should be recycled soon
    // to free up memory.
    @set_time_limit(0);
    @raise_memory_limit("192M");
    if (function_exists('apache_child_terminate')) {
        @apache_child_terminate();
    }

    // use current (non-conflicting) time stamp
    $importcode = get_new_importcode();
    if (!$filename = make_upload_directory('temp/gradeimport/cvs/'.$USER->id, true)) {
        die;
    }
    $filename = $filename.'/'.$importcode;

    $text = $mform->get_file_content('userfile');
    // trim utf-8 bom
    $textlib = textlib_get_instance();
    /// normalize line endings and do the encoding conversion
    $text = $textlib->convert($text, $formdata->encoding);
    $text = $textlib->trim_utf8_bom($text);
    // Fix mac/dos newlines
    $text = preg_replace('!\r\n?!',"\n",$text);
    $fp = fopen($filename, "w");
    fwrite($fp,$text);
    fclose($fp);

    $fp = fopen($filename, "r");

    // --- get header (field names) ---
    $header = split($csv_delimiter, fgets($fp,1024), PARAM_RAW);

    // print some preview
    $numlines = 0; // 0 preview lines displayed

    print_heading(get_string('importpreview', 'grades'));
    echo '<table>';
    echo '<tr>';
    foreach ($header as $h) {
        $h = clean_param($h, PARAM_RAW);
        echo '<th>'.$h.'</th>';
    }
    echo '</tr>';
    while (!feof ($fp) && $numlines <= $formdata->previewrows) {
        $lines = split($csv_delimiter, fgets($fp,1024));
        echo '<tr>';
        foreach ($lines as $line) {
            echo '<td>'.$line.'</td>';;
        }
        $numlines ++;
        echo '</tr>';
    }
    echo '</table>';

    // display the mapping form with header info processed
    $mform2 = new grade_import_mapping_form(null, array('gradeitems'=>$gradeitems, 'header'=>$header));
    $mform2->set_data(array('importcode'=>$importcode, 'id'=>$id));
    $mform2->display();

//} else if (($formdata = data_submitted()) && !empty($formdata->map)) {
 
// else if grade import mapping form is submitted
} else if ($formdata = $mform2->get_data()) {

    $importcode = clean_param($formdata->importcode, PARAM_FILE);
    $filename = $CFG->dataroot.'/temp/gradeimport/cvs/'.$USER->id.'/'.$importcode;

    if (!file_exists($filename)) {
        error('error processing upload file');
    }

    if ($fp = fopen($filename, "r")) {
        // --- get header (field names) ---
        $header = split($csv_delimiter, clean_param(fgets($fp,1024), PARAM_RAW));

        foreach ($header as $i => $h) {
            $h = trim($h); $header[$i] = $h; // remove whitespace
        }
    } else {
        error ('could not open file');
    }

    $map = array();
    // loops mapping_0, mapping_1 .. mapping_n and construct $map array
    foreach ($header as $i => $head) {
        $map[$i] = $formdata->{'mapping_'.$i};
    }

    // if mapping informatioin is supplied
    $map[clean_param($formdata->mapfrom, PARAM_RAW)] = clean_param($formdata->mapto, PARAM_RAW);

    // check for mapto collisions
    $maperrors = array();
    foreach ($map as $i=>$j) {
        if ($j == 0) {
            // you can have multiple ignores
            continue;
        } else {
            if (!isset($maperrors[$j])) {
                $maperrors[$j] = true;
            } else {
                // collision
                unlink($filename); // needs to be uploaded again, sorry
                error('mapping collision detected, 2 fields maps to the same grade item '.$j);
            }
        }
    }

    // Large files are likely to take their time and memory. Let PHP know
    // that we'll take longer, and that the process should be recycled soon
    // to free up memory.
    @set_time_limit(0);
    @raise_memory_limit("192M");
    if (function_exists('apache_child_terminate')) {
        @apache_child_terminate();
    }

    // we only operate if file is readable
    if ($fp = fopen($filename, "r")) {

        // read the first line makes sure this doesn't get read again
        $header = split($csv_delimiter, fgets($fp,1024));

        $newgradeitems = array(); // temporary array to keep track of what new headers are processed
        $status = true;

        while (!feof ($fp)) {
            // add something
            $line = split($csv_delimiter, fgets($fp,1024));

            if(count($line) <= 1){
                // there is no data on this line, move on
                continue;
            }

            // array to hold all grades to be inserted
            $newgrades = array();
            // array to hold all feedback
            $newfeedbacks = array();
            // each line is a student record
            foreach ($line as $key => $value) {
                //decode encoded commas
                $value = clean_param($value, PARAM_RAW);
                $value = preg_replace($csv_encode,$csv_delimiter2,trim($value));

                /*
                 * the options are
                 * 1) userid, useridnumber, usermail, username - used to identify user row
                 * 2) new - new grade item
                 * 3) id - id of the old grade item to map onto
                 * 3) feedback_id - feedback for grade item id
                 */

                $t = explode("_", $map[$key]);
                $t0 = $t[0];
                if (isset($t[1])) {
                    $t1 = (int)$t[1];
                } else {
                    $t1 = '';
                }

                switch ($t0) {
                    case 'userid': //
                        if (!$user = get_record('user','id', addslashes($value))) {
                            // user not found, abort whold import
                            import_cleanup($importcode);
                            notify("user mapping error, could not find user with id \"$value\"");
                            $status = false;
                            break 3;
                        }
                        $studentid = $value;
                    break;
                    case 'useridnumber':
                        if (!$user = get_record('user', 'idnumber', addslashes($value))) {
                             // user not found, abort whold import
                            import_cleanup($importcode);
                            notify("user mapping error, could not find user with idnumber \"$value\"");
                            $status = false;
                            break 3;
                        }
                        $studentid = $user->id;
                    break;
                    case 'useremail':
                        if (!$user = get_record('user', 'email', addslashes($value))) {
                            import_cleanup($importcode);
                            notify("user mapping error, could not find user with email address \"$value\"");
                            $status = false;
                            break 3;
                        }
                        $studentid = $user->id;
                    break;
                    case 'username':
                        if (!$user = get_record('user', 'username', addslashes($value))) {
                            import_cleanup($importcode);
                            notify("user mapping error, could not find user with username \"$value\"");
                            $status = false;
                            break 3;
                        }
                        $studentid = $user->id;
                    break;
                    case 'new':
                        // first check if header is already in temp database

                        if (empty($newgradeitems[$key])) {

                            $newgradeitem = new object();
                            $newgradeitem->itemname = $header[$key];
                            $newgradeitem->importcode = $importcode;
                            $newgradeitem->importer   = $USER->id;

                            // failed to insert into new grade item buffer
                            if (!$newgradeitems[$key] = insert_record('grade_import_newitem', addslashes_recursive($newgradeitem))) {
                                $status = false;
                                import_cleanup($importcode);
                                notify(get_string('importfailed', 'grades'));
                                break 3;
                            }
                            // add this to grade_import_newitem table
                            // add the new id to $newgradeitem[$key]
                        }
                        $newgrade = new object();
                        $newgrade->newgradeitem = $newgradeitems[$key];
                        $newgrade->finalgrade   = $value;
                        $newgrades[] = $newgrade;

                        // if not, put it in
                        // else, insert grade into the table
                    break;
                    case 'feedback':
                        if ($t1) {
                            // case of an id, only maps id of a grade_item
                            // this was idnumber
                            if (!$gradeitem = new grade_item(array('id'=>$t1, 'courseid'=>$course->id))) {
                                // supplied bad mapping, should not be possible since user
                                // had to pick mapping
                                $status = false;
                                import_cleanup($importcode);
                                notify(get_string('importfailed', 'grades'));
                                break 3;
                            }

                            // t1 is the id of the grade item
                            $feedback = new object();
                            $feedback->itemid   = $t1;
                            $feedback->feedback = $value;
                            $newfeedbacks[] = $feedback;
                        }
                    break;
                    default:
                        // existing grade items
                        if (!empty($map[$key])) {
                            if ($value === '' or $value == '-') {
                                $value = null; // no grade

                            } else if (!is_numeric($value)) {
                            // non numeric grade value supplied, possibly mapped wrong column
                                echo "<br/>t0 is $t0";
                                echo "<br/>grade is $value";
                                $status = false;
                                import_cleanup($importcode);
                                notify(get_string('badgrade', 'grades'));
                                break 3;
                            }

                            // case of an id, only maps id of a grade_item
                            // this was idnumber
                            if (!$gradeitem = new grade_item(array('id'=>$map[$key], 'courseid'=>$course->id))) {
                                // supplied bad mapping, should not be possible since user
                                // had to pick mapping
                                $status = false;
                                import_cleanup($importcode);
                                notify(get_string('importfailed', 'grades'));
                                break 3;
                            }

                            // check if grade item is locked if so, abort
                            if ($gradeitem->is_locked()) {
                                $status = false;
                                import_cleanup($importcode);
                                notify(get_string('gradeitemlocked', 'grades'));
                                break 3;
                            }

                            $newgrade = new object();
                            $newgrade->itemid     = $gradeitem->id;
                            $newgrade->finalgrade = $value;
                            $newgrades[] = $newgrade;
                        } // otherwise, we ignore this column altogether
                          // because user has chosen to ignore them (e.g. institution, address etc)
                    break;
                }
            }

            // no user mapping supplied at all, or user mapping failed
            if (empty($studentid) || !is_numeric($studentid)) {
                // user not found, abort whold import
                $status = false;
                import_cleanup($importcode);
                notify('user mapping error, could not find user!');
                break;
            }

            // insert results of this students into buffer
            if ($status and !empty($newgrades)) {

                foreach ($newgrades as $newgrade) {

                    // check if grade_grade is locked and if so, abort
                    if (!empty($newgrade->itemid) and $grade_grade = new grade_grade(array('itemid'=>$newgrade->itemid, 'userid'=>$studentid))) {
                        if ($grade_grade->is_locked()) {
                            // individual grade locked
                            $status = false;
                            import_cleanup($importcode);
                            notify(get_string('gradelocked', 'grades'));
                            break 2;
                        }
                    }

                    $newgrade->importcode = $importcode;
                    $newgrade->userid     = $studentid;
                    $newgrade->importer   = $USER->id;
                    if (!insert_record('grade_import_values', addslashes_recursive($newgrade))) {
                        // could not insert into temporary table
                        $status = false;
                        import_cleanup($importcode);
                        notify(get_string('importfailed', 'grades'));
                        break 2;
                    }
                }
            }

            // updating/inserting all comments here
            if ($status and !empty($newfeedbacks)) {
                foreach ($newfeedbacks as $newfeedback) {
                    $sql = "SELECT *
                              FROM {$CFG->prefix}grade_import_values
                             WHERE importcode=$importcode AND userid=$studentid AND itemid=$newfeedback->itemid AND importer={$USER->id}";
                    if ($feedback = get_record_sql($sql)) {
                        $newfeedback->id = $feedback->id;
                        update_record('grade_import_values', addslashes_recursive($newfeedback));

                    } else {
                        // the grade item for this is not updated
                        $newfeedback->importcode = $importcode;
                        $newfeedback->userid     = $studentid;
                        $newfeedback->importer   = $USER->id;
                        insert_record('grade_import_values', addslashes_recursive($newfeedback));
                    }
                }
            }
        }

        /// at this stage if things are all ok, we commit the changes from temp table
        if ($status) {
            grade_import_commit($course->id, $importcode);
        }
        // temporary file can go now
        unlink($filename);
    } else {
        error ('import file '.$filename.' not readable');
    }

} else {
    // display the standard upload file form
    $mform->display();
}

print_footer();
?>
