<?php  // $Id$
/// Item analysis displays a table of quiz questions and their performance 

    require_once($CFG->libdir.'/tablelib.php');

/// Item analysis displays a table of quiz questions and their performance 

class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {     /// This function just displays the report
        global $CFG, $SESSION, $db, $QUIZ_QTYPES;
        $strnoquiz = get_string('noquiz','quiz');
        $strnoattempts = get_string('noattempts','quiz');
        
        if (!$quiz->questions) {
            $this->print_header_and_tabs($cm, $course, $quiz, $reportmode="analysis");
            print_heading($strnoattempts);
            return true;
        }
        
    /// Check to see if groups are being used in this quiz
        if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
            $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id&amp;mode=overview");
        } else {
            $currentgroup = false;
        }

    /// Get all users: students
        if ($currentgroup) {
            $users = get_group_students($currentgroup);
        }
        else {
            $users = get_course_students($course->id);
        }

        if(empty($users)) {
            $this->print_header_and_tabs($cm, $course, $quiz, $reportmode="analysis");
            print_heading($strnoattempts);
            return true;
        }
        
        // set Table and Analysis stats options
        if(!isset($SESSION->quiz_analysis_table)) {
            $SESSION->quiz_analysis_table = array('attemptselection' => 0, 'lowmarklimit' => 0, 'pagesize' => 10);
        }

        foreach($SESSION->quiz_analysis_table as $option => $value) {
            $urlparam = optional_param($option, NULL);
            if($urlparam === NULL) {
                $$option = $value;
            }
            else {
                $$option = $SESSION->quiz_analysis_table[$option] = $urlparam;
            }
        }
      
        $scorelimit = $quiz->grade * $lowmarklimit/ 100;
        
        // ULPGC ecastro DEBUG this is here to allow for different SQL to select attempts
        switch ($attemptselection) {
        case QUIZ_ALLATTEMPTS : 
            $limit = '';
            $group = '';
            break;
        case QUIZ_HIGHESTATTEMPT :
            $limit = ', max(qa.sumgrades) ';
            $group = ' GROUP BY qa.userid ';
            break;
        case QUIZ_FIRSTATTEMPT :
            $limit = ', min(qa.timemodified) ';
            $group = ' GROUP BY qa.userid ';            
            break;
        case QUIZ_LASTATTEMPT : 
            $limit = ', max(qa.timemodified) ';
            $group = ' GROUP BY qa.userid ';            
            break;
        }

        $select = 'SELECT  qa.* '.$limit;
        $sql = 'FROM '.$CFG->prefix.'user u '.
               'LEFT JOIN '.$CFG->prefix.'quiz_attempts qa ON u.id = qa.userid '.
               'WHERE u.id IN ('.implode(',', array_keys($users)).') AND ( qa.quiz = '.$quiz->id.') '. // ULPGC ecastro
               ' AND ( qa.sumgrades >= '.$scorelimit.' ) ';
                                                                                   // ^^^^^^ es posible seleccionar aqu� TODOS los quizzes, como quiere Jussi,
                                                                                   // pero habr�a que llevar la cuenta ed cada quiz para restaura las preguntas (quizquestions, states)
        /// Fetch the attempts
        $attempts = get_records_sql($select.$sql.$group);

        if(empty($attempts)) {
            $this->print_header_and_tabs($cm, $course, $quiz, $reportmode="analysis");
            ($strnoattempts);
            $this->print_options_form($quiz, $cm, $attemptselection, $lowmarklimit, $pagesize);
            return true;
        }

    /// Here we rewiew all attempts and record data to construct the table
        $questions = array();
        $statstable = array();
        $questionarray = array(); 
        foreach ($attempts as $attempt) {
            $questionarray[] = quiz_questions_in_quiz($attempt->layout);
        }
        $questionlist = quiz_questions_in_quiz(implode(",", $questionarray));
        $questionarray = array_unique(explode(",",$questionlist));
        $questionlist = implode(",", $questionarray);
        unset($questionarray);
        $accepted_qtypes = array(SHORTANSWER, TRUEFALSE, MULTICHOICE, MATCH, NUMERICAL, CALCULATED);        

        foreach ($attempts as $attempt) {
            $sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
                   "  FROM {$CFG->prefix}quiz_questions q,".
                   "       {$CFG->prefix}quiz_question_instances i".
                   " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
                   "   AND q.id IN ($questionlist)";

            if (!$quizquestions = get_records_sql($sql)) {
                error('No questions found');
            }
        
            // Load the question type specific information
            if (!quiz_get_question_options($quizquestions)) {
                error('Could not load question options');
            }
            echo "estoy aqui";
            // Restore the question sessions to their most recent states
            // creating new sessions where required
            if (!$states = quiz_restore_question_sessions($quizquestions, $quiz, $attempt)) {
                error('Could not restore question sessions');
            }
            print_object($states);
            $numbers = explode(',', $questionlist);
            $statsrow = array();
            foreach ($numbers as $i) {              
                if (!isset($quizquestions[$i]) or !isset($states[$i])) {
                    continue;
                }
                $qtype = ($quizquestions[$i]->qtype==4) ? $states[$i]->options->question->qtype : $quizquestions[$i]->qtype;
                if (!in_array ($qtype, $accepted_qtypes)){
                    continue;
                }                
                $q = quiz_get_question_responses($quizquestions[$i], $states[$i]);
                $qid = $q->id;
                if (!isset($questions[$qid])) {
                    $questions[$qid]['id'] = $qid;
                    $questions[$qid]['qname'] = $quizquestions[$i]->name;
                    foreach ($q->responses as $answer => $r) {
                        $r->count = 0;
                        $questions[$qid]['responses'][$answer] = $r->answer;
                        $questions[$qid]['rcounts'][$answer] = 0;
                        $questions[$qid]['credits'][$answer] = $r->credit;
                        $statsrow[$qid] = 0;
                    }                    
                }
                $responses = quiz_get_question_actual_response($quizquestions[$i], $states[$i]);
                foreach ($responses as $resp){
                    if ($resp) {
                        if ($key = array_search($resp, $questions[$qid]['responses'])) {                 
                            $questions[$qid]['rcounts'][$key]++;
                        } else {
                            $test->responses = $QUIZ_QTYPES[$quizquestions[$i]->qtype]->get_correct_responses($quizquestions[$i], $states[$i]);
                            if ($key = $QUIZ_QTYPES[$quizquestions[$i]->qtype]->check_response($quizquestions[$i], $states[$i], $test)) {
                                $questions[$qid]['rcounts'][$key]++;
                            } else {
                                $questions[$qid]['responses'][] = $resp;
                                $questions[$qid]['rcounts'][] = 1;
                                $questions[$qid]['credits'][] = 0;
                            }
                        }
                    }
                }
                $statsrow[$qid] = quiz_get_question_fraction_grade($quizquestions[$i], $states[$i]);
            }
            $attemptscores[$attempt->id] = $attempt->sumgrades;   
            $statstable[$attempt->id] = $statsrow;
        } // Statistics Data table built
        
        unset($attempts);
        unset($quizquestions);
        unset($states);

        // now calculate statistics and set the values in the $questions array
        $top = max($attemptscores);
        $bottom = min($attemptscores);
        $gap = ($top - $bottom)/3;
        $top -=$gap;
        $bottom +=$gap;
        foreach ($questions as $qid=>$q) {
            $questions[$qid] = $this->report_question_stats(&$q, $attemptscores, $statstable, $top, $bottom);
        }
        unset($attemptscores);
        unset($statstable);
        
    /// Now check if asked download of data
        if ($download = optional_param('download', NULL)) {
            $filename = clean_filename("$course->shortname ".format_string($quiz->name,true));
            switch ($download) {
            case "Excel" :
                $this->Export_Excel($questions, $filename);
                break;
            case "OOo": 
                $this->Export_OOo($questions, $filename);
                break;
            case "CSV": 
                $this->Export_CSV($questions, $filename);
                break;
            }
        }
        
        $this->print_header_and_tabs($cm, $course, $quiz, $reportmode="analysis");
    /// Construct the table for this particular report

        $tablecolumns = array('id', 'qname',    'answers', 'credits', 'rcounts', 'rpercent', 'facility', 'sd','discrimination_index', 'discrimination_coeff');
        $tableheaders = array(get_string('qidtitle','quiz_analysis'), get_string('qtexttitle','quiz_analysis'), 
                        get_string('responsestitle','quiz_analysis'), get_string('rfractiontitle','quiz_analysis'), 
                        get_string('rcounttitle','quiz_analysis'), get_string('rpercenttitle','quiz_analysis'), 
                        get_string('facilitytitle','quiz_analysis'), get_string('stddevtitle','quiz_analysis'), 
                        get_string('dicsindextitle','quiz_analysis'), get_string('disccoefftitle','quiz_analysis')); 

        $table = new flexible_table('mod-quiz-report-itemanalysis');

        $table->define_columns($tablecolumns);
        $table->define_headers($tableheaders);
        $table->define_baseurl($CFG->wwwroot.'/mod/quiz/report.php?q='.$quiz->id.'&mode=analysis');

        $table->sortable(true);
        $table->collapsible(true);
        $table->initialbars(false);
        
        $table->column_class('id', 'numcol');
        $table->column_class('credits', 'numcol');
        $table->column_class('rcounts', 'numcol');
        $table->column_class('rpercent', 'numcol');
        $table->column_class('facility', 'numcol');
        $table->column_class('sd', 'numcol'); 
        $table->column_class('discrimination_index', 'numcol');
        $table->column_class('discrimination_coeff', 'numcol');
   
        $table->column_suppress('id');
        $table->column_suppress('qname');
        $table->column_suppress('facility');
        $table->column_suppress('sd');
        $table->column_suppress('discrimination_index');
        $table->column_suppress('discrimination_coeff');

        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('id', 'itemanalysis');
        $table->set_attribute('class', 'generaltable generalbox');
        
        // Start working -- this is necessary as soon as the niceties are over
        $table->setup();

        $tablesort = $table->get_sql_sort();
        $sorts = explode(",",trim($tablesort));  
        if ($tablesort and is_array($sorts)) {
            $sortindex = array();
            $sortorder = array ();
            foreach ($sorts as $sort) {
                $data = explode(" ",trim($sort));
                $sortindex[] = trim($data[0]);
                $s = trim($data[1]);               
                if ($s=="ASC") {
                    $sortorder[] = SORT_ASC;
                } else {
                    $sortorder[] = SORT_DESC;
                }
            }
            if (count($sortindex)>0) {
                $sortindex[] = "id";
                $sortorder[] = SORT_ASC;
                foreach($questions as $qid => $row){
                    $index1[$qid] = $row[$sortindex[0]];
                    $index2[$qid] = $row[$sortindex[1]];
                }
                array_multisort($index1, $sortorder[0], $index2, $sortorder[1], $questions);
            }
        }

        // Now it is time to page the data, simply slice the keys in the array 
        if (!isset($pagesize)){
            $pagesize = 10;
        }
        $table->pagesize($pagesize, count($questions));
        $start = $table->get_page_start();
        $pagequestions = array_slice(array_keys($questions), $start, $pagesize);
        
        foreach($pagequestions as $qnum) {
            $q = $questions[$qnum];
            $qid = $q['id'];
            $question = get_record('quiz_questions', 'id', $qid);         
            $qnumber = " (".link_to_popup_window('/mod/quiz/question.php?id='.$qid,'editquestion', $qid, 450, 550, get_string('edit'), 'none', true ).") ";
            $qname = '<div class="qname">'.format_text($question->name." :  ", $question->questiontextformat, NULL, $quiz->course).'</div>';
            $qicon = quiz_print_question_icon($question, false, true);
            $qreview = quiz_get_question_review($quiz, $question);
            $qtext = format_text($question->questiontext, $question->questiontextformat, NULL, $quiz->course);          
            $qquestion = $qname."\n".$qtext."\n";
            
            $format_options->para = false;
            $format_options->newlines = false;
            unset($responses);
            foreach ($q['responses'] as $aid=>$resp){
                unset($response);
                if ($q['credits'][$aid] <= 0) {
                    $qclass = 'uncorrect';
                } elseif ($q['credits'][$aid] == 1) {
                    $qclass = 'correct';
                } else {
                    $qclass = 'partialcorrect';
                }
                $response->credit = '<span class="'.$qclass.'">('.format_float($q['credits'][$aid],2).') </span>';                
                $response->text = '<span class="'.$qclass.'">'.format_text("$resp", FORMAT_MOODLE, $format_options, $quiz->course).' </span>';
                $count = $q['rcounts'][$aid].'/'.$q['count'];
                $response->rcount = $count;  // format_text("$count", FORMAT_MOODLE, $format_options, $quiz->course);
                $response->rpercent =  '('.format_float($q['rcounts'][$aid]/$q['count']*100,0).'%)';
                $responses[] = $response;
            }
            
            $facility = format_float($q['facility']*100,0)." %";
            $qsd = format_float($q['qsd'],3);
            $di = format_float($q['disc_index'],2);
            $dc = format_float($q['disc_coeff'],2);
            
            $response = array_shift($responses);
            $table->add_data(array($qnumber."\n<br>".$qicon."\n ".$qreview, $qquestion, $response->text, $response->credit, $response->rcount, $response->rpercent, $facility, $qsd, $di, $dc));
            foreach($responses as $response) {
                $table->add_data(array('', '', $response->text, $response->credit, $response->rcount, $response->rpercent, '', '', '', ''));
            }
        }
        
        echo '<div id="titlecontainer" class="quiz-report-title">';
        echo get_string("analysistitle", "quiz_analysis");
        helpbutton("itemanalysis", get_string("reportanalysis","quiz_analysis"), "quiz");
        echo '</div>';

        echo '<div id="tablecontainer">';
        $table->print_html();

        $this->print_options_form($quiz, $cm, $attemptselection, $lowmarklimit, $pagesize);
        return true;
    }


    function print_options_form($quiz, $cm, $attempts, $lowlimit=0, $pagesize=10) {
        global $CFG, $USER;
        echo '<div class="controls">';
        echo '<form id="options" name="options" action="report.php" method="post">';
        echo '<p class="quiz-report-options">'.get_string('analysisoptions', 'quiz').': </p>';
        echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
        echo '<input type="hidden" name="q" value="'.$quiz->id.'" />';
        echo '<input type="hidden" name="mode" value="analysis" />';
        echo '<table id="analysis-options" align="center">';
        echo '<tr align="left"><td><label for="attemptselection">'.get_string('attemptselection', 'quiz_analysis').'</label></td><td>';
        $options = array ( QUIZ_ALLATTEMPTS     => get_string("attemptsall", 'quiz_analysis'),
                           QUIZ_HIGHESTATTEMPT => get_string("attemptshighest", 'quiz_analysis'),
                           QUIZ_FIRSTATTEMPT => get_string("attemptsfirst", 'quiz_analysis'),
                           QUIZ_LASTATTEMPT  => get_string("attemptslast", 'quiz_analysis'));
        choose_from_menu($options, "attemptselection", "$attempts", "");
        echo '</td></tr>';
        echo '<tr align="left">';
        echo '<td><label for="lowmarklimit">'.get_string('lowmarkslimit', 'quiz_analysis').'</label></td>';
        echo '<td><input type="text" id="lowmarklimit" name="lowmarklimit" size="1" value="'.$lowlimit.'" /> % </td>';
        echo '</tr>';
        echo '<tr align="left">';
        echo '<td><label for="pagesize">'.get_string('pagesize', 'quiz_analysis').'</label></td>';
        echo '<td><input type="text" id="pagesize" name="pagesize" size="1" value="'.$pagesize.'" /></td>';
        echo '</tr>';
        echo '<tr><td colspan="2" align="center">';
        echo '<input type="submit" value="'.get_string('go').'" />';
        helpbutton("analysisoptions", get_string("analysisoptions",'quiz_analysis'), 'quiz_analysis');
        echo '</td></tr></table>';
        echo '</form>';
        echo '</div>';    
        echo "\n";
 
        echo '<table align="center"><tr>';
        unset($options);
        $options["id"] = "$cm->id";
        $options["q"] = "$quiz->id";
        $options["mode"] = "analysis";
        $options['sesskey'] = $USER->sesskey;
        $options["noheader"] = "yes";
        echo '<td>';        
        $options["download"] = "Excel";
        print_single_button("report.php", $options, get_string("downloadexcel"));
        echo "</td>\n";
        echo '<td>';        
        
        if (file_exists("$CFG->libdir/phpdocwriter/lib/include.php")) {
            $options["download"] = "OOo";
            print_single_button("report.php", $options, get_string("downloadooo", "quiz_analysis"));
            echo "</td>\n";
        }
        echo '<td>';
        $options["download"] = "CSV";
        print_single_button('report.php', $options, get_string("downloadtext"));
        echo "</td>\n";
        echo "<td>";
        helpbutton("analysisdownload", get_string("analysisdownload","quiz"), "quiz");
        echo "</td>\n";
        echo '</tr></table>';
}

    function report_question_stats(&$q, &$attemptscores, &$questionscores, $top, $bottom) {
        $qstats = array();
        $qid = $q['id'];
        $top_scores = $top_count = 0;
        $bottom_scores = $bottom_count = 0;
        foreach ($questionscores as $aid => $qrow){
            if (isset($qrow[$qid])){
                $qstats[] =  array($attemptscores[$aid],$qrow[$qid]);
                if ($attemptscores[$aid]>=$top){
                    $top_scores +=$qrow[$qid];
                    $top_count++;
                }
                if ($attemptscores[$aid]<=$bottom){
                    $bottom_scores +=$qrow[$qid];
                    $bottom_count++;
                }               
            }
        }
        
        $n = count($qstats);
        $sumx = array_reduce($qstats, "stats_sumx");
        $sumg = $sumx->x;
        $sumq = $sumx->y;
        $sumx2 = array_reduce($qstats, "stats_sumx2");
        $sumg2 = $sumx2->x;
        $sumq2 = $sumx2->y;
        $sumxy = array_reduce($qstats, "stats_sumxy");
        $sumgq = $sumxy->x;
        
        $q['count'] = $n;
        $q['facility'] = $sumq/$n;
        if ($n<2) {
            $q['qsd'] = sqrt(($sumq2 - $sumq*$sumq/$n)/($n));
            $gsd = sqrt(($sumg2 - $sumg*$sumg/$n)/($n));
        } else {
            $q['qsd'] = sqrt(($sumq2 - $sumq*$sumq/$n)/($n-1));
            $gsd = sqrt(($sumg2 - $sumg*$sumg/$n)/($n-1));
        }
        $q['disc_index'] = ($top_scores - $bottom_scores)/max($top_count, $bottom_count, 1);
        $div = $n*$gsd*$q['qsd'];
        if ($div!=0) {
            $q['disc_coeff'] = ($sumgq - $sumg*$sumq/$n)/($n*$gsd*$q['qsd']);
        } else {
            $q['disc_coeff'] = -999;
        }
        return $q;
    }

    function Export_Excel(&$questions, $filename) {
        global $CFG;
        require_once("$CFG->libdir/excel/Worksheet.php");
        require_once("$CFG->libdir/excel/Workbook.php");
        
        $filename .= ".xls";
        header("Content-Type: application/vnd.ms-excel");   
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
        header("Pragma: public");
        header("Content-Transfer-Encoding: binary");

        $workbook = new Workbook("-");
        // Creating the first worksheet
        $sheettitle = get_string('reportanalysis','quiz_analysis');
        $myxls =& $workbook->add_worksheet($sheettitle);
        /// format types
        $format =& $workbook->add_format();
        $format->set_bold(0);
        $formatbc =& $workbook->add_format();
        $formatbc->set_bold(1);
        $formatb =& $workbook->add_format();
        $formatb->set_bold(1);
        $formaty =& $workbook->add_format();
        $formaty->set_bg_color('yellow');
        $formatyc =& $workbook->add_format();
        $formatyc->set_bg_color('yellow'); //bold text on yellow bg
        $formatyc->set_bold(1);
        $formatyc->set_align('center');
        $formatc =& $workbook->add_format();
        $formatc->set_align('center');
        $formatbc->set_align('center');
        $formatbpct =& $workbook->add_format();
        $formatbpct->set_bold(1);
        $formatbpct->set_num_format('0.0%');
        $formatbrt =& $workbook->add_format();
        $formatbrt->set_bold(1);
        $formatbrt->set_align('right');
        $formatred =& $workbook->add_format();
        $formatred->set_bold(1);
        $formatred->set_color('red');
        $formatred->set_align('center');
        $formatblue =& $workbook->add_format();
        $formatblue->set_bold(1);
        $formatblue->set_color('blue');
        $formatblue->set_align('center');
        // Here starts workshhet headers
        $myxls->write_string(0,0,$sheettitle,$formatb);

        $headers = array(get_string('qidtitle','quiz_analysis'), get_string('qtypetitle','quiz_analysis'), 
                        get_string('qnametitle','quiz_analysis'), get_string('qtexttitle','quiz_analysis'), 
                        get_string('responsestitle','quiz_analysis'), get_string('rfractiontitle','quiz_analysis'), 
                        get_string('rcounttitle','quiz_analysis'), get_string('rpercenttitle','quiz_analysis'), 
                        get_string('qcounttitle','quiz_analysis'), 
                        get_string('facilitytitle','quiz_analysis'), get_string('stddevtitle','quiz_analysis'), 
                        get_string('dicsindextitle','quiz_analysis'), get_string('disccoefftitle','quiz_analysis')); 

        $col = 0;
        foreach ($headers as $item) {
            $myxls->write(2,$col,$item,$formatbc);
            $col++;
        }
        
        $row = 3;
        foreach($questions as $q) {       
            $rows = $this->print_row_stats_data(&$q);
            foreach($rows as $rowdata){
                $col = 0;
                foreach($rowdata as $item){
                    $myxls->write($row,$col,$item,$format);
                    $col++;
                }
                $row++;
            }
        }
        $workbook->close();
        exit;
    }


    function Export_OOo(&$questions, $filename) {
        global $CFG;
        require_once("$CFG->libdir/phpdocwriter/lib/include.php");
        import('phpdocwriter.pdw_document');
        header("Content-Type: application/download\n");   
        header("Content-Disposition: attachment; filename=\"$filename.sxw\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
        header("Pragma: public");
        header("Content-Transfer-Encoding: binary");

        $sxw = new pdw_document;
        $sxw->SetFileName($filename);
        $sxw->SetAuthor('Moodle');
        $sxw->SetTitle(get_string('reportanalysis','quiz_analysis'));
        $sxw->SetDescription(get_string('reportanalysis','quiz_analysis').' - '.$filename);
        $sxw->SetLanguage('es','ES');
        $sxw->SetStdFont("Times New Roman",12);
        $sxw->AddPageDef(array('name'=>'Standard', 'margins'=>'1,1,1,1', 'w'=>'29.7', 'h'=>'21'));
        $sxw->Write(get_string('analysistitle','quiz_analysis'));
        $sxw->Ln(3);

        $headers = array(get_string('qidtitle','quiz_analysis'), get_string('qtypetitle','quiz_analysis'), 
                        get_string('qnametitle','quiz_analysis'), get_string('qtexttitle','quiz_analysis'), 
                        get_string('responsestitle','quiz_analysis'), get_string('rfractiontitle','quiz_analysis'), 
                        get_string('rcounttitle','quiz_analysis'), get_string('rpercenttitle','quiz_analysis'), 
                        get_string('qcounttitle','quiz_analysis'), 
                        get_string('facilitytitle','quiz_analysis'), get_string('stddevtitle','quiz_analysis'), 
                        get_string('dicsindextitle','quiz_analysis'), get_string('disccoefftitle','quiz_analysis')); 

        foreach($headers as $key=>$header){
            $headers[$key] = eregi_replace ("<br?>", " ",$header);
        }

        unset($data);
        foreach($questions as $q) {       
            $rows = $this->print_row_stats_data(&$q);
            foreach($rows as $row){
                $data[] = $row;
            }
        }
        $sxw->Table($headers,$data);
        $sxw->Output();
        exit;
    }

    function Export_CSV(&$questions, $filename) {

        $headers = array(get_string('qidtitle','quiz_analysis'), get_string('qtypetitle','quiz_analysis'), 
                        get_string('qnametitle','quiz_analysis'), get_string('qtexttitle','quiz_analysis'), 
                        get_string('responsestitle','quiz_analysis'), get_string('rfractiontitle','quiz_analysis'), 
                        get_string('rcounttitle','quiz_analysis'), get_string('rpercenttitle','quiz_analysis'), 
                        get_string('qcounttitle','quiz_analysis'), 
                        get_string('facilitytitle','quiz_analysis'), get_string('stddevtitle','quiz_analysis'), 
                        get_string('dicsindextitle','quiz_analysis'), get_string('disccoefftitle','quiz_analysis')); 

        $text = implode("\t", $headers)." \n";
        
        $filename .= ".txt";

        header("Content-Type: application/download\n");   
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
        header("Pragma: public");

        echo $text;

        foreach($questions as $q) {       
            $rows = $this->print_row_stats_data(&$q);
            foreach($rows as $row){
                $text = implode("\t", $row);
                echo $text." \n";
            }
        }
        exit;
    }

    function print_row_stats_data(&$q) {
        $qid = $q['id'];
        $question = get_record('quiz_questions', 'id', $qid);

        $options->para = false;
        $options->newlines = false;

        $qtype = $question->qtype;

        $qname = format_text($question->name, FORMAT_MOODLE, $options);
        $qtext = format_text($question->questiontext, FORMAT_MOODLE, $options);          
                
        unset($responses);
        foreach ($q['responses'] as $aid=>$resp){
            unset($response);
            if ($q['credits'][$aid] <= 0) {
                $qclass = 'uncorrect';
            } elseif ($q['credits'][$aid] == 1) {
                $qclass = 'correct';
            } else {
                $qclass = 'partialcorrect';
            }
            $response->credit = " (".format_float($q['credits'][$aid],2).") ";                
            $response->text = format_text("$resp", FORMAT_MOODLE, $options);
            $count = $q['rcounts'][$aid].'/'.$q['count'];
            $response->rcount = $count;  
            $response->rpercent =  '('.format_float($q['rcounts'][$aid]/$q['count']*100,0).'%)';
            $responses[] = $response;
        }
        $count = format_float($q['count'],0);
        $facility = format_float($q['facility']*100,0);
        $qsd = format_float($q['qsd'],4);
        $di = format_float($q['disc_index'],3);
        $dc = format_float($q['disc_coeff'],3);
        
        unset($result);
        $response = array_shift($responses);        
        $result[] = array($qid, $qtype, $qname, $qtext, $response->text, $response->credit, $response->rcount, $response->rpercent, $count, $facility, $qsd, $di, $dc);   
        foreach($responses as $response){
            $result[] = array('', '', '', '', $response->text, $response->credit, $response->rcount, $response->rpercent, '', '', '', '', ''); 
        }
        return $result;
    }


}    

define('QUIZ_ALLATTEMPTS', 0);
define('QUIZ_HIGHESTATTEMPT', 1);
define('QUIZ_FIRSTATTEMPT', 2);
define('QUIZ_LASTATTEMPT', 3);

function stats_sumx($sum, $data){
    $sum->x += $data[0];
    $sum->y += $data[1];
    return $sum;
}       

function stats_sumx2($sum, $data){
    $sum->x += $data[0]*$data[0];
    $sum->y += $data[1]*$data[1];
    return $sum;
}    

function stats_sumxy($sum, $data){
    $sum->x += $data[0]*$data[1];
    return $sum;
}

?>