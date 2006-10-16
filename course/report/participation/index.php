<?php  // $Id$

    require_once('../../../config.php');
    require_once($CFG->libdir.'/statslib.php');

    define('DEFAULT_PAGE_SIZE', 20);
    define('SHOW_ALL_PAGE_SIZE', 5000);
    
    $id         = required_param('id', PARAM_INT); // course id.
    $moduleid   = optional_param('moduleid', 0, PARAM_INT); // module id.
    $oldmod     = optional_param('oldmod', 0, PARAM_INT);
    $roleid     = optional_param('roleid',0,PARAM_INT); // which role to show
    $instanceid = optional_param('instanceid', 0, PARAM_INT); // instance we're looking at.
    $timefrom   = optional_param('timefrom', 0, PARAM_INT); // how far back to look...
    $action     = optional_param('action', '', PARAM_ALPHA);
    $page       = optional_param('page', 0, PARAM_INT);                     // which page to show
    $perpage    = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT);  // how many per page

    if ($action != 'view' && $action != 'post') { 
        $action = ''; // default to all (don't restrict)
    }

    // reset instance if changing module.
    if (!empty($moduleid) && !empty($oldmod) && $moduleid != $oldmod) {
        $instanceid = 0;
    }

    if (!$course = get_record('course','id',$id)) {
        print_error('invalidcourse');
    }

    if ($roleid != 0 && !$role = get_record('role','id',$roleid)) {
        print_error('invalidrole');
    }

    require_login($course->id);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    
    if (!has_capability('moodle/site:viewreports', $context)) {
        print_error('mustbeteacher', '', $CFG->wwwroot.'/course/view.php?id='.$course->id);
    }
    

    add_to_log($course->id, "course", "report participation", "report/participation/index.php?id=$course->id", $course->id); 
    
    $strparticipation = get_string('participationreport');
    $strviews         = get_string('views');
    $strposts         = get_string('posts');
    $strview          = get_string('view');
    $strpost          = get_string('post');
    $strallactions    = get_string('allactions');
    $strreports       = get_string('reports');

    $strnav = "<a href=\"../../view.php?id=$course->id\">$course->shortname</a> -> 
               <a href=\"../../report.php?id=$course->id\">$strreports</a> -> ". $strparticipation;
    
    print_header("$course->shortname: $strparticipation", "$course->fullname",
                 "<a href=\"../../view.php?id=$course->id\">$course->shortname</a> ->
                  <a href=\"../../report.php?id=$course->id\">$strreports</a> ->
                  $strparticipation");

//    print_header($course->fullname.' '.$strparticipation,$strparticipation,$strnav);

    $allowedmodules = array('assignment','book','chat','choice','exercise','forum','glossary','hotpot',
                            'journal','lesson','questionnaire','quiz','resource','scorm',
                            'survey','wiki','workshop'); // some don't make sense here - eg 'label'

    if (!$modules = get_records_sql('SELECT DISTINCT module,name FROM '.$CFG->prefix.'course_modules cm JOIN '.
                                    $CFG->prefix.'modules m ON cm.module = m.id WHERE course = '.$course->id)) {
        print_error('noparticipatorycms', '', $CFG->wwwroot.'/course/view.php?id='.$course->id);
    }


    $modoptions = array();
    foreach ($modules as $m) {
        if (in_array($m->name,$allowedmodules)) {
            $modoptions[$m->module] = get_string('modulename',$m->name);
        }
    }

    $timeoptions = array();
    // get minimum log time for this course
    $minlog = get_field_sql('SELECT min(time) FROM '.$CFG->prefix.'log WHERE course = '.$course->id);

    $now = usergetmidnight(time());

    // days
    for ($i = 1; $i < 7; $i++) {
        if (strtotime('-'.$i.' days',$now) >= $minlog) {
            $timeoptions[strtotime('-'.$i.' days',$now)] = get_string('numdays','moodle',$i);
        }
    }
    // weeks
    for ($i = 1; $i < 10; $i++) {
        if (strtotime('-'.$i.' weeks',$now) >= $minlog) {
            $timeoptions[strtotime('-'.$i.' weeks',$now)] = get_string('numweeks','moodle',$i);
        }
    }
    // months 
    for ($i = 2; $i < 12; $i++) {
        if (strtotime('-'.$i.' months',$now) >= $minlog) {
            $timeoptions[strtotime('-'.$i.' months',$now)] = get_string('nummonths','moodle',$i);
        }
    }
    // try a year
    if (strtotime('-1 year',$now) >= $minlog) {
        $timeoptions[strtotime('-1 year',$now)] = get_string('lastyear');
    }

    $useroptions = array();
    if ($roles = get_roles_on_exact_context(get_context_instance(CONTEXT_COURSE,$course->id))) {
        foreach ($roles as $r) {
            $useroptions[$r->id] = $r->name;
        }
    }
    $actionoptions = array('' => $strallactions,
                           'view' => $strview,
                           'post' => $strpost,
                           );
    
    
    // print first controls.
    echo '<form action="index.php" method="get">'."\n".
        '<input type="hidden" name="id" value="'.$course->id.'" />'."\n".
        '<input type="hidden" name="oldmod" value="'.$moduleid.'" />'."\n".
        '<input type="hidden" name="instanceid" value="'.$instanceid.'" />'."\n".
        '<table align="center" cellpadding="10"><tr>'."\n".
        "<td>\n".
        get_string('activitymodule').'&nbsp;';
    choose_from_menu($modoptions,'moduleid',$moduleid);
    echo "</td><td>\n".
        get_string('lookback').'&nbsp;';
    choose_from_menu($timeoptions,'timefrom',$timefrom);
    echo "</td><td>".
        get_string('showonly').'&nbsp;';
    choose_from_menu($useroptions,'roleid',$roleid,'');
    echo "</td><td>\n".
        get_string('showactions'),'&nbsp;';
    choose_from_menu($actionoptions,'action',$action,'');
    echo '</td><td>';
    helpbutton('participationreport',get_string('participationreport'));
    echo 
        '<input type="submit" value="'.get_string('go').'" />'."\n".
        "</td></tr></table>\n".
        "</form>\n";

    if (empty($moduleid)) {
        notify(get_string('selectamodule'));
        print_footer();
        exit;
    }
    
    $baseurl =  $CFG->wwwroot.'/course/report/participation/index.php?id='.$course->id.'&amp;roleid='
        .$roleid.'&amp;instanceid='.$instanceid.'&amp;timefrom='.$timefrom.'&amp;moduleid='
        .$moduleid.'&action='.$action.'&amp;perpage='.$perpage;


    // from here assume we have at least the module we're using.
    $module = get_record('modules','id',$moduleid);
    $modulename = get_string('modulename',$module->name);

    require_once($CFG->dirroot.'/mod/'.$module->name.'/lib.php');

    $viewfun = $module->name.'_get_view_actions';
    $postfun = $module->name.'_get_post_actions';

    if (!function_exists($viewfun) || !function_exists($postfun)) {
        error(get_string('modulemissingcode','error',$module->name),$baseurl);
    }

    $viewnames = $viewfun();
    $postnames = $postfun();

    // get all instances of this module in the course.
    if (!$instances = get_all_instances_in_course($module->name,$course)) {
        error(get_string('noinstances','error',$modulename));
    }
    
    $instanceoptions = array();
    
    foreach ($instances as $instance) {
        $instanceoptions[$instance->id] = $instance->name;
    }
    
    if (count($instanceoptions) == 1) { // just display it if there's only one.
        $instanceid = array_pop(array_keys($instanceoptions));
    }

    echo '<form action="'.$CFG->wwwroot.'/course/report/participation/index.php" method="post">'.
        '<input type="hidden" name="id" value="'.$course->id.'" />'."\n".
        '<input type="hidden" name="oldmod" value="'.$moduleid.'" />'."\n".
        '<input type="hidden" name="timefrom" value="'.$timefrom.'" />'."\n".
        '<input type="hidden" name="action" value="'.$action.'" />'."\n".
        '<input type="hidden" name="roleid" value="'.$roleid.'" />'."\n".
        '<input type="hidden" name="moduleid" value="'.$moduleid.'" />'."\n".
        '<table align="center"><tr><td>';
    choose_from_menu($instanceoptions,'instanceid',$instanceid);
    echo '<input type="submit" value="'.get_string('go').'" /></td></tr></table>'."\n".
        "</form>\n";
        
    if (!empty($instanceid) && !empty($roleid)) {
        if (!$cm = get_coursemodule_from_instance($module->name,$instanceid,$course->id)) {
            print_error('cmunknown');
        }

        require_once($CFG->dirroot.'/lib/tablelib.php');
        $table = new flexible_table('course-participation-'.$course->id.'-'.$cm->id.'-'.$roleid);
        $table->course = $course;

        $table->define_columns(array('fullname','count',''));
        $table->define_headers(array(get_string('user'),((!empty($action)) ? get_string($action) : get_string('allactions')),get_string('select')));
        $table->define_baseurl($baseurl);

        $table->set_attribute('align','center');
        $table->set_attribute('cellpadding','5');
        $table->set_attribute('class', 'generaltable generalbox');

        $table->sortable(true,'lastname','ASC');
        
        $table->set_control_variables(array(
                                            TABLE_VAR_SORT    => 'ssort',
                                            TABLE_VAR_HIDE    => 'shide',
                                            TABLE_VAR_SHOW    => 'sshow',
                                            TABLE_VAR_IFIRST  => 'sifirst',
                                            TABLE_VAR_ILAST   => 'silast',
                                            TABLE_VAR_PAGE    => 'spage'
                                            ));
        $table->setup();
        

        $primary_roles = sql_primary_role_subselect();   // In dmllib.php
        $sql = 'SELECT DISTINCT prs.userid, u.firstname,u.lastname,u.idnumber,count(l.action) as count FROM ('.$primary_roles.') prs'
            .' JOIN '.$CFG->prefix.'user u ON u.id = prs.userid LEFT JOIN '.$CFG->prefix.'log l ON prs.userid = l.userid '
            .' AND prs.courseid = l.course AND l.time > '.$timefrom.' AND l.course = '.$course->id.' AND l.module = \''.$module->name.'\' '
            .' AND l.cmid = '.$cm->id;
        switch ($action) {
            case 'view':
                $sql .= ' AND action IN (\''.implode('\',\'',$viewnames).'\' )';
                break;
            case 'post':
                $sql .= ' AND action IN (\''.implode('\',\'',$postnames).'\' )';
                break;
            default:
                // some modules have stuff we want to hide, ie mail blocked etc so do actually need to limit here.
                $sql .= ' AND action IN (\''.implode('\',\'',array_merge($viewnames,$postnames)).'\' )'; 

        }
        
        $sql .= ' WHERE prs.courseid = '.$course->id.' AND prs.primary_roleid = '.$roleid.' AND prs.contextlevel = '.CONTEXT_COURSE.' AND prs.courseid = '.$course->id;
        
        if ($table->get_sql_where()) {
            $sql .= ' AND '.$table->get_sql_where(); //initial bar 
        }

        $sql .= ' GROUP BY prs.userid,u.firstname,u.lastname,u.idnumber,l.userid';

        if ($table->get_sql_sort()) {
            $sql .= ' ORDER BY '.$table->get_sql_sort();
        }

        $countsql = 'SELECT COUNT(DISTINCT(prs.userid)) FROM ('.$primary_roles.') prs '
            .' JOIN '.$CFG->prefix.'user u ON u.id = prs.userid WHERE prs.courseid = '.$course->id 
            .' AND prs.primary_roleid = '.$roleid.' AND prs.contextlevel = '.CONTEXT_COURSE;
        
        $totalcount = count_records_sql($countsql);

        if ($table->get_sql_where()) {
            $matchcount = count_records_sql($countsql.' AND '.$table->get_sql_where());
        } else {
            $matchcount = $totalcount;
        }

        echo '<p align="center"><b>'.$modulename . ' ' . $strviews.': '.implode(', ',$viewnames).'<br />'
            . $modulename . ' ' . $strposts.': '.implode(', ',$postnames).'</b></p>';
 
        $table->initialbars($totalcount > $perpage);
        $table->pagesize($perpage, $matchcount);

        if($table->get_page_start() !== '' && $table->get_page_size() !== '') {
            $sql .= ' '.sql_paging_limit($table->get_page_start(), $table->get_page_size());
        }

        if (!$users = get_records_sql($sql)) {
            $users = array(); // tablelib will handle saying 'Nothing to display' for us.
        }

        $data = array();
        
        $a->count = $totalcount;
        $a->items = $role->name;
        
        if ($matchcount != $totalcount) {
            $a->items .= ' ('.get_string('matched').' '.$matchcount.')';
        }
        
        echo '<h2>'.get_string('counteditems', '', $a).'</h2>';
        echo '
<script Language="JavaScript">
<!--
function checksubmit(form) {
    var destination = form.formaction.options[form.formaction.selectedIndex].value;
    if (destination == "" || !checkchecked(form)) {
        form.formaction.selectedIndex = 0;
        return false;
    } else {
        return true;
    }
}

function checkchecked(form) {
    var inputs = document.getElementsByTagName(\'INPUT\');
    var checked = false;
    inputs = filterByParent(inputs, function() {return form;});
    for(var i = 0; i < inputs.length; ++i) {
        if(inputs[i].type == \'checkbox\' && inputs[i].checked) {
            checked = true;
        }
    }
    return checked;
}

function checknos() {
    void(d=document);
    void(el=d.getElementsByTagName(\'INPUT\'));
    for(i=0;i<el.length;i++) {
        if (el[i].value == 0) {
            void(el[i].checked=1)
        }
    }
}

//-->
</script>
';
        echo '<form action="'.$CFG->wwwroot.'/user/action_redir.php" method="post" name="studentsform" onSubmit="return checksubmit(this);">';
        echo '<input type="hidden" name="id" value="'.$id.'" />';
        echo '<input type="hidden" name="returnto" value="'.$_SERVER['REQUEST_URI'].'" />';
        echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';

        foreach ($users as $u) {
            $data = array('<a href="'.$CFG->wwwroot.'/user/view.php?id='.$u->userid.'&course='.$course->id.'">'.fullname($u,true).'</a>',
                          ((!empty($u->count)) ? get_string('yes').' ('.$u->count.') ' : get_string('no')),
                          '<input type="checkbox" name="user'.$u->userid.'" value="'.$u->count.'" />',
                          );
            $table->add_data($data);
        }

        $table->print_html();

        if ($perpage == SHOW_ALL_PAGE_SIZE) {
            echo '<div id="showall"><a href="'.$baseurl.'&amp;perpage='.DEFAULT_PAGE_SIZE.'">'.get_string('showperpage', '', DEFAULT_PAGE_SIZE).'</a></div>';
        }
        else if ($matchcount > 0 && $perpage < $matchcount) {
            echo '<div id="showall"><a href="'.$baseurl.'&amp;perpage='.SHOW_ALL_PAGE_SIZE.'">'.get_string('showall', '', $matchcount).'</a></div>';
        }
    
        echo '<br /><center>';
        echo '<input type="button" onclick="checkall()" value="'.get_string('selectall').'" /> ';
        echo '<input type="button" onclick="checknone()" value="'.get_string('deselectall').'" /> ';
        if ($perpage >= $matchcount) {
            echo '<input type="button" onclick="checknos()" value="'.get_string('selectnos').'" />';
        }
        $displaylist['messageselect.php'] = get_string('messageselectadd');
        choose_from_menu ($displaylist, "formaction", "", get_string("withselectedusers"), "if(checksubmit(this.form))this.form.submit();", "");
        helpbutton("participantswithselectedusers", get_string("withselectedusers"));
        echo '<input type="submit" value="' . get_string('ok') . '"';
        echo '</center></form>';
    }
    
    print_footer();

?>
