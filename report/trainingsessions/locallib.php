<?php

/**
* decodes a course structure giving an ordered and
* recursive image of the course.
* The course structure will recognize topic, weekly and flexipage
* course format, keeping an accurate image of the course ordering.
*
* @param int $courseid
* @param reference $itemcount a recursive propagating counter in case of flexipage
* or recursive content.
* @return a complex structure representing the course organisation
*/
function reports_get_course_structure($courseid, &$itemcount){
    global $CFG, $DB;
    
    $structure = array();

    if (!$course = $DB->get_record('course', array('id' => $courseid))){
        print_error('errorbadcoursestructure', 'report_trainingsessions', $courseid);
    }
    
    if ($course->format == 'page'){
        include_once $CFG->dirroot.'/course/format/page/lib.php';
        // get first top level page (contains course structure)
        if (!$pages = $DB->get_records_select('format_page', " courseid = $course->id AND parent = 0 ", 'sortorder')){
            print_error('errorcoursestructurefirstpage', 'report_trainingsessions');        
        }
        $structure = array();
        foreach($pages as $key => $page){
            if (!($page->display & DISP_PUBLISH)) continue;
            
            $pageelement = new StdClass;
            $pageelement->type = 'page';
            $pageelement->name = $page->nametwo;
            
            $pageelement->subs = page_get_structure_from_page($page, $itemcount);
            $structure[] = $pageelement;
        }
    } else {
        // browse through course_sections and collect course items.
        $structure = array();

        if ($sections = $DB->get_records("course_sections", array('course' => $courseid), 'section ASC')) {
            foreach ($sections as $section) {
                $element = new StdClass;
                $element->type = 'section';
                $element->plugintype = 'section';
                $element->instance = $section;
                $element->instance->visible = $section->visible;
                $element->id = $section->id;
                //shall we try to capture any title in there ?
                if (preg_match('/<h[1-7][^>]*?>(.*?)<\\/h[1-7][^>]*?>/i', $section->summary, $matches)){
                    $element->name = $matches[1];
                } else {
                    if ($section->section){
                        $element->name = get_string('section').' '.$section->section ;
                    } else {
                        $element->name = get_string('headsection', 'report_trainingsessions') ;
                    }
                }

                if (!empty($section->sequence)) {
                    $element->subs = array();
                    $sequence = explode(",", $section->sequence);
                    foreach ($sequence as $seq) {
                       	if (!$cm = $DB->get_record('course_modules', array('id' => $seq))){
                       		// if (debugging()) notify("missing module of id $seq");
                       		continue;
                    	}
                       $module = $DB->get_record('modules', array('id' => $cm->module));
                       if (preg_match('/label$/', $module->name)) continue; // discard all labels
                       $moduleinstance = $DB->get_record($module->name, array('id' => $cm->instance));
                       $sub = new StdClass;
                       $sub->id                 = $cm->id;
                       $sub->plugin             = 'mod';
                       $sub->type               = $module->name;
                       $sub->instance           = $cm;
                       $sub->name               = $moduleinstance->name;
                       $sub->visible            = $cm->visible;
                       $element->subs[] = $sub;
                       $itemcount++;
                    }
                }
                $structure[] = $element;    
            }
        }
    }

    return $structure;
}

