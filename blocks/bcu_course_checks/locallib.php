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

function block_bcu_course_checks_get_course_properties($courseid) {
    global $DB;
    $course = $DB->get_record('course', array('id' => $courseid));
    return $course;
}

function block_bcu_course_checks_check_sections($courseid) {
    global $CFG;
    require_once($CFG->dirroot.'/course/lib.php');
    $status;
    $course = get_course($courseid);
    $courseinf = course_get_format($course)->get_course();
    $modinfo = get_fast_modinfo($course);
    foreach ($modinfo->get_section_info_all() as $section => $value) {
        if ($section <= $courseinf->numsections) {
            if ($value->section > 0) {
                if ($value->name === null) {
                    $status[$value->section]['name'] = false;
                } else {
                    $status[$value->section]['name'] = true;
                }
                if (strlen($value->summary) === 0) {
                    $status[$value->section]['summary'] = false;
                } else {
                    $status[$value->section]['summary'] = true;
                }
                if (strlen($value->sequence) === 0) {
                    $status[$value->section]['content'] = false;
                } else {
                    $status[$value->section]['content'] = true;
                }
                if ($value->visible === "0") {
                    $status[$value->section]['visible'] = false;
                } else {
                    $status[$value->section]['visible'] = true;
                }
            }
        } else {
            break;
        }
    }
    return $status;
}

function block_bcu_course_checks_check_format($courseid) {
    global $CFG;
    require_once($CFG->dirroot.'/course/lib.php');
    $course = get_course($courseid);
    $courseinf = course_get_format($course)->get_course();
    return $courseinf;
}

function block_bcu_course_checks_get_all_blocks() {
    GLOBAL $DB, $CFG;
    if (!$blocks = $DB->get_records('block', array(), 'name ASC')) {
        print_error('noblocks', 'error');  // Should never happen.
    }
    foreach ($blocks as $blockid => $block) {
        $blockname = $block->name;
        if (file_exists("$CFG->dirroot/blocks/$blockname/block_$blockname.php")) {
            $blocknames[$blockname.'-'.get_string('pluginname', 'block_'.$blockname)]
                    = get_string('pluginname', 'block_'.$blockname);
        } else {
            $blocknames[$blockname] = $blockname;
        }
    }
    core_collator::asort($blocknames);
    return $blocknames;
}

function block_bcu_course_checks_get_all_formats() {
    GLOBAL $CFG;
    require_once($CFG->dirroot.'/course/lib.php');
    $formats = get_sorted_course_formats(true);
    foreach ($formats as $format) {
        $formatnames[$format] = get_string('pluginname', "format_$format");
    }
    core_collator::asort($formatnames);
    return $formatnames;
}

function block_bcu_course_checks_get_course_blocks($course) {
    GLOBAL $CFG, $PAGE, $DB;
    require_once($CFG->libdir. '/blocklib.php');
    $blockmanager = $PAGE->blocks;
    $blockmanager->load_blocks(true);
    $reqblocks = explode(',', get_config('bcu_course_checks', 'blocksenabled'));
    foreach ($reqblocks as $required) {
        $blockname = explode('-', $required);
        if ($blockmanager->is_block_present($blockname[0])) {
            $status[$blockname[1]] = true;
        } else {
            $status[$blockname[1]] = false;
        }
    }
    return $status;
}

function block_bcu_course_checks_get_course_overviewfiles($courseid) {
    GLOBAL $CFG, $COURSE;
    if ($COURSE instanceof stdClass) {
        require_once($CFG->libdir. '/coursecatlib.php');
        $course = new course_in_list($COURSE);
    }
    $courseimg = false;
    foreach ($course->get_course_overviewfiles() as $file) {
        $isimage = $file->is_valid_image();
        if ($isimage) {
            $courseimg = true;
        }
    }
    return $courseimg;
}

function block_bcu_course_checks_get_guest_access($courseid) {
    global $DB;
    $course = $DB->get_record('enrol', array('courseid' => $courseid, 'enrol' => 'guest'));
    
    if ($course && $course->status == 1) {
        return true;
    }
    return false;
}

function block_bcu_course_checks_course_cleanup($courseid, $cleaniterations) {
    global $CFG;
    require_once($CFG->dirroot.'/course/lib.php');
    $course = get_course($courseid);
    $courseinf = course_get_format($course)->get_course();
    $modinfo = get_fast_modinfo($course);
    $i = 0;
    $sectionadjust = (int)$courseinf->numsections;
    if ($courseinf->numsections > 1) {
        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($section <= $courseinf->numsections) {
                $sectioninf = $modinfo->get_section_info($i)->sequence;
                // Check that the section has no content and no summary assigned.
                if (strlen($sectioninf) === 0 && strlen($thissection->summary) === 0) {
                    $move = move_section_to($course, $i, (int)$courseinf->numsections);
                    course_get_format($course)->update_course_format_options(array('numsections' => $sectionadjust - 1));
                    $cleaniterations++;
                    return block_bcu_course_checks_course_cleanup($courseid, $cleaniterations);
                }
            }
            $i++;
        }
    }
    return $cleaniterations;
}

function block_bcu_course_checks_get_outdated_assignments($courseinfo) {
    global $DB, $OUTPUT;
    
    $anyfails = false;
    
    $content = html_writer::start_tag('fieldset', null, array('class'=>'clearfix collapsible'));
    
    if($courseinfo->timecreated > $courseinfo->startdate) {
        $datecheck = $courseinfo->timecreated;
    } else {
        $datecheck = $courseinfo->startdate;
    }
    
    $assignments = $DB->get_records_sql('SELECT cm.id, a.name, a.duedate FROM {course_modules} cm JOIN {assign} a ON cm.instance = a.id WHERE cm.course = ? AND a.duedate < ?', array($courseinfo->id, $datecheck));
    foreach($assignments as $assignment) {
        $anyfails = true;
        
        if(strlen($assignment->name)>30) {
            $name = substr($assignment->name, 0, 30).'...';
        } else {
            $name = $assignment->name;
        }
        
        $content .= html_writer::tag('span', $name, array('class'=>'assignmenttitle', 'title'=>$assignment->name)).html_writer::tag('span', html_writer::link(new moodle_url('/course/modedit.php', array('update' => $assignment->id, 'return'=>1)), $OUTPUT->pix_icon('t/edit', '')), array('class' => 'bcu_course_status'));
        $content .= html_writer::tag('span', userdate($assignment->duedate), array('class'=>'assignmentdate'));
        $content .= html_writer::empty_tag('hr', array('class'=>'assignmenthr'));
    }
    
    $content .= html_writer::end_tag('fieldset');
        
    return array('content'=>$content, 'status'=>$anyfails);
}