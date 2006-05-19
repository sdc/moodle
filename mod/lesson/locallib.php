<?php
/// mnielsen
/// locallib.php is the new lib file for lesson module.
/// including locallib.php is the same as including the old lib.php

/**
* Next page -> any page not seen before
*/    
if (!defined("LESSON_UNSEENPAGE")) {
    define("LESSON_UNSEENPAGE", 1); // Next page -> any page not seen before
}
/**
* Next page -> any page not answered correctly
*/
if (!defined("LESSON_UNANSWEREDPAGE")) {
    define("LESSON_UNANSWEREDPAGE", 2); // Next page -> any page not answered correctly
}

/**
* Define different lesson flows for next page
*/
$LESSON_NEXTPAGE_ACTION = array (0 => get_string("normal", "lesson"),
                          LESSON_UNSEENPAGE => get_string("showanunseenpage", "lesson"),
                          LESSON_UNANSWEREDPAGE => get_string("showanunansweredpage", "lesson") );

// Lesson jump types defined
//  TODO: instead of using define statements, create an array with all the jump values

/**
 * Jump to Next Page
 */
if (!defined("LESSON_NEXTPAGE")) {
    define("LESSON_NEXTPAGE", -1);
}
/**
 * End of Lesson
 */
if (!defined("LESSON_EOL")) {
    define("LESSON_EOL", -9);
}
/**
 * Jump to an unseen page within a branch and end of branch or end of lesson
 */
if (!defined("LESSON_UNSEENBRANCHPAGE")) {
    define("LESSON_UNSEENBRANCHPAGE", -50);
}
/**
 * Jump to Previous Page
 */
if (!defined("LESSON_PREVIOUSPAGE")) {
    define("LESSON_PREVIOUSPAGE", -40);
}
/**
 * Jump to a random page within a branch and end of branch or end of lesson
 */
if (!defined("LESSON_RANDOMPAGE")) {
    define("LESSON_RANDOMPAGE", -60);
}
/**
 * Jump to a random Branch
 */
if (!defined("LESSON_RANDOMBRANCH")) {
    define("LESSON_RANDOMBRANCH", -70);
}
/**
 * Cluster Jump
 */
if (!defined("LESSON_CLUSTERJUMP")) {
    define("LESSON_CLUSTERJUMP", -80);
}
/**
 * Undefined
 */    
if (!defined("LESSON_UNDEFINED")) {
    define("LESSON_UNDEFINED", -99);
}

// Lesson question types defined

/**
 * Short answer question type
 */
if (!defined("LESSON_SHORTANSWER")) {
    define("LESSON_SHORTANSWER",   "1");
}        
/**
 * True/False question type
 */
if (!defined("LESSON_TRUEFALSE")) {
    define("LESSON_TRUEFALSE",     "2");
}
/**
 * Multichoice question type
 *
 * If you change the value of this then you need 
 * to change it in restorelib.php as well.
 */
if (!defined("LESSON_MULTICHOICE")) {
    define("LESSON_MULTICHOICE",   "3");
}
/**
 * Random question type - not used
 */
if (!defined("LESSON_RANDOM")) {
    define("LESSON_RANDOM",        "4");
}
/**
 * Matching question type
 *
 * If you change the value of this then you need
 * to change it in restorelib.php, in mysql.php 
 * and postgres7.php as well.
 */
if (!defined("LESSON_MATCHING")) {
    define("LESSON_MATCHING",      "5");
}
/**
 * Not sure - not used
 */
if (!defined("LESSON_RANDOMSAMATCH")) {
    define("LESSON_RANDOMSAMATCH", "6");
}
/**
 * Not sure - not used
 */
if (!defined("LESSON_DESCRIPTION")) {
    define("LESSON_DESCRIPTION",   "7");
}
/**
 * Numerical question type
 */
if (!defined("LESSON_NUMERICAL")) {
    define("LESSON_NUMERICAL",     "8");
}
/**
 * Multichoice with multianswer question type
 */
if (!defined("LESSON_MULTIANSWER")) {
    define("LESSON_MULTIANSWER",   "9");
}
/**
 * Essay question type
 */
if (!defined("LESSON_ESSAY")) {
    define("LESSON_ESSAY", "10");
}

/**
 * Lesson question type array.
 * Contains all question types used
 */
$LESSON_QUESTION_TYPE = array ( LESSON_MULTICHOICE => get_string("multichoice", "quiz"),
                              LESSON_TRUEFALSE     => get_string("truefalse", "quiz"),
                              LESSON_SHORTANSWER   => get_string("shortanswer", "quiz"),
                              LESSON_NUMERICAL     => get_string("numerical", "quiz"),
                              LESSON_MATCHING      => get_string("match", "quiz"),
                              LESSON_ESSAY           => get_string("essay", "lesson")
//                            LESSON_DESCRIPTION   => get_string("description", "quiz"),
//                            LESSON_RANDOM        => get_string("random", "quiz"),
//                            LESSON_RANDOMSAMATCH => get_string("randomsamatch", "quiz"),
//                            LESSON_MULTIANSWER   => get_string("multianswer", "quiz"),
                              );

// Non-question page types

/**
 * Branch Table page
 */
if (!defined("LESSON_BRANCHTABLE")) {
    define("LESSON_BRANCHTABLE",   "20");
}
/**
 * End of Branch page
 */
if (!defined("LESSON_ENDOFBRANCH")) {
    define("LESSON_ENDOFBRANCH",   "21");
}
/**
 * Start of Cluster page
 */
if (!defined("LESSON_CLUSTER")) {
    define("LESSON_CLUSTER",   "30");
}
/**
 * End of Cluster page
 */
if (!defined("LESSON_ENDOFCLUSTER")) {
    define("LESSON_ENDOFCLUSTER",   "31");
}

// other variables...

/**
 * Flag for the editor for the answer textarea.
 */
if (!defined("LESSON_ANSWER_EDITOR")) {
    define("LESSON_ANSWER_EDITOR",   "1");
}
/**
 * Flag for the editor for the response textarea.
 */