/**
* get the complete inner structure for one page of a page menu.
* Recursive function.
*
* @param record $page
* @param reference $itemcount a recursive propagating counter in case of flexipage
* or recursive content.
*/
function page_get_structure_from_page($page, &$itemcount){
    global $VISITED_PAGES;
    
    if (!isset($VISITED_PAGES)) $VISITED_PAGES = array();

    if (in_array($page->id, $VISITED_PAGES)) return;    
    $VISITED_PAGES[] = $page->id;    
    
    $structure = array();
    
    // get page items from first page. They are located in the center column    
    $select = "pageid = ? AND (position = 'c' OR position = 'r') ";
    $pageitems = $DB->get_records_select('format_page_items', $select, array($page->id), 'position, sortorder');
    
    // analyses course content component stack
	if ($pageitems){
	    foreach($pageitems as $pi){
	        if ($pi->blockinstance){
	            // is a block
	            $b = $DB->get_record('block_instance', array('id' => $pi->blockinstance));
	            $block = $DB->get_record('block', array('id' => $b->blockid));
	            $blockinstance = block_instance($block->name, $b);
	            $element = new StdClass;
	            $element->type = $block->name;
	            $element->plugintype = 'block';
	            $element->instance = $b;
	            $element->instance->visible = $element->instance->visible * $pi->visible; // a bloc can be hidden by its page_module insertion.
	            $element->name = (!empty($blockinstance->config->title)) ? $blockinstance->config->title : '' ;
	            $element->id = $b->id;
	            // $itemcount++;
	                        
	            // tries to catch modules, pages or resources in content
	
	            $source = @$blockinstance->config->text;
	            // if there is no subcontent, do not consider this bloc in reports.
	            if ($element->subs = page_get_structure_in_content($source, $itemcount)){
	                $structure[] = $element;
	            }            
	        } else {
	            // is a module
	            $cm = $DB->get_record('course_modules', array('id' => $pi->cmid));
	            $module = $DB->get_record('modules', array('id' => $cm->module));
	            
	            switch($module->name){
	                case 'customlabel':;
	                case 'label':{
	                }
	                break;
	                case 'pagemenu':{
	                    // continue;
	                    // if a page menu, we have to get substructure
	                    $element = new StdClass;
	                    $menu = $DB->get_record('pagemenu', array('id' => $cm->instance));
	                    $element->type = 'pagemenu';
	                    $element->plugin = 'mod';
	                    $element->name = $menu->name;
	                    $menulinks = array();
	                    /*
	                    if ($next = $DB->get_record('pagemenu_links', array('pagemenuid' => $menu->id, 'previd' => 0))){ // firstone
	                        $menulinks[] = $next;
	                        while($next = $DB->get_record_select('pagemenu_links', "pagemenuid = ? AND id = ?", array($menu->id, $next->nextid))){
	                            $menulinks[] = $next;
	                            if ($next->nextid == 0) break;
	                        }
	                    }
	                    */
	                    global $CFG;
	                    include_once($CFG->dirroot.'/mod/pagemenu/locallib.php');
	        			$linkid = pagemenu_get_first_linkid($menu->id);
				        while ($linkid) {
				
				            $link     = $DB->get_record('pagemenu_links', array('id' => $linkid));
				            $linkid   = $link->nextid;
				
				            // Update info
			                $menulinks[] = $link;
			                
				        }
	
	                    $element->subs = array();
	                    foreach($menulinks as $link){
	                        if ($link->type == 'page'){
	
	                            $linkdata = $DB->get_record('pagemenu_link_data', array('linkid' => $link->id, 'name' => 'pageid'));
	                            $subpage = $DB->get_record('format_page', array('id' => $linkdata->value));
	                            
	                            $subelement = new StdClass;
	                            $subelement->type = 'page';
	                            $subelement->name = $subpage->nametwo;
	                            
	                            $subelement->subs = page_get_structure_from_page($subpage, $itemcount);
	
	                            if ($subpages = $DB->get_records('format_page', array('parent' => $subpage->id), 'sortorder')){
	                            	foreach($subpages as $sp){
	                            		if (in_array($sp->id, $VISITED_PAGES)) continue;
	                            		if ($sp->display & DISP_PUBLISH){
				                            $subsubelement = new StdClass;
				                            $subsubelement->type = 'page';
				                            $subsubelement->name = $sp->nametwo;		                            
				                            $subsubelement->subs = page_get_structure_from_page($sp, $itemcount);
											$subelement->subs[] = $subsubelement;
				                        }
	                            	}
	                            }
	
	                            $element->subs[] = $subelement;
	                            
	                        }
	                    }
	                    $structure[] = $element;
	                    
	                }
	                break;
	                default:{
	                    $element = new StdClass;
	                    $element->type = $module->name;
	                    $element->plugin = 'mod';
	                    $moduleinstance = $DB->get_record($module->name, array('id' => $cm->instance));
	                    $element->name = $moduleinstance->name;
	                    $element->instance = $cm;
	                    $element->instance->visible = $element->instance->visible * $pi->visible; // a bloc can be hidden by its page_module insertion.
	                    $element->id = $cm->id;
	                    $structure[] = $element;
	                    $itemcount++;
	                }
	            }
	        }
		}
	}
    return $structure;
}

/**
* get substructures hidden in content. this applies to content in HTML blocks that
* may be inserted in page based formats. Not applicable to topic and weekly format.
*
* @param string $source the textual source code of the content
* @param reference $itemcount a recursive propagating counter in case of flexipage
* or recursive content.
*/
function page_get_structure_in_content($source, &$itemcount){
    global $VISITED_PAGES;

    $structure = array();

    // get all links
    $pattern = '/href=\\"(.*)\\"/';
    preg_match_all($pattern, $source, $matches);
    if (isset($matches[1])){
        foreach($matches[1] as $href){
            // jump to another page
            if (preg_match('/course\\/view.php\\?id=(\\d+)&page=(\\d+)/', $href, $matches)){
                if (in_array($matches[2], $VISITED_PAGES)) continue;
                $page = $DB->get_record('format_page', array('id' => $matches[2]));
                $element = new StdClass;
                $element->type = 'pagemenu';
                $element->plugin = 'mod';
                $element->subs = page_get_structure_from_page($page, $itemcount);
                $structure[] = $element;
                $VISITED_PAGES[] = $matches[2];
            }
            // points a module
            if (preg_match('/mod\\/([a-z_]+)\\/.*\\?id=(\\d+)/', $href, $matches)){
                $element = new StdClass;
                $element->type = $matches[1];
                $element->plugin = 'mod';
                $module = $DB->get_record('modules', array('name' => $element->type));
                $cm = $DB->get_record('course_modules', array('id' => $matches[2]));
                $moduleinstance = $DB->get_record($element->type, array('id' => $cm->instance));
                $element->name = $moduleinstance->name;
                $element->instance = &$cm;
                $element->id = $cm->id;
                $structure[] = $element;
                $itemcount++;
            }
        }
    }

    return $structure;
}

