<?php  // $Id$

/*************************************************
    ACTIONS handled are:

    closeconversation
    confirmclose
    getsubject
    insertentries
    openconversation
    printdialogue
    showdialogues
    updatesubject

************************************************/

    require_once("../../config.php");
    require_once("lib.php");
    require_once("locallib.php");

    require_variable($id);    // Course Module ID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $dialogue = get_record("dialogue", "id", $cm->instance)) {
        error("Course module dialogue is incorrect");
    }

    require_login($course->id, false, $cm);

    // set up some general variables
    $usehtmleditor = can_use_html_editor();

    $strdialogues = get_string("modulenameplural", "dialogue");
    $strdialogue  = get_string("modulename", "dialogue");

    // ... print the header and...
    print_header_simple("$dialogue->name", "",
                 "<a href=\"index.php?id=$course->id\">$strdialogues</a> ->
                  <a hre=\"view.php?id=$cm->id\">$dialogue->name</a>",
                  "", "", true);


    require_variable($action); // need something to do!

    // vet conversation id, if present
    if (!empty($_REQUEST['cid'])) {
        if ($conversation = get_record("dialogue_conversations", "id", $_REQUEST['cid'])) {
            if (($conversation->userid <> $USER->id) and ($conversation->recipientid <> $USER->id)) {
                error("Dialogue id incorrect");
            }
        } else {
            error("Dialogue: Conversation record not found");
        }
    }

    /************** close conversation ************************************/
    if ($action == 'closeconversation') {
        if (empty($_GET['cid'])) {
            error("Close dialogue: Missing conversation id");
        }
        else {
            $conversationid = $_GET['cid'];
        }
        if (!set_field("dialogue_conversations", "closed", 1, "id", $conversationid)) {
            error("Close dialogue: unable to set closed");
        }
        if (!set_field("dialogue_conversations", "lastid", $USER->id, "id", $conversationid)) {
            error("Close dialogue: unable to set lastid");
        }
        $pane=$_GET['pane'];

        add_to_log($course->id, "dialogue", "closed", "view.php?id=$cm->id", "$conversationid");
        redirect("view.php?id=$cm->id&amp;pane=$pane", get_string("dialogueclosed", "dialogue"));
    }


    /****************** confirm close ************************************/
    elseif ($action == 'confirmclose' ) {

        if (empty($_GET['cid'])) {
            error("Confirm Close: conversation id missing");
        }
        if (!$conversation = get_record("dialogue_conversations", "id", $_GET['cid'])) {
            error("Confirm close: cannot get conversation record");
        }
        if ($conversation->userid == $USER->id) {
            if (!$user = get_record("user", "id", $conversation->recipientid)) {
                error("Confirm Close: cannot get recipient record");
            }
        }
        else {
            if (!$user = get_record("user", "id", $conversation->userid)) {
                error("Confirm Close: cannot get user record");
            }
        }
        $pane = $_GET['pane'];
        notice_yesno(get_string("confirmclosure", "dialogue", fullname($user)),
             "dialogues.php?action=closeconversation&amp;id=$cm->id&amp;cid=$conversation->id&amp;pane=$pane",
             "view.php?id=$cm->id&amp;pane=$pane");
    }

    /****************** get subject ************************************/
    elseif ($action == 'getsubject' ) {

        if (empty($_GET['cid'])) {
            error("Confirm Close: conversation id missing");
        }
        print_heading(get_string("addsubject", "dialogue"));
        echo "<form name=\"getsubjectform\" method=\"post\" action=\"dialogues.php\">\n";
        echo "<input type=\"hidden\" name=\"action\" value=\"updatesubject\"/>\n";
        echo "<input type=\"hidden\" name=\"id\" value=\"".p($_GET[id])."\"/>\n";
        echo "<input type=\"hidden\" name=\"cid\" value=\"".p($_GET[cid])."\"/>\n";
        echo "<input type=\"hidden\" name=\"pane\" value=\"".p($_GET[pane])."\"/>\n";
        echo "<table align=\"center\" border=\"1\" width=\"60%\">\n";
        echo "<tr><td align=\"right\"><b>".get_string("subject", "dialogue")."</b></td>";
        echo "<td><input type=\"text\" size=\"50\" maxsize=\"100\" name=\"subject\"
                value=\"\" /></td></tr>\n";
        echo "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"".
            get_string("addsubject", "dialogue")."\" /></td></tr>\n";
        echo "</table></form>\n";
    }


    /****************** insert conversation entries ******************************/
    elseif ($action == 'insertentries' ) {

        $timenow = time();
        $n = 0;
        // get all the open conversations for this user
        if ($conversations = dialogue_get_conversations($dialogue, $USER, "closed = 0")) {
            foreach ($conversations as $conversation) {
                $textarea_name = "reply$conversation->id";
                $stripped_text = '';
                if (isset($_POST[$textarea_name])) {
                    $stripped_text = strip_tags(trim($_POST[$textarea_name]));
                }
                if ($stripped_text) {
                    unset($item);
                    $item->dialogueid = $dialogue->id;
                    $item->conversationid = $conversation->id;
                    $item->userid = $USER->id;
                    $item->timecreated = time();
                    // reverse the dialogue mail default
                    $item->mailed = !$dialogue->maildefault;
                    $item->text = clean_text($_POST[$textarea_name]);
                    if (!$item->id = insert_record("dialogue_entries", $item)) {
                        error("Insert Entries: Could not insert dialogue record!");
                    }
                    if (!set_field("dialogue_conversations", "lastid", $USER->id, "id", $conversation->id)) {
                        error("Insert Dialogue Entries: could not set lastid");
                    }
                    if (!set_field("dialogue_conversations", "timemodified", $timenow, "id",
                            $conversation->id)) {
                        error("Insert Dialogue Entries: could not set lastid");
                    }
                    // reset seenon time
                    if (!set_field("dialogue_conversations", "seenon", 0, "id",
                            $conversation->id)) {
                        error("Insert Dialogue Entries: could not reset seenon");
                    }
                    add_to_log($course->id, "dialogue", "add entry", "view.php?id=$cm->id", "$item->id");
                    $n++;
                }
            }
        }
        redirect("view.php?id=$cm->id&amp;pane={$_POST['pane']}", get_string("numberofentriesadded",
                    "dialogue", $n));
    }

    /****************** list closed conversations *********************************/
    elseif ($action == 'listclosed') {

        print_simple_box( text_to_html($dialogue->intro) , "center");
        echo "<br />";

        dialogue_list_closed_conversations($dialogue);
    }

    /****************** open conversation ************************************/
    elseif ($action == 'openconversation' ) {

        if (empty($_POST['recipientid'])) {
            redirect("view.php?id=$cm->id", get_string("nopersonchosen", "dialogue"));
        } else {
            $recipientid = $_POST['recipientid'];
            if (substr($recipientid, 0, 1) == 'g') { // it's a group
                $groupid = intval(substr($recipientid, 1));
                if ($groupid) { // it's a real group
                    $recipients = get_records_sql("SELECT u.*
                                FROM {$CFG->prefix}user u,
                                     {$CFG->prefix}groups_members g
                                WHERE g.groupid = $groupid and
                                      u.id = g.userid");
                } else { // it's all participants
                    $recipients = get_course_students($course->id);
                }
            } else {
                $recipients[$recipientid] = get_record("user", "id", $recipientid);
            }
            if ($recipients) {
                $n = 0;
                foreach ($recipients as $recipient) {
                    if ($recipient->id == $USER->id) { // teacher could be member of a group
                        continue;
                    }
                    $stripped_text = strip_tags(trim($_POST['firstentry']));
                    if (!$stripped_text) {
                        redirect("view.php?id=$cm->id", get_string("notextentered", "dialogue"));
                    }
                    unset($conversation);
                    $conversation->dialogueid = $dialogue->id;
                    $conversation->userid = $USER->id;
                    $conversation->recipientid = $recipient->id;
                    $conversation->lastid = $USER->id; // this USER is adding an entry too
                    $conversation->timemodified = time();
                    $conversation->subject = clean_text($_POST['subject']); // may be blank
                    if (!$conversation->id = insert_record("dialogue_conversations", $conversation)) {
                        error("Open dialogue: Could not insert dialogue record!");
                    }
                    add_to_log($course->id, "dialogue", "open", "view.php?id=$cm->id", "$dialogue->id");

                    // now add the entry
                    unset($entry);
                    $entry->dialogueid = $dialogue->id;
                    $entry->conversationid = $conversation->id;
                    $entry->userid = $USER->id;
                    $entry->timecreated = time();
                    // reverse the dialogue default value
                    $entry->mailed = !$dialogue->maildefault;
                    $entry->text = clean_text($_POST['firstentry']);
                    if (!$entry->id = insert_record("dialogue_entries", $entry)) {
                        error("Insert Entries: Could not insert dialogue record!");
                    }
                    add_to_log($course->id, "dialogue", "add entry", "view.php?id=$cm->id", "$entry->id");
                    $n++;
                }
                print_heading(get_string("numberofentriesadded", "dialogue", $n));
            } else {
                redirect("view.php?id=$cm->id", get_string("noavailablepeople", "dialogue"));
            }
            if (isset($groupid)) {
                if ($groupid) { // a real group
                    if (!$group = get_record("groups", "id", $groupid)) {
                        error("Dialogue open conversation: Group not found");
                    }
                    redirect("view.php?id=$cm->id", get_string("dialogueopened", "dialogue", $group->name));
                } else { // all participants
                    redirect("view.php?id=$cm->id", get_string("dialogueopened", "dialogue",
                                get_string("allparticipants")));
                }
            } else {
                if (!$user =  get_record("user", "id", $conversation->recipientid)) {
                    error("Open dialogue: user record not found");
                }
                redirect("view.php?id=$cm->id", get_string("dialogueopened", "dialogue", fullname($user) ));
            }
        }
    }


    /****************** print dialogue (allowing new entry)********************/
    elseif ($action == 'printdialogue') {

        // if (!$conversation = get_record("dialogue_conversations", "id", $_GET['cid'])) {
        //     error("Print Dialogue: can not get conversation record");
        // }

        print_simple_box( text_to_html($dialogue->intro) , "center");
        echo "<br />";

        dialogue_print_conversation($dialogue, $conversation);
    }


    /****************** show dialogues ****************************************/
    elseif ($action == 'showdialogues') {

        if (!$conversation = get_record("dialogue_conversations", "id", $_GET['cid'])) {
            error("Show Dialogue: can not get conversation record");
        }

        print_simple_box( text_to_html($dialogue->intro) , "center");
        echo "<br />";

        dialogue_show_conversation($dialogue, $conversation);
        dialogue_show_other_conversations($dialogue, $conversation);
    }


    /****************** update subject ****************************************/
    elseif ($action == 'updatesubject') {

        if (!$conversation = get_record("dialogue_conversations", "id", $_POST['cid'])) {
            error("Update Subject: can not get conversation record");
        }

        if (!$_POST['subject']) {
            redirect("view.php?id=$cm->id&amp;pane=$_POST[pane]", get_string("nosubject", "dialogue"));
        } elseif (!set_field("dialogue_conversations", "subject", clean_text($_POST['subject']), "id", $_POST['cid'])) {
            error("Update subject: could not update conversation record");
        }
        redirect("view.php?id=$cm->id&amp;pane=$_POST[pane]", get_string("subjectadded", "dialogue"));
    }


    /*************** no man's land **************************************/
    else {
        error("Fatal Error: Unknown Action: ".$action."\n");
    }

    print_footer($course);

?>