if (!defined("LESSON_RESPONSE_EDITOR")) {
    define("LESSON_RESPONSE_EDITOR",   "2");
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other lesson functions go here.  Each of them must have a name that 
/// starts with lesson_

/**
 * Given some question info and some data about the the answers
 * this function parses, organises and saves the question
 *
 * This is only used when IMPORTING questions and is only called
 * from format.php
 * Lifted from mod/quiz/lib.php - 
 *    1. all reference to oldanswers removed
 *    2. all reference to quiz_multichoice table removed
 *    3. In SHORTANSWER questions usecase is store in the qoption field
 *    4. In NUMERIC questions store the range as two answers
 *    5. TRUEFALSE options are ignored
 *    6. For MULTICHOICE questions with more than one answer the qoption field is true
 * 
 * @param opject $question Contains question data like question, type and answers.
 * @return object Returns $result->error or $result->notice.
 **/
function lesson_save_question_options($question) {
    
    $timenow = time();
    switch ($question->qtype) {
        case LESSON_SHORTANSWER:

            $answers = array();
            $maxfraction = -1;

            // Insert all the new answers
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    $answer = new stdClass;
                    $answer->lessonid   = $question->lessonid;
                    $answer->pageid   = $question->id;
                    if ($question->fraction[$key] >=0.5) {
                        $answer->jumpto = LESSON_NEXTPAGE;
                    }
                    $answer->timecreated   = $timenow;
                    $answer->grade = $question->fraction[$key] * 100;
                    $answer->answer   = $dataanswer;
                    $answer->feedback = $question->feedback[$key];
                    if (!$answer->id = insert_record("lesson_answers", $answer)) {
                        $result->error = "Could not insert shortanswer quiz answer!";
                        return $result;
                    }
                    $answers[] = $answer->id;
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }
                }
            }


            /// Perform sanity checks on fractional grades
            if ($maxfraction != 1) {
                $maxfraction = $maxfraction * 100;
                $result->notice = get_string("fractionsnomax", "quiz", $maxfraction);
                return $result;
            }
            break;

        case LESSON_NUMERICAL:   // Note similarities to SHORTANSWER

            $answers = array();
            $maxfraction = -1;

            
            // for each answer store the pair of min and max values even if they are the same 
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    $answer = new stdClass;
                    $answer->lessonid   = $question->lessonid;
                    $answer->pageid   = $question->id;
                    $answer->jumpto = LESSON_NEXTPAGE;
                    $answer->timecreated   = $timenow;
                    $answer->grade = $question->fraction[$key] * 100;
                    $min = $question->answer[$key] - $question->tolerance[$key];
                    $max = $question->answer[$key] + $question->tolerance[$key];
                    $answer->answer   = $min.":".$max;
                    // $answer->answer   = $question->min[$key].":".$question->max[$key]; original line for min/max
                    $answer->response = $question->feedback[$key];
                    if (!$answer->id = insert_record("lesson_answers", $answer)) {
                        $result->error = "Could not insert numerical quiz answer!";
                        return $result;
                    }
                    
                    $answers[] = $answer->id;
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }
                }
            }

            /// Perform sanity checks on fractional grades
            if ($maxfraction != 1) {
                $maxfraction = $maxfraction * 100;
                $result->notice = get_string("fractionsnomax", "quiz", $maxfraction);
                return $result;
            }
        break;


        case LESSON_TRUEFALSE:

            // the truth
            $answer->lessonid   = $question->lessonid;
            $answer->pageid = $question->id;
            $answer->timecreated   = $timenow;
            $answer->answer = get_string("true", "quiz");
            $answer->grade = $question->answer * 100;
            if ($answer->grade > 50 ) {
                $answer->jumpto = LESSON_NEXTPAGE;
            }
            if (isset($question->feedbacktrue)) {
                $answer->response = $question->feedbacktrue;
            }
            if (!$true->id = insert_record("lesson_answers", $answer)) {
                $result->error = "Could not insert quiz answer \"true\")!";
                return $result;
            }

            // the lie    
            $answer = new stdClass;
            $answer->lessonid   = $question->lessonid;
            $answer->pageid = $question->id;
            $answer->timecreated   = $timenow;
            $answer->answer = get_string("false", "quiz");
            $answer->grade = (1 - (int)$question->answer) * 100;
            if ($answer->grade > 50 ) {
                $answer->jumpto = LESSON_NEXTPAGE;
            }
            if (isset($question->feedbackfalse)) {
                $answer->response = $question->feedbackfalse;
            }
            if (!$false->id = insert_record("lesson_answers", $answer)) {
                $result->error = "Could not insert quiz answer \"false\")!";
                return $result;
            }

          break;


        case LESSON_MULTICHOICE:

            $totalfraction = 0;
            $maxfraction = -1;

            $answers = array();

            // Insert all the new answers
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    $answer = new stdClass;
                    $answer->lessonid   = $question->lessonid;
                    $answer->pageid   = $question->id;
                    $answer->timecreated   = $timenow;
                    $answer->grade = $question->fraction[$key] * 100;
                    // changed some defaults
                    /* Original Code
                    if ($answer->grade > 50 ) {
                        $answer->jumpto = LESSON_NEXTPAGE;
                    }
                    Replaced with:                    */
                    if ($answer->grade > 50 ) {
                        $answer->jumpto = LESSON_NEXTPAGE;
                        $answer->score = 1;
                    }
                    // end Replace
                    $answer->answer   = $dataanswer;
                    $answer->response = $question->feedback[$key];
                    if (!$answer->id = insert_record("lesson_answers", $answer)) {
                        $result->error = "Could not insert multichoice quiz answer! ";
                        return $result;
                    }
                    // for Sanity checks
                    if ($question->fraction[$key] > 0) {                 
                        $totalfraction += $question->fraction[$key];
                    }
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }
                }
            }

            /// Perform sanity checks on fractional grades
            if ($question->single) {
                if ($maxfraction != 1) {
                    $maxfraction = $maxfraction * 100;
                    $result->notice = get_string("fractionsnomax", "quiz", $maxfraction);
                    return $result;
                }
            } else {
                $totalfraction = round($totalfraction,2);
                if ($totalfraction != 1) {
                    $totalfraction = $totalfraction * 100;
                    $result->notice = get_string("fractionsaddwrong", "quiz", $totalfraction);
                    return $result;
                }
            }
        break;

        case LESSON_MATCHING:

            $subquestions = array();

            $i = 0;
            // Insert all the new question+answer pairs
            foreach ($question->subquestions as $key => $questiontext) {
                $answertext = $question->subanswers[$key];
                if (!empty($questiontext) and !empty($answertext)) {
                    $answer = new stdClass;
                    $answer->lessonid   = $question->lessonid;
                    $answer->pageid   = $question->id;
                    $answer->timecreated   = $timenow;
                    $answer->answer = $questiontext;
                    $answer->response   = $answertext; 
                    if ($i == 0) {
                        // first answer contains the correct answer jump
                        $answer->jumpto = LESSON_NEXTPAGE;
                    }
                    if (!$subquestion->id = insert_record("lesson_answers", $answer)) {
                        $result->error = "Could not insert quiz match subquestion!";
                        return $result;
                    }
                    $subquestions[] = $subquestion->id;
                    $i++;
                }
            }

            if (count($subquestions) < 3) {
                $result->notice = get_string("notenoughsubquestions", "quiz");
                return $result;
            }

            break;


        case LESSON_RANDOMSAMATCH:
            $options->question = $question->id;
            $options->choose = $question->choose;
            if ($existing = get_record("quiz_randomsamatch", "question", $options->question)) {
                $options->id = $existing->id;
                if (!update_record("quiz_randomsamatch", $options)) {
                    $result->error = "Could not update quiz randomsamatch options!";
                    return $result;
                }
            } else {
                if (!insert_record("quiz_randomsamatch", $options)) {
                    $result->error = "Could not insert quiz randomsamatch options!";
                    return $result;
                }
            }
        break;

        case LESSON_MULTIANSWER:
            if (!$oldmultianswers = get_records("quiz_multianswers", "question", $question->id, "id ASC")) {
                $oldmultianswers = array();
            }

            // Insert all the new multi answers
            foreach ($question->answers as $dataanswer) {
                if ($oldmultianswer = array_shift($oldmultianswers)) {  // Existing answer, so reuse it
                    $multianswer = $oldmultianswer;
                    $multianswer->positionkey = $dataanswer->positionkey;
                    $multianswer->norm = $dataanswer->norm;
                    $multianswer->answertype = $dataanswer->answertype;

                    if (! $multianswer->answers = quiz_save_multianswer_alternatives
                            ($question->id, $dataanswer->answertype,
                             $dataanswer->alternatives, $oldmultianswer->answers))
                    {
                        $result->error = "Could not update multianswer alternatives! (id=$multianswer->id)";
                        return $result;
                    }
                    if (!update_record("quiz_multianswers", $multianswer)) {
                        $result->error = "Could not update quiz multianswer! (id=$multianswer->id)";
                        return $result;
                    }
                } else {    // This is a completely new answer
                    $multianswer = new stdClass;
                    $multianswer->question = $question->id;
                    $multianswer->positionkey = $dataanswer->positionkey;
                    $multianswer->norm = $dataanswer->norm;
                    $multianswer->answertype = $dataanswer->answertype;

                    if (! $multianswer->answers = quiz_save_multianswer_alternatives
                            ($question->id, $dataanswer->answertype,
                             $dataanswer->alternatives))
                    {
                        $result->error = "Could not insert multianswer alternatives! (questionid=$question->id)";
                        return $result;
                    }
                    if (!insert_record("quiz_multianswers", $multianswer)) {
                        $result->error = "Could not insert quiz multianswer!";
                        return $result;
                    }
                }
            }
        break;

        case LESSON_RANDOM:
        break;

        case LESSON_DESCRIPTION:
        break;

        default:
            $result->error = "Unsupported question type ($question->qtype)!";
            return $result;
        break;
    }
    return true;
}