/**
* a raster for html printing of a report structure.
*
* @param string ref $str a buffer for accumulating output
* @param object $structure a course structure object.
*/
function training_reports_print_allcourses_html(&$str, &$aggregate){
	global $CFG, $COURSE, $OUTPUT, $DB;

	$output = array();
	$courses = array();
	$courseids = array();
	$return = new StdClass;
	$return->elapsed = 0;
	if (!empty($aggregate['coursetotal'])){	
		foreach($aggregate['coursetotal'] as $cid => $cdata){
			if ($cid != 0){
				if (!in_array($cid, $courseids)){
					$courses[$cid] = $DB->get_record('course', array('id' =>  $cid), 'id,idnumber,shortname,fullname,category');
					$courseids[$cid] = '';
				}
				@$output[$courses[$cid]->category][$cid] = $cdata;
				@$catids[$courses[$cid]->category] = '';
			} else {
				// echo "ignoring hidden $cdata->elapsed ";
				$output[0][SITEID]->elapsed += $cdata->elapsed;
				$output[0][SITEID]->events += $cdata->events;
			}
			$return->elapsed += $cdata->elapsed;
		}

		$coursecats = $DB->get_records_list('course_categories', 'id', array_keys($catids));
	}

	if (!empty($output)){	
		
		$elapsedstr = get_string('elapsed', 'report_trainingsessions');
		$hitsstr = get_string('hits', 'report_trainingsessions');
		$coursestr = get_string('course');
		
		if (isset($output[0])){
			$str .= '<h2>'.get_string('site').'</h2>';
			$str .= $elapsedstr.' : '.format_time($output[0][SITEID]->elapsed).'<br/>';
			$str .= $hitsstr.' : '.$output[0][SITEID]->events;
		}
		
		foreach($output as $catid => $catdata){
			if ($catid == 0) continue;
			$str .= '<h2>'.$coursecats[$catid]->name.'</h2>';
			$str .= '<table class="generaltable" width="100%">';
			$str .= '<tr class="header"><td class="header c0" width="70%"><b>'.$coursestr.'</b></td><td class="header c1" width="15%"><b>'.$elapsedstr.'</b></td><td class="header c2" width="15%"><b>'.$hitsstr.'</b></td></tr>';
			foreach($catdata as $cid => $cdata){
				$ccontext = context_course::instance($cid);
				if (has_capability('report/trainingsessions:view', $ccontext)){
					$str .= '<tr valign="top"><td>'.$courses[$cid]->fullname.'</td><td>';
					$str .= format_time($cdata->elapsed).'<br/>';
					$str .= '</td><td>';
					$str .= $cdata->events;
					$str .= '</td></tr>';
				} else {
					$str .= '<tr valign="top"><td>'.$courses[$cid]->fullname.'</td><td colspan="2">';
					$str .= get_string('nopermissiontoview', 'report_trainingsessions');
					$str .= '</td></tr>';
				}
			}
			$str .= '</table>';
		}
	} else {
		$str .= $OUTPUT->box(get_string('nodata', 'report_trainingsessions'), 'generalbox', '', true);
	}

	return $return;
}

/**
* a raster for html printing of a report structure.
*
* @param string ref $str a buffer for accumulating output
* @param object $structure a course structure object.
*/
function training_reports_print_html(&$str, $structure, &$aggregate, &$done, $indent='', $level = 1){
	global $CFG, $COURSE;

    if (isset($CFG->block_use_stats_ignoremodules)){
        $ignoremodulelist = explode(',', $CFG->block_use_stats_ignoremodules);
    } else {
    	$ignoremodulelist = array();
    }

    if (empty($structure)) {
        $str .= get_string('nostructure', 'report_trainingsessions');
        return;
    }

    $indent = str_repeat('&nbsp;&nbsp;', $level);
    $suboutput = '';

    // initiates a blank dataobject
    if (!isset($dataobject)){
    	$dataobject = new StdClass;
        $dataobject->elapsed = 0;
        $dataobject->events = 0;
    }

    if (is_array($structure)){
        // if an array of elements produce sucessively each output and collect aggregates
        foreach($structure as $element){
            if (isset($element->instance) && empty($element->instance->visible)) continue; // non visible items should not be displayed
            $res = training_reports_print_html($str, $element, $aggregate, $done, $indent, $level);
            $dataobject->elapsed += $res->elapsed;
            $dataobject->events += (0 + @$res->events);
        } 
    } else {
    	$nodestr = '';
        if (!isset($structure->instance) || !empty($structure->instance->visible)){ // non visible items should not be displayed
            // name is not empty. It is a significant module (non structural)
            if (!empty($structure->name)){
                $nodestr .= "<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" class=\"sessionreport\">";
                $nodestr .= "<tr class=\"sessionlevel{$level}\" valign=\"top\">";
                $nodestr .= "<td class=\"sessionitem\">";
                $nodestr .= $indent;
                if (debugging()){
                    $nodestr .= '['.$structure->type.'] ';
                }
                $nodestr .= shorten_text($structure->name, 85);
                $nodestr .= '</td>';
                $nodestr .= "<td class=\"reportvalue\" align=\"right\">";
                if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])){
                    $done++;
                    $dataobject = $aggregate[$structure->type][$structure->id];
                } 
                if (!empty($structure->subs)) {
                    $res = training_reports_print_html($suboutput, $structure->subs, $aggregate, $done, $indent, $level + 1);
                    $dataobject->elapsed += $res->elapsed;
                    $dataobject->events += $res->events;
                }                

				if (!in_array($structure->type, $ignoremodulelist)){
					if (!empty($dataobject->timesource) && $dataobject->timesource == 'credit' && $dataobject->elapsed){
						$nodestr .= get_string('credittime', 'block_use_stats');
					}
					if (!empty($dataobject->timesource) && $dataobject->timesource == 'declared' && $dataobject->elapsed){
						$nodestr .= get_string('declaredtime', 'block_use_stats');
					}
	                $nodestr .= training_reports_format_time($dataobject->elapsed, 'html');
	                $nodestr .= ' ('.(0 + @$dataobject->events).')';
	            } else {
	            	$nodestr .= get_string('ignored', 'block_use_stats');
	            }
    
                // plug here specific details
                $nodestr .= '</td>';
                $nodestr .= '</tr>';
                $nodestr .= "</table>\n";
            } else {
                // It is only a structural module that should not impact on level
                if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])){
                    $dataobject = $aggregate[$structure->type][$structure->id];
                }
                if (!empty($structure->subs)) {
                    $res = training_reports_print_html($suboutput, $structure->subs, $aggregate, $done, $indent, $level);
                    $dataobject->elapsed += $res->elapsed;
                    $dataobject->events += $res->events;
                }
            }
    
            if (!empty($structure->subs)){
                $str .= "<table width=\"100%\" class=\"trainingreport\">";
                $str .= "<tr valign=\"top\">";
                $str .= "<td colspan=\"2\">";
                $str .= $suboutput;
                $str .= '</td>';
                $str .= '</tr>';
                $str .= "</table>\n";
            }
            $str .= $nodestr;
            if (!empty($structure->subs)){
            	if ($str .= '<p></p>');
            }
        }
    }   
    return $dataobject;
}

