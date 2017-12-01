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

define('NO_OUTPUT_BUFFERING', true);

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->libdir.'/adminlib.php');

require_once('locallib.php');

admin_externalpage_setup( 'toolsdctoolsleapcore' );

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pageheader', 'tool_sdctools'));

echo sdctools_tableofcontents('leapcorereport');

$lc_types = array(
    'leapcore_core',
    'leapcore_english',
    'leapcore_maths',
    'leapcore_ppd',
    'leapcore_test',
    'leapcore_a2_artdes',
    'leapcore_a2_artdesphoto',
    'leapcore_a2_artdestext',
    'leapcore_a2_biology',
    'leapcore_a2_busstud',
    'leapcore_a2_chemistry',
    'leapcore_a2_englishlang',
    'leapcore_a2_englishlit',
    'leapcore_a2_envsci',
    'leapcore_a2_envstud',
    'leapcore_a2_filmstud',
    'leapcore_a2_geography',
    'leapcore_a2_history',
    'leapcore_a2_law',
    'leapcore_a2_maths',
    'leapcore_a2_mathsfurther',
    'leapcore_a2_media',
    'leapcore_a2_philosophy',
    'leapcore_a2_physics',
    'leapcore_a2_psychology',
    'leapcore_a2_sociology',
    'leapcore_btecex_applsci',
);

echo $OUTPUT->box_start();
echo $OUTPUT->heading( get_string( 'leapcorereport', 'tool_sdctools' ) );

$out = '';

foreach ( $lc_types as $lc_type ) {

    $out .= '<h4>Courses with &quot;' . $lc_type . '&quot; set</h4>';

    $lc_res = $DB->get_records_select( 'course', "idnumber LIKE '%|".$lc_type."|%'", null, "id ASC", 'id, shortname, fullname' );
    $tmp = $lc_type;
    ${$tmp} = 0;
    if ( $lc_res ) {
        $out .= "<ul>\n";
        foreach ( $lc_res as $lc ) {
            ${$tmp}++;
            $out .= '<li>ID: ' . $lc->id . ' - ';
            $out .= '<a href="' . $CFG->wwwroot . '/course/view.php?id=' . $lc->id . '" title="' . $lc->fullname . '">' . $lc->shortname . '</a>';
            $out .= "</li>\n";
        }
        $out .= "</ul>\n";
        $plural = ( ${$tmp} == 1 ) ? '' : 's';
        $out .= '<p>Total: '.${$tmp}.' course' . $plural . ".</p>\n";
    } else {
        $out .= '<p>No courses have ' . $lc_type . '.</p>'."\n";
    }

} // END foreach lc types.

$lc_total = $DB->get_records_select( 'course', "idnumber LIKE '%|leapcore_%|%'", null, null, 'id' );
$lc_total = count($lc_total);
$courses_total = $DB->count_records( 'course' );
$percent = ( $courses_total > 0) ? number_format( ( $lc_total / $courses_total ) * 100, 2) : '0.0%';

$out .= "<h3>Totals</h3>\n";
$out .= '<p>Total courses with LEAPCORE set: ' . $lc_total . ' out of ' . $courses_total . ' courses (' . $percent . "%).</p>\n";

echo $out;

echo $OUTPUT->box_end();
echo $OUTPUT->footer();
