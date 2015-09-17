<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A page to give full cross-Moodle details for a given course.
 *
 * @package    tool_sdctools
 * @copyright  2013-2015 Paul Vaughan {@link http://commoodle.southdevon.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define( 'NO_OUTPUT_BUFFERING', true );

require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/config.php' );
require_once( $CFG->dirroot . '/course/lib.php');
require_once( $CFG->libdir . '/adminlib.php');

require_once( 'locallib.php' );

admin_externalpage_setup( 'toolsdctoolscourse' );

//$cid = optional_param( 'id', 0, PARAM_INT );
//$pictures = optional_param( 'pictures', 0, PARAM_INT );
//$cc = optional_param( 'coursecode', '', PARAM_ALPHANUM );

$securewwwroot = ( empty( $CFG->loginhttps ) ) ? $CFG->wwwroot : str_replace( 'http:', 'https:', $CFG->wwwroot );

echo $OUTPUT->header();
echo $OUTPUT->heading( get_string( 'pageheader', 'tool_sdctools' ) );

echo sdctools_tableofcontents( 'leapblockcoursecodes' );

echo $OUTPUT->box_start();
echo $OUTPUT->heading( get_string( 'leapblockcoursecodes', 'tool_sdctools' ) );

// Iterate through every course in the database, checking for a Leap block (checking it's installed
// first?) and extracting the course codes out of it.

// Get all courses.
$courses = $DB->get_records( 'course', null, 'id ASC', 'id,shortname,fullname' );
$courses_num = count( $courses );

// Iterate through each course.
foreach ( $courses as $course ) {
    
    if ( $course->id == 1 ) {
        continue;
    }

    //echo '<h3>' . $course->fullname . ' (' . number_format( $course->id ) . ')</h3>';
    echo '<h3>' . $course->fullname . ' (' . html_writer::link(new moodle_url( '/course/view.php', array( 'id' => $course->id ) ), number_format( $course->id ) ) . ')</h3>';

    $coursecontext = context_course::instance( $course->id );
    if ( !$blockrecord = $DB->get_record( 'block_instances', array( 'blockname' => 'leap', 'parentcontextid' => $coursecontext->id ) ) ) {
        echo '<p>No Leap block found for course "' . $course->id . '" (' . $course->shortname . ').</p>';
        continue;
    }

    if ( !$blockinstance = block_instance( 'leap', $blockrecord ) ) {
        echo '<p>No Leap block instance found for course "' . $course->id . '" (' . $course->shortname . ').</p>';
        continue;
    }

    // Tracker setting, if set.
    if ( isset( $blockinstance->config->trackertype ) && !empty( $blockinstance->config->trackertype ) ) {
        $gt = $blockinstance->config->trackertype;
        if ( $gt == 'english' || $gt == 'maths' || $gt == 'core' || $gt == 'test' ) {
            $gt = ucfirst( $gt );
        } else if ( $gt == 'ppd' ) {
            $gt = strtoupper( $gt );
        }
        echo '<p>Grade Tracker type found: ' . $gt . '.</p>' . "\n";
    }

    // Course codes, if set, warning if not.
    if ( isset( $blockinstance->config->coursecodes ) && !empty( $blockinstance->config->coursecodes ) ) {
        echo '<p>Course codes found:</p>' . "\n";
        $codes = explode( ',', $blockinstance->config->coursecodes );
        foreach ( $codes as $key => $value ) {
            $codes[ $key ] = strtoupper( trim( $value ) );
        }
        sort( $codes );

        echo '<ul>';
        foreach ( $codes as $code ) {
            echo '<li>' . $code . '</li>';
        }
        echo '</ul>';

    } else {
        echo '<p>No course codes found. This is probably an oversight...</p>';
    }



}











echo $OUTPUT->box_end();

echo $OUTPUT->footer();

/*
// If we have a course code, go search for it.
if ( isset( $cc ) && !empty( $cc ) ) {

    // If that course ID is -1 or -2, loop through ALL courses in different orders.
    $allcid = array();
    if ($cid == -1) {
        $headingnumber = 1;
        $allcourses = $DB->get_records('course', null, 'id ASC', 'id');
        foreach ($allcourses as $allcourse) {
            $allcid[] = $allcourse->id;
        }

    } else if ($cid == -2) {
        $headingnumber = 1;
        $allcourses = $DB->get_records('course', null, 'fullname ASC', 'id');
        foreach ($allcourses as $allcourse) {
            $allcid[] = $allcourse->id;
        }

    } else {
        $headingnumber = 0;
        $allcid[] = $cid;
    }

    foreach ($allcid as $cid) {

        $course = $DB->get_record('course', array('id' => $cid), '*');

        echo $OUTPUT->box_start();
        if ($cid == 1) {
            echo $OUTPUT->heading(get_string('coursereportfor', 'tool_sdctools').$course->fullname . ' ('.get_string('site').')');
        } else {
            echo $OUTPUT->heading(get_string('coursereportfor', 'tool_sdctools').$course->fullname);
        }

        echo '<dl>';
        echo '<dt>'.get_string('coursedetail', 'tool_sdctools').'</dt>';
        $buttons = array();
        $buttons[] = html_writer::link(new moodle_url('/course/view.php',
                        array('id' => $cid)),
                        html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('t/hide'),
                        'alt' => get_string('view'), 'class' => 'iconsmall')), array('title' => get_string('view')));
        $buttons[] = html_writer::link(new moodle_url('/course/edit.php',
                        array('id' => $cid)),
                        html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('t/edit'),
                        'alt' => get_string('edit'), 'class' => 'iconsmall')), array('title' => get_string('edit')));

        echo '  <dd><b>'.get_string('full', 'tool_sdctools').':</b> '.$course->fullname.' '.implode(' ', $buttons).'</dd>';
        echo '  <dd><b>'.get_string('short', 'tool_sdctools').':</b> '.$course->shortname.'</dd>';

        $coursecategory = $DB->get_record('course_categories', array('id' => $course->category), 'name');
        if ($coursecategory) {
            echo '  <dd><b>'.get_string('category').':</b> '.$coursecategory->name.'</dd>';
        } else {
            echo '  <dd><b>'.get_string('category').':</b> '.get_string('nocategory', 'tool_sdctools').'</dd>';
        }
        echo '  <dd><b>'.get_string('id', 'tool_sdctools').':</b> '.$course->id.'</dd>';
        $idnumber = ($course->idnumber == '') ? get_string('blank', 'tool_sdctools') : $course->idnumber;
        echo '  <dd><b>'.get_string('idnumbercourse').':</b> '.$idnumber.'</dd>';
        $visible = ($course->visible == 1) ? get_string('yes') : '<span class="error">'.get_string('no').'</span>';
        echo '  <dd><b>'.get_string('visible').':</b> '.$visible.'</dd>';
        echo '</dl>';

        // Created and modified.
        echo '<dl>';
        echo '<dt>'.get_string('createdmodified', 'tool_sdctools').'</dt>';
        echo '  <dd><b>'.get_string('created', 'tool_sdctools').'</b> '.strftime(get_string('strftimedaydate'), $course->timecreated).' ('.sdctools_timeago($course->timecreated).')</dd>';
        echo '  <dd><b>'.get_string('modified', 'tool_sdctools').'</b> '.strftime(get_string('strftimedaydate'), $course->timemodified).' ('.sdctools_timeago($course->timemodified).')</dd>';
        if ((time() - $course->timemodified > (60*60*24*365.25*2))) {
            $out = get_string('overtwoyears', 'tool_sdctools');
        } else if ((time() - $course->timemodified > (60*60*24*365.25))) {
            $out = get_string('overoneyear', 'tool_sdctools');
        } else if ((time() - $course->timemodified > (60*60*24*180))) {
            $out = get_string('oversixmonths', 'tool_sdctools');
        } else {
            $out = get_string('withinsixmonths', 'tool_sdctools');
        }
        echo '  <dd><b>'.get_string('updatecheck', 'tool_sdctools').'</b> '.$out.'</dd>';
        echo '</dl>';

        // Format and Access.
        echo '<dl>';
        echo '<dt>'.get_string('formataccess', 'tool_sdctools').'</dt>';
        echo '  <dd><b>'.get_string('format', 'tool_sdctools').'</b> '.ucfirst($course->format).'</dd>';
        if ($course->format == 'weeks') {
            echo '  <dd><b>'.get_string('startdate', 'tool_sdctools').'</b> '.strftime(get_string('strftimedaydate'), $course->startdate).'</dd>';
        }
        $courseformat = $DB->get_record('course_format_options', array('courseid' => $cid, 'name' => 'numsections'), 'value');
        echo '  <dd><b>'.get_string('sections').':</b> '.$courseformat->value.'</dd>';




//$modinfo = get_fast_modinfo($course);
//echo '<pre>'; print_object($modinfo); echo '</pre>';
//$coursesections = $modinfo->get_cms($modinfo);
//$coursesections = $modinfo->get_instances($modinfo);
//echo '<pre>'; print_object($coursesections); echo '</pre>';




        $mods = unserialize($modinfo);
        $cmods = count($mods);
        $modulebreakdown = array();
        $out = ' (';
        foreach ($mods as $key => $value) {
            $modulebreakdown[$value->mod]++;
        }
        ksort($modulebreakdown);
        foreach ($modulebreakdown as $key => $value) {
            $out .=  ucfirst($key).': '.$value.'. ';
        }
        $out = rtrim($out) . ')';
        echo '  <dd><b>'.get_string('managemodules').':</b> '.$cmods.$out.'</dd>';
        echo '  <dd><b>'.get_string('avgmodules', 'tool_sdctools').'</b> '.number_format($cmods/$courseformat->value, 1).'</dd>';
        $hits = $DB->get_record_sql('SELECT COUNT(*) AS hits FROM mdl_log WHERE course = '.$cid.';');
        echo '  <dd><b>'.get_string('activity', 'tool_sdctools').'</b> '.number_format($hits->hits).get_string('hitssincecreation', 'tool_sdctools').'</dd>';
        $lastaccessed = $DB->get_record_select('log', 'course = '.$cid.' ORDER BY id DESC LIMIT 1', null, 'time, userid');
        if ($lastaccessed) {
            $lastaccesseduser = $DB->get_record('user', array('id' => $lastaccessed->userid), 'firstname, lastname');
            $out = strftime(get_string('strftimedaydatetime'), $lastaccessed->time).' ('.sdctools_timeago($lastaccessed->time).
            ') '.get_string('by', 'tool_sdctools').' '.html_writer::link(new moodle_url('/user/view.php', array('id' => $lastaccessed->userid)), $lastaccesseduser->firstname.' '.$lastaccesseduser->lastname);
        } else {
            $out = get_string('never', 'tool_sdctools');
        }
        echo '  <dd><b>'.get_string('lastaccessed', 'tool_sdctools').'</b> '.$out.'</dd>';
        // SELECT COUNT( mdl_grade_grades.id ) FROM mdl_grade_grades, mdl_grade_items WHERE mdl_grade_grades.itemid = mdl_grade_items.id AND courseid =7
        // $cs_n = $DB->get_records_select('course_sections', 'name LIKE ? AND course = ?', $searchterm, null, 'id');
        $grades = $DB->get_record_sql("SELECT COUNT(mdl_grade_grades.id) AS grades FROM mdl_grade_grades, mdl_grade_items
            WHERE mdl_grade_grades.itemid = mdl_grade_items.id AND courseid = ".$cid);
        if ($grades) {
            echo '  <dd><b>'.get_string('grades', 'tool_sdctools').'</b> '.number_format($grades->grades).'</dd>';
        }
        echo '</dl>';


        // Enrollees...
        echo '<dl>';
        echo '<dt>'.get_string('enrollees', 'tool_sdctools').'</dt>';
        $coursecontext = $DB->get_record('context', array('instanceid' => $cid, 'contextlevel' => '50'), 'id');
        // Get all roles, then remove what we don't need.
        $roles = $DB->get_records('role', null, 'id ASC', 'id, name, description');
        unset ($roles['6'], $roles['7'], $roles['8'], $roles['10']);
        foreach ($roles as $role) {
            $roleids = $DB->get_records('role_assignments', array('contextid' => $coursecontext->id, 'roleid' => $role->id, 'component' => ''), null, 'userid');
            $rolecount = count($roleids);
            $roleusers = array();
            foreach ($roleids as $rid) {
                $roleusers[] = $DB->get_record('user', array('id' => $rid->userid), 'id, firstname, lastname');
            }
            if ($roleusers) {
                $roleusersorted = array();
                foreach ($roleusers as $roleuser) {
                    $roleusersorted[$roleuser->id] = $roleuser->firstname.' '.$roleuser->lastname.' ('.$roleuser->id.')';
                }
                asort($roleusersorted);
                $out = '';
                foreach ($roleusersorted as $roleuserkey => $roleuservalue) {
                    $haspicture = $DB->get_record('user', array('id' => $roleuserkey), 'picture');
                    // If pictures are on and the user has uploaded one.
                    if ($pictures && $haspicture->picture > 0) {
                        $out .= html_writer::link(new moodle_url('/user/view.php', array('id' => $roleuserkey)),
                            html_writer::empty_tag('img', array('src' => $CFG->wwwroot.'/user/pix.php/'.$roleuserkey.'/f1.jpg',
                                'alt' => $roleuservalue)), array('title' => $roleuservalue));
                    } else {
                        $out .= html_writer::link(new moodle_url('/user/view.php', array('id' => $roleuserkey)), $roleuservalue).', ';
                    }
                }
                $out = sdctools_trimcomma($out);
            } else {
                $out = get_string('none', 'tool_sdctools');
            }
            $plural = (substr($role->name, -1) == 's') ? '' : $rolecount == 1 ? '' : 's';
            echo '  <dd><b><abbr title="'.strip_tags($role->description).'">'.number_format($rolecount).' '.
                ucfirst($role->name).$plural.':</abbr></b> '.$out.'</dd>';
        }
        echo '</dl>';

        // Admin things...
        echo '<dl>';
        echo '<dt>'.get_string('administrativethings', 'tool_sdctools').'</dt>';
        $lastbackup = $DB->get_record('backup_courses', array('courseid' => $cid), 'laststarttime, laststatus');
        if ($lastbackup) {
            $out = '';
            switch ($lastbackup->laststatus) {
                case 1:
                    $out = '<span class="sdctool_ok">'.get_string('ok').'</span>';
                    break;
                case 2:
                    $out = '<span class="sdctool_unfinished">'.get_string('unfinished').'</span>';
                    break;
                case 3:
                    $out = '<span class="sdctool_skipped">'.get_string('skipped').'</span>';
                    break;
                case 0:
                    $out = '<span class="sdctool_error">'.get_string('error').'</span>';
                    break;
            }
            echo '  <dd><b>'.get_string('lastbackup', 'tool_sdctools').'</b> '.strftime(get_string('strftimedaydatetime'), $lastbackup->laststarttime).'</dd>';
            echo '  <dd><b>'.get_string('backupstatus', 'tool_sdctools').'</b> '.$out.'</dd>';
        } else {
            echo '  <dd><b>'.get_string('lastbackup', 'tool_sdctools').'</b> '.get_string('nonefound', 'tool_sdctools').'</dd>';
        }
        echo '  <dd><b>'.get_string('enrolplugindef', 'tool_sdctools').'</b> '.get_string('enrolpluginlist', 'tool_sdctools').'</dd>';
        $enrolmentplugins = $DB->get_records_select('enrol', 'courseid = '.$cid, null, 'enrol', 'enrol, status');
        if ($enrolmentplugins) {
            $out = '';
            foreach ($enrolmentplugins as $eplugin) {
                $out .= $eplugin->enrol.', ';
            }
            $out = sdctools_trimcomma($out);
            $edit = '';
            if ($out !== 'manual, self' && $out !== 'category, manual, self') {
                $edit = ' (<a href="'.$CFG->wwwroot.'/enrol/instances.php?id='.$cid.'">'.get_string('edit').'</a>)';
            }
            echo '  <dd><b>'.get_string('enrolplugin', 'tool_sdctools').'</b> '.$out.$edit.'</dd>';
        } else {
            echo '  <dd><b>'.get_string('enrolplugin', 'tool_sdctools').'</b> '.get_string('nonefound', 'tool_sdctools').'</dd>';
        }

        // TODO: move this to configuration and add as default.
        $needle = 'absence';
        $searchterm = array('%'.$needle.'%', $cid);
        $out = '';
        $csn = $DB->get_records_select('course_sections', 'name LIKE ? AND course = ?', $searchterm, null, 'id');
        if ($csn) {
            // $csn_count = count($csn);
            // $out .= ucfirst(get_string('sectionname', 'tool_sdctools')).': '.$csncount.'. ';
            $out .= get_string('sectionname', 'tool_sdctools').', ';
        }
        $css = $DB->get_records_select('course_sections', 'summary LIKE ? and course like ?', $searchterm, null, 'id');
        if ($css) {
            // $csscount = count($css);
            // $out .= ucfirst(get_string('sectionsummary', 'tool_sdctools')).': '.$csscount.'. ';
            $out .= get_string('sectionsummary', 'tool_sdctools').', ';
        }
        $rn = $DB->get_records_select('resource', 'name LIKE ? and course like ?', $searchterm, null, 'id');
        if ($rn) {
            // $rncount = count($rn);
            // $out .= ucfirst(get_string('resourcename', 'tool_sdctools')).': '.$rncount.'. ';
            $out .= get_string('resourcename', 'tool_sdctools').', ';
        }
        $ri = $DB->get_records_select('resource', 'intro LIKE ? and course like ?', $searchterm, null, 'id');
        if ($ri) {
            // $ricount = count($ri);
            // $out .= ucfirst(get_string('resourceintro', 'tool_sdctools')).': '.$ricount.'. ';
            $out .= get_string('resourceintro', 'tool_sdctools').', ';
        }
        $pn = $DB->get_records_select('page', 'name LIKE ? and course like ?', $searchterm, null, 'id');
        if ($pn) {
            // $pncount = count($pn);
            // $out .= ucfirst(get_string('pagename', 'tool_sdctools')).': '.$pncount.'. ';
            $out .= get_string('pagename', 'tool_sdctools').', ';
        }
        $pi = $DB->get_records_select('page', 'intro LIKE ? and course like ?', $searchterm, null, 'id');
        if ($pi) {
            // $picount = count($pi);
            // $out .= ucfirst(get_string('pageintro', 'tool_sdctools')).': '.$picount.'. ';
            $out .= get_string('pageintro', 'tool_sdctools').', ';
        }
        $un = $DB->get_records_select('url', 'name LIKE ? and course like ?', $searchterm, null, 'id');
        if ($un) {
            // $uncount = count($un);
            // $out .= ucfirst(get_string('urlname', 'tool_sdctools')).': '.$uncount.'. ';
            $out .= get_string('urlname', 'tool_sdctools').', ';
        }
        $ln = $DB->get_records_select('label', 'name LIKE ? and course like ?', $searchterm, null, 'id');
        if ($ln) {
            // $lncount = count($ln);
            // $out .= ucfirst(get_string('labelname', 'tool_sdctools')).': '.$lncount.'. ';
            $out .= get_string('labelname', 'tool_sdctools');
        }
        $out = sdctools_trimcomma($out);
        if ($out) {
            echo '  <dd><b>'.get_string('absenceactivity', 'tool_sdctools').'</b> '.get_string('yes').' ('.$out.')</dd>';
        } else {
            echo '  <dd><b>'.get_string('absenceactivity', 'tool_sdctools').'</b> '.get_string('nonefound', 'tool_sdctools').'</dd>';
        }
        echo '</dl>';

        // Picture control.
        if ($pictures) {
            echo '<p>'.html_writer::link(new moodle_url('coursereports.php', array('id' => $cid, 'pictures' => 0)), get_string('turnpicturesoff', 'tool_sdctools')).'</p>';
            echo '<p>'.get_string('picturewarning', 'tool_sdctools').'</p>';
        } else {
            echo '<p>'.html_writer::link(new moodle_url('coursereports.php', array('id' => $cid, 'pictures' => 1)), get_string('turnpictureson', 'tool_sdctools')).'</p>';
        }

        echo $OUTPUT->box_end();

    } // End of if ($cid)

    // $_SERVER["REQUEST_TIME_FLOAT"] is only available in PHP 5.4 and newer.
    echo '<p>Report took '.(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]).' seconds to generate.</p>';


} // end if $cid

*/
