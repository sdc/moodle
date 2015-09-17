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
 * @copyright  2013-2015 Paul Vaughan {@link http://commoodle.southdevon.ac.uk}
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

$stredit    = get_string('edit');
$strdelete  = get_string('delete');
$site       = get_site();
$stdwhere   = ' AND deleted = 0 AND id != 1'; // No deleted users and not the guest user.

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pageheader', 'tool_sdctools'));

echo sdctools_tableofcontents('users');


// Users obviously restored from backups.
echo $OUTPUT->box_start();
echo $OUTPUT->heading(get_string('restoredusersheader', 'tool_sdctools'));
echo '<p>'.get_string('restoredusersstrapline', 'tool_sdctools')."</p>\n";

$restored = $DB->get_records_select('user', 'password = "restored" '.$stdwhere, null, 'lastaccess DESC');

if (!$restored) {
    echo '<p>'.get_string('norestoredusers', 'tool_sdctools').'</p>';
} else {
    echo '<p>'.get_string('restoredusers', 'tool_sdctools', number_format(count($restored))).'</p>';

    $table = new html_table();
    $table->head = array ();
    $table->align = array();
    $table->head[] = '#';
    $table->align[] = '';
    $table->head[] = get_string('id', 'tool_sdctools');
    $table->align[] = 'left';
    $table->head[] = get_string('fullnameuser');
    $table->align[] = 'left';
    $table->head[] = get_string('username');
    $table->align[] = 'left';
    $table->head[] = get_string('firstaccess');
    $table->align[] = 'left';
    $table->head[] = get_string('lastaccess');
    $table->align[] = 'left';
    $table->head[] = get_string('actions');
    $table->align[] = 'left';
    $table->width = "75%";

    $items = 0;
    foreach ($restored as $user) {

        $buttons = array();
        $row = array ();

        if (is_siteadmin($USER) or !is_siteadmin($user)) {
            // Edit.
            $buttons[] = html_writer::link(new moodle_url($securewwwroot.'/user/editadvanced.php',
                array('id' => $user->id, 'course' => $site->id)),
                html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('t/edit'),
                'alt' => $stredit, 'class' => 'iconsmall')), array('title' => $stredit));
            // Delete.
            $buttons[] = html_writer::link(new moodle_url($securewwwroot.'/admin/user.php',
                array('delete' => $user->id, 'sesskey' => sesskey())),
                html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('t/delete'),
                'alt' => $strdelete, 'class' => 'iconsmall')), array('title' => $strdelete));

        }
        if ($user->firstaccess) {
            $strfirstaccess = format_time(time() - $user->firstaccess);
        } else {
            $strfirstaccess = get_string('never');
        }
        if ($user->lastaccess) {
            $strlastaccess = format_time(time() - $user->lastaccess);
        } else {
            $strlastaccess = get_string('never');
        }
        $fullname = fullname($user, true);
        $row[] = number_format(++$items);
        $row[] = $user->id;
        $row[] = '<a href="'.$securewwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$site->id.'">'.$fullname.'</a>';
        $row[] = $user->username;
        $row[] = $strfirstaccess;
        $row[] = $strlastaccess;
        $row[] = implode(' ', $buttons);
        $table->data[] = $row;
    }

    if (!empty($table)) {
        echo html_writer::table($table);
    }

}

// End Users obviously restored from backups table.
echo $OUTPUT->box_end();



// Users obviously restored from backups.
echo $OUTPUT->box_start();
echo $OUTPUT->heading(get_string('anonusersheader', 'tool_sdctools'));
echo '<p>'.get_string('anonusersstrapline', 'tool_sdctools')."</p>\n";

$anon = $DB->get_records_select('user', 'firstname LIKE "anonfirstname%" AND lastname LIKE "anonlastname%" '.$stdwhere, null, 'lastaccess DESC');

if (!$anon) {
    echo '<p>'.get_string('noanonusers', 'tool_sdctools').'</p>';
} else {
    echo '<p>'.get_string('anonusers', 'tool_sdctools', number_format(count($anon))).'</p>';

    $table = new html_table();
    $table->head = array ();
    $table->align = array();
    $table->head[] = '#';
    $table->align[] = '';
    $table->head[] = get_string('id', 'tool_sdctools');
    $table->align[] = 'left';
    $table->head[] = get_string('fullnameuser');
    $table->align[] = 'left';
    $table->head[] = get_string('username');
    $table->align[] = 'left';
    $table->head[] = get_string('firstaccess');
    $table->align[] = 'left';
    $table->head[] = get_string('lastaccess');
    $table->align[] = 'left';
    $table->head[] = get_string('actions');
    $table->align[] = 'left';
    $table->width = "75%";

    $items = 0;
    foreach ($anon as $user) {

        $buttons = array();
        $row = array ();

        if (is_siteadmin($USER) or !is_siteadmin($user)) {
            // Edit.
            $buttons[] = html_writer::link(new moodle_url($securewwwroot.'/user/editadvanced.php',
                array('id' => $user->id, 'course' => $site->id)),
                html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('t/edit'),
                'alt' => $stredit, 'class' => 'iconsmall')), array('title' => $stredit));
            // Delete.
            $buttons[] = html_writer::link(new moodle_url($securewwwroot.'/admin/user.php',
                array('delete' => $user->id, 'sesskey' => sesskey())),
                html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('t/delete'),
                'alt' => $strdelete, 'class' => 'iconsmall')), array('title' => $strdelete));

        }
        if ($user->firstaccess) {
            $strfirstaccess = format_time(time() - $user->firstaccess);
        } else {
            $strfirstaccess = get_string('never');
        }
        if ($user->lastaccess) {
            $strlastaccess = format_time(time() - $user->lastaccess);
        } else {
            $strlastaccess = get_string('never');
        }
        $fullname = fullname($user, true);
        $row[] = number_format(++$items);
        $row[] = $user->id;
        $row[] = '<a href="'.$securewwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$site->id.'">'.$fullname.'</a>';
        $row[] = $user->username;
        $row[] = $strfirstaccess;
        $row[] = $strlastaccess;
        $row[] = implode(' ', $buttons);
        $table->data[] = $row;
    }

    if (!empty($table)) {
        echo html_writer::table($table);
    }

}

// End drawing 'no emails' table.
echo $OUTPUT->box_end();



// End the page.
echo $OUTPUT->footer();
