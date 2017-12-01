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

class block_bcu_course_checks extends block_base
{
    
    private $renderer;
    
    private function load_renderer() {
        $this->renderer = $this->page->get_renderer('block_bcu_course_checks');
    }
    
    public function init() {
        
        $this->title = get_string('creationprogress', 'block_bcu_course_checks');
    }

    public function get_content() {
        global $OUTPUT, $COURSE, $PAGE, $USER;
        $this->load_renderer();
        if ($this->content !== null) {
            return $this->content;
        }
        $this->renderer->setconfig($this->config);
        if (has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
            $cache = cache::make('block_bcu_course_checks', 'coursechecksdata');
            $this->content = new stdClass;
            if ($COURSE->format != 'site') {
                if ($PAGE->user_is_editing()) {
                    $cleanup = '';
                    $clean = optional_param('cleanme', false, PARAM_BOOL);
                    if ($clean) {
                        if (has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
                            require_sesskey();
                            $clean = block_bcu_course_checks_course_cleanup($COURSE->id, 0);
                            $cleanup .= html_writer::div(get_string('cleansuccess', 'block_bcu_course_checks', $clean),
                                    'alert alert-success');
                        } else {
                            $cleanup .= html_writer::div(get_string('cleanpermfailure', 'block_bcu_course_checks'),
                                    'alert alert-danger');
                        }
                    }
                    $this->content->text = $this->renderer->render_course_status($COURSE->id);
                    $cache->set('coursechecksdata'.$USER->id.'_'. $COURSE->id, $this->content->text);
                    $cache->set('coursecheckstimestampp_'.$USER->id.'_'. $COURSE->id, time());
                    
                    $this->content->text = $cleanup.$this->renderer->render_cleanup($COURSE->id).$this->content->text;
                    return $this->content;
                } else {
                    $this->content->text = $this->get_cached_content($USER->id, $COURSE->id);
                    return $this->content;
                }
            }
            return $this->content;
        }
    }

    public function get_cached_content($user, $course) {
        global $PAGE;
        $cachettl = 300; // Cache for five minutes.
        $cache = cache::make('block_bcu_course_checks', 'coursechecksdata');
        if ($cachetimestamp = $cache->get('coursecheckstimestampp_'.$user.'_'. $course)) {
            if ((time() - $cachetimestamp) < $cachettl) {
                
                $sections = array('block_bcu_course_checks_essentials', 'block_bcu_course_checks_sections', 'block_bcu_course_checks_blocks', 'block_bcu_course_checks_assignments');
                foreach($sections as $section) {
                    $PAGE->requires->js_init_call('M.util.init_collapsible_region', array($section, '', get_string('clicktohideshow')));
                }
                
                $content = $cache->get('coursechecksdata'.$user.'_'. $course);
            }
        }

        if (empty ($content)) {
            // Re-fetch content and rebuild cache.
            $content = $this->renderer->render_course_status($course);
            $cache->set('coursechecksdata'.$user.'_'. $course, $content);
            $cache->set('coursecheckstimestampp_'.$user.'_'. $course, time());
        }
        return $content;
    }

    public function applicable_formats() {
        return array('course' => true, 'site' => true);
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function hide_header() {
        return false;
    }

    public function has_config() {
        return true;
    }
}