/**
 * Given an array of value, creates a popup menu to be part of a form.
 * 
 * @param array $options Used to create the popup menu values ( $options["value"]["label"] ).
 * @param string $name Name of the select form element.
 * @param string $selected Current value selected in the popup menu.
 * @param string $nothing If set, used as the first value in the popup menu.
 * @param string $script OnChange javascript code.
 * @param string|int $nothingvalue Value of the $nothing parameter.
 * @param boolean $return False: Print out the popup menu automatically  True: Return the popup menu.
 * @return string May return the popup menu as a string.
 * @todo replace the use of this function with choose_from_menu in lib/weblib.php
 **/
function lesson_choose_from_menu ($options, $name, $selected="", $nothing="choose", $script="", $nothingvalue="0", $return=false) {    
    if ($nothing == "choose") {
        $nothing = get_string("choose")."...";
    }

    if ($script) {
        $javascript = "onChange=\"$script\"";
    } else {
        $javascript = "";
    }

    $output = "<label for=$name class=hidden-label>$name</label><SELECT id=$name NAME=$name $javascript>\n";
    if ($nothing) {
        $output .= "   <OPTION VALUE=\"$nothingvalue\"\n";
        if ($nothingvalue == $selected) {
            $output .= " SELECTED";
        }
        $output .= ">$nothing</OPTION>\n";
    }
    if (!empty($options)) {
        foreach ($options as $value => $label) {
            $output .= "   <OPTION VALUE=\"$value\"";
            if ($value == $selected) {
                $output .= " SELECTED";
            }
            // stop zero label being replaced by array index value
            // if ($label) {
            //    $output .= ">$label</OPTION>\n";
            // } else {
            //     $output .= ">$value</OPTION>\n";
            //  }
            $output .= ">$label</OPTION>\n";
            
        }
    }
    $output .= "</SELECT>\n";

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}   

/**
 * Determins if a jumpto value is correct or not.
 *
 * returns true if jumpto page is (logically) after the pageid page or
 * if the jumpto value is a special value.  Returns false in all other cases.
 * 
 * @param int $pageid Id of the page from which you are jumping from.
 * @param int $jumpto The jumpto number.
 * @return boolean True or false after a series of tests.
 * @todo Can be optimized to only have to make 1 or 2 database calls instead of 1 foreach page in the lesson
 **/
function lesson_iscorrect($pageid, $jumpto) {
    
    // first test the special values
    if (!$jumpto) {
        // same page
        return false;
    } elseif ($jumpto == LESSON_NEXTPAGE) {
        return true;
    } elseif ($jumpto == LESSON_UNSEENBRANCHPAGE) {
        return true;
    } elseif ($jumpto == LESSON_RANDOMPAGE) {
        return true;
    } elseif ($jumpto == LESSON_CLUSTERJUMP) {
        return true;
    } elseif ($jumpto == LESSON_EOL) {
        return true;
    }
    // we have to run through the pages from pageid looking for jumpid
    $apageid = get_field("lesson_pages", "nextpageid", "id", $pageid);
    while (true) {
        if ($jumpto == $apageid) {
            return true;
        }
        if ($apageid) {
            $apageid = get_field("lesson_pages", "nextpageid", "id", $apageid);
        } else {
            return false;
        }
    }
    return false; // should never be reached
}

/**
 * Checks to see if a page is a branch table or is
 * a page that is enclosed by a branch table and an end of branch or end of lesson.
 * May call this function: {@link lesson_is_page_in_branch()}
 *
 * @param int $lesson_id Id of the lesson to which the page belongs.
 * @param int $pageid Id of the page.
 * @return boolean True or false.
 * @todo Change $lesson_id param to $lessonid.
 **/
function lesson_display_branch_jumps($lesson_id, $pageid) {
    if($pageid == 0) {
        // first page
        return false;
    }
    // get all of the lesson pages
    if (!$lessonpages = get_records_select("lesson_pages", "lessonid = $lesson_id")) {
        // adding first page
        return false;
    }

    if ($lessonpages[$pageid]->qtype == LESSON_BRANCHTABLE) {
        return true;
    }
    
    return lesson_is_page_in_branch($lessonpages, $pageid);
}

