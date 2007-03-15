<?php
/**
 * Defines the editing form for the calculated question type.
 *
 * @copyright &copy; 2007 Jamie Pratt
 * @author Jamie Pratt me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

/**
 * calculated editing form definition.
 */
class question_edit_calculated_form extends question_edit_form {
    /**
     * Handle to the question type for this question.
     *
     * @var question_calculated_qtype
     */
    var $qtypeobj;
    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    function definition_inner(&$mform) {
        global $QTYPES;
        $this->qtypeobj =& $QTYPES[$this->qtype()];

//------------------------------------------------------------------------------------------
/*      //not working now datasetdependent code cannot handle multiple answer formulas and not needed ??
        $repeated = array();
        $repeated[] =& $mform->createElement('header', 'answerhdr', get_string('answerhdr', 'qtype_calculated', '{no}'));

        $repeated[] =& $mform->createElement('text', 'answers', get_string('correctanswerformula', 'quiz'));
        $repeatedoptions['answers']['type'] = PARAM_NOTAGS;

        $creategrades = get_grade_options();
        $gradeoptions = $creategrades->gradeoptions;
        $repeated[] =& $mform->createElement('select', 'fraction', get_string('grade'), $gradeoptions);
        $repeatedoptions['fraction']['default'] = 0;

        $repeated[] =& $mform->createElement('text', 'tolerance', get_string('tolerance', 'qtype_calculated'));
        $repeatedoptions['tolerance']['type'] = PARAM_NUMBER;
        $repeatedoptions['tolerance']['default'] = 0.01;
        $repeated[] =& $mform->createElement('select', 'tolerancetype', get_string('tolerancetype', 'quiz'), $this->qtypeobj->tolerance_types());

        $repeated[] =&  $mform->createElement('select', 'correctanswerlength', get_string('correctanswershows', 'qtype_calculated'), range(0, 9));
        $repeatedoptions['correctanswerlength']['default'] = 2;

        $answerlengthformats = array('1' => get_string('decimalformat', 'quiz'), '2' => get_string('significantfiguresformat', 'quiz'));
        $repeated[] =&  $mform->createElement('select', 'correctanswerformat', get_string('correctanswershowsformat', 'qtype_calculated'), $answerlengthformats);

        $repeated[] =&  $mform->createElement('htmleditor', 'feedback', get_string('feedback', 'quiz'));
        $repeatedoptions['feedback']['type'] = PARAM_RAW;

        if (isset($this->question->options)){
            $count = count($this->question->options->answers);
        } else {
            $count = 0;
        }
        $repeatsatstart = $count + 1;
        $this->repeat_elements($repeated, $repeatsatstart, $repeatedoptions, 'noanswers', 'addanswers', 1, get_string('addmoreanswerblanks', 'qtype_calculated'));*/
//------------------------------------------------------------------------------------------
        $label = get_string("sharedwildcards", "qtype_datasetdependent");
        $html2 = $this->qtypeobj->print_dataset_definitions_category($this->question);
        $mform->insertElementBefore($mform->createElement('static','list',$label,$html2),'questiontext');

        $mform->addElement('header', 'answerhdr', get_string('answerhdr', 'qtype_calculated'));

        $mform->addElement('text', 'answers[0]', get_string('correctanswerformula', 'quiz'));
        $mform->setType('answers[0]', PARAM_NOTAGS);

/*        $creategrades = get_grade_options();
        $gradeoptions = $creategrades->gradeoptions;
        $mform->addElement('select', 'fraction[0]', get_string('grade'), $gradeoptions);
        $mform->setDefault('fraction[0]', 0);*/
        $mform->addElement('hidden', 'fraction[0]', 1);
      //  $mform->setConstants(array('fraction[0]'=>PARAM_INT));

        $tolgrp = array();
        $tolgrp[] =& $mform->createElement('text', 'tolerance[0]', get_string('tolerance', 'qtype_calculated'));
        $mform->setType('tolerance[0]', PARAM_NUMBER);
        $mform->setDefault('tolerance[0]', 0.01);
        $tolgrp[] =& $mform->createElement('select', 'tolerancetype[0]', get_string('tolerancetype', 'quiz'), $this->qtypeobj->tolerance_types());
        $mform->addGroup($tolgrp, 'tolgrp', get_string('tolerance', 'qtype_calculated'), null, false);

        $anslengrp = array();
        $anslengrp[] =&  $mform->createElement('select', 'correctanswerlength[0]', get_string('correctanswershows', 'qtype_calculated'), range(0, 9));
        $mform->setDefault('correctanswerlength[0]', 2);

        $answerlengthformats = array('1' => get_string('decimalformat', 'quiz'), '2' => get_string('significantfiguresformat', 'quiz'));
        $anslengrp[] =&  $mform->createElement('select', 'correctanswerformat[0]', get_string('correctanswershowsformat', 'qtype_calculated'), $answerlengthformats);
        $mform->addGroup($anslengrp, 'anslengrp', get_string('correctanswershows', 'qtype_calculated'), null, false);

        $mform->addElement('htmleditor', 'feedback[0]', get_string('feedback', 'quiz'));
        $mform->setType('feedback', PARAM_RAW);

//------------------------------------------------------------------------------------------
        $repeated = array();
        $repeated[] =& $mform->createElement('header', 'unithdr', get_string('unithdr', 'qtype_numerical', '{no}'));

        $repeated[] =& $mform->createElement('text', 'unit', get_string('unit', 'quiz'));
        $mform->setType('unit', PARAM_NOTAGS);

        $repeated[] =& $mform->createElement('text', 'multiplier', get_string('multiplier', 'quiz'));
        $mform->setType('multiplier', PARAM_NUMBER);

        if (isset($this->question->options)){
            $countunits = count($this->question->options->units);
        } else {
            $countunits = 0;
        }
        $repeatsatstart = $countunits + 1;
        $this->repeat_elements($repeated, $repeatsatstart, array(), 'nounits', 'addunits', 2, get_string('addmoreunitblanks', 'qtype_calculated', '{no}'));

        $firstunit =& $mform->getElement('multiplier[0]');
        $firstunit->freeze();
        $firstunit->setValue('1.0');
        $firstunit->setPersistantFreeze(true);

        //hidden elements
        $mform->addElement('hidden', 'wizard', 'datasetdefinitions');
        $mform->setType('wizard', PARAM_ALPHA);


    }

