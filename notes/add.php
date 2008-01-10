<?php // $Id$

    require_once('../config.php');
    require_once('lib.php');

/// retrieve parameters
    $courseid      = required_param('course', PARAM_INT);
    $userid        = required_param('user', PARAM_INT);

/// locate course information
    if (!($course = get_record('course', 'id', $courseid))) {
        error('Incorrect course id found');
    }

/// require login to access notes
    require_login($course->id);

/// locate context information
    $context = get_context_instance(CONTEXT_COURSE, $course->id);

/// check capability
    require_capability('moodle/notes:manage', $context);


/// locate user information
    if (!($user = get_record('user', 'id', $userid))) {
        error('Incorrect user id found');
    }

/// build-up form
    require_once('edit_form.php');

/// create form
    $noteform = new note_edit_form();

/// if form was cancelled then return to the previous notes list
    if ($noteform->is_cancelled()) {
        redirect($CFG->wwwroot . '/notes/index.php?course=' . $courseid . '&amp;user=' . $userid);
    }

/// if data was submitted and validated, then save it to database
    if ($formdata = $noteform->get_data()) {
        $note = new object();
        $note->courseid = $formdata->course;
        $note->content = $formdata->content;
        $note->format = FORMAT_PLAIN;
        $note->userid = $formdata->user;
        $note->publishstate = $formdata->publishstate;
        if (note_save($note)) {
            add_to_log($note->courseid, 'notes', 'add', 'index.php?course='.$note->courseid.'&amp;user='.$note->userid . '#note-' . $note->id , 'add note');
        }
        // redirect to notes list that contains this note
        redirect($CFG->wwwroot . '/notes/index.php?course=' . $note->courseid . '&amp;user=' . $note->userid);
    }

    if($noteform->is_submitted()) {
        // if data was submitted with errors, then use it as default for new form
        $note = $noteform->get_submitted_data(false);
    } else {
        // if data was not submitted yet, then use default values
        $note = new object();
        $note->id = 0;
        $note->course = $courseid;
        $note->user = $userid;
        $note->publishstate = optional_param('state', NOTES_STATE_PUBLIC, PARAM_ALPHA);
    }
    $noteform->set_data($note);
    $strnotes = get_string('addnewnote', 'notes');

/// output HTML
    $nav = array();
    if (has_capability('moodle/course:viewparticipants', $context) || has_capability('moodle/site:viewparticipants', get_context_instance(CONTEXT_SYSTEM))) {
        $nav[] = array('name' => get_string('participants'), 'link' => $CFG->wwwroot . '/user/index.php?id=' . $course->id, 'type' => 'misc');
    }
    $nav[] = array('name' => fullname($user), 'link' => $CFG->wwwroot . '/user/view.php?id=' . $user->id. '&amp;course=' . $course->id, 'type' => 'misc');
    $nav[] = array('name' => get_string('notes', 'notes'), 'link' => $CFG->wwwroot . '/notes/index.php?course=' . $course->id . '&amp;user=' . $user->id, 'type' => 'misc');
    $nav[] = array('name' => $strnotes, 'link' => '', 'type' => 'activity');

    print_header($course->shortname . ': ' . $strnotes, $course->fullname, build_navigation($nav));

    print_heading(fullname($user));

    $noteform->display();
    print_footer();
?>
