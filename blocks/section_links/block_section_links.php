<?PHP //$Id$

class CourseBlock_section_links extends MoodleBlock {

    function CourseBlock_section_links ($course) {
        if ($course->format == 'topics') {
            $this->title = get_string('topics', 'block_section_links');
        }
        else if ($course->format == 'weeks') {
            $this->title = get_string('weeks', 'block_section_links');
        }
        else {
            $this->title = get_string('blockname', 'block_section_links');
        }
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->course = $course;
        $this->version = 2004050500;
    }
    
    function applicable_formats() {
        return (COURSE_FORMAT_WEEKS | COURSE_FORMAT_TOPICS);
    }

    function get_content() {
        global $CFG, $USER;

        $highlight = 0;

        if($this->content !== NULL) {
            return $this->content;
        }

        if ($this->course->format == 'weeks') {
            $highlight = ceil((time()-$this->course->startdate)/604800);
            $linktext = get_string('jumptocurrentweek', 'block_section_links');
        }
        else if ($this->course->format == 'topics') {
            $highlight = $this->course->marker;
            $linktext = get_string('jumptocurrenttopic', 'block_section_links');
        }
        $inc = 1;
        if ($this->course->numsections > 22) {
            $inc = 2;
        }
        if ($this->course->numsections > 40) {
            $inc = 5;
        }
        $courseid = $this->course->id;
        if ($display = get_field('course_display', 'display', 'course', $courseid, 'userid', $USER->id)) {
            $link = "$CFG->wwwroot/course/view.php?id=$courseid&amp;topic=";
        } else {
            $link = '#';
        }
        $text = '<font size=-1>';
        for ($i = $inc; $i <= $this->course->numsections; $i += $inc) {
            if ($i == $highlight) {
                $text .= "<a href=\"$link$i\"><b>$i</b></a> ";
            } else {
                $text .= "<a href=\"$link$i\">$i</a> ";
            }
        }
        if ($highlight) {
            $text .= "<br><a href=\"$link$highlight\">$linktext</a>";
        }
        
        $this->content = New object;
        $this->content->header = 'Hello';
        $this->content->footer = '';
        $this->content->text = $text;
        return $this->content;
    }
}
?>