    function set_data($question) {
        if (isset($question->options)){
            $answers = $question->options->answers;
            if (count($answers)) {
                $key = 0;
                foreach ($answers as $answer){
                    $default_values['answers['.$key.']'] = $answer->answer;
                    $default_values['fraction['.$key.']'] = $answer->fraction;
                    $default_values['tolerance['.$key.']'] = $answer->tolerance;
                    $default_values['correctanswerlength['.$key.']'] = $answer->correctanswerlength;
                    $default_values['correctanswerformat['.$key.']'] = $answer->correctanswerformat;
                    $default_values['feedback['.$key.']'] = $answer->feedback;
                    $key++;
                }
            }
            $units  = array_values($question->options->units);
            // make sure the default unit is at index 0
            usort($units, create_function('$a, $b', // make sure the default unit is at index 0
            'if (1.0 === (float)$a->multiplier) { return -1; } else '.
            'if (1.0 === (float)$b->multiplier) { return 1; } else { return 0; }'));
            if (count($units)) {
                $key = 0;
                foreach ($units as $unit){
                    $default_values['unit['.$key.']'] = $unit->unit;
                    $default_values['multiplier['.$key.']'] = $unit->multiplier;
                    $key++;
                }
            }
        }
        $default_values['submitbutton'] = get_string('nextpage', 'qtype_calculated');
        $default_values['makecopy'] = get_string('makecopynextpage', 'qtype_calculated');
        $question = (object)((array)$question + $default_values);


        parent::set_data($question);
    }

    function qtype() {
        return 'calculated';
    }

    function validation($data){
        $errors = array();
        $answers = $data['answers'];
        $answercount = 0;
        //check grades
        /*$totalfraction = 0;
        $maxfraction = -1; */
        $possibledatasets = $this->qtypeobj->find_dataset_names($data['questiontext']);
        $mandatorydatasets = array();
        foreach ($answers as $key => $answer){
            $mandatorydatasets += $this->qtypeobj->find_dataset_names($data['questiontext']);
        }      
        if (count($possibledatasets) == 0 && count($mandatorydatasets )==0){
            $errors['questiontext']=get_string('atleastonewildcard', 'qtype_datasetdependent');
            foreach ($answers as $key => $answer){
                $errors['answers['.$key.']'] = get_string('atleastonewildcard', 'qtype_datasetdependent');
            }      
        }  
        foreach ($answers as $key => $answer){
            //check no of choices
            $trimmedanswer = trim($answer);
            if (($trimmedanswer!='')||$answercount==0){
                $eqerror = qtype_calculated_find_formula_errors($trimmedanswer);
                if (FALSE !== $eqerror){
                    $errors['answers['.$key.']'] = $eqerror;
                }
            }
            if ($trimmedanswer!=''){
                if ('2' == $data['correctanswerformat'][$key]
                        && '0' == $data['correctanswerlength'][$key]) {
                    $errors['correctanswerlength['.$key.']'] = get_string('zerosignificantfiguresnotallowed','quiz');
                }
                if (!is_numeric($data['tolerance'][$key])){
                    $errors['tolerance['.$key.']'] = get_string('mustbenumeric', 'qtype_calculated');
                }

                $answercount++;
            }
            //check grades

            //TODO how should grade checking work here??
            /*if ($answer != '') {
                if ($data['fraction'][$key] > 0) {
                    $totalfraction += $data['fraction'][$key];
                }
                if ($data['fraction'][$key] > $maxfraction) {
                    $maxfraction = $data['fraction'][$key];
                }
            }*/
        }
        //grade checking :
        /// Perform sanity checks on fractional grades
        /*if ( ) {
            if ($maxfraction != 1) {
                $maxfraction = $maxfraction * 100;
                $errors['fraction[0]'] = get_string('errfractionsnomax', 'qtype_multichoice', $maxfraction);
            }
        } else {
            $totalfraction = round($totalfraction,2);
            if ($totalfraction != 1) {
                $totalfraction = $totalfraction * 100;
                $errors['fraction[0]'] = get_string('errfractionsaddwrong', 'qtype_multichoice', $totalfraction);
            }
        }*/
        $units  = $data['unit'];
        if (count($units)) {
            foreach ($units as $key => $unit){
                if (is_numeric($unit)){
                    $errors['unit['.$key.']'] = get_string('mustnotbenumeric', 'qtype_calculated');
                }
                $trimmedunit = trim($unit);
                $trimmedmultiplier = trim($data['multiplier'][$key]);
                if (!empty($trimmedunit)){
                    if (empty($trimmedmultiplier)){
                        $errors['multiplier['.$key.']'] = get_string('youmustenteramultiplierhere', 'qtype_calculated');
                    }
                    if (!is_numeric($trimmedmultiplier)){
                        $errors['multiplier['.$key.']'] = get_string('mustbenumeric', 'qtype_calculated');
                    }

                }
            }
        }
        if ($answercount==0){
            $errors['answers[0]'] = get_string('atleastoneanswer', 'qtype_calculated');
        }

        return $errors;
    }
}
?>