/**
 * Checks to see if a page is a cluster page or is
 * a page that is enclosed by a cluster page and an end of cluster or end of lesson 
 * May call this function: {@link lesson_is_page_in_cluster()}
 * 
 * @param int $lesson_id Id of the lesson to which the page belongs.
 * @param int $pageid Id of the page.
 * @return boolean True or false.
 * @todo Change $lesson_id param to $lessonid.
 **/
function lesson_display_cluster_jump($lesson_id, $pageid) {
    if($pageid == 0) {
        // first page
        return false;
    }
    // get all of the lesson pages
    if (!$lessonpages = get_records_select("lesson_pages", "lessonid = $lesson_id")) {
        // adding first page
        return false;
    }

    if ($lessonpages[$pageid]->qtype == LESSON_CLUSTER) {
        return true;
    }
    
    return lesson_is_page_in_cluster($lessonpages, $pageid);

}

/**
 * Checks to see if a LESSON_CLUSTERJUMP or 
 * a LESSON_UNSEENBRANCHPAGE is used in a lesson.
 *
 * This function is only executed when a teacher is 
 * checking the navigation for a lesson.
 *
 * @param int $lesson Id of the lesson that is to be checked.
 * @return boolean True or false.
 **/
function lesson_display_teacher_warning($lesson) {
    // get all of the lesson answers
    if (!$lessonanswers = get_records_select("lesson_answers", "lessonid = $lesson")) {
        // no answers, then not useing cluster or unseen
        return false;
    }
    // just check for the first one that fulfills the requirements
    foreach ($lessonanswers as $lessonanswer) {
        if ($lessonanswer->jumpto == LESSON_CLUSTERJUMP || $lessonanswer->jumpto == LESSON_UNSEENBRANCHPAGE) {
            return true;
        }
    }
    
    // if no answers use either of the two jumps
    return false;
}


/**
 * Interprets LESSON_CLUSTERJUMP jumpto value.
 *
 * This will select a page randomly
 * and the page selected will be inbetween a cluster page and end of cluter or end of lesson
 * and the page selected will be a page that has not been viewed already
 * and if any pages are within a branch table or end of branch then only 1 page within 
 * the branch table or end of branch will be randomly selected (sub clustering).
 * 
 * @param int $lesson Id of the lesson.
 * @param int $user Id of the user.
 * @param int $pageid Id of the current page from which we are jumping from.
 * @return int The id of the next page.
 * @todo Change $lesson param to $lessonid and $user param to $userid
 **/
function lesson_cluster_jump($lesson, $user, $pageid) {
    // get the number of retakes
    if (!$retakes = count_records("lesson_grades", "lessonid", $lesson, "userid", $user)) {
        $retakes = 0;
    }

    // get all the lesson_attempts aka what the user has seen
    if ($seen = get_records_select("lesson_attempts", "lessonid = $lesson AND userid = $user AND retry = $retakes", "timeseen DESC")) {
        foreach ($seen as $value) { // load it into an array that I can more easily use
            $seenpages[$value->pageid] = $value->pageid;
        }
    } else {
        $seenpages = array();
    }

    // get the lesson pages
    if (!$lessonpages = get_records_select("lesson_pages", "lessonid = $lesson")) {
        error("Error: could not find records in lesson_pages table");
    }
    // find the start of the cluster
    while ($pageid != 0) { // this condition should not be satisfied... should be a cluster page
        if ($lessonpages[$pageid]->qtype == LESSON_CLUSTER) {
            break;
        }
        $pageid = $lessonpages[$pageid]->prevpageid;
    }

    $pageid = $lessonpages[$pageid]->nextpageid; // move down from the cluster page
    
    $clusterpages = array();
    while (true) {  // now load all the pages into the cluster that are not already inside of a branch table.
        if ($lessonpages[$pageid]->qtype == LESSON_ENDOFCLUSTER) {
            // store the endofcluster page's jump
            $exitjump = get_field("lesson_answers", "jumpto", "pageid", $pageid, "lessonid", $lesson);
            if ($exitjump == LESSON_NEXTPAGE) {
                $exitjump = $lessonpages[$pageid]->nextpageid;
            }
            if ($exitjump == 0) {
                $exitjump = LESSON_EOL;
            }
            break;
        } elseif (!lesson_is_page_in_branch($lessonpages, $pageid) && $lessonpages[$pageid]->qtype != LESSON_ENDOFBRANCH) {
            // load page into array when it is not in a branch table and when it is not an endofbranch
            $clusterpages[] = $lessonpages[$pageid];
        }
        if ($lessonpages[$pageid]->nextpageid == 0) {
            // shouldn't ever get here... should be using endofcluster
            $exitjump = LESSON_EOL;
            break;
        } else {
            $pageid = $lessonpages[$pageid]->nextpageid;
        }
    }

    // filter out the ones we have seen
    $unseen = array();
    foreach ($clusterpages as $clusterpage) {
        if ($clusterpage->qtype == LESSON_BRANCHTABLE) {            // if branchtable, check to see if any pages inside have been viewed
            $branchpages = lesson_pages_in_branch($lessonpages, $clusterpage->id); // get the pages in the branchtable
            $flag = true;
            foreach ($branchpages as $branchpage) {
                if (array_key_exists($branchpage->id, $seenpages)) {  // check if any of the pages have been viewed
                    $flag = false;
                }
            }
            if ($flag && count($branchpages) > 0) {
                // add branch table
                $unseen[] = $clusterpage;
            }        
        } else {
            // add any other type of page that has not already been viewed
            if (!array_key_exists($clusterpage->id, $seenpages)) {
                $unseen[] = $clusterpage;
            }
        }
    }

    if (count($unseen) > 0) { // it does not contain elements, then use exitjump, otherwise find out next page/branch
        $nextpage = $unseen[rand(0, count($unseen)-1)];
    } else {
        return $exitjump; // seen all there is to see, leave the cluster
    }
    
    if ($nextpage->qtype == LESSON_BRANCHTABLE) { // if branch table, then pick a random page inside of it
        $branchpages = lesson_pages_in_branch($lessonpages, $nextpage->id);
        return $branchpages[rand(0, count($branchpages)-1)]->id;
    } else { // otherwise, return the page's id
        return $nextpage->id;
    }
}

/**
 * Returns pages that are within a branch table and another branch table, end of branch or end of lesson
 * 
 * @param array $lessonpages An array of lesson page objects.
 * @param int $branchid The id of the branch table that we would like the containing pages for.
 * @return array An array of lesson page objects.
 **/
function lesson_pages_in_branch($lessonpages, $branchid) {
    $pageid = $lessonpages[$branchid]->nextpageid;  // move to the first page after the branch table
    $pagesinbranch = array();
    
    while (true) { 
        if ($pageid == 0) { // EOL
            break;
        } elseif ($lessonpages[$pageid]->qtype == LESSON_BRANCHTABLE) {
            break;
        } elseif ($lessonpages[$pageid]->qtype == LESSON_ENDOFBRANCH) {
            break;
        }
        $pagesinbranch[] = $lessonpages[$pageid];
        $pageid = $lessonpages[$pageid]->nextpageid;
    }
    
    return $pagesinbranch;
}

