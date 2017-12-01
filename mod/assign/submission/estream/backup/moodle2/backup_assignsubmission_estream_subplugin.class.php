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
 * backup class for the Planet eStream Assignment Submission Plugin
 * extends submission plugin base class
 *
 * @package        assignsubmission_estream
 * @copyright        Planet Enterprises Ltd
 * @license        http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();
class backup_assignsubmission_estream_subplugin extends backup_subplugin
{
    protected function define_submission_subplugin_structure() {
            $subplugin = $this->get_subplugin_element();
            $subpluginwrapper = new backup_nested_element($this->get_recommended_name());
            $subpluginelement = new backup_nested_element('submission_estream', null, array('cdid', 'embedcode', 'submission'));
            $subplugin->add_child($subpluginwrapper);
            $subpluginwrapper->add_child($subpluginelement);
            $subpluginelement->set_source_table('assignsubmission_estream', array('submission' => backup::VAR_PARENTID));
            $subpluginelement->annotate_files('assignsubmission_estream', 'submissions_estream', 'submission');
            return $subplugin;
    }
}
