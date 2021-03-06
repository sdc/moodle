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
 * Link to SDC Tools admin page
 *
 * @package    tool_sdctools
 * @copyright  2013-2015 Paul Vaughan {@link http://commoodle.southdevon.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined( 'MOODLE_INTERNAL' ) || die;

// Define the variable for the 'off' state. 'Off' is defined as "Sat, 21 Mar 2037 01:35:21 GMT".
define( 'BACKUPOFF_TIME', 2121212121 );

/**
 * Draw a common table of contents.
 */
function sdctools_tableofcontents( $highlight = false ) {
    global $OUTPUT;

    $out = '<ul>';

    $pre = $suf = '';
    if ( $highlight == strtolower( 'index' ) ) {
        $pre = '<strong>';
        $suf = '</strong>';
    }
    $out .= '<li>'.$pre.html_writer::link(new moodle_url('index.php'), get_string('pluginname', 'tool_sdctools')).$suf.'</li>';

    $pre = $suf = '';
    if ( $highlight == strtolower( 'emails' ) ) {
        $pre = '<strong>';
        $suf = '</strong>';
    }
    $out .= '<li>'.$pre.html_writer::link(new moodle_url('emails.php'), get_string('emailchecks', 'tool_sdctools')).$suf.'</li>';

    if ($highlight == strtolower('users')) {
        $out .= '<li><strong>'.html_writer::link(new moodle_url('users.php'), get_string('userchecks', 'tool_sdctools')).'</strong></li>';
    } else {
        $out .= '<li>'.html_writer::link(new moodle_url('users.php'), get_string('userchecks', 'tool_sdctools')).'</li>';
    }

    $pre = $suf = '';
    if ( $highlight == strtolower( 'coursereports' ) ) {
        $pre = '<strong>';
        $suf = '</strong>';
    }
    $out .= '<li>'.$pre.html_writer::link(new moodle_url('coursereports.php'), get_string('coursereports', 'tool_sdctools')).$suf;

    $pre = $suf = '';
    if ( $highlight == strtolower( 'coursereports_pictures' ) ) {
        $pre = '<strong>';
        $suf = '</strong>';
    }
    $out .= ' ('.$pre.html_writer::link(new moodle_url('coursereports.php', array('pictures' => 1)), get_string('coursereportspictures', 'tool_sdctools')).$suf.')</li>';

    // Course code search in the Leap block.
    $pre = $suf = '';
    if ( $highlight == strtolower( 'leapblockcoursecodes' ) ) {
        $pre = '<strong>';
        $suf = '</strong>';
    }
    $out .= '<li>'.$pre.html_writer::link(new moodle_url('leapblockcoursecodes.php'), get_string('leapblockcoursecodes', 'tool_sdctools')).$suf.'</li>';

    $out .= '<li>' . html_writer::link( new moodle_url('leapcorereport.php'), get_string('leapcorereport', 'tool_sdctools') . ' (deprecated)', array( 'class' => 'dimmed' ) ) . '</li>';

    $pre = $suf = '';
    if ( $highlight == strtolower( 'leapchecks' ) ) {
        $pre = '<strong>';
        $suf = '</strong>';
    }
    $out .= '<li>' . $pre . html_writer::link( new moodle_url( 'leapchecks.php' ), get_string( 'leapchecks', 'tool_sdctools' ) ) . $suf . '</li>';

    $pre = $suf = '';
    if ( $highlight == strtolower( 'backuptoggle' ) ) {
        $pre = '<strong>';
        $suf = '</strong>';
    }
    $out .= '<li>'.$pre.html_writer::link(new moodle_url('backuptoggle.php'), get_string('backuptoggle', 'tool_sdctools')).$suf.'</li>';

    $out .= '</ul>';

    return $out;
}

/**
 * Pit the course's ID at the start of the name.
 */
function sdctools_idprefix($in) {
    $out = '';
    foreach ($in as $key => $value) {
        $out[$key] = $key.': '.$value;
    }

    return $out;
}

/**
 * A 'time ago' script.
 */
function sdctools_timeago($int, $ago = true, $short = true) {

    $in = ($ago) ? (time() - $int) : $int;

    $secsyear = 60*60*24*365.25;
    $secsday  = 60*60*24;
    $secshour = 60*60;
    $secsmin  = 60;

    $years = intval($in / $secsyear);
    $remainder = $in % $secsyear;
    $days = intval($remainder / $secsday);
    $remainder = $remainder % $secsday;
    $hours = intval($remainder / $secshour);
    $remainder = $remainder % $secshour;
    $minutes = intval($remainder / $secsmin);
    $remainder = $remainder % $secsmin;
    $seconds = intval($remainder);

    $out = '';
    // Omit any period of time with no data.
    if ($years) {
        $out .= $years;
        if ($short) {
            $out .= 'y, ';
        } else {
            $out .= ($years == 1) ? ' '.get_string('year').', ' : ' '.get_string('years').', ';
        }
    }
    if ($days) {
        $out .= $days;
        if ($short) {
            $out .= 'd, ';
        } else {
            $out .= ($days == 1) ? ' '.get_string('day').', ' : ' '.get_string('days').', ';
        }
    }
    if ($hours) {
        $out .= $hours;
        if ($short) {
            $out .= 'h, ';
        } else {
            $out .= ($hours == 1) ? ' '.get_string('hour').', ' : ' '.get_string('hours').', ';
        }
    }
    if ($minutes) {
        $out .= $minutes;
        if ($short) {
            $out .= 'm, ';
        } else {
            $out .= ($minutes == 1) ? ' '.get_string('minute').', ' : ' '.get_string('minutes').', ';
        }
    }
    if ($seconds) {
        $out .= $seconds;
        if ($short) {
            $out .= 's ';
        } else {
            $out .= ($seconds == 1) ? ' '.get_string('second', 'tool_sdctools') : ' '.get_string('seconds');
        }
    }

    if ($ago) {
        return sdctools_trimcomma($out).' '.get_string('ago', 'tool_sdctools');
    } else {
        return sdctools_trimcomma($out);
    }
}

/**
 * Trimming the ', ' off the end of a string.
 */
function sdctools_trimcomma($in) {
    $in = trim($in);
    if (substr($in, -1) == ',') {
        return  substr($in, 0, strlen($in)-1);
    } else {
        return $in;
    }
}
