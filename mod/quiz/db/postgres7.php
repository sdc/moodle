<?PHP // $Id$

function quiz_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2003010100) {
        execute_sql(" ALTER TABLE {$CFG->prefix}quiz ADD review integer DEFAULT '0' NOT NULL AFTER `grademethod` ");
    }

    if ($oldversion < 2003010301) {
        table_column("quiz_truefalse", "true", "trueanswer", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "");
        table_column("quiz_truefalse", "false", "falseanswer", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "");
        table_column("quiz_questions", "type", "qtype", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "");
    }

    if ($oldversion < 2003022303) {
        modify_database ("", "CREATE TABLE prefix_quiz_randommatch (
                                  id SERIAL PRIMARY KEY,
                                  question integer NOT NULL default '0',
                                  choose integer NOT NULL default '4'
                              );");
    }
    if ($oldversion < 2003030303) {
        table_column("quiz_questions", "", "defaultgrade", "INTEGER", "6", "UNSIGNED", "1", "NOT NULL", "image");
    }

    if ($oldversion < 2003033100) {
        modify_database ("", "ALTER TABLE prefix_quiz_randommatch RENAME prefix_quiz_randomsamatch ");
        modify_database ("", "CREATE TABLE prefix_quiz_match_sub (
                                 id SERIAL PRIMARY KEY,
                                 question integer NOT NULL default '0',
                                 questiontext text NOT NULL default '',
                                 answertext varchar(255) NOT NULL default ''
                              );");
        modify_database ("", "CREATE INDEX prefix_quiz_match_sub_question_idx ON prefix_quiz_match_sub (question);");

        modify_database ("", "CREATE TABLE prefix_quiz_multichoice (
                                 id SERIAL PRIMARY KEY,
                                 question integer NOT NULL default '0',
                                 layout integer NOT NULL default '0',
                                 answers varchar(255) NOT NULL default '',
                                 single integer NOT NULL default '0'
                               );");
        modify_database ("", "CREATE INDEX prefix_quiz_multichoice_question_idx ON prefix_quiz_multichoice (question);");
    }

    if ($oldversion < 2003040901) {
        table_column("quiz", "", "shufflequestions", "INTEGER", "5", "UNSIGNED", "0", "NOT NULL", "review");
        table_column("quiz", "", "shuffleanswers", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "shufflequestions");
    }
    if ($oldversion < 2003042702) {
        modify_database ("", "CREATE TABLE prefix_quiz_match (
                                 id SERIAL PRIMARY KEY,
                                 question integer NOT NULL default '0',
                                 subquestions varchar(255) NOT NULL default ''
                               );");
        modify_database ("", "CREATE INDEX prefix_quiz_match_question_idx ON prefix_quiz_match (question);");
    }
    if ($oldversion < 2003071001) {
        modify_database ("", " CREATE TABLE prefix_quiz_numerical (
                               id SERIAL PRIMARY KEY,
                               question integer NOT NULL default '0',
                               answer integer NOT NULL default '0',
                               min varchar(255) NOT NULL default '',
                               max varchar(255) NOT NULL default ''
                               ); ");
        modify_database ("", "CREATE INDEX prefix_quiz_numerical_answer_idx ON prefix_quiz_numerical (answer);");
    }

    if ($oldversion < 2003072400) {
        execute_sql(" INSERT INTO {$CFG->prefix}log_display VALUES ('quiz', 'review', 'quiz', 'name') ");
    }

    if ($oldversion < 2003082300) {
        modify_database ("", " CREATE TABLE prefix_quiz_multianswers (
                               id SERIAL PRIMARY KEY,
                               question integer NOT NULL default '0',
                               answers varchar(255) NOT NULL default '',
                               positionkey varchar(255) NOT NULL default '',
                               answertype integer NOT NULL default '0',
                               norm integer NOT NULL default '1'
                              ); ");
        modify_database ("", "CREATE INDEX prefix_quiz_multianswers_question_idx ON prefix_quiz_multianswers (question);");

        table_column("quiz", "", "attemptonlast", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "attempts");

        table_column("quiz_questions", "", "stamp", "varchar", "255", "", "qtype");
    }

    if ($oldversion < 2003082301) {
        table_column("quiz_questions", "", "version", "integer", "10", "", "1", "not null", "stamp");
        if ($questions = get_records("quiz_questions")) {
            foreach ($questions as $question) {
                $stamp = make_unique_id_code();
                if (!set_field("quiz_questions", "stamp", $stamp, "id", $question->id)) {
                    notify("Error while adding stamp to question id = $question->id");
                }
            }
        }
    }

    if ($oldversion < 2003082700) {
        table_column("quiz_categories", "", "stamp", "varchar", "255", "", "", "not null");
        if ($categories = get_records("quiz_categories")) {
            foreach ($categories as $category) {
                $stamp = make_unique_id_code();
                if (!set_field("quiz_categories", "stamp", $stamp, "id", $category->id)) {
                    notify("Error while adding stamp to category id = $category->id");
                }
            }
        }
    }

    if ($oldversion < 2003111100) {
        $duplicates = get_records_sql("SELECT stamp as id,count(*) as cuenta
                                       FROM {$CFG->prefix}quiz_questions
                                       GROUP BY stamp
                                       HAVING count(*)>1");

        if ($duplicates) {
            notify("You have some quiz questions with duplicate stamps IDs.  Cleaning these up.");
            foreach ($duplicates as $duplicate) {
                $questions = get_records("quiz_questions","stamp",$duplicate->id);
                $add = 1;
                foreach ($questions as $question) {
                    echo "Changing question id $question->id stamp to ".$duplicate->id.$add."<br>";
                    set_field("quiz_questions","stamp",$duplicate->id.$add,"id",$question->id);
                    $add++;
                }
            }
        } else {
            notify("Checked your quiz questions for stamp duplication errors, but no problems were found.", "green");
        }
    }

    if ($oldversion < 2004021300) {
        table_column("quiz_questions", "", "questiontextformat", "integer", "2", "", "0", "not null", "questiontext");
    }

    if ($oldversion < 2004021900) {
        modify_database("","INSERT INTO prefix_log_display VALUES ('quiz', 'add', 'quiz', 'name');");
        modify_database("","INSERT INTO prefix_log_display VALUES ('quiz', 'update', 'quiz', 'name');");
    }

    if ($oldversion < 2004051700) {
        include_once("$CFG->dirroot/mod/quiz/lib.php");
        quiz_refresh_events();
    }
    if ($oldversion < 2004060200) {
        table_column("quiz", "", "timelimit", "integer", "", "", "0", "NOT NULL", "");
    }

    if ($oldversion < 2004070700) {
        table_column("quiz", "", "password", "varchar", "255", "", "", "not null", "");
        table_column("quiz", "", "subnet", "varchar", "255", "", "", "not null", "");
    }

    if ($oldversion < 2004073001) {
        // Six new tables:


        modify_database ( "", "BEGIN;");

        // One table for handling units for numerical questions
        modify_database ("", " CREATE TABLE prefix_quiz_numerical_units (
                               id SERIAL8 PRIMARY KEY,
                               question INT8  NOT NULL default '0',
                               multiplier decimal(40,20) NOT NULL default '1.00000000000000000000',
                               unit varchar(50) NOT NULL default ''
                );" );


        // Four tables for handling distribution and storage of
        // individual data for dataset dependent question types
        modify_database ("", " CREATE TABLE prefix_quiz_attemptonlast_datasets (
                               id SERIAL8 PRIMARY KEY,
                               category INT8  NOT NULL default '0',
                               userid INT8  NOT NULL default '0',
                               datasetnumber INT8  NOT NULL default '0',
                               CONSTRAINT  category UNIQUE (category,userid)
            ) ;");

        modify_database ("", " CREATE TABLE prefix_quiz_dataset_definitions (
                               id SERIAL8 PRIMARY KEY,
                               category INT8  NOT NULL default '0',
                               name varchar(255) NOT NULL default '',
                               type INT8 NOT NULL default '0',
                               options varchar(255) NOT NULL default '',
                               itemcount INT8  NOT NULL default '0'
            ) ; ");

        modify_database ("", " CREATE TABLE prefix_quiz_dataset_items (
                               id SERIAL8 PRIMARY KEY,
                               definition INT8  NOT NULL default '0',
                               number INT8  NOT NULL default '0',
                               value varchar(255) NOT NULL default ''
                             ) ; ");

        modify_database ("", "CREATE INDEX prefix_quiz_dataset_items_definition_idx ON prefix_quiz_dataset_items (definition);");

        modify_database ("", " CREATE TABLE prefix_quiz_question_datasets (
                               id SERIAL8 PRIMARY KEY,
                               question INT8  NOT NULL default '0',
                               datasetdefinition INT8  NOT NULL default '0'
            ) ; ");

        modify_database ("", "CREATE INDEX prefix_quiz_question_datasets_question_datasetdefinition_idx ON prefix_quiz_question_datasets (question,datasetdefinition);");

        // One table for new question type calculated
        //  - the first dataset dependent question type
        modify_database ("", " CREATE TABLE prefix_quiz_calculated (
                               id SERIAL8 PRIMARY KEY,
                               question INT8  NOT NULL default '0',
                               answer INT8  NOT NULL default '0',
                               tolerance varchar(20) NOT NULL default '0.0',
                               tolerancetype INT8 NOT NULL default '1',
                               correctanswerlength INT8 NOT NULL default '2'
                ) ; ");

        modify_database ("", "CREATE INDEX prefix_quiz_calculated_question_idx ON  prefix_quiz_calculated (question);");

        modify_database ( "", "COMMIT;");
    }
    
    return true;
}

?>