/**
* a raster for html printing of a report structure header
* with all the relevant data about a user.
*
*/
function training_reports_print_header_html($userid, $courseid, $data, $short = false, $withcompletion = true, $withnooutofstructure = false){
    global $CFG, $DB, $OUTPUT;
    
    $user = $DB->get_record('user', array('id' => $userid));
    $course = $DB->get_record('course', array('id' => $courseid));
    
    echo "<center>";
    echo "<div style=\"width:80%;text-align:left;padding:3px;\" class=\"userinfobox\">";

    $usergroups = groups_get_all_groups($courseid, $userid, 0, 'g.id, g.name');
    echo '<h1>';
    echo $OUTPUT->user_picture($user, array('size' => 32, 'courseid' => $course->id));    
    echo fullname($user).'</h1>';

    // print group status
    if (!empty($usergroups)){
        print_string('groups');
        echo ' : ';
        foreach($usergroups as $group){
            $str = $group->name;        
            if ($group->id == get_current_group($courseid)){
                $str = "<b>$str</b>";
            }
            $groupnames[] = $str;
        }
        echo implode(', ', $groupnames);
                
    }
    
    // print roles list
    $context = context_course::instance($courseid);
	$roles = role_fix_names(get_all_roles(), context_system::instance(), ROLENAME_ORIGINAL);
    echo '<br/>';
    print_string('roles');
    echo ' : ';
    $userroles = get_user_roles($context, $userid);
    $uroles = array();
    
    foreach($userroles as $rid => $r){
    	$uroles[] = $roles[$r->roleid]->localname;
	}
    echo implode (",", $uroles);

    if (!empty($data->linktousersheet)){
        echo "<br/><a href=\"{$CFG->wwwroot}/report/trainingsessions/index.php?view=user&amp;id={$courseid}&amp;userid=$userid\">".get_string('seedetails', 'report_trainingsessions').'</a>';
    }

	if($withcompletion){
	    // print completion bar
	    if (!empty($data->items)){
	        $completed = $data->done / $data->items;
	    } else {
	        $completed = 0;
	    }
	    $remaining = 1 - $completed;
	    $completedpc = ceil($completed * 100);
	    $remainingpc = 100 - $completedpc;
	    $completedwidth = floor(500 * $completed);
	    $remainingwidth = floor(500 * $remaining);
	
	    echo '<p class="completionbar">';
	    print_string('done', 'report_trainingsessions');
	    
	    echo "<img src=\"{$CFG->wwwroot}/report/trainingsessions/pix/green.gif\" style=\"width:{$completedwidth}px\" class=\"donebar\" align=\"top\" title=\"{$completedpc} %\" />";
	    echo "<img src=\"{$CFG->wwwroot}/report/trainingsessions/pix/blue.gif\" style=\"width:{$remainingwidth}px\" class=\"remainingbar\" align=\"top\"  title=\"{$remainingpc} %\" />";
	}
    
    // Start printing the overall times
    
    if (!$short){

        echo '<br/>';
        echo get_string('equlearningtime', 'report_trainingsessions');
        echo training_reports_format_time(0 + @$data->elapsed, 'html');
        echo ' ('.(0 + @$data->events).')';
		echo $OUTPUT->help_icon('equlearningtime', 'report_trainingsessions');

        echo '<br/>';
        echo get_string('activitytime', 'report_trainingsessions');
        echo training_reports_format_time(0 + @$data->activityelapsed, 'html');
		echo $OUTPUT->help_icon('activitytime', 'report_trainingsessions');
    
        // plug here specific details
    }    
    echo '<br/>';

    echo get_string('workingsessions', 'report_trainingsessions');
    echo 0 + $data->sessions;
    if (@$data->sessions == 0 && (@$completedwidth > 0)){
		echo $OUTPUT->help_icon('checklistadvice', 'report_trainingsessions');
	}
    
    echo '</p></div></center>';

	// add printing for global course time (out of activities)    
    if (!$short){
    	if (!$withnooutofstructure){
			echo $OUTPUT->heading(get_string('outofstructure', 'report_trainingsessions'));    
	        echo "<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" class=\"sessionreport\">";
	        echo "<tr class=\"sessionlevel2\" valign=\"top\">";
	        echo "<td class=\"sessionitem\">";
	        print_string('courseglobals', 'report_trainingsessions');
	        echo '</td>';
	        echo "<td class=\"sessionvalue\">";
	        echo training_reports_format_time($data->course->elapsed).' ('.$data->course->hits.')';
	        echo '</td>';
	        echo '</tr>';
		}
        if (isset($data->upload)){
	        echo "<tr class=\"sessionlevel2\" valign=\"top\">";
	        echo "<td class=\"sessionitem\">";
	        print_string('uploadglobals', 'report_trainingsessions');
	        echo '</td>';
	        echo "<td class=\"sessionvalue\">";
	        echo training_reports_format_time($data->upload->elapsed).' ('.$data->upload->hits.')';
	        echo '</td>';
	        echo '</tr>';
	    }
        echo '</table>';
    }
}

