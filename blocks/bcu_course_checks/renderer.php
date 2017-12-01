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
 * Version details
 *
 * @package    block
 * @subpackage bcu_course_checks
 * @copyright  2014 Michael Grant <michael.grant@bcu.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once($CFG->dirroot.'/blocks/bcu_course_checks/locallib.php');
require_once($CFG->libdir.'/weblib.php');

defined('MOODLE_INTERNAL') || die;

class block_bcu_course_checks_renderer extends plugin_renderer_base {

    private $_config;

    public function setconfig($config) {
        $this->_config = $config;
        if ($this->_config === null) {
            $this->_config = new stdClass();
        }
        $defaults = get_config('bcu_course_checks');
        foreach ($defaults as $key => $value) {
            if(get_config('bcu_course_checks', 'usersoptions') === '1') {
                if (!ISSET($this->_config->$key)) {
                    $this->_config->$key = $value;
                }
            } else {
                $this->_config->$key = $value;
            }
        }
    }

    public function render_course_status($courseid) {
        $content = '';
        $essentials = '';
        $recommended = '';
        $status = false;
        $courseinfo = block_bcu_course_checks_get_course_properties($courseid);

        if ($this->_config->coursesummary) {
            $essentials .= get_string('coursesummary', 'block_bcu_course_checks') . $this->set_image($this->is_property_set($courseinfo->summary)).'<br>';
            if(!$courseinfo->summary) {
                $status = true;
            }
        }

        if ($this->_config->courseimage) {
            $courseoverviewfiles = block_bcu_course_checks_get_course_overviewfiles($courseid);
            $essentials .= get_string('courseimage', 'block_bcu_course_checks') . $this->set_image($courseoverviewfiles)."<br>";
            if(!$courseoverviewfiles) {
                $status = true;
            }
        }

        if ($this->_config->coursevisible) {
            $essentials .= get_string('coursevisible', 'block_bcu_course_checks') . $this->set_image($this->is_property_set($courseinfo->visible)).'<br>';
            if(!$courseinfo->visible) {
                $status = true;
            }
        }

        if ($this->_config->courseguest) {
            $courseguestaccess = block_bcu_course_checks_get_guest_access($courseid);
            $essentials .= get_string('guestaccess', 'block_bcu_course_checks') . $this->set_image($this->is_property_set($courseguestaccess)).'<br>';
            if(!$courseguestaccess) {
                $status = true;
            }
        }
        if(strlen($essentials)>0) {
            $content .= $this->render_check_section($essentials, 'block_bcu_course_checks_essentials', 'essentials', $status);
        }
        
        $sections = block_bcu_course_checks_check_sections($courseid);
        
        if ($sections && ($this->_config->renamedsections || $this->_config->summarysections
                || $this->_config->contentsections || $this->_config->visiblesections)) {
            
            
            $courseformat = block_bcu_course_checks_check_format($courseid);
            $recommended = $this->check_sections($sections, $courseformat);
            $content .= $this->render_check_section($recommended['table'], 'block_bcu_course_checks_sections', 'sectionchecks', $recommended['status']);
        }
        
        
        if (!empty($this->_config->blocksenabled)) {
            $courseblocks = block_bcu_course_checks_get_course_blocks($courseinfo);
            $blocks = '';
            $anyfails = false;
            foreach ($courseblocks as $key => $value) {
                if(!$value) {
                    $anyfails = true;
                }
                $blocks .= $key . $this->set_image($value)."<br>";
            }
            $content .= $this->render_check_section($blocks, 'block_bcu_course_checks_blocks', 'suggestedblocks', $anyfails);   
            
        }
        
        if($this->_config->assignmentchecks) {
            $assignments = block_bcu_course_checks_get_outdated_assignments($courseinfo);
            $content .= $this->render_check_section($assignments['content'], 'block_bcu_course_checks_assignments', 'assignments', $assignments['status']);
        }
        
        return $content;
    }
    