/**
 * Interprets the LESSON_UNSEENBRANCHPAGE jump.
 * 
 * will return the pageid of a random unseen page that is within a branch
 *
 * @see lesson_pages_in_branch()
 * @param int $lesson Id of the lesson.
 * @param int $user Id of the user.
 * @param int $pageid Id of the page from which we are jumping.
 * @return int Id of the next page.
 * @todo Change params $lesson to $lessonid and $user to $userid.
 **/
function lesson_unseen_question_jump($lesson, $user, $pageid) {
    // get the number of retakes
    if (!$retakes = count_records("lesson_grades", "lessonid", $lesson, "userid", $user)) {
        $retakes = 0;
    }

    // get all the lesson_attempts aka what the user has seen
    if ($viewedpages = get_records_select("lesson_attempts", "lessonid = $lesson AND userid = $user AND retry = $retakes", "timeseen DESC")) {
        foreach($viewedpages as $viewed) {
            $seenpages[] = $viewed->pageid;
        }
    } else {
        $seenpages = array();
    }

    // get the lesson pages
    if (!$lessonpages = get_records_select("lesson_pages", "lessonid = $lesson")) {
        error("Error: could not find records in lesson_pages table");
    }
    
    if ($pageid == LESSON_UNSEENBRANCHPAGE) {  // this only happens when a student leaves in the middle of an unseen question within a branch series
        $pageid = $seenpages[0];  // just change the pageid to the last page viewed inside the branch table
    }

    // go up the pages till branch table
    while ($pageid != 0) { // this condition should never be satisfied... only happens if there are no branch tables above this page
        if ($lessonpages[$pageid]->qtype == LESSON_BRANCHTABLE) {
            break;
        }
        $pageid = $lessonpages[$pageid]->prevpageid;
    }
    
    $pagesinbranch = lesson_pages_in_branch($lessonpages, $pageid);
    
    // this foreach loop stores all the pages that are within the branch table but are not in the $seenpages array
    $unseen = array();
    foreach($pagesinbranch as $page) {    
        if (!in_array($page->id, $seenpages)) {
            $unseen[] = $page->id;
        }
    }

    if(count($unseen) == 0) {
        if(isset($pagesinbranch)) {
            $temp = end($pagesinbranch);
            $nextpage = $temp->nextpageid; // they have seen all the pages in the branch, so go to EOB/next branch table/EOL
        } else {
            // there are no pages inside the branch, so return the next page
            $nextpage = $lessonpages[$pageid]->nextpageid;
        }
        if ($nextpage == 0) {
            return LESSON_EOL;
        } else {
            return $nextpage;
        }
    } else {
        return $unseen[rand(0, count($unseen)-1)];  // returns a random page id for the next page
    }
}

/**
 * Handles the unseen branch table jump.
 *
 * @param int $lesson Lesson id.
 * @param int $user User id.
 * @return int Will return the page id of a branch table or end of lesson
 * @todo Change $lesson param to $lessonid and change $user param to $userid.
 **/
function lesson_unseen_branch_jump($lesson, $user) {
    if (!$retakes = count_records("lesson_grades", "lessonid", $lesson, "userid", $user)) {
        $retakes = 0;
    }

    if (!$seenbranches = get_records_select("lesson_branch", "lessonid = $lesson AND userid = $user AND retry = $retakes",
                "timeseen DESC")) {
        error("Error: could not find records in lesson_branch table");
    }

    // get the lesson pages
    if (!$lessonpages = get_records_select("lesson_pages", "lessonid = $lesson")) {
        error("Error: could not find records in lesson_pages table");
    }
    
    // this loads all the viewed branch tables into $seen untill it finds the branch table with the flag
    // which is the branch table that starts the unseenbranch function
    $seen = array();    
    foreach ($seenbranches as $seenbranch) {
        if (!$seenbranch->flag) {
            $seen[$seenbranch->pageid] = $seenbranch->pageid;
        } else {
            $start = $seenbranch->pageid;
            break;
        }
    }
    // this function searches through the lesson pages to find all the branch tables
    // that follow the flagged branch table
    $pageid = $lessonpages[$start]->nextpageid; // move down from the flagged branch table
    while ($pageid != 0) {  // grab all of the branch table till eol
        if ($lessonpages[$pageid]->qtype == LESSON_BRANCHTABLE) {
            $branchtables[] = $lessonpages[$pageid]->id;
        }
        $pageid = $lessonpages[$pageid]->nextpageid;
    }
    $unseen = array();
    foreach ($branchtables as $branchtable) {
        // load all of the unseen branch tables into unseen
        if (!array_key_exists($branchtable, $seen)) {
            $unseen[] = $branchtable;
        }
    }
    if (count($unseen) > 0) {
        return $unseen[rand(0, count($unseen)-1)];  // returns a random page id for the next page
    } else {
        return LESSON_EOL;  // has viewed all of the branch tables
    }
}

/**
 * Handles the random jump between a branch table and end of branch or end of lesson (LESSON_RANDOMPAGE).
 * 
 * @param int $lesson Lesson id.
 * @param int $pageid The id of the page that we are jumping from (?)
 * @return int The pageid of a random page that is within a branch table
 * @todo Change $lesson param to $lessonid.
 **/
function lesson_random_question_jump($lesson, $pageid) {
    // get the lesson pages
    if (!$lessonpages = get_records_select("lesson_pages", "lessonid = $lesson")) {
        error("Error: could not find records in lesson_pages table");
    }

    // go up the pages till branch table
    while ($pageid != 0) { // this condition should never be satisfied... only happens if there are no branch tables above this page

        if ($lessonpages[$pageid]->qtype == LESSON_BRANCHTABLE) {
            break;
        }
        $pageid = $lessonpages[$pageid]->prevpageid;
    }

    // get the pages within the branch    
    $pagesinbranch = lesson_pages_in_branch($lessonpages, $pageid);
    
    if(count($pagesinbranch) == 0) {
        // there are no pages inside the branch, so return the next page
        return $lessonpages[$pageid]->nextpageid;
    } else {
        return $pagesinbranch[rand(0, count($pagesinbranch)-1)]->id;  // returns a random page id for the next page
    }
}

/**
 * Check to see if a page is below a branch table (logically).
 * 
 * Will return true if a branch table is found logically above the page.
 * Will return false if an end of branch, cluster or the beginning
 * of the lesson is found before a branch table.
 *
 * @param array $pages An array of lesson page objects.
 * @param int $pageid Id of the page for testing.
 * @return boolean
 */
