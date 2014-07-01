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
 * Admin plugin to do whatever we want!
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

admin_externalpage_setup('toolsdctools');

if (empty($CFG->loginhttps)) {
    $securewwwroot = $CFG->wwwroot;
} else {
    $securewwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
}

//$stredit    = get_string('edit');
//$strdelete  = get_string('delete');
//$site       = get_site();
//$stdwhere   = ' AND deleted = 0 AND id != 1'; // No deleted users and not the guest user.

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pageheader', 'tool_sdctools'));

echo sdctools_tableofcontents('backuptoggle');


// Backups turned off.
echo $OUTPUT->box_start();
echo $OUTPUT->heading(get_string('backupsoffheader', 'tool_sdctools'));
echo '<p>'.get_string('backupsoffstrapline', 'tool_sdctools')."</p>\n";

$backupoff = $DB->get_records_select('backup_courses', 'nextstarttime = "'.BACKUPOFF_TIME.'"', null, 'courseid ASC');

if (!$backupoff) {
    echo '<p>'.get_string('nobackupsoff', 'tool_sdctools').'</p>';
} else {
    echo '<p>'.get_string('backupsoff', 'tool_sdctools', number_format(count($backupoff))).'</p>';

    $table = new html_table();
    $table->head = array ();
    $table->align = array();
    $table->head[] = '#';
    $table->align[] = '';
    $table->head[] = get_string('id', 'tool_sdctools');
    $table->align[] = 'left';
    $table->head[] = get_string('fullname');
    $table->align[] = 'left';
    $table->head[] = get_string('turnonheader', 'tool_sdctools');
    $table->align[] = 'left';
    $table->width = "100%";

    $items = 0;
    foreach ($backupoff as $boff) {

        $buttons = array();
        $row = array ();

        if (is_siteadmin($USER) or !is_siteadmin($user)) {
            $buttons[] = html_writer::link(new moodle_url($securewwwroot.'/admin/tools/sdctool/backuptoggle.php',
                array('id' => $boff->courseid, 'sesskey' => sesskey())),
                html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('t/edit'),
                'alt' => get_string('turnonheader', 'tool_sdctools'), 'class' => 'iconsmall')), array('title' => get_string('turnonheader', 'tool_sdctools')));
        }

        $course = $DB->get_record('course', array('id' => $boff->courseid), 'fullname');

        $row[] = number_format(++$items);
        $row[] = $boff->courseid;
        $row[] = '<a href="'.$securewwwroot.'/course/view.php?id='.$boff->courseid.'">'.$course->fullname.'</a>';
        //$row[] = '<a href="'.$securewwwroot.'/admin/tools/sdctool/backuptoggle.php?turnoff='.$boff->courseid.'">'.get_string('turnon', 'tool_sdctools').'</a>';
        $row[] = implode(' ', $buttons);
        $table->data[] = $row;
    }

    if (!empty($table)) {
        echo html_writer::table($table);
    }

}

echo $OUTPUT->box_end();

// Backups not turned off.
echo $OUTPUT->box_start();
echo $OUTPUT->heading(get_string('backupsonheader', 'tool_sdctools'));
echo '<p>'.get_string('backupsonstrapline', 'tool_sdctools')."</p>\n";
echo $OUTPUT->box_end();

// End the page.
echo $OUTPUT->footer();