/**
* prints a report over each connection session
*
*/
function training_reports_print_session_list(&$str, $sessions){
	global $OUTPUT;
	
	$str .= $OUTPUT->heading(get_string('sessions', 'report_trainingsessions'), 2);
	if (empty($sessions)){
		$str .= $OUTPUT->box(get_string('nosessions', 'report_trainingsessions'));
		return;
	}

	// effective printing of available sessions
	$str .= '<table width="100%" id="session-table">';
	$str .= '<tr valign="top">';
	$str .= '<td width="33%"><b>'.get_string('sessionstart', 'report_trainingsessions').'</b></td>';
	$str .= '<td width="33%"><b>'.get_string('sessionend', 'report_trainingsessions').'</b></td>';
	$str .= '<td width="33%"><b>'.get_string('duration', 'report_trainingsessions').'</b></td>';
	$str .= '</tr>';
	
	$totalelapsed = 0;

	foreach($sessions as $s){
		if (!isset($s->sessionstart)) continue;
		$sessionenddate = (isset($s->sessionend)) ? userdate(@$s->sessionend) : '' ;
		$str .= '<tr valign="top">';
		$str .= '<td>'.userdate($s->sessionstart).'</td>';
		$str .= '<td>'.$sessionenddate.'</td>';
		$str .= '<td>'.format_time($s->elapsed).'</td>';
		$str .= '</tr>';
		$totalelapsed += $s->elapsed;
	}
	$str .= '<tr valign="top">';
	$str .= '<td><br/><b>'.get_string('totalsessions', 'report_trainingsessions').' '.$OUTPUT->help_icon('totalsessiontime', 'report_trainingsessions').'</b></td>';
	$str .= '<td></td>';
	$str .= '<td><br/>'.format_time($totalelapsed).'</td>';
	$str .= '</tr>';

	$str .= '</table>';
		
}

/**
* special time formating
*
*/
function training_reports_format_time($timevalue, $mode = 'html'){
    if ($timevalue){
        if ($mode == 'html'){
            return format_time($timevalue);
        } else {
            // for excel time format we need have a fractional day value
            return  $timevalue / DAYSECS;
        }
    } else {
        return get_string('unvisited', 'report_trainingsessions');
    }
}

/**
* a raster for xls printing of a report structure header
* with all the relevant data about a user.
*
*/
function training_reports_print_header_xls(&$worksheet, $userid, $courseid, $data, $xls_formats){
    global $CFG, $DB;
    
    $user = $DB->get_record('user', array('id' => $userid));
    $course = $DB->get_record('course', array('id' => $courseid));
    
    $row = 0;

    $worksheet->set_row(0, 40, $xls_formats['t']);    
    $worksheet->write_string($row, 0, get_string('sessionreports', 'report_trainingsessions'), $xls_formats['t']);    
    $worksheet->merge_cells($row, 0, 0, 12);    
    $row++;
    $worksheet->write_string($row, 0, get_string('user').' :', $xls_formats['p']);    
    $worksheet->write_string($row, 1, fullname($user));    
    $row++;
    $worksheet->write_string($row, 0, get_string('idnumber').' :', $xls_formats['p']);    
    $worksheet->write_string($row, 1, $user->idnumber);    
    $row++;
    $worksheet->write_string($row, 0, get_string('email').' :', $xls_formats['p']);    
    $worksheet->write_string($row, 1, $user->email);    
    $row++;
    $worksheet->write_string($row, 0, get_string('city').' :', $xls_formats['p']);    
    $worksheet->write_string($row, 1, $user->city);    
    $row++;
    $worksheet->write_string($row, 0, get_string('institution').' :', $xls_formats['p']);    
    $worksheet->write_string($row, 1, $user->institution);    
    $row++;    
    $worksheet->write_string($row, 0, get_string('course', 'report_trainingsessions').' :', $xls_formats['p']);    
    $worksheet->write_string($row, 1, $course->fullname);  
    $row++;    
    $worksheet->write_string($row, 0, get_string('from').' :', $xls_formats['p']);    
    $worksheet->write_string($row, 1, userdate($data->from));  
    $row++;    
    $worksheet->write_string($row, 0, get_string('to').' :', $xls_formats['p']);    
    $worksheet->write_string($row, 1, userdate(time()));  
    $row++;    

    $usergroups = groups_get_all_groups($courseid, $userid, 0, 'g.id, g.name');

    // print group status
    $worksheet->write_string($row, 0, get_string('groups').' :', $xls_formats['p']);    
    $str = '';
    if (!empty($usergroups)){
        foreach($usergroups as $group){
            $str = $group->name;        
            if ($group->id == groups_get_course_group($course)){
                $str = "[$str]";
            }
            $groupnames[] = $str;
        }
        $str = implode(', ', $groupnames);
                
    }

    $worksheet->write_string($row, 1, $str);    
    $row++;    

    $context = context_course::instance($courseid);
    $worksheet->write_string($row, 0, get_string('roles').' :', $xls_formats['p']);
    $roles = get_user_roles($context, $userid);
    $rolenames = array();
    foreach($roles as $role){
    	$rolenames[] = $role->shortname;
    }
    $worksheet->write_string($row, 1, strip_tags(implode(",", $rolenames)));

    $row++;
    // print completion bar
    if (empty($data->items)){
    	$completed = 0;
	} else {
	    $completed = (0 + @$data->done) / $data->items;
	}
    $remaining = 1 - $completed;
    $completedpc = ceil($completed * 100);
    $remainingpc = 100 - $completedpc;

    $worksheet->write_string($row, 0, get_string('done', 'report_trainingsessions'), $xls_formats['p']);
    $worksheet->write_string($row, 1, (0 + @$data->done). ' ' . get_string('over', 'report_trainingsessions'). ' '. (0 + @$data->items). ' ('.$completedpc.' %)');
    $row++;    
    $worksheet->write_string($row, 0, get_string('elapsed', 'report_trainingsessions').' :', $xls_formats['p']);    
    $worksheet->write_number($row, 1, training_reports_format_time((0 + @$data->elapsed), 'xls'), $xls_formats['zt']);
    $row++;    
    $worksheet->write_string($row, 0, get_string('hits', 'report_trainingsessions').' :', $xls_formats['p']);    
    $worksheet->write_number($row, 1, (0 + @$data->events));

    return $row;
}