function lesson_is_page_in_branch($pages, $pageid) {
    $pageid = $pages[$pageid]->prevpageid; // move up one

    // go up the pages till branch table    
    while (true) {
        if ($pageid == 0) {  // ran into the beginning of the lesson
            return false;
        } elseif ($pages[$pageid]->qtype == LESSON_ENDOFBRANCH) { // ran into the end of another branch table
            return false;
        } elseif ($pages[$pageid]->qtype == LESSON_CLUSTER) { // do not look beyond a cluster
            return false;
        } elseif ($pages[$pageid]->qtype == LESSON_BRANCHTABLE) { // hit a branch table
            return true;
        }
        $pageid = $pages[$pageid]->prevpageid;
    }

}

/**
 * Check to see if a page is below a cluster page (logically).
 * 
 * Will return true if a cluster is found logically above the page.
 * Will return false if an end of cluster or the beginning
 * of the lesson is found before a cluster page.
 *
 * @param array $pages An array of lesson page objects.
 * @param int $pageid Id of the page for testing.
 * @return boolean
 */
function lesson_is_page_in_cluster($pages, $pageid) {
    $pageid = $pages[$pageid]->prevpageid; // move up one

    // go up the pages till branch table    
    while (true) {
        if ($pageid == 0) {  // ran into the beginning of the lesson
            return false;
        } elseif ($pages[$pageid]->qtype == LESSON_ENDOFCLUSTER) { // ran into the end of another branch table
            return false;
        } elseif ($pages[$pageid]->qtype == LESSON_CLUSTER) { // hit a branch table
            return true;
        }
        $pageid = $pages[$pageid]->prevpageid;
    }
}

/**
 * Prints the contents for the left menu
 *
 * Runs through all of the lesson pages and calls {@link lesson_print_tree_link_menu()}
 * to print out the link.
 * 
 * @see lesson_print_tree_link_menu()
 * @param int $lessonid Lesson id.
 * @param int $pageid Page id of the first page of the lesson.
 * @param int $id The cmid of the lesson.
 * @param boolean $showpages An optional paramater to show question pages as well as branch tables in the left menu (NYI)
 * @todo change $id param to $cmid.  Finnish implementing the $showpages feature.  
 *       Not necessary to pass $pageid, we can find that out in the function.  
 *       Also, no real need for {@link lesson_print_tree_link_menu()}.  Everything can be handled in this function.
 */
function lesson_print_tree_menu($lessonid, $pageid, $id, $showpages=false) {
    if (!$pages = get_records_select("lesson_pages", "lessonid = $lessonid")) {
        error("Error: could not find lesson pages");
    }
    echo '<ul>';
    while ($pageid != 0) {
        lesson_print_tree_link_menu($pages[$pageid], $id, true);            
        $pageid = $pages[$pageid]->nextpageid;
    }
    echo '</ul>';
}

/**
 * Prints the actual link for the left menu
 *
 * Only prints branch tables that have display set to on.
 * 
 * @param object $page Lesson page object.
 * @param int $id The cmid of the lesson.
 * @param boolean $showpages An optional paramater to show question pages as well as branch tables in the left menu (NYI)
 * @todo change $id param to $cmid.  Finnish implementing the $showpages feature.
 */
function lesson_print_tree_link_menu($page, $id, $showpages=false) { 
    if ($page->qtype == LESSON_BRANCHTABLE && !$page->display) {
        return false;
    } elseif ($page->qtype != LESSON_BRANCHTABLE) {
        return false;
    }
    
    /*elseif ($page->qtype != LESSON_BRANCHTABLE && !$showpages) {
        return false;
    } elseif (!in_array($page->qtype, $LESSON_QUESTION_TYPE)) {
        return false;
    }*/
    
    // set up some variables  NoticeFix  changed whole function
    $output = "";
    $class = ' class="leftmenu_not_selected_link" ';
    
    if($page->id == optional_param('pageid', 0, PARAM_INT)) { 
        $class = ' class="leftmenu_selected_link" '; 
    } 
    
    $output .= '<li>';
    
    $output .= "<a $class href=\"view.php?id=$id&amp;action=navigation&amp;pageid=$page->id\">".format_string($page->title,true)."</a>\n"; 
      
    $output .= "</li>";     

    echo $output;
} 

/**
 * Prints out the tree view list.
 *
 * Each page in the lesson is printed out as a link.  If the page is a branch table
 * or an end of branch then the link color changes and the answer jumps are also printed
 * alongside the links.  Also, the editing buttons (move, update, delete) are printed
 * next to the links.
 * 
 * @uses $USER
 * @uses $CFG
 * @param int $pageid Page id of the first page of the lesson.
 * @param object $lesson Object of the current lesson.
 * @param int $cmid The course module id of the lesson.
 * @param string $pixpath Path to the pictures.
 * @todo $pageid does not need to be passed.  Can be found in the function.
 *       This function is only called once.  It should be removed and the code inside it moved to view.php
 */
function lesson_print_tree($pageid, $lesson, $cmid) {
    global $USER, $CFG;

    if(!$pages = get_records_select("lesson_pages", "lessonid = $lesson->id")) {
        error("Error: could not find lesson pages");
    }
    echo "<table>";
    while ($pageid != 0) {
        echo "<tr><td>";
        if(($pages[$pageid]->qtype != LESSON_BRANCHTABLE) && ($pages[$pageid]->qtype != LESSON_ENDOFBRANCH)) {
            $output = "<a style='color:#DF041E;' href=\"view.php?id=$cmid&display=".$pages[$pageid]->id."\">".format_string($pages[$pageid]->title,true)."</a>\n";
        } else {
            $output = "<a href=\"view.php?id=$cmid&display=".$pages[$pageid]->id."\">".format_string($pages[$pageid]->title,true)."</a>\n";
            
            if($answers = get_records_select("lesson_answers", "lessonid = $lesson->id and pageid = $pageid")) {
                $output .= "Jumps to: ";
                $end = end($answers);
                foreach ($answers as $answer) {
                    if ($answer->jumpto == 0) {
                        $output .= get_string("thispage", "lesson");
                    } elseif ($answer->jumpto == LESSON_NEXTPAGE) {
                        $output .= get_string("nextpage", "lesson");
                    } elseif ($answer->jumpto == LESSON_EOL) {
                        $output .= get_string("endoflesson", "lesson");
                    } elseif ($answer->jumpto == LESSON_UNSEENBRANCHPAGE) {
                        $output .= get_string("unseenpageinbranch", "lesson");  
                    } elseif ($answer->jumpto == LESSON_PREVIOUSPAGE) {
                        $output .= get_string("previouspage", "lesson");
                    } elseif ($answer->jumpto == LESSON_RANDOMPAGE) {
                        $output .= get_string("randompageinbranch", "lesson");
                    } elseif ($answer->jumpto == LESSON_RANDOMBRANCH) {
                        $output .= get_string("randombranch", "lesson");
                    } elseif ($answer->jumpto == LESSON_CLUSTERJUMP) {
                        $output .= get_string("clusterjump", "lesson");            
                    } else {
                        $output .= format_string($pages[$answer->jumpto]->title);
                    }
                    if ($answer->id != $end->id) {
                        $output .= ", ";
                    }
                }
            }
        }
        
        echo $output;        
        if (isteacheredit($lesson->course)) {
          if (count($pages) > 1) {
              echo "<a title=\"move\" href=\"lesson.php?id=$cmid&action=move&pageid=".$pages[$pageid]->id."\">\n".
                  "<img src=\"$CFG->pixpath/t/move.gif\" hspace=\"2\" height=11 width=11 alt=\"move\" border=0></a>\n";
          }
          echo "<a title=\"update\" href=\"lesson.php?id=$cmid&amp;action=editpage&amp;pageid=".$pages[$pageid]->id."\">\n".
              "<img src=\"$CFG->pixpath/t/edit.gif\" hspace=\"2\" height=11 width=11 alt=\"edit\" border=0></a>\n".
              "<a title=\"delete\" href=\"lesson.php?id=$cmid&amp;sesskey=".$USER->sesskey."&amp;action=confirmdelete&amp;pageid=".$pages[$pageid]->id."\">\n".
              "<img src=\"$CFG->pixpath/t/delete.gif\" hspace=\"2\" height=11 width=11 alt=\"delete\" border=0></a>\n";
        }
        echo "</tr></td>";
        $pageid = $pages[$pageid]->nextpageid;
    }
    echo "</table>";
}

