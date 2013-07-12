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
    echo '<li><strong>'.get_string('operatingsystem', 'tool_sdctools').':</strong> '.$osver[0].'</li>';
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
        echo '<li><strong>'.get_string('averageload', 'tool_sdctools').':</strong> '.get_string('averageloadexample', 'tool_sdctools').' '.$loads[0].' / '.$loads[1].' / '.$loads[2].' ['.$cpucount.'.0] (<a href="http://blog.scoutapp.com/articles/2009/07/31/understanding-load-averages">'.get_string('linuxcpuload', 'tool_sdctools').'</a>)</li>';
        echo '<li><strong>'.get_string('processes', 'tool_sdctools').':</strong> '.get_string('processesexample', 'tool_sdctools').' '.$loads[3].'</li>';
    }
}
echo '<li><strong>'.get_string('webserver', 'tool_sdctools').':</strong> '.$_SERVER["SERVER_SOFTWARE"].'</li>';
echo '<li><strong>'.get_string('phpversion', 'tool_sdctools').':</strong> '.phpversion().'</li>';
if (is_readable('/proc/uptime') && $uptime = @file('/proc/uptime')) {
    $utime = explode(' ', $uptime[0]);
    $out = sdctools_timeago($utime[0], false);
    echo '<li><strong>'.get_string('serveruptime', 'tool_sdctools').':</strong> '.$out.'</li>';
}
echo '</ul>';

// End drawing 'Server details' table.
echo $OUTPUT->box_end();



echo $OUTPUT->box_start();
echo $OUTPUT->heading(get_string('moodledetailsheader', 'tool_sdctools'));

echo '<ul>';
echo '<li><strong>'.get_string('moodleversion', 'tool_sdctools').':</strong> '.$CFG->release.' ['.get_string('internal', 'tool_sdctools').': '.$CFG->version.']</li>';
$users_active = $DB->get_record_sql('SELECT COUNT(*) AS users FROM mdl_user WHERE deleted = 0;');
$users_deleted = $DB->get_record_sql('SELECT COUNT(*) AS users FROM mdl_user WHERE deleted = 1;');
echo '<li><strong>'.get_string('usersactivedeletedtotal', 'tool_sdctools').':</strong> '.number_format($users_active->users).' / '.number_format($users_deleted->users).' / '.number_format($users_active->users + $users_deleted->users).'</li>';
$courses_visible = $DB->get_record_sql('SELECT COUNT(*) AS courses FROM mdl_course WHERE visible = 1;');
$courses_hidden = $DB->get_record_sql('SELECT COUNT(*) AS courses FROM mdl_course WHERE visible = 0;');
echo '<li><strong>'.get_string('coursesvisiblehiddentotal', 'tool_sdctools').':</strong> '.number_format($courses_visible->courses).' / '.number_format($courses_hidden->courses).' / '.number_format($courses_visible->courses + $courses_hidden->courses).'</li>';
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
    $out .= get_string('running', 'tool_sdctools');
}
echo '<li><strong>'.get_string('backupstatus', 'tool_sdctools').':</strong> '.$out.'</li>';

echo '</ul>';

// End drawing 'Moodle details' table.
echo $OUTPUT->box_end();


// Recent new user stats.
echo $OUTPUT->box_start();
echo $OUTPUT->heading(get_string('newuserstatsheading', 'tool_sdctools'));
echo '<p>'.get_string('newuserstatsstrapline', 'tool_sdctools').'</p>';
$now = time();
$userstats = $DB->get_records_sql("SELECT 
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - 60) ) AS one, 
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*5)) ) AS five, 
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*15)) ) AS fifteen,
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*60)) ) AS onehour,
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*60*2)) ) AS twohours,
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*60*6)) ) AS sixhours,
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*60*12)) ) AS twelvehours,
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*60*24)) ) AS twentyfour,
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*60*24*2)) ) AS fortyeight,
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*60*24*7)) ) AS oneweek,
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*60*24*14)) ) AS twoweeks,
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*60*24*30)) ) AS onemonth,
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*60*24*30*2)) ) AS twomonths,
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*60*24*30*3)) ) AS threemonths,
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*60*24*30*6)) ) AS sixmonths,
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*60*24*365)) ) AS oneyear,
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*60*24*365*2)) ) AS twoyears,
    ( SELECT COUNT(*) FROM mdl_user WHERE firstaccess > (".$now." - (60*60*24*365*3)) ) AS threeyears
;");

$table = new html_table();
$table->head = array ();
$table->head[] = '1m';
$table->head[] = '5m';
$table->head[] = '15m';
$table->head[] = '1h';
$table->head[] = '2h';
$table->head[] = '6h';
$table->head[] = '12h';
$table->head[] = '1d';
$table->head[] = '2d';
$table->head[] = '1w';
$table->head[] = '2w';
$table->head[] = '1mn';
$table->head[] = '2mn';
$table->head[] = '3mn';
$table->head[] = '6mn';
$table->head[] = '1y';
$table->head[] = '2y';
$table->head[] = '3y';
$table->width = "100%";
$row = array ();
$row[] = number_format($userstats[0]->one);
$row[] = number_format($userstats[0]->five);
$row[] = number_format($userstats[0]->fifteen);
$row[] = number_format($userstats[0]->onehour);
$row[] = number_format($userstats[0]->twohours);
$row[] = number_format($userstats[0]->sixhours);
$row[] = number_format($userstats[0]->twelvehours);
$row[] = number_format($userstats[0]->twentyfour);
$row[] = number_format($userstats[0]->fortyeight);
$row[] = number_format($userstats[0]->oneweek);
$row[] = number_format($userstats[0]->twoweeks);
$row[] = number_format($userstats[0]->onemonth);
$row[] = number_format($userstats[0]->twomonths);
$row[] = number_format($userstats[0]->threemonths);
$row[] = number_format($userstats[0]->sixmonths);
$row[] = number_format($userstats[0]->oneyear);
$row[] = number_format($userstats[0]->twoyears);
$row[] = number_format($userstats[0]->threeyears);
$table->data[] = $row;

if (!empty($table)) {
    echo html_writer::table($table);
} else {
    echo '<ul><li>'.get_string('nonefound', 'tool_sdctools').'</li></ul>';
}

echo '<p>'.get_string('firstaccessstrapline', 'tool_sdctools').'</p>';
$firstaccess = $DB->get_records_sql("SELECT 0,
    ( SELECT COUNT(*) FROM mdl_user WHERE deleted = 0 AND firstaccess = 0 ) AS no, 
    ( SELECT COUNT(*) FROM mdl_user WHERE deleted = 0 AND firstaccess > 0 ) AS yes,
    ( SELECT COUNT(*) FROM mdl_user WHERE deleted = 0 ) AS total
;");

$table = new html_table();
$table->head = array ();
$table->head[] = get_string('no');
$table->head[] = get_string('yes');
$table->head[] = get_string('total');
$table->width = "25%";
$row = array ();
$row[] = number_format($firstaccess[0]->no);
$row[] = number_format($firstaccess[0]->yes);
$row[] = number_format($firstaccess[0]->total);
$table->data[] = $row;

if (!empty($table)) {
    echo html_writer::table($table);
} else {
    echo '<ul><li>'.get_string('nonefound', 'tool_sdctools').'</li></ul>';
}

// End Recent new user stats.
echo $OUTPUT->box_end();


// End the page.
echo $OUTPUT->footer();

