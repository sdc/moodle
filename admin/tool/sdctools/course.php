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

require_once('../../../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('toolsdctoolscourse');

$cid = optional_param('id', false, PARAM_INT);

if (empty($CFG->loginhttps)) {
    $securewwwroot = $CFG->wwwroot;
} else {
    $securewwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
}

$sitecontext = context_system::instance();

$numcourses =  $DB->count_records("course");

$showcourses = ($numcourses < COURSE_MAX_COURSES_PER_DROPDOWN) ? true : false; 


echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pageheader', 'tool_sdctools'));

if ($ccc = $DB->get_records('course', null, 'fullname', 'id, shortname, fullname, category')) {
    foreach ($ccc as $cc) {
        if ($cc->category) {
            $courses[$cc->id] = format_string(get_course_display_name_for_list($cc));
        } else {
            $courses[$cc->id] = format_string($cc->fullname) . ' (Site)';
        }
    }
}

asort($courses);
//print_object($courses);

// Adding in the course's ID at the end of the name.
//foreach ($courses as $key => $value) {
//    $courses[$key] = $value.' (ID: '.$key.')';
//}
//print_object($courses);

echo '<form class="courseselectform" action="'.$securewwwroot.'/admin/tool/sdctools/course.php" method="get">'."\n";
echo "<div>\n";
//echo '<input type="hidden" name="chooselog" value="1" />'."\n";

echo html_writer::label(get_string('selectacourse'), 'menuid', false, array('class' => 'accesshide'));
echo html_writer::select($courses, 'id', $cid, false);

echo '<input type="submit" value="'.get_string('gettheselogs').'" />';
echo '</div>';
echo '</form>';

if ($cid) {
    print ('got a cid');
}


echo $OUTPUT->footer();