/**
 * Calculates a user's grade for a lesson.
 *
 * @param object $lesson The lesson that the user is taking.
 * @param int $retries The attempt number.
 * @param int $userid Id of the user (optinal, default current user).
 * @return object { nquestions => number of questions answered
                    attempts => number of question attempts
                    total => max points possible
                    earned => points earned by student
                    grade => calculated percentage grade
                    nmanual => number of manually graded questions
                    manualpoints => point value for manually graded questions }
 */
function lesson_grade($lesson, $ntries, $userid = 0) {  
    global $USER;

    if (empty($userid)) {
        $userid = $USER->id;
    }
    
    // Zero out everything
    $ncorrect     = 0;
    $nviewed      = 0;
    $score        = 0;
    $nmanual      = 0;
    $manualpoints = 0;
    $thegrade     = 0;
    $nquestions   = 0;
    $total        = 0;
    $earned       = 0;

    if ($useranswers = get_records_select("lesson_attempts",  "lessonid = $lesson->id AND 
            userid = $userid AND retry = $ntries", "timeseen")) {
        // group each try with its page
        $attemptset = array();
        foreach ($useranswers as $useranswer) {
            $attemptset[$useranswer->pageid][] = $useranswer;                                
        }
        
        // Drop all attempts that go beyond max attempts for the lesson
        foreach ($attemptset as $key => $set) {
            $attemptset[$key] = array_slice($set, 0, $lesson->maxattempts);
        }
        
        $pageids = implode(",", array_keys($attemptset));
        
        // get only the pages and their answers that the user answered
        $pages = get_records_select("lesson_pages", "lessonid = $lesson->id AND id IN($pageids)");
        $answers = get_records_select("lesson_answers", "lessonid = $lesson->id AND pageid IN($pageids)");
        
        // Number of pages answered
        $nquestions = count($pages);

        foreach ($attemptset as $attempts) {
            if ($lesson->custom) {
                $attempt = end($attempts);
                // If essay question, handle it, otherwise add to score
                if ($pages[$attempt->pageid]->qtype == LESSON_ESSAY) {
                    $essayinfo = unserialize($attempt->useranswer);
                    $earned += $essayinfo->score;
                    $nmanual++;
                    $manualpoints += $answers[$attempt->answerid]->score;
                } else {
                    $earned += $answers[$attempt->answerid]->score;
                }
            } else {
                foreach ($attempts as $attempt) {
                    $earned += $attempt->correct;
                }
                $attempt = end($attempts); // doesn't matter which one
                // If essay question, increase numbers
                if ($pages[$attempt->pageid]->qtype == LESSON_ESSAY) {
                    $nmanual++;
                    $manualpoints++;
                }
            }
            // Number of times answered
            $nviewed += count($attempts);
        }
        
        if ($lesson->custom) {
            $bestscores = array();
            // Find the highest possible score per page to get our total
            foreach ($answers as $answer) {
                if(isset($bestscores[$answer->pageid]) and $bestscores[$answer->pageid] < $answer->score) {
                    $bestscores[$answer->pageid] = $answer->score;
                } else {
                    $bestscores[$answer->pageid] = $answer->score;
                }
            }
            $total = array_sum($bestscores);
        } else {
            // Check to make sure the student has answered the minimum questions
            if ($lesson->minquestions and $nquestions < $lesson->minquestions) {
                // Nope, increase number viewed by the amount of unanswered questions
                $total =  $nviewed + ($lesson->minquestions - $nquestions);
            } else {
                $total = $nviewed;
            }
        }
    }
    
    if ($total) { // not zero
        $thegrade = round(100 * $earned / $total, 5);
    }
    
    // Build the grade information object
    $gradeinfo               = new stdClass;
    $gradeinfo->nquestions   = $nquestions;
    $gradeinfo->attempts     = $nviewed;
    $gradeinfo->total        = $total;
    $gradeinfo->earned       = $earned;
    $gradeinfo->grade        = $thegrade;
    $gradeinfo->nmanual      = $nmanual;
    $gradeinfo->manualpoints = $manualpoints;
    
    return $gradeinfo;
}

/**
 * Prints the on going message to the user.
 *
 * With custom grading On, displays points 
 * earned out of total points possible thus far.
 * With custom grading Off, displays number of correct
 * answers out of total attempted.
 *
 * @param object $lesson The lesson that the user is taking.
 * @return void
 **/
function lesson_print_ongoing_score($lesson) {
    global $USER;
    
    if (isteacher($lesson->course)) {
        echo "<p align=\"center\">".get_string('teacherongoingwarning', 'lesson').'</p>';
    } else {
        $ntries = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id);
        if (isset($USER->modattempts[$lesson->id])) {
            $ntries--;
        }
        $gradeinfo = lesson_grade($lesson, $ntries);
        
        $a = new stdClass;
        if ($lesson->custom) {
            $a->score = $gradeinfo->earned;
            $a->currenthigh = $gradeinfo->total;
            print_simple_box(get_string("ongoingcustom", "lesson", $a), "center");
        } else {
            $a->correct = $gradeinfo->earned;
            $a->viewed = $gradeinfo->attempts;
            print_simple_box(get_string("ongoingnormal", "lesson", $a), "center");
        }
    }
}