/**
* a raster for xls printing of a report structure.
*
*/
function training_reports_print_xls(&$worksheet, &$structure, &$aggregate, &$done, &$row, &$xls_formats, $level = 1){

    if (empty($structure)) {
        $str = get_string('nostructure', 'report_trainingsessions');
        $worksheet->write_string($row, 1, $str);
        return;
    }

    // makes a blank dataobject.
    if (!isset($dataobject)){
    	$dataobject = new StdClass;
        $dataobject->elapsed = 0;
        $dataobject->events = 0;
    }

    if (is_array($structure)){
        foreach($structure as $element){
            if (isset($element->instance) && empty($element->instance->visible)) continue; // non visible items should not be displayed
            $res = training_reports_print_xls($worksheet, $element, $aggregate, $done, $row, $xls_formats, $level);
            $dataobject->elapsed += $res->elapsed;
            $dataobject->events += $res->events;
        } 
    } else {
        $format = (isset($xls_formats['a'.$level])) ? $xls_formats['a'.$level] : $xls_formats['z'] ;
        $timeformat = $xls_formats['zt'];
        
        if (!isset($element->instance) || !empty($element->instance->visible)){ // non visible items should not be displayed
            if (!empty($structure->name)){
                // write element title 
                $indent = str_pad('', 3 * $level, ' ');
                $str = $indent.shorten_text($structure->name, 85);
                $worksheet->set_row($row, 18, $format);
                $worksheet->write_string($row, 0, $str, $format);
                $worksheet->write_blank($row, 1, $format);

                if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])){
                    $done++;
                    $dataobject = $aggregate[$structure->type][$structure->id];
                } 

                $thisrow = $row; // saves the current row for post writing aggregates
                $row++;
                if (!empty($structure->subs)) {
                    // debug_trace("with subs");
                    $res = training_reports_print_xls($worksheet, $structure->subs, $aggregate, $done, $row, $xls_formats, $level + 1);
                    $dataobject->elapsed += $res->elapsed;
                    $dataobject->events += $res->events;
                }
                
                $str = training_reports_format_time($dataobject->elapsed, 'xls');
                $worksheet->write_number($thisrow, 2, $str, $timeformat);
                $worksheet->write_number($thisrow, 3, $dataobject->events, $format);
    
            } else {
                // It is only a structural module that should not impact on level
                if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])){
                    $dataobject = $aggregate[$structure->type][$structure->id];
                }
                if (!empty($structure->subs)) {
                    $res = training_reports_print_xls($worksheet, $structure->subs, $aggregate, $done, $row, $xls_formats, $level);
                    $dataobject->elapsed += $res->elapsed;
                    $dataobject->events += $res->events;
                }
            }            
        }
    }
    return $dataobject;
}

/**
* sets up a set fo formats
* @param object $workbook
* @return array of usable formats keyed by a label
*
* Formats : 
* t : Big Title
* tt : section caption
* p : bolded paragraph
* z : numeric (normal)
* zt : time format
* zd : date format
*/
function training_reports_xls_formats(&$workbook){
	$xls_formats = array();
    $xls_formats['t'] =& $workbook->add_format();
    $xls_formats['t']->set_size(20);
    $xls_formats['tt'] =& $workbook->add_format();
    $xls_formats['tt']->set_size(10);
    $xls_formats['tt']->set_color(1);
    $xls_formats['tt']->set_fg_color(4);
    $xls_formats['tt']->set_bold(1);
    $xls_formats['p'] =& $workbook->add_format();
    $xls_formats['p']->set_bold(1);
    $xls_formats['a1'] =& $workbook->add_format();
    $xls_formats['a1']->set_size(14);
    $xls_formats['a1']->set_fg_color(31);
    $xls_formats['a2'] =& $workbook->add_format();
    $xls_formats['a2']->set_size(12);
    $xls_formats['a3'] =& $workbook->add_format();
    $xls_formats['a3']->set_size(9);
    $xls_formats['z'] =& $workbook->add_format();
    $xls_formats['z']->set_size(9);
    $xls_formats['zt'] =& $workbook->add_format();
    $xls_formats['zt']->set_size(9);
    $xls_formats['zt']->set_num_format('[h]:mm:ss');
    $xls_formats['zd'] =& $workbook->add_format();
    $xls_formats['zd']->set_size(9);
    $xls_formats['zd']->set_num_format('aaaa/mm/dd hh:mm');
    
    return $xls_formats;
}

/**
* initializes a new worksheet with static formats
* @param int $userid
* @param int $startrow
* @param array $xls_formats
* @param object $workbook
* @return the initialized worksheet.
*/
function training_reports_init_worksheet($userid, $startrow, &$xls_formats, &$workbook, $purpose = 'usertimes'){
    global $DB, $CFG;
    
    $user = $DB->get_record('user', array('id' => $userid));

	if ($purpose == 'usertimes' || $purpose == 'allcourses'){
		if ($CFG->latinexcelexport){
	    	$sheettitle = mb_convert_encoding(fullname($user), 'ISO-8859-1', 'UTF-8');		
	    } else {
	    	$sheettitle = fullname($user);
	    }
	} else {
		if ($CFG->latinexcelexport){
    		$sheettitle = mb_convert_encoding(fullname($user), 'ISO-8859-1', 'UTF-8').' ('.get_string('sessions', 'report_trainingsessions').')';
    	} else {
    		$sheettitle = fullname($user).' ('.get_string('sessions', 'report_trainingsessions').')';
    	}
	}

    $worksheet =& $workbook->add_worksheet($sheettitle);
	if ($purpose == 'usertimes'){
    	$worksheet->set_column(0,0,20);
	    $worksheet->set_column(1,1,74);
    	$worksheet->set_column(2,2,12);
    	$worksheet->set_column(3,3,4);
	} elseif ($purpose == 'allcourses'){
    	$worksheet->set_column(0,0,50);
	    $worksheet->set_column(1,1,50);
    	$worksheet->set_column(2,2,12);
    	$worksheet->set_column(3,3,4);
	} else {
    	$worksheet->set_column(0,0,30);
	    $worksheet->set_column(1,1,30);
    	$worksheet->set_column(2,2,20);
    	$worksheet->set_column(3,3,10);
	}
    $worksheet->set_column(4,4,12);
    $worksheet->set_column(5,5,4);
    $worksheet->set_column(6,6,12);
    $worksheet->set_column(7,7,4);
    $worksheet->set_column(8,8,12);
    $worksheet->set_column(9,9,4);
    $worksheet->set_column(10,10,12);
    $worksheet->set_column(11,11,4);
    $worksheet->set_column(12,12,12);
    $worksheet->set_column(13,13,4);

    $worksheet->set_row($startrow - 1, 12, $xls_formats['tt']);
    $worksheet->write_string($startrow - 1, 0, get_string('item', 'report_trainingsessions'), $xls_formats['tt']);
    $worksheet->write_blank($startrow - 1,1, $xls_formats['tt']);
    $worksheet->write_string($startrow - 1, 2, get_string('elapsed', 'report_trainingsessions'), $xls_formats['tt']);
    $worksheet->write_string($startrow - 1, 3, get_string('hits', 'report_trainingsessions'), $xls_formats['tt']);
    
    return $worksheet;
}

