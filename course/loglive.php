<?php // $Id$
      //  Displays live view of recent logs

    require_once("../config.php");
    require_once("lib.php");

    require_variable($id);

    require_login();

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    if (! isteacher($course->id)) {
        error("Only teachers can view logs");
    }

    session_write_close();

    // we override the default framename so header/footer
    // links open in a new window 
    if (empty($CFG->framename) || $CFG->framename==='_top') {
        $CFG->framename = '_blank';
    }

    $strlivelogs = get_string("livelogs");
    $strupdatesevery = get_string("updatesevery", "moodle", COURSE_LIVELOG_REFRESH);

    print_header("$strlivelogs ($strupdatesevery)", "$strlivelogs", "", "", 
                 '<meta http-equiv="Refresh" content="'.COURSE_LIVELOG_REFRESH.'; url=loglive.php?id='.$id.'" />');

    $user=0;
    $date=time() - 3600;

    print_log($course, $user, $date, "l.time DESC", 0, 500, 
              "loglive.php?id=$course->id&amp;user=$user&amp;date=$date");

    print_footer($course);

    exit;

?>
