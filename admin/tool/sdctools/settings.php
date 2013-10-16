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
 * @copyright  2013 Paul Vaughan {@link http://commoodle.southdevon.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    // Add to development menu.
    $ADMIN->add('development', new admin_externalpage('toolsdctools', get_string('pluginname', 'tool_sdctools'),
        $CFG->wwwroot.'/'.$CFG->admin.'/tool/sdctools/index.php', 'moodle/site:config'));

    // Add to reports menu with a different lang string.
    // $ADMIN->add('reports', new admin_externalpage('toolsdctoolsemail', get_string('emptyemailname', 'tool_sdctools'),
    //     $CFG->wwwroot.'/'.$CFG->admin.'/tool/sdctools/index.php', 'moodle/site:config'));

    // Add courses report to reports menu.
    $ADMIN->add('reports', new admin_externalpage('toolsdctoolscourse', get_string('coursereportname', 'tool_sdctools'),
        $CFG->wwwroot.'/'.$CFG->admin.'/tool/sdctools/coursereports.php', 'moodle/site:config'));
}
