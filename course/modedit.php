<?php // $Id$

//  adds or updates modules in a course using new formslib

    require_once("../config.php");
    require_once("lib.php");
    require_once($CFG->libdir.'/gradelib.php');

    require_login();

    $add    = optional_param('add', 0, PARAM_ALPHA);
    $update = optional_param('update', 0, PARAM_INT);
    $return = optional_param('return', 0, PARAM_BOOL); //return to course/view.php if false or mod/modname/view.php if true
    $type   = optional_param('type', '', PARAM_ALPHANUM);

    if (!empty($add)) {
        $section = required_param('section', PARAM_INT);
        $course = required_param('course', PARAM_INT);

        if (! $course = get_record("course", "id", $course)) {
            error("This course doesn't exist");
        }

        require_login($course);
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        require_capability('moodle/course:manageactivities', $context);

        if (! $module = get_record("modules", "name", $add)) {
            error("This module type doesn't exist");
        }

        $cw = get_course_section($section, $course->id);

        if (!course_allowed_module($course, $module->id)) {
            error("This module has been disabled for this particular course");
        }

        $cm = null;

        $form->section          = $section;  // The section number itself - relative!!! (section column in course_sections)
        $form->visible          = $cw->visible;
        $form->course           = $course->id;
        $form->module           = $module->id;
        $form->modulename       = $module->name;
        $form->groupmode        = $course->groupmode;
        $form->groupingid       = $course->defaultgroupingid;
        $form->groupmembersonly = 0;
        $form->instance         = '';
        $form->coursemodule     = '';
        $form->add              = $add;
        $form->return           = 0; //must be false if this is an add, go back to course view on cancel

        if (!empty($type)) {
            $form->type = $type;
        }

        $sectionname = get_section_name($course->format);
        $fullmodulename = get_string("modulename", $module->name);

        if ($form->section && $course->format != 'site') {
            $heading->what = $fullmodulename;
            $heading->to   = "$sectionname $form->section";
            $pageheading = get_string("addinganewto", "moodle", $heading);
        } else {
            $pageheading = get_string("addinganew", "moodle", $fullmodulename);
        }

        $CFG->pagepath = 'mod/'.$module->name;
        if (!empty($type)) {
            $CFG->pagepath .= '/'.$type;
        } else {
            $CFG->pagepath .= '/mod';
        }

        $navlinksinstancename = '';
    } else if (!empty($update)) {
        if (! $cm = get_record("course_modules", "id", $update)) {
            error("This course module doesn't exist");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("This course doesn't exist");
        }

        require_login($course); // needed to setup proper $COURSE
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        require_capability('moodle/course:manageactivities', $context);

        if (! $module = get_record("modules", "id", $cm->module)) {
            error("This module doesn't exist");
        }

        if (! $form = get_record($module->name, "id", $cm->instance)) {
            error("The required instance of this module doesn't exist");
        }

        if (! $cw = get_record("course_sections", "id", $cm->section)) {
            error("This course section doesn't exist");
        }

        $form->coursemodule     = $cm->id;
        $form->section          = $cw->section;  // The section number itself - relative!!! (section column in course_sections)
        $form->visible          = $cm->visible; //??  $cw->visible ? $cm->visible : 0; // section hiding overrides
        $form->cmidnumber       = $cm->idnumber;          // The cm IDnumber
        $form->groupmode        = groups_get_activity_groupmode($cm); // locked later if forced
        $form->groupingid       = $cm->groupingid;
        $form->groupmembersonly = $cm->groupmembersonly;
        $form->course           = $course->id;
        $form->module           = $module->id;
        $form->modulename       = $module->name;
        $form->instance         = $cm->instance;
        $form->return           = $return;
        $form->update           = $update;

        // add existing outcomes
        if ($items = grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$form->modulename,
                                           'iteminstance'=>$form->instance, 'courseid'=>$COURSE->id))) {
            foreach ($items as $item) {
                if (!empty($item->outcomeid)) {
                    $form->{'outcome_'.$item->outcomeid} = 1;
                }
            }
        }

        $sectionname = get_section_name($course->format);
        $fullmodulename = get_string("modulename", $module->name);

        if ($form->section && $course->format != 'site') {
            $heading->what = $fullmodulename;
            $heading->in   = "$sectionname $cw->section";
            $pageheading = get_string("updatingain", "moodle", $heading);
        } else {
            $pageheading = get_string("updatinga", "moodle", $fullmodulename);
        }

        $navlinksinstancename = array('name' => format_string($form->name,true), 'link' => "$CFG->wwwroot/mod/$module->name/view.php?id=$cm->id", 'type' => 'activityinstance');

        $CFG->pagepath = 'mod/'.$module->name;
        if (!empty($type)) {
            $CFG->pagepath .= '/'.$type;
        } else {
            $CFG->pagepath .= '/mod';
        }
    } else {
        error('Invalid operation.');
    }

    $modmoodleform = "$CFG->dirroot/mod/$module->name/mod_form.php";
    if (file_exists($modmoodleform)) {
        require_once($modmoodleform);

    } else {
        error('No formslib form description file found for this activity.');
    }

    $modlib = "$CFG->dirroot/mod/$module->name/lib.php";
    if (file_exists($modlib)) {
        include_once($modlib);
    } else {
        error("This module is missing important code! ($modlib)");
    }

    $mformclassname = 'mod_'.$module->name.'_mod_form';
    $mform =& new $mformclassname($form->instance, $cw->section, $cm);
    $mform->set_data($form);

    if ($mform->is_cancelled()) {
        if ($return && !empty($cm->id)){
            redirect("$CFG->wwwroot/mod/$module->name/view.php?id=$cm->id");
        } else {
            redirect("view.php?id=$course->id#section-".$cw->section);
        }
    } else if ($fromform = $mform->get_data()) {
        if (empty($fromform->coursemodule)) { //add
            $cm = null;
            if (! $course = get_record("course", "id", $fromform->course)) {
                error("This course doesn't exist");
            }
            $fromform->instance = '';
            $fromform->coursemodule = '';
        } else { //update
            if (! $cm = get_record("course_modules", "id", $fromform->coursemodule)) {
                error("This course module doesn't exist");
            }

            if (! $course = get_record("course", "id", $cm->course)) {
                error("This course doesn't exist");
            }
            $fromform->instance = $cm->instance;
            $fromform->coursemodule = $cm->id;
        }

        require_login($course->id); // needed to setup proper $COURSE

        if (!empty($fromform->coursemodule)) {
            $context = get_context_instance(CONTEXT_MODULE, $fromform->coursemodule);
        } else {
            $context = get_context_instance(CONTEXT_COURSE, $course->id);
        }
        require_capability('moodle/course:manageactivities', $context);

        $fromform->course = $course->id;
        $fromform->modulename = clean_param($fromform->modulename, PARAM_SAFEDIR);  // For safety

        $addinstancefunction    = $fromform->modulename."_add_instance";
        $updateinstancefunction = $fromform->modulename."_update_instance";

        if (!isset($fromform->groupingid)) {
            $fromform->groupingid = 0;
        }

        if (!isset($fromform->groupmembersonly)) {
            $fromform->groupmembersonly = 0;
        }

        if (!isset($fromform->name)) { //label
            $fromform->name = $fromform->modulename;
        }

        if (!empty($fromform->update)) {

            if (!empty($course->groupmodeforce) or !isset($fromform->groupmode)) {
                $fromform->groupmode = $cm->groupmode; // keep original
            }

            $returnfromfunc = $updateinstancefunction($fromform);
            if (!$returnfromfunc) {
                error("Could not update the $fromform->modulename", "view.php?id=$course->id");
            }
            if (is_string($returnfromfunc)) {
                error($returnfromfunc, "view.php?id=$course->id");
            }

            set_coursemodule_visible($fromform->coursemodule, $fromform->visible);
            set_coursemodule_groupmode($fromform->coursemodule, $fromform->groupmode);
            set_coursemodule_groupingid($fromform->coursemodule, $fromform->groupingid);
            set_coursemodule_groupmembersonly($fromform->coursemodule, $fromform->groupmembersonly);

            if (isset($fromform->cmidnumber)) { //label
                // set cm idnumber
                set_coursemodule_idnumber($fromform->coursemodule, $fromform->cmidnumber);
            }

            add_to_log($course->id, "course", "update mod",
                       "../mod/$fromform->modulename/view.php?id=$fromform->coursemodule",
                       "$fromform->modulename $fromform->instance");
            add_to_log($course->id, $fromform->modulename, "update",
                       "view.php?id=$fromform->coursemodule",
                       "$fromform->instance", $fromform->coursemodule);

        } else if (!empty($fromform->add)){

            if (!empty($course->groupmodeforce) or !isset($fromform->groupmode)) {
                $fromform->groupmode = 0; // do not set groupmode
            }

            if (!course_allowed_module($course,$fromform->modulename)) {
                error("This module ($fromform->modulename) has been disabled for this particular course");
            }

            $returnfromfunc = $addinstancefunction($fromform);
            if (!$returnfromfunc) {
                error("Could not add a new instance of $fromform->modulename", "view.php?id=$course->id");
            }
            if (is_string($returnfromfunc)) {
                error($returnfromfunc, "view.php?id=$course->id");
            }

            $fromform->instance = $returnfromfunc;

            // course_modules and course_sections each contain a reference
            // to each other, so we have to update one of them twice.

            if (! $fromform->coursemodule = add_course_module($fromform) ) {
                error("Could not add a new course module");
            }
            if (! $sectionid = add_mod_to_section($fromform) ) {
                error("Could not add the new course module to that section");
            }

            if (! set_field("course_modules", "section", $sectionid, "id", $fromform->coursemodule)) {
                error("Could not update the course module with the correct section");
            }

            // make sure visibility is set correctly (in particular in calendar)
            set_coursemodule_visible($fromform->coursemodule, $fromform->visible);

            if (isset($fromform->cmidnumber)) { //label
                // set cm idnumber
                set_coursemodule_idnumber($fromform->coursemodule, $fromform->cmidnumber);
            }

            add_to_log($course->id, "course", "add mod",
                       "../mod/$fromform->modulename/view.php?id=$fromform->coursemodule",
                       "$fromform->modulename $fromform->instance");
            add_to_log($course->id, $fromform->modulename, "add",
                       "view.php?id=$fromform->coursemodule",
                       "$fromform->instance", $fromform->coursemodule);
        } else {
            error("Data submitted is invalid.");
        }

        //sync idnumber with grade_item
        if ($grade_item = grade_item::fetch(array('itemtype'=>'mod', 'itemmodule'=>$fromform->modulename,
                     'iteminstance'=>$fromform->instance, 'itemnumber'=>0, 'courseid'=>$COURSE->id))) {
            if ($grade_item->idnumber != $fromform->cmidnumber) {
                $grade_item->idnumber = $fromform->cmidnumber;
                $grade_item->update();
            }
        }

        // add outcomes if requested
        if ($outcomes = grade_outcome::fetch_all_available($COURSE->id)) {
            $grade_items = array();

            foreach($outcomes as $outcome) {
                $elname = 'outcome_'.$outcome->id;

                if (array_key_exists($elname, $fromform) and $fromform->$elname) {
                    // we have a request for new outcome grade item
                    $grade_item = new grade_item();

                    // Outcome grade_item.itemnumber start at 1000
                    $max_itemnumber = 999;
                    if ($items = grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$fromform->modulename,
                                 'iteminstance'=>$fromform->instance, 'courseid'=>$COURSE->id))) {
                        $exists = false;
                        foreach($items as $item) {
                            if ($item->outcomeid == $outcome->id) {
                                $exists = true;
                                break;
                            }
                            if (empty($item->outcomeid)) {
                                continue;
                            }
                            if ($item->itemnumber > $max_itemnumber) {
                                $max_itemnumber = $item->itemnumber;
                            }
                        }
                    }
                    if ($exists) {
                        continue;
                    }
                    $grade_item->courseid     = $COURSE->id;
                    $grade_item->itemtype     = 'mod';
                    $grade_item->itemmodule   = $fromform->modulename;
                    $grade_item->iteminstance = $fromform->instance;
                    $grade_item->itemnumber   = $max_itemnumber + 1;
                    $grade_item->itemname     = $outcome->fullname;
                    $grade_item->outcomeid    = $outcome->id;
                    $grade_item->gradetype    = GRADE_TYPE_SCALE;
                    $grade_item->scaleid      = $outcome->scaleid;

                    $grade_item->insert();

                    // TODO comment on these next 4 lines
                    if ($item = grade_item::fetch(array('itemtype'=>'mod', 'itemmodule'=>$grade_item->itemmodule,
                                 'iteminstance'=>$grade_item->iteminstance, 'itemnumber'=>0, 'courseid'=>$COURSE->id))) {
                        $grade_item->set_parent($item->categoryid);
                        $grade_item->move_after_sortorder($item->sortorder);
                    }
                    $grade_items[] = $grade_item;
                }
            }

            // Create a grade_category to represent this module, if outcomes have been attached
            if (!empty($grade_items)) {
                // Add the module's normal grade_item as a child of this category
                $item_params = array('itemtype'=>'mod',
                                     'itemmodule'=>$fromform->modulename,
                                     'iteminstance'=>$fromform->instance,
                                     'itemnumber'=>0,
                                     'courseid'=>$COURSE->id);
                $item = grade_item::fetch($item_params);

                // Only create the category if it will contain at least 2 items
                if ($item OR count($grade_items) > 1) { // If we are here it means there is at least 1 outcome
                    $cat_params = array('courseid'=>$COURSE->id, 'fullname'=>$fromform->name);
                    $grade_category = grade_category::fetch($cat_params);

                    if (!$grade_category) {
                        $grade_category = new grade_category($cat_params);
                        $grade_category->courseid = $COURSE->id;
                        $grade_category->fullname = $fromform->name;
                        $grade_category->insert();
                    }

                    $sortorder = $grade_category->sortorder;

                    if ($item) {
                        $item->set_parent($grade_category->id);
                        $sortorder = $item->sortorder;
                    }

                    // Add the outcomes as children of this category
                    foreach ($grade_items as $gi) {
                        $gi->set_parent($grade_category->id);
                        $gi->move_after_sortorder($sortorder);
                    }
                }
            }
        }

        rebuild_course_cache($course->id);

        redirect("$CFG->wwwroot/mod/$module->name/view.php?id=$fromform->coursemodule");
        exit;

    } else {
        if (!empty($cm->id)) {
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        } else {
            $context = get_context_instance(CONTEXT_COURSE, $course->id);
        }
        require_capability('moodle/course:manageactivities', $context);

        $streditinga = get_string("editinga", "moodle", $fullmodulename);
        $strmodulenameplural = get_string("modulenameplural", $module->name);

        $navlinks = array();
        $navlinks[] = array('name' => $strmodulenameplural, 'link' => "$CFG->wwwroot/mod/$module->name/index.php?id=$course->id", 'type' => 'activity');
        if ($navlinksinstancename) {
            $navlinks[] = $navlinksinstancename;
        }
        $navlinks[] = array('name' => $streditinga, 'link' => '', 'type' => 'title');

        $navigation = build_navigation($navlinks);

        print_header_simple($streditinga, '', $navigation, $mform->focus(), "", false);

        if (!empty($cm->id)) {
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);
            $currenttab = 'update';
            include_once($CFG->dirroot.'/'.$CFG->admin.'/roles/tabs.php');
        }
        $icon = '<img src="'.$CFG->modpixpath.'/'.$module->name.'/icon.gif" alt=""/>';

        print_heading_with_help($pageheading, "mods", $module->name, $icon);
        $mform->display();
        print_footer($course);
    }
?>
