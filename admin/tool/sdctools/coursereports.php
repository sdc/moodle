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
 * @copyright  2013 Paul Vaughan {@link http://commoodle.southdevon.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/adminlib.php');

require_once('locallib.php');

admin_externalpage_setup('toolsdctoolscourse');

$cid = optional_param('id', 0, PARAM_INT);
$pictures = optional_param('pictures', 0, PARAM_INT);

if (empty($CFG->loginhttps)) {
    $securewwwroot = $CFG->wwwroot;
} else {
    $securewwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
}

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pageheader', 'tool_sdctools'));

echo sdctools_tableofcontents();


/* If we have a course ID, print a nice report about that course. */
if ($cid) {

    // If that course ID is -1, loop through ALL courses.
    $allcid = array();
    if ($cid == -1) {
        $allcourses = $DB->get_records('course', null, 'id ASC', 'id');
        foreach ($allcourses as $allcourse) {
            $allcid[] = $allcourse->id;
        }

    } else if ($cid == -2) {
        $allcourses = $DB->get_records('course', null, 'fullname ASC', 'id');
        foreach ($allcourses as $allcourse) {
            $allcid[] = $allcourse->id;
        }

    } else {
        $allcid[] = $cid;
    }

    foreach ($allcid as $cid) {

        $course = $DB->get_record('course', array('id' => $cid), '*');

        echo $OUTPUT->box_start();
        echo $OUTPUT->heading(get_string('coursereportfor', 'tool_sdctools').$course->fullname);

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
            echo '  <dd><b>'.get_string('category').':</b> No category</dd>';
        }
        echo '  <dd><b>'.get_string('id', 'tool_sdctools').':</b> '.$course->id.'</dd>';
        $idnumber = ($course->idnumber == '') ? '<i>(blank)</i>' : $course->idnumber; 
        echo '  <dd><b>'.get_string('idnumbercourse').':</b> '.$idnumber.'</dd>';
        $visible = ($course->visible == 1) ? get_string('yes') : '<span class="error">'.get_string('no').'</span>';
        echo '  <dd><b>Visible:</b> '.$visible.'</dd>';
        echo '</dl>';

        echo '<dl>';
        echo '<dt>Created and Modified</dt>';
        echo '  <dd><b>Created:</b> '.strftime(get_string('strftimedaydate'), $course->timecreated).' ('.sdctools_timeago($course->timecreated).')</dd>';
        echo '  <dd><b>Modified:</b> '.strftime(get_string('strftimedaydate'), $course->timemodified).' ('.sdctools_timeago($course->timemodified).')</dd>';
        echo '</dl>';


        // Details. 
        echo '<dl>';
        echo '<dt>Format and Access</dt>';
        echo '  <dd><b>Format:</b> '.ucfirst($course->format).'</dd>';
        if ($course->format == 'weeks') {
            echo '  <dd><b>Start Date:</b> '.strftime(get_string('strftimedaydate'), $course->startdate).'</dd>';
        }
        $courseformat = $DB->get_record('course_format_options', array('courseid' => $cid, 'name' => 'numsections'), 'value');
        echo '  <dd><b>Sections:</b> '.$courseformat->value.'</dd>';
        $mods = unserialize($course->modinfo);
        $cmods = count($mods);
        echo '  <dd><b>Modules:</b> '.$cmods.'</dd>';
        echo '  <dd><b>Average Modules per Section:</b> '.number_format($cmods/$courseformat->value, 1).'</dd>';
        $hits = $DB->get_record_sql('SELECT COUNT(*) AS hits FROM mdl_log WHERE course = '.$cid.';');
        echo '  <dd><b>Activity:</b> '.number_format($hits->hits)." 'hits' since creation</dd>";
        $lastaccessed = $DB->get_record_select('log', 'course = '.$cid.' ORDER BY id DESC LIMIT 1', null, 'time, userid');
        if ($lastaccessed) {
            $lastaccesseduser = $DB->get_record('user', array('id' => $lastaccessed->userid), 'firstname, lastname');
            $out = strftime(get_string('strftimedaydatetime'), $lastaccessed->time).' ('.sdctools_timeago($lastaccessed->time).
            ') by '.html_writer::link(new moodle_url('/user/view.php', array('id' => $lastaccessed->userid)), $lastaccesseduser->firstname.' '.$lastaccesseduser->lastname);
        } else {
            $out = '<i>(never)</i>';
        }
        echo '  <dd><b>Last accessed:</b> '.$out.'</dd>';

        echo '</dl>';


        // Students and Teachers and such...
        echo '<dl>';
        echo '<dt>Enrollees</dt>';
        $coursecontext = $DB->get_record('context', array('instanceid' => $cid, 'contextlevel' => '50'), 'id');
        // Get all roles, then remove what we don't need.
        $roles = $DB->get_records('role', null, 'id ASC', 'id, name, description');
        unset ($roles['6'], $roles['7'], $roles['8'], $roles['10']);
        foreach ($roles as $role) {
            $roleids = $DB->get_records('role_assignments', array('contextid' => $coursecontext->id, 'roleid' => $role->id), null, 'userid');
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
                    if ($pictures) {
                        $out .= html_writer::link(new moodle_url('/user/view.php', array('id' => $roleuserkey)),
                            html_writer::empty_tag('img', array('src' => $CFG->wwwroot.'/user/pix.php/'.$roleuserkey.'/f1.jpg', 'alt' => $roleuservalue)), array('title' => $roleuservalue));
                    } else {
                        $out .= html_writer::link(new moodle_url('/user/view.php', array('id' => $roleuserkey)), $roleuservalue).', ';
                    }
                }
                $out = sdctools_trimcomma($out);
            } else {
                $out = '<i>(none)</i>';
            }
            $plural = (substr($role->name, -1) == 's') ? '' : $rolecount == 1 ? '' : 's';
            echo '  <dd><b><abbr title="'.strip_tags($role->description).'">'.number_format($rolecount).' '.ucfirst($role->name).$plural.':</abbr></b> '.$out.'</dd>';
        }
        echo '</dl>';

        // Students and Teachers and such...
        echo '<dl>';
        echo '<dt>Administrative Things</dt>';
        $lastbackup = $DB->get_record('backup_courses', array('courseid' => $cid), 'laststarttime, laststatus');
        if ($lastbackup) {
            $out = '';
            switch ($lastbackup->laststatus) {
                case 1:
                    $out = '<span style="color: #070;">OK</span>';
                    break;
                case 2:
                    $out = '<span style="color: #f00;">Unfinished</span>';
                    break;
                case 3:
                    $out = '<span style="color: #f70;">Skipped</span>';
                    break;
                case 0:
                    $out = '<span style="color: #f00;">Error</span>';
                    break;
            }
            echo '  <dd><b>Last Backup:</b> '.strftime(get_string('strftimedaydatetime'), $lastbackup->laststarttime).'</dd>';
            echo '  <dd><b>Backup Status:</b> '.$out.'</dd>';
        } else {
            echo '  <dd><b>Last Backup:</b> <i>(none found)</i></dd>';
        }
        echo '  <dd><b>Enrolment Plugin Defaults:</b> &quot;manual self&quot; or &quot;category manual self&quot;</dd>';
        $enrolmentplugins = $DB->get_records_select('enrol', 'courseid = '.$cid, null, 'enrol', 'enrol, status');
        if ($enrolmentplugins) {
            $out = '';
            foreach ($enrolmentplugins as $eplugin) {
                $out .= $eplugin->enrol.', ';
            }
            $out = sdctools_trimcomma($out);
            $edit = '';
            if ($out !== 'manual, self' && $out !== 'category, manual, self') {
                $edit = ' (<a href="'.$CFG->wwwroot.'/enrol/instances.php?id='.$cid.'">Edit</a>)';
            }
            echo '  <dd><b>Enrolment Plugins:</b> '.$out.$edit.'</dd>';
        } else {
            echo '  <dd><b>Enrolment Plugins:</b> <i>(none found)</i></dd>';
        }

        $needle = 'absence';
        $searchterm = array('%'.$needle.'%', $cid);
        $out = '';
        $cs_n = $DB->get_records_select('course_sections', 'name LIKE ? AND course = ?', $searchterm, null, 'id');
        if ($cs_n) {
            //$cs_n_count = count($cs_n);
            //$out .= 'Section name: '.$cs_n_count.'. ';
            $out .= 'section name, ';
        }
        $cs_s = $DB->get_records_select('course_sections', 'summary LIKE ? and course like ?', $searchterm, null, 'id');
        if ($cs_s) {
            //$cs_s_count = count($cs_s);
            //$out .= 'Section summary: '.$cs_s_count.'. ';
            $out .= 'section summary, ';
        }
        $r_n = $DB->get_records_select('resource', 'name LIKE ? and course like ?', $searchterm, null, 'id');
        if ($r_n) {
            //$r_n_count = count($r_n);
            //$out .= 'Resource name: '.$r_n_count.'. ';
            $out .= 'resource name, ';
        }
        $r_i = $DB->get_records_select('resource', 'intro LIKE ? and course like ?', $searchterm, null, 'id');
        if ($r_i) {
            //$r_i_count = count($r_i);
            //$out .= 'Resource introduction: '.$r_i_count.'. ';
            $out .= 'resource introduction, ';
        }
        $p_n = $DB->get_records_select('page', 'name LIKE ? and course like ?', $searchterm, null, 'id');
        if ($p_n) {
            //$p_n_count = count($p_n);
            //$out .= 'Page name: '.$p_n_count.'. ';
            $out .= 'page name, ';
        }
        $p_i = $DB->get_records_select('page', 'intro LIKE ? and course like ?', $searchterm, null, 'id');
        if ($p_i) {
            //$p_i_count = count($p_i);
            //$out .= 'Page intro: '.$p_i_count.'. ';
            $out .= 'page intro, ';
        }
        $u_n = $DB->get_records_select('url', 'name LIKE ? and course like ?', $searchterm, null, 'id');
        if ($u_n) {
            //$u_n_count = count($u_n);
            //$out .= 'URL name: '.$u_n_count.'. ';
            $out .= 'URL name, ';
        }
        $l_n = $DB->get_records_select('label', 'name LIKE ? and course like ?', $searchterm, null, 'id');
        if ($l_n) {
            //$l_n_count = count($l_n);
            //$out .= 'Label name: '.$l_n_count.'. ';
            $out .= 'label name';
        }
        $out = sdctools_trimcomma($out);
        if ($out) {
            echo '  <dd><b>Absence Activity:</b> Yes ('.$out.')</dd>';
        } else {
            echo '  <dd><b>Absence Activity:</b> <i>(none found)</i></dd>';
        }
        echo '</dl>';

        // Picture control.
        if ($pictures) {
            echo '<p>'.html_writer::link(new moodle_url('coursereports.php', array('id' => $cid, 'pictures' => 0)), get_string('turnpicturesoff', 'tool_sdctools')).'</p>';
        } else {
            echo '<p>'.html_writer::link(new moodle_url('coursereports.php', array('id' => $cid, 'pictures' => 1)), get_string('turnpictureson', 'tool_sdctools')).'</p>';
        }

        echo $OUTPUT->box_end();

    } // End of if ($cid)

} else { // If we don't have the $cid, print a nice form.

    if ($ccc = $DB->get_records('course', null, 'fullname', 'id, shortname, fullname, category')) {
        foreach ($ccc as $cc) {
            if ($cc->category) {
                $courses[$cc->id] = format_string(get_course_display_name_for_list($cc));
            } else {
                $courses[$cc->id] = format_string($cc->fullname) . ' (Site)';
            }
        }
    }

    // Courses in alphanumeric order by name.
    $courses_alpha = $courses;
    asort($courses_alpha);
    $courses_alpha = array(0 => 'Select...', -1 => 'All courses') + sdctools_idprefix($courses_alpha);

    // Courses in numeric order by course ID.
    $courses_numeric = sdctools_idprefix($courses);
    ksort($courses_numeric);
    $courses_numeric = array(0 => 'Select...', -2 => 'All courses') + $courses_numeric;

    unset($courses);

    // Choose a course from a list in course name order.
    echo $OUTPUT->box_start();
    echo $OUTPUT->heading(get_string('coursereportname', 'tool_sdctools'));
    echo '<p>'.get_string('timewarning', 'tool_sdctools').'</p>';
    echo '<p>'.get_string('byname', 'tool_sdctools').'</p>';
    echo '<form class="courseselectalphaform" action="coursereports.php" method="get">'."\n";
    echo "  <div>\n";
    echo html_writer::label(get_string('selectacourse'), 'menuid', false, array('class' => 'accesshide'));
    echo html_writer::select($courses_alpha, 'id', $cid, false);
    echo '  <input type="hidden" name="pictures" value="'.$pictures.'" />';
    echo '  <input type="submit" value="'.get_string('getcoursereport', 'tool_sdctools').'" />';
    echo '  </div>';
    echo '</form>';

    // Choose a course from a list in course name order.
    echo '<p>&nbsp;</p><p>'.get_string('byid', 'tool_sdctools').'</p>';
    echo '<form class="courseselectnumericform" action="coursereports.php" method="get">'."\n";
    echo "  <div>\n";
    echo html_writer::label(get_string('selectacourse'), 'menuid', false, array('class' => 'accesshide'));
    echo html_writer::select($courses_numeric, 'id', $cid, false);
    echo '  <input type="hidden" name="pictures" value="'.$pictures.'" />';
    echo '  <input type="submit" value="'.get_string('getcoursereport', 'tool_sdctools').'" />';
    echo '  </div>';
    echo '</form>';
    echo $OUTPUT->box_end();

} // end if $cid

echo $OUTPUT->footer();