/**
* print session table in an initialied worksheet
* @param object $worksheet
* @param int $row
* @param array $sessions
* @param object $xls_formats
*/
function training_reports_print_sessions_xls(&$worksheet, $row, &$sessions, &$xls_formats){
	
	$totalelapsed = 0;
	
	if (!empty($sessions)){
		foreach($sessions as $s){
		    $worksheet->write_number($row, 0, training_reports_format_time(@$s->sessionstart, 'xls'), $xls_formats['zd']);	
		    if (!empty($s->sessionend)){
			    $worksheet->write_number($row, 1, training_reports_format_time(@$s->sessionend, 'xls'), $xls_formats['zd']);	
			}
		    $worksheet->write_string($row, 2, format_time(0 + @$s->elapsed), $xls_formats['tt']);	
		    $worksheet->write_number($row, 3, training_reports_format_time(0 + @$s->elapsed, 'xls'), $xls_formats['zt']);	
		    $totalelapsed += 0 + @$s->elapsed;
		    $row++;
		}	
	}
	return $totalelapsed;
}

/**
* a raster for Excel printing of a report structure.
*
* @param ref $worksheet a buffer for accumulating output
* @param object $aggregate aggregated logs to explore.
*/
function training_reports_print_allcourses_xls(&$worksheet, &$aggregate, $row, &$xls_formats){
	global $CFG, $COURSE, $DB;

	$output = array();
	$courses = array();
	$courseids = array();
	$return = new StdClass;
	$return->elapsed = 0;
	$return->events = 0;
	if (!empty($aggregate['coursetotal'])){	
		foreach($aggregate['coursetotal'] as $cid => $cdata){
			if ($cid != 0){
				if (!in_array($cid, $courseids)){
					$courses[$cid] = $DB->get_record('course', array('id' => $cid), 'id,idnumber,shortname,fullname,category');
					$courseids[$cid] = '';
				}
				$output[$courses[$cid]->category][$cid] = $cdata;
				$catids[$courses[$cid]->category] = '';
			} else {
				// echo "ignoring hidden $cdata->elapsed ";
				$output[0][SITEID]->elapsed += $cdata->elapsed;
				$output[0][SITEID]->events += $cdata->events;
			}
			$return->elapsed += $cdata->elapsed;
			$return->events += $cdata->events;
		}

		$coursecats = $DB->get_records_list('course_categories', 'id', array_keys($catids));
	}

	if (!empty($output)){	
		
		$elapsedstr = get_string('elapsed', 'report_trainingsessions');
		$hitsstr = get_string('hits', 'report_trainingsessions');
		$coursestr = get_string('course');
		
		if (isset($output[0])){
	    	$worksheet->write_string($row, 0, get_string('site'), $xls_formats['tt']);	
	    	$row++;
	    	$worksheet->write_string($row, 0, $elapsedstr, $xls_formats['p']);	
	    	$worksheet->write_number($row, 1, training_reports_format_time($output[0][SITEID]->elapsed, 'xls'), $xls_formats['zt']);	
	    	$row++;
	    	$worksheet->write_string($row, 0, $hitsstr, $xls_formats['p']);	
	    	$worksheet->write_number($row, 1, $output[0][SITEID]->events, $xls_formats['z']);	
	    	$row++;
		}
		
		foreach($output as $catid => $catdata){
			if ($catid == 0) continue;
	    	$worksheet->write_string($row, 0, $coursecats[$catid]->name, $xls_formats['tt']);	
	    	$row++;
	    	$worksheet->write_string($row, 0, $coursestr, $xls_formats['tt']);	
	    	$worksheet->write_string($row, 1, $elapsedstr, $xls_formats['tt']);	
	    	$worksheet->write_string($row, 2, $hitsstr, $xls_formats['tt']);	
	    	$row++;
	    	
			foreach($catdata as $cid => $cdata){
				$ccontext = context_course::instance($cid);
				if (has_capability('report/trainingsessions:view', $ccontext)){
			    	$worksheet->write_string($row, 0, $courses[$cid]->fullname, $xls_formats['p']);	
			    	$worksheet->write_number($row, 1, training_reports_format_time($cdata->elapsed, 'xls'), $xls_formats['zt']);	
			    	$worksheet->write_number($row, 2, $cdata->events, $xls_formats['z']);
			    	$row++;
				} else {
			    	$worksheet->write_string($row, 0, $courses[$cid]->fullname, $xls_formats['p']);	
			    	$worksheet->write_string($row, 2, get_string('nopermissiontoview', 'report_trainingsessions'), $xls_formats['p']);	
				}
			}
		}
	}

	return $return;
}

