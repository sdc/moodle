<?php  // $Id$

///////////////////
/// DESCRIPTION ///
///////////////////

/// QUESTION TYPE CLASS //////////////////

//
// The question type 'description' is not really a question type
// and it therefore often sticks to some kind of odd behaviour
//

class description_qtype extends default_questiontype {

    function name() {
        return 'description';
    }
    
    function is_usable_by_random() {
        return false;
    }

    function get_question_options(&$question) {
        // No options to be restored for this question type
        return true;
    }

    function save_question_options($question) {
        /// No options to be saved for this question type:
        return true;
    }

    function print_question(&$question, &$state, $number, $cmoptions, $options) {
        global $CFG;
        $isfinished = question_state_is_graded($state->last_graded) || $state->event == QUESTION_EVENTCLOSE;

        // For editing teachers print a link to an editing popup window
        $editlink = '';
        if (has_capability('moodle/question:manage', get_context_instance(CONTEXT_COURSE, $cmoptions->course))) {
            $stredit = get_string('edit');
            $linktext = '<img src="'.$CFG->pixpath.'/t/edit.gif" border="0" alt="'.$stredit.'" />';
            $editlink = link_to_popup_window('/question/question.php?id='.$question->id, $stredit, $linktext, 450, 550, $stredit, '', true);
        }

        $questiontext = $this->format_text($question->questiontext, $question->questiontextformat, $cmoptions);
        $image = get_question_image($question, $cmoptions->course);

        $generalfeedback = '';
        if ($isfinished && $options->generalfeedback) {
            $generalfeedback = $this->format_text($question->generalfeedback,
                    $question->questiontextformat, $cmoptions);
        }

        include "$CFG->dirroot/question/type/description/question.html";
    }

    function actual_number_of_questions($question) {
        /// Used for the feature number-of-questions-per-page
        /// to determine the actual number of questions wrapped
        /// by this question.
        /// The question type description is not even a question
        /// in itself so it will return ZERO!
        return 0;
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        $state->raw_grade = 0;
        $state->penalty = 0;
    }

}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new description_qtype());
?>
