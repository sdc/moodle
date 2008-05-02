<?php //$Id$

class block_admin extends block_list {
    function init() {
        $this->title = get_string('administration');
        $this->version = 2004081200;
    }

    function get_content() {

        global $CFG, $USER, $SITE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content = '';
        } else if ($this->instance->pageid == SITEID) {
            return $this->content = '';
        }

        if (!empty($this->instance->pageid)) {
            $context = get_context_instance(CONTEXT_COURSE, $this->instance->pageid);
        }

        if (empty($context)) {
            $context = get_context_instance(CONTEXT_SYSTEM);
        }

        if (!$course = get_record('course', 'id', $this->instance->pageid)) {
            $course = $SITE;
        }

        if (!has_capability('moodle/course:view', $context)) {  // Just return 
            return $this->content;
        }

        if (empty($CFG->loginhttps)) {
            $securewwwroot = $CFG->wwwroot;
        } else {
            $securewwwroot = str_replace('http:','https:',$CFG->wwwroot);
        }

    /// Course editing on/off

        if (has_capability('moodle/course:update', $context)) {
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/edit.gif" alt="" />';
            if (isediting($this->instance->pageid)) {
                $this->content->items[]='<a href="view.php?id='.$this->instance->pageid.'&amp;edit=off&amp;sesskey='.sesskey().'">'.get_string('turneditingoff').'</a>';
            } else {
                $this->content->items[]='<a href="view.php?id='.$this->instance->pageid.'&amp;edit=on&amp;sesskey='.sesskey().'">'.get_string('turneditingon').'</a>';
            }
            
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/edit.php?id='.$this->instance->pageid.'">'.get_string('settings').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/settings.gif" alt="" />';
        }
            

    /// Assign roles to the course

        if (has_capability('moodle/role:assign', $context)) { 
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.$context->id.'">'.get_string('assignroles', 'role').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/roles.gif" alt="" />';         
            
        }

    /// Manage metacourses
        if ($course->metacourse) {
            if (has_capability('moodle/course:managemetacourse', $context)) { 
                $strchildcourses = get_string('childcourses');
                $this->content->items[]='<a href="importstudents.php?id='.$this->instance->pageid.'">'.$strchildcourses.'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/course.gif" alt="" />';
            } else if (has_capability('moodle/role:assign', $context)) {
                $strchildcourses = get_string('childcourses');
                $this->content->items[]='<span class="dimmed_text">'.$strchildcourses.'</span>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/course.gif" alt="" />';
            }
        }


    /// Manage groups in this course

        if (($course->groupmode || !$course->groupmodeforce) && has_capability('moodle/course:managegroups', $context)) {
            $strgroups = get_string('groups');
            $this->content->items[]='<a title="'.$strgroups.'" href="'.$CFG->wwwroot.'/course/groups.php?id='.$this->instance->pageid.'">'.$strgroups.'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/group.gif" alt="" />';
        }

    /// Backup this course

        if (has_capability('moodle/site:backup', $context)) { 
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/backup/backup.php?id='.$this->instance->pageid.'">'.get_string('backup').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/backup.gif" alt="" />';
        }
            
    /// Restore to this course
        if (has_capability('moodle/site:restore', $context)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/files/index.php?id='.$this->instance->pageid.'&amp;wdir=/backupdata">'.get_string('restore').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/restore.gif" alt="" />';
        }
        
    /// Import data from other courses
        if (has_capability('moodle/site:import', $context)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/import.php?id='.$this->instance->pageid.'">'.get_string('import').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/restore.gif" alt="" />';
        }
        
    /// Reset this course
        if (has_capability('moodle/course:reset', $context)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/reset.php?id='.$this->instance->pageid.'">'.get_string('reset').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/return.gif" alt="" />';
        }
        
    /// View course reports
        if (has_capability('moodle/site:viewreports', $context)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/report.php?id='.$this->instance->pageid.'">'.get_string('reports').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/stats.gif" alt="" />';
        }
        
    /// Manage questions
        if (has_capability('moodle/question:manage', $context)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/question/edit.php?courseid='.$this->instance->pageid.'&amp;clean=true">'.get_string('questions', 'quiz').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/questions.gif" alt="" />';
        }

    /// Manage scales
        if (has_capability('moodle/course:managescales', $context)) {
            $this->content->items[]='<a href="scales.php?id='.$this->instance->pageid.'">'.get_string('scales').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/scales.gif" alt="" />';      
        }
        

    /// Manage files
        if (has_capability('moodle/course:managefiles', $context)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/files/index.php?id='.$this->instance->pageid.'">'.get_string('files').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/files.gif" alt="" />';
        }

    /// Authorize hooks
        if ($course->enrol == 'authorize' || (empty($course->enrol) && $CFG->enrol == 'authorize')) {
            require_once($CFG->dirroot.'/enrol/authorize/const.php');
            $paymenturl = '<a href="'.$CFG->wwwroot.'/enrol/authorize/index.php?course='.$course->id.'">'.get_string('payments').'</a> ';
            if (has_capability('enrol/authorize:managepayments', $context)) {
                if ($cnt = count_records('enrol_authorize', 'status', AN_STATUS_AUTH, 'courseid', $course->id)) {
                    $paymenturl .= '<a href="'.$CFG->wwwroot.'/enrol/authorize/index.php?status='.AN_STATUS_AUTH.'&amp;course='.$course->id.'">'.get_string('paymentpending', 'moodle', $cnt).'</a>';
                }
            }
            $this->content->items[] = $paymenturl;
            $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/payment.gif" alt="" />';
        }

    /// View course grades (or just your own grades, same link)
        if (has_capability('moodle/course:viewcoursegrades', $context) or 
            (has_capability('moodle/user:viewusergrades', $context) && $course->showgrades)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/grade/index.php?id='.$this->instance->pageid.'">'.get_string('grades').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/grades.gif" alt="" />';
        }

        if (empty($course->metacourse)) {
            if (has_capability('moodle/legacy:guest', $context, NULL, false)) {   // Are a guest now
                $this->content->items[]='<a href="enrol.php?id='.$this->instance->pageid.'">'.get_string('enrolme', '', $course->shortname).'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/user.gif" alt="" />';
            } else if (has_capability('moodle/role:unassignself', $context, NULL, false)) {  // Have some role
                $this->content->items[]='<a href="unenrol.php?id='.$this->instance->pageid.'">'.get_string('unenrolme', '', $course->shortname).'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/user.gif" alt="" />';
            }
        }

   /// Link to the user own profile
        $this->content->items[]='<a href="'.$CFG->wwwroot.'/user/view.php?id='.$USER->id.'&amp;course='.$course->id.'">'.get_string('profile').'</a>';
        $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/user.gif" alt="" />';

        return $this->content;
    }

    function applicable_formats() {
        return array('all' => false, 'course' => true);
    }
}

?>