    public function render_check_section($content, $id, $title, $expanded=true) {
        return print_collapsible_region($content, 'block_bcu_course_checks_collapsible', $id, $this->render_title($title, $expanded), '', !$expanded, true);
    }
    
    public function render_title($title, $status) {
        $content = get_string($title, 'block_bcu_course_checks');
        
        if($status) {
            // We use the expanded setting to track if there are issues, and need to flip it for the correct image.
            $content .= $this->set_image(false);
        } else {
            $content .= $this->set_image(true);
        }
        return $content;
    }

    public function render_cleanup($courseid) {
        GLOBAL $PAGE;
        $cleanup = '';
        if ($this->_config->coursecleanup && $PAGE->user_is_editing()) {
            $cleanup = html_writer::link(new moodle_url('', array('cleanme' => true, 'id' => $courseid, 'sesskey' => sesskey())),
                    get_string('cleanupbutton', 'block_bcu_course_checks'),
                    array('class' => 'btn btn-primary btn-large btn-block'));
        }

        return $cleanup;
    }

    public function check_sections($sections, $courseformat) {

        $tableheadings = array(
            'renamedsections' => get_string('renamedsections', 'block_bcu_course_checks'),
            'summarysections' => get_string('summarysections', 'block_bcu_course_checks'),
            'contentsections' => get_string('contentsections', 'block_bcu_course_checks'),
            'visiblesections' => get_string('visiblesections', 'block_bcu_course_checks')
        );

        $formats = explode(',', $this->_config->formatsname);
        if (!in_array($courseformat->format, $formats)) {
            $this->_config->renamedsections = false;
        }
        $table = new html_table();
        $table->data        = array();
        $table->attributes['class'] = 'generaltable bcu_course_checks sectiontable';
        $table->size = array("15%", "21%", "21%", "21%", "21%");
        $table->head        = array(' ');

        foreach ($tableheadings as $key => $value) {
            if ($this->_config->$key) {
                $table->head[] = $value;
            }
        }
        $anyfails = false;
        foreach ($sections as $key => $value) {
            list($renamed, $summary, $content, $visible) = array(true, true, true, true);
            $row = new html_table_row();

            $cells[] = new html_table_cell($key);

            if ($this->_config->renamedsections) {
                $renamed = $this->is_property_set($value['name']);
                $cells[] = new html_table_cell($this->set_image($renamed, 'tableimg'));
            }
            if ($this->_config->summarysections) {
                $summary = $this->is_property_set($value['summary']);
                $cells[] = new html_table_cell($this->set_image($summary, 'tableimg'));
            }
            if ($this->_config->contentsections) {
                $content = $this->is_property_set($value['content']);
                $cells[] = new html_table_cell($this->set_image($content, 'tableimg'));
            }
            if ($this->_config->visiblesections) {
                $visible = $this->is_property_set($value['visible']);
                $cells[] = new html_table_cell($this->set_image($visible, 'tableimg'));
            }
            $row->cells = $cells;
            $cells = array();
            $table->data[] = $row;
            
            
            if(!$renamed || !$summary || !$content || !$visible) {
                $anyfails = true;
            }
        }
        $tabular = html_writer::table($table);
        
        $table = html_writer::tag('div', $tabular, array('style' => 'display: table', 'class' => 'table-responsive'));
        return array('table'=>$table, 'status'=>$anyfails);
    }

    public function set_image($status, $class='bcu_course_status') {
        global $OUTPUT;
        if ($status) {
            return html_writer::tag('span', $OUTPUT->pix_icon('i/grade_correct', ''), array('class' => $class));
        } else {
            $this->errors = true;
            return html_writer::tag('span', $OUTPUT->pix_icon('i/grade_incorrect', ''), array('class' => $class));;
        }
    }

    public function is_property_set($courseproperty) {
        if ($courseproperty) {
            return true;
        }
        return false;
    }
}