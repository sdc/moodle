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
 * @package    tool
 * @subpackage sdctools
 * @copyright  2013 Paul Vaughan {@link http://commoodle.southdevon.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once('../../../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('toolsdctools');

if (empty($CFG->loginhttps)) {
    $securewwwroot = $CFG->wwwroot;
} else {
    $securewwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
}

$stredit    = get_string('edit');
$site = get_site();

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pageheader', 'tool_sdctools'));


// Users without email addresses.
echo $OUTPUT->box_start();
echo $OUTPUT->heading(get_string('noemailheader', 'tool_sdctools'));
echo '<p>'.get_string('noemailstrapline', 'tool_sdctools')."</p>\n";

$noemail = get_users(true, 'email = ""', false, null, null, null, null, null, 500);

if (!$noemail) {
    echo '<p>'.get_string('noemptyemails', 'tool_sdctools').'</p>';
} else {
    echo '<p>'.get_string('emptyemails', 'tool_sdctools').'</p>';

    $table = new html_table();
    $table->head = array ();
    $table->align = array();
    $table->head[] = get_string('fullnameuser');
    $table->align[] = 'left';
    $table->head[] = get_string('username');
    $table->align[] = 'left';
    $table->head[] = get_string('lastaccess');
    $table->align[] = 'left';
    $table->head[] = get_string('actions');
    $table->align[] = 'left';
    $table->width = "50%";

    foreach ($noemail as $user) {

        $buttons = array();
        $row = array ();

        // Edit button.
        if (is_siteadmin($USER) or !is_siteadmin($user)) {
            $buttons[] = html_writer::link(new moodle_url($securewwwroot.'/user/editadvanced.php',
                array('id' => $user->id, 'course' => $site->id)),
                html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('t/edit'),
                'alt' => $stredit, 'class' => 'iconsmall')), array('title' => $stredit));
        }
        if ($user->lastaccess) {
            $strlastaccess = format_time(time() - $user->lastaccess);
        } else {
            $strlastaccess = get_string('never');
        }
        $fullname = fullname($user, true);
        $row[] = '<a href="'.$securewwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$site->id.'">'.$fullname.'</a>';
        $row[] = $user->username;
        $row[] = $strlastaccess;
        $row[] = implode(' ', $buttons);
        $table->data[] = $row;
    }

    if (!empty($table)) {
        echo html_writer::table($table);
    }

} // End drawing 'no emails' table.

echo $OUTPUT->box_end();
echo $OUTPUT->footer();
