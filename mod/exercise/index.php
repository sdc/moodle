<?PHP // $Id$

    require("../../config.php");
    require("lib.php");
    require("locallib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);
    add_to_log($course->id, "exercise", "view all", "index.php?id=$course->id", "");

    $strexercises = get_string("modulenameplural", "exercise");
    $strexercise = get_string("modulename", "exercise");
    $strweek = get_string("week");
    $strtopic = get_string("topic");
    $strname = get_string("name");
    $strtitle = get_string("title", "exercise");
    $strphase = get_string("phase", "exercise");
    $strgrade = get_string("grade");
    $strdeadline = get_string("deadline", "exercise");
    $strsubmitted = get_string("submitted", "assignment");

    print_header_simple("$strexercises", "", "$strexercises", "", "", true, "", navmenu($course));

    if (! $exercises = get_all_instances_in_course("exercise", $course)) {
        notice("There are no exercises", "../../course/view.php?id=$course->id");
        die;
    }

    $timenow = time();

    if ($course->format == "weeks") {
        if (isteacher($course->id)) {
            $table->head  = array ($strweek, $strname, $strtitle, $strphase, $strsubmitted, $strdeadline);
        } else {
            $table->head  = array ($strweek, $strname, $strtitle, $strgrade, $strsubmitted, $strdeadline);
        }
        $table->align = array ("CENTER", "LEFT", "LEFT","center","LEFT", "LEFT");
    } else if ($course->format == "topics") {
        if (isteacher($course->id)) {
            $table->head  = array ($strtopic, $strname, $strtitle, $strphase, $strsubmitted, $strdeadline);
        } else {
            $table->head  = array ($strtopic, $strname, $strtitle, $strgrade, $strsubmitted, $strdeadline);
        }
        $table->align = array ("CENTER", "LEFT", "LEFT", "center", "LEFT", "LEFT");
    } else {
        $table->head  = array ($strname, $strsubmitted, $strdeadline);
        $table->align = array ("LEFT", "LEFT", "LEFT");
    }

    foreach ($exercises as $exercise) {
        if ($exercise->deadline > $timenow) {
            $due = userdate($exercise->deadline);
        } else {
            $due = "<FONT COLOR=\"red\">".userdate($exercise->deadline)."</FONT>";
        }
        if ($submissions = exercise_get_user_submissions($exercise, $USER)) {
            foreach ($submissions as $submission) {
                if ($submission->late) {
                    $submitted = "<FONT COLOR=\"red\">".userdate($submission->timecreated)."</FONT>";
                    } 
                else {
                    $submitted = userdate($submission->timecreated);
                    }
                $link = "<A HREF=\"view.php?id=$exercise->coursemodule\">$exercise->name</A>";
                $title = $submission->title;
                if ($course->format == "weeks" or $course->format == "topics") {
                    if (isteacher($course->id)) {
                        $phase = '';
                        switch ($exercise->phase) {
                            case 1: $phase = get_string("phase1short", "exercise");
                                    break;
                            case 2: $phase = get_string("phase2short", "exercise");
                                    if ($num = exercise_count_unassessed_student_submissions($exercise)) {
                                        $phase .= " [".get_string("unassessed", "exercise", $num)."]";
                                    }
                                    break;
                            case 3: $phase = get_string("phase3short", "exercise");
                                    if ($num = exercise_count_unassessed_student_submissions($exercise)) {
                                        $phase .= " [".get_string("unassessed", "exercise", $num)."]";
                                    }
                                    break;
                        }
                        $table->data[] = array ($exercise->section, $link, $title, $phase, 
                                $submitted, $due);
                    } else { // it's a student
                        if ($assessments = exercise_get_user_assessments($exercise, $USER)) { // should be only one...
                            foreach ($assessments as $studentassessment) {
                                break;
                            }
                            if ($studentassessment->timegraded) { // it's been assessed
                                if ($teacherassessment = exercise_get_submission_assessment($submission)) {
                                    $actualgrade = number_format(($studentassessment->gradinggrade * 
                                        $exercise->gradinggrade / 100.0) + ($teacherassessment->grade * 
                                        $exercise->grade / 100.0), 1);
                                    if ($submission->late) {
                                        $actualgrade = "<font color=\"red\">(".$actualgrade.")<font color=\"red\">";
                                    }
                                    $actualgrade .= " (".get_string("maximumshort").": ".
                                        number_format($exercise->gradinggrade + $exercise->grade, 0).")";
                                    $table->data[] = array ($exercise->section, $link, $title, $actualgrade, 
                                        $submitted, $due);
                                }
                            } else {
                                $table->data[] = array ($exercise->section, $link, $title, 
                                    "-", $submitted, $due);
                            }
                        }
                    } 
                } else {
                    $table->data[] = array ($link, $submitted, $due);
                }
            }
        } else {
            $submitted = get_string("no");
            $title = '';
            $link = "<A HREF=\"view.php?id=$exercise->coursemodule\">$exercise->name</A>";
            if ($course->format == "weeks" or $course->format == "topics") {
                if (isteacher($course->id)) {
                    $table->data[] = array ($exercise->section, $link, $title, $exercise->phase, 
                            $submitted, $due);
                } else {
                    $table->data[] = array ($exercise->section, $link, $title, "-", $submitted, $due);
                } 
            } else {
                $table->data[] = array ($link, $submitted, $due);
            }
        }
    }
    echo "<BR>";

    print_table($table);

    print_footer($course);
?>
