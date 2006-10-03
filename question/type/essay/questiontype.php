<?php  // $Id$

//////////////////
///   ESSAY   ///
/////////////////

/// QUESTION TYPE CLASS //////////////////
class question_essay_qtype extends default_questiontype {

    function name() {
        return 'essay';
    }

    function save_question_options($question) {
        if ($answer = get_record("question_answers", "question", $question->id)) {
            // Existing answer, so reuse it
            $answer->answer   = $question->feedback;
            $answer->feedback = $question->feedback;
            $answer->fraction = $question->fraction;
            if (!update_record("question_answers", $answer)) {
                $result->error = "Could not update quiz answer!";
                return $result;
            }
        } else {
            unset($answer);
            $answer->question = $question->id;
            $answer->answer   = $question->feedback;
            $answer->feedback = $question->feedback;
            $answer->fraction = $question->fraction;
            if (!$answer->id = insert_record("question_answers", $answer)) {
                $result->error = "Could not insert quiz answer!";
                return $result;
            }
        }
        return true;
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        global $CFG;
        static $htmleditorused = false;

        $answers       = &$question->options->answers;
        $readonly      = empty($options->readonly) ? '' : 'disabled="disabled"';
        
        // Only use the rich text editor for the first essay question on a page.
        $usehtmleditor = can_use_html_editor() && !$htmleditorused;
        
        $formatoptions          = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para    = false;
        
        $inputname = $question->name_prefix;
        $stranswer = get_string("answer", "quiz").': ';
        
        /// set question text and media
        $questiontext = format_text($question->questiontext,
                                   $question->questiontextformat,
                                   $formatoptions, $cmoptions->course);
                         
        $image = get_question_image($question, $cmoptions->course);

        // feedback handling
        $feedback = '';
        if ($options->feedback) {
            foreach ($answers as $answer) {
                $feedback = format_text($answer->feedback, '', $formatoptions, $cmoptions->course);
            }
        }
        
        // get response value
        if (isset($state->responses[''])) {
            $value = stripslashes_safe($state->responses['']);            
        } else {
            $value = "";
        }

        // answer
        if (empty($options->readonly)) {    
            // the student needs to type in their answer so print out a text editor
            $answer = print_textarea($usehtmleditor, 18, 80, 630, 400, $inputname, $value, $cmoptions->course, true);
        } else {
            // it is read only, so just format the students answer and output it
            $safeformatoptions = new stdClass;
            $safeformatoptions->para = false;
            $answer = format_text($value, FORMAT_MOODLE,
                                  $safeformatoptions, $cmoptions->course);
        }
        
        include("$CFG->dirroot/question/type/essay/display.html");

        if ($usehtmleditor) {
            use_html_editor($inputname);
            $htmleditorused = true;
        }
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        // All grading takes place in Manual Grading

        clean_param($state->responses[''], PARAM_CLEANHTML);
        
        $state->raw_grade = 0;
        $state->penalty = 0;

        return true;
    }
}    
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QTYPES['essay'] = new question_essay_qtype();
// The following adds the questiontype to the menu of types shown to teachers
$QTYPE_MENU['essay'] = get_string("essay", "quiz");
// Add essay to the list of manually graded questions
$QTYPE_MANUAL = isset($QTYPE_MANUAL) ? $QTYPE_MANUAL.",'essay'" : "'essay'";

?>
