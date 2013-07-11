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
 * Strings for component 'tool_sdctools', language 'en'.
 *
 * @package    tool_sdctools
 * @copyright  2013 Paul Vaughan {@link http://commoodle.southdevon.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname']       = 'SDC Tools';
$string['pageheader']       = 'South Devon College Admin Tools';

// Table of contents strings.
$string['tableofcontents']        = 'Table of Contents';
$string['emailchecks']            = 'Email Checks';
$string['coursereports']          = 'Course Reports';
$string['coursereportspictures']  = 'with pictures';
$string['second']                 = 'second';

// Server details strings.
$string['serverdetailsheader']        = 'Server details';
$string['moodledetailsheader']        = 'Moodle details';
$string['operatingsystem']            = 'Operating System';
$string['averageload']                = 'Average Load';
$string['averageloadexample']         = '(1/5/15m [100%])';
$string['linuxcpuload']               = 'Understanding Linux CPU load';
$string['processes']                  = 'Processes';
$string['processesexample']           = '(running/total)';
$string['webserver']                  = 'Web Server';
$string['phpversion']                 = 'PHP Version';
$string['serveruptime']               = 'Server Uptime';
$string['moodleversion']              = 'Moodle Version';
$string['internal']                   = 'Internal';
$string['usersactivedeletedtotal']    = 'Users: Active / Deleted / Total';
$string['coursesvisiblehiddentotal']  = 'Courses: Visible / Hidden / Total';
$string['running']                    = ' and running';
$string['backupstatus']               = 'Backup Status';

// Email-checking strings.
$string['emptyemailname']   = 'Empty email check';
$string['noemailheader']    = 'Users without email addresses';
$string['noemailstrapline'] = 'Having no email address prevents a user logging in to Moodle, as they are presented with their profile to edit, but are not allowed to change their email address.';
$string['noemptyemails']    = '<strong>Result:</strong> All users in the \'users\' table have email addresses.';
$string['emptyemails']      = '<strong>Result:</strong> The following users have empty email fields and cannot currently log in:';
$string['nonsdcemailheader']    = 'Users with non-SDC email addresses';
$string['nonsdcemailstrapline'] = 'Users with email addresses which aren\'t from SDC email accounts (e.g. Gmail, Hotmail).';
$string['nonsdcemails']         = '<strong>Result:</strong> The following users have non-SDC (personal) email addresses:';
$string['sdcemails']            = '<strong>Result:</strong> No users have non-SDC (personal) email addresses.';

// Course report strings.
$string['coursereportname'] = 'Cross-Moodle course report';
$string['timewarning']      = 'A single report is generated quite quickly, but viewing all reports can take considerable time to generate and may place a heavy load on the server.';
$string['byname']           = 'Choose course by name (A, B, C, D):';
$string['byid']             = 'Choose course by course ID (1, 2, 3, 4):';
$string['getcoursereport']  = 'Get this course\'s report';
$string['coursereportfor']  = 'Course report for ';
$string['turnpicturesoff']  = 'Turn pictures off';
$string['turnpictureson']   = 'Turn pictures on';
$string['ago']              = 'ago';
$string['coursedetail']     = 'Course Detail';
$string['full']             = 'Full';
$string['short']            = 'Short';
$string['id']               = 'ID';
