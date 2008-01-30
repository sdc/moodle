<?PHP //$Id$

class block_participants extends block_list {
    function init() {
        $this->title = get_string('people');
        $this->version = 2004052600;
    }

    function get_content() {
          
        global $USER, $CFG, $COURSE;
    
        // the following 3 lines is need to pass _self_test();
        if (empty($this->instance->pageid)) {
            return '';  
        }
        
        
        // only 2 possible contexts, site or course
        if ($this->instance->pageid == SITEID) { // site context
              $currentcontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
              $canviewparticipants = has_capability('moodle/site:viewparticipants', $currentcontext);
        } else { // course context
            /// MDL-13252 Always get the course context or else the context may be incorrect in the user/index.php
            $currentcontext = get_context_instance(CONTEXT_COURSE, $COURSE->id);
            $canviewparticipants = has_capability('moodle/course:viewparticipants', $currentcontext);
        }
        
        if (!$canviewparticipants) {
              $this->context = '';
              return $this->content;
        }

        if ($this->content !== NULL) {
            return $this->content;
        }
        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';


        if (empty($this->instance->pageid)) {
            $this->instance->pageid = SITEID;
        }

        if ($COURSE->id != SITEID || $canviewparticipants) {

            $this->content->items[] = '<a title="'.get_string('listofallpeople').'" href="'.
                                      $CFG->wwwroot.'/user/index.php?contextid='.$currentcontext->id.'">'.get_string('participants').'</a>';
            $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/users.gif" height="16" width="16" alt="" />';
        }


        return $this->content;
    }
    
    // my moodle can only have SITEID and it's redundant here, so take it away
    function applicable_formats() {
        return array('all' => true, 'my' => false);
    }

}

?>