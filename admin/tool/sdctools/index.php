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

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pageheader', 'tool_sdctools'));

echo sdctools_tableofcontents();


/* Some server details. */
echo $OUTPUT->box_start();
echo $OUTPUT->heading(get_string('serverdetailsheader', 'tool_sdctools'));

echo '<ul>';
if (is_readable('/proc/version') && $osver = @file('/proc/version')) {
    echo '<li><strong>Operating System:</strong> '.$osver[0].'</li>';
}
/* For some reason, I can't use two file functions in the same if statement... */
if (is_readable('/proc/loadavg') && $loadavg = @file('/proc/loadavg')) {
    if (is_readable('/proc/cpuinfo') && $cpuinfo = @file('/proc/cpuinfo')) {
        $loads = explode(' ', $loadavg[0]);
        $cpucount = 0;
        foreach ($cpuinfo as $cpu) {
            if (preg_match('/processor/', $cpu)) {
                $cpucount++;
            }
        }
        echo '<li><strong>Average Load:</strong> (1/5/15m [100%]) '.$loads[0].' / '.$loads[1].' / '.$loads[2].' ['.$cpucount.'.0] (<a href="http://blog.scoutapp.com/articles/2009/07/31/understanding-load-averages">Understanding Linux CPU load</a>)</li>';
        echo '<li><strong>Processes:</strong> (running/total) '.$loads[3].'</li>';
    }
}
echo '<li><strong>Web Server:</strong> '.$_SERVER["SERVER_SOFTWARE"].'</li>';
echo '<li><strong>PHP Version:</strong> '.phpversion().'</li>';
if (is_readable('/proc/uptime') && $uptime = @file('/proc/uptime')) {
    $utime = explode(' ', $uptime[0]);
    $out = sdctools_timeago($utime[0], false);
    echo '<li><strong>Server Uptime:</strong> '.$out.'</li>';
}
echo '</ul>';

echo $OUTPUT->heading(get_string('moodledetailsheader', 'tool_sdctools'));

echo '<ul>';
echo '<li><strong>Moodle Version:</strong> '.$CFG->release.' [Internal: '.$CFG->version.']</li>';
$users_active = $DB->get_record_sql('SELECT COUNT(*) AS users FROM mdl_user WHERE deleted = 0;');
$users_deleted = $DB->get_record_sql('SELECT COUNT(*) AS users FROM mdl_user WHERE deleted = 1;');
echo '<li><strong>Users: Active / Deleted / Total:</strong> '.number_format($users_active->users).' / '.number_format($users_deleted->users).' / '.number_format($users_active->users + $users_deleted->users).'</li>';
$courses_visible = $DB->get_record_sql('SELECT COUNT(*) AS courses FROM mdl_course WHERE visible = 1;');
$courses_hidden = $DB->get_record_sql('SELECT COUNT(*) AS courses FROM mdl_course WHERE visible = 0;');
echo '<li><strong>Courses: Visible / Hidden / Total:</strong> '.number_format($courses_visible->courses).' / '.number_format($courses_hidden->courses).' / '.number_format($courses_visible->courses + $courses_hidden->courses).'</li>';
// backups
$out = '';
$backup_status = $DB->get_record('config_plugins', array('plugin' => 'backup', 'name' => 'backup_auto_active'), 'value');
if ($backup_status->value == 0) {
    // Disabled.
    $out = '<span class="error">'.get_string('autoactivedisabled', 'backup').'</span> ('.
        html_writer::link(new moodle_url('/admin/settings.php', array('section' => 'automated')), get_string('automatedsetup', 'backup')).')';
} else if ($backup_status->value == 1) {
    // Enabled.
    $out = get_string('autoactiveenabled', 'backup');
} else if ($backup_status->value == 2) {
    // Manual.
    $out = get_string('autoactivemanual', 'backup');
}
$backup_running = $DB->get_record('config_plugins', array('plugin' => 'backup', 'name' => 'backup_auto_running'), 'value');
if ($backup_running && $backup_running->value == 1) {
    $out .= ' and running';
}
echo '<li><strong>Backup Status:</strong> '.$out.'</li>';


echo '</ul>';


// End drawing 'Server details' table.
echo $OUTPUT->box_end();



// End the page.
echo $OUTPUT->footer();