/**
* a raster for printing in raw format 
* with all the relevant data about a user.
*
*/
function trainingsessions_print_globalheader_raw($userid, $courseid, &$data, &$rawstr, $from, $to){
    global $CFG, $COURSE, $DB;

    $user = $DB->get_record('user', array('id' => $userid));
    if ($courseid != $COURSE->id){
	    $course = $DB->get_record('course', array('id' => $courseid));
	} else {
		$course = &$COURSE;
	}

	$resultset = array();
    $usergroups = groups_get_all_groups($courseid, $userid, 0, 'g.id, g.name');

    if (!empty($usergroups)){
        foreach($usergroups as $group){
            $str = $group->name;        
            if ($group->id == groups_get_course_group($course)){
                $str = "$str";
            }
            $groupnames[] = $str;
        }
        $resultset[] = implode(', ', $groupnames); // entity        
    } else {
        $resultset[] = get_string('outofgroup', 'report_trainingsessions'); // entity        
    }

	$resultset[] = $user->id; // userid
	$firstenroll = $DB->get_field_select('user_enrolments', 'MIN(timestart)', " timestart != 0 AND userid = ? ", array($user->id));
	$resultset[] = ($firstenroll) ? date('d/m/Y', $firstenroll) : '' ; // from date
	$firstlogin = $DB->get_field_select('log', 'MIN(time)', " userid = ? AND action = 'login' ", array($user->id));
	$resultset[] = ($firstlogin) ? date('d/m/Y', $firstlogin) : '' ; // firstlogin
	$lastlogin = $DB->get_field_select('log', 'MAX(time)', " userid = ? AND action = 'login' ", array($user->id));
	$resultset[] = ($lastlogin) ? date('d/m/Y', $lastlogin) : '' ; // firstlogin
	$resultset[] = date('d/m/Y', $from); // from date
	$resultset[] = date('d/m/Y', $to); // to date
	$resultset[] = date('d/m/Y', $to - DAYSECS * 7); // last week of period
	if (!empty($CFG->report_trainingsessions_csv_iso)){
		$namestr = mb_convert_encoding(strtoupper(trim(preg_replace('/\s+/', ' ', $user->lastname))), 'ISO-8859-1', 'UTF-8');
	} else {
		$namestr = strtoupper(trim(preg_replace('/\s+/', ' ', $user->lastname)));
	}
	$namestr = mb_ereg_replace('/�|�|�|�/', 'E', $namestr);
	$namestr = mb_ereg_replace('/�|a/', 'A', $namestr);
	$namestr = mb_ereg_replace('/�/', 'C', $namestr);
	$namestr = mb_ereg_replace('/�|�|/', 'U', $namestr);
	$namestr = mb_ereg_replace('/�/', 'I', $namestr);
	$resultset[] = $namestr;
	if (!empty($CFG->report_trainingsessions_csv_iso)){
		$namestr = mb_convert_encoding(strtoupper(trim(preg_replace('/\s+/', ' ', $user->firstname))), 'ISO-8859-1', 'UTF-8');
	} else {
		$namestr = strtoupper(trim(preg_replace('/\s+/', ' ', $user->firstname)));
	}
	$namestr = mb_ereg_replace('/�|�|�|�/', 'E', $namestr);
	$namestr = mb_ereg_replace('/�|a/', 'A', $namestr);
	$namestr = mb_ereg_replace('/�/', 'C', $namestr);
	$namestr = mb_ereg_replace('/�|�|/', 'U', $namestr);
	$namestr = mb_ereg_replace('/�/', 'I', $namestr);
	$resultset[] = $namestr;

    $resultset[] = raw_format_duration(@$data->elapsed); // elapsed time
    $resultset[] = raw_format_duration(@$data->weekelapsed); // elapsed time this week

    // $context = context_course::instance($courseid);
	// $roles = get_user_roles_in_context($userid, $context);
	// $resultset[] = $roles;

	if (!empty($CFG->report_trainingsessions_csv_iso)){
		$rawstr .= mb_convert_encoding(implode(';', $resultset)."\n", 'ISO-8859-1', 'UTF-8');
	} else {
		$rawstr .= implode(';', $resultset)."\n";
	}
}

function raw_format_duration($secs){
	$min = floor($secs / 60);
	$hours = floor($min / 60);
	$days = floor($hours / 24);

	$hours = $hours - $days * 24;
	$min = $min - ($days * 24 * 60 + $hours * 60);
	$secs = $secs - ($days * 24 * 60 * 60 + $hours * 60 * 60 + $min * 60);
	
	if ($days){	
		return $days.' '.get_string('days')." $hours ".get_string('hours')." $min ".get_string('min')." $secs ".get_string('secs');
	}
	if ($hours){	
		return $hours.' '.get_string('hours')." $min ".get_string('min')." $secs ".get_string('secs');
	}
	if ($min){	
		return $min.' '.get_string('min')." $secs ".get_string('secs');
	}
	return $secs.' '.get_string('secs');
}


/**
*
*
*/
function trainingsessions_get_course_users($courseid){
	global $DB;

    $sql = "SELECT 
    			DISTINCT u.id, u.firstname, u.lastname, u.idnumber
            FROM 
            	{user} u
            JOIN 
            	{user_enrolments} ue 
            ON 
            	ue.userid = u.id
            JOIN 
            	{enrol} e 
           	ON 
           		e.id = ue.enrolid
			ORDER BY 
				u.firstname ASC, 
				u.lastname ASC
    ";
	
    $users = $DB->get_records_sql($sql, null);
    
    return $users;
	
}