/**
 * Prints tabs for the editing and adding pages.  Each tab is a question type.
 *  
 * @param array $qtypes The question types array (may not need to pass this because it is defined in this file)
 * @param string $selected Current selected tab
 * @param string $link The base href value of the link for the tab
 * @param string $onclick Javascript for the tab link
 * @return void
 */
function lesson_qtype_menu($qtypes, $selected="", $link="", $onclick="") {
    $tabs = array();
    $tabrows = array();

    foreach ($qtypes as $qtype => $qtypename) {
        $tabrows[] = new tabobject($qtype, "$link&amp;qtype=$qtype\" onClick=\"$onclick\"", $qtypename);
    }
    $tabs[] = $tabrows;
    print_tabs($tabs, $selected);
    echo "<input type=\"hidden\" name=\"qtype\" value=\"$selected\" /> \n";

}

/**
 * Checks to see if the nickname is naughty or not.
 * 
 * @todo Move this to highscores.php
 */
function lesson_check_nickname($name) {

    if (empty($name)) {
        return false;
    }
    
    $filterwords = explode(',', get_string('censorbadwords'));
    
    foreach ($filterwords as $filterword) {
        if (strstr($name, $filterword)) {
            return false;
        }
    }
    return true;
}

/**
 * Prints out a Progress Bar which depicts a user's progress within a lesson.
 *
 * Currently works best with a linear lesson.  Clusters are counted as a single page.
 * Also, only viewed branch tables and questions that have been answered correctly count
 * toward lesson completion (or progress).  Only Students can see the Progress bar as well.
 *
 * @param object $lesson The lesson that the user is currently taking.
 * @param object $course The course that the to which the lesson belongs.
 * @return boolean The return is not significant as of yet.  Will return true/false.
 * @author Mark Nielsen
 **/
function lesson_print_progress_bar($lesson, $course) {
    global $CFG, $USER;
    
    // lesson setting to turn progress bar on or off
    if (!$lesson->progressbar) {
        return false;
    }
    
    // catch teachers
    if (isteacher($course->id)) {
        notify(get_string('progressbarteacherwarning', 'lesson', $course->teachers));
        return false;
    }
    if (!isset($USER->modattempts[$lesson->id])) {
        // all of the lesson pages
        if (!$pages = get_records('lesson_pages', 'lessonid', $lesson->id)) {
            return false;
        } else {
            foreach ($pages as $page) {
                if ($page->prevpageid == 0) {
                    $pageid = $page->id;  // find the first page id
                    break;
                }
            }
        }
    
        // current attempt number
        if (!$ntries = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id)) {
            $ntries = 0;  // may not be necessary
        }
    
        $viewedpageids = array();
    
        // collect all of the correctly answered questions
        if ($viewedpages = get_records_select("lesson_attempts", "lessonid = $lesson->id AND userid = $USER->id AND retry = $ntries AND correct = 1", 'timeseen DESC', 'pageid, id')) {
            $viewedpageids = array_keys($viewedpages);
        }
        // collect all of the branch tables viewed
        if ($viewedbranches = get_records_select("lesson_branch", "lessonid = $lesson->id AND userid = $USER->id AND retry = $ntries", 'timeseen DESC', 'pageid, id')) {
            $viewedpageids = array_merge($viewedpageids, array_keys($viewedbranches));
        }

        // Filter out the following pages:
        //      End of Cluster
        //      End of Branch
        //      Pages found inside of Clusters
        // Do not filter out Cluster Page(s) because we count a cluster as one.
        // By keeping the cluster page, we get our 1
        $validpages = array(); 
        while ($pageid != 0) {
            if ($pages[$pageid]->qtype == LESSON_CLUSTER) {
                $clusterpageid = $pageid; // copy it
                $validpages[$clusterpageid] = 1;  // add the cluster page as a valid page
                $pageid = $pages[$pageid]->nextpageid;  // get next page
            
                // now, remove all necessary viewed paged ids from the viewedpageids array.
                while ($pages[$pageid]->qtype != LESSON_ENDOFCLUSTER and $pageid != 0) {
                    if (in_array($pageid, $viewedpageids)) {
                        unset($viewedpageids[array_search($pageid, $viewedpageids)]);  // remove it
                        // since the user did see one page in the cluster, add the cluster pageid to the viewedpageids
                        if (!in_array($clusterpageid, $viewedpageids)) { 
                            $viewedpageids[] = $clusterpageid;
                        }
                    }
                    $pageid = $pages[$pageid]->nextpageid;
                }
            } elseif ($pages[$pageid]->qtype == LESSON_ENDOFCLUSTER or $pages[$pageid]->qtype == LESSON_ENDOFBRANCH) {
                // dont count these, just go to next
                $pageid = $pages[$pageid]->nextpageid;
            } else {
                // a counted page
                $validpages[$pageid] = 1;
                $pageid = $pages[$pageid]->nextpageid;
            }
        }    
    
        // progress calculation as a percent
        $progress = round(count($viewedpageids)/count($validpages), 2) * 100; 
    } else {
        $progress = 100;
    }

    // print out the Progress Bar.  Attempted to put as much as possible in the style sheets.
    echo '<div class="progress_bar" align="center">';
    echo '<table class="progress_bar_table"><tr>';
    if ($progress != 0) {  // some browsers do not repsect the 0 width.
        echo '<td width="'.$progress.'%" class="progress_bar_completed">';
        echo '</td>';
    }
    echo '<td class="progress_bar_todo">';
    echo '<div class="progress_bar_token"></div>';
    echo '</td>';
    echo '</tr></table>';
    echo '</div>';
    
    return true;
}

/**
 * Determines if a user can view the left menu.  The determining factor
 * is whether a user has a grade greater than or equal to the lesson setting
 * of displayleftif
 *
 * @param object $lesson Lesson object of the current lesson
 * @return boolean 0 if the user cannot see, or $lesson->displayleft to keep displayleft unchanged
 * @author Mark Nielsen
 **/
function lesson_displayleftif($lesson) {
    global $CFG, $USER;
    
    if (!empty($lesson->displayleftif)) {
        // get the current user's max grade for this lesson
        if ($maxgrade = get_record_sql('SELECT userid, MAX(grade) AS maxgrade FROM '.$CFG->prefix.'lesson_grades WHERE userid = '.$USER->id.' AND lessonid = '.$lesson->id.' GROUP BY userid')) {
            if ($maxgrade->maxgrade < $lesson->displayleftif) {
                return 0;  // turn off the displayleft
            }
        } else {
            return 0; // no grades
        }
    }
    
    // if we get to here, keep the original state of displayleft lesson setting
    return $lesson->displayleft;
}

?>
