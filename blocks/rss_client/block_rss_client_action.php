<?php //$Id$

    require_once('../../config.php');
    require_once($CFG->libdir .'/rsslib.php');
    require_once(MAGPIE_DIR .'rss_fetch.inc');

    require_login();
    global $USER;
    
    //ensure that the logged in user is not using the guest account
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referrer = $_SERVER['HTTP_REFERER'];
    } else {
        $referrer = $CFG->wwwroot;
    }
    if (isguest()) {
        error(get_string('noguestpost', 'forum'), $referrer);
    }
    
    optional_variable($act, 'none');
    optional_variable($rssid, 'none');
    optional_variable($courseid, '');
    optional_variable($url);
    optional_variable($preferredtitle, '');
    optional_variable($item);

    if (!defined('MAGPIE_OUTPUT_ENCODING')) {
        define('MAGPIE_OUTPUT_ENCODING', get_string('thischarset'));  // see bug 3107
    }
    
    $straddedit = get_string('feedsaddedit', 'block_rss_client');
    if ( isadmin() ) {
        $stradmin = get_string('administration');
        $strconfiguration = get_string('configuration');
        $navigation = "<a href=\"$CFG->wwwroot/$CFG->admin/index.php\">$stradmin</a> -> ".
        "<a href=\"$CFG->wwwroot/$CFG->admin/configure.php\">$strconfiguration</a> -> $straddedit";
    } else if (is_numeric($courseid) && $course = get_record('course', 'id', $courseid, '', '', '', '', 'shortname') ) {
        $navigation = "<a href=\"$CFG->wwwroot/course/view.php?id=$courseid\">$course->shortname</a> -> $straddedit";
    } else {
        $navigation = $straddedit;
    }
    
    print_header(get_string('feedsaddedit', 'block_rss_client'), 
                 get_string('feedsaddedit', 'block_rss_client'), 
                 $navigation );

    //check to make sure that the user is allowed to post new feeds
    $submitters = $CFG->block_rss_client_submitters;
    $isteacher = false;
    if (is_numeric($courseid)) {
        $isteacher = isteacher($courseid);
    }
    
    //if the user is an admin or course teacher then allow the user to
    //assign categories to other uses than personal
    if (!( isadmin() || $submitters == SUBMITTERS_ALL_ACCOUNT_HOLDERS || ($submitters == SUBMITTERS_ADMIN_AND_TEACHER && $isteacher) ) ) {
        error(get_string('noguestpost', 'forum'), $referrer);
    }

    if ($act == 'none') {
        rss_display_feeds();
        rss_get_form($act, $url, $rssid, $preferredtitle);

    } else if ($act == 'updfeed') {
        require_variable($url);
        
        // By capturing the output from fetch_rss this way
        // error messages do not display and clutter up the moodle interface
        // however, we do lose out on seeing helpful messages like "cache hit", etc.
        ob_start();
        $rss = fetch_rss($url);
        $rsserror = ob_get_contents();
        ob_end_clean();
        
        $dataobject->id = $rssid;
        if ($rss === false) {
            $dataobject->description = '';
            $dataobject->title = '';
            $dataobject->preferredtitle = '';
        } else {
            $dataobject->description = addslashes(rss_unhtmlentities($rss->channel['description']));
            $dataobject->title = addslashes(rss_unhtmlentities($rss->channel['title']));
            $dataobject->preferredtitle = addslashes($preferredtitle);
        }
        $dataobject->url = addslashes($url);

        if (!update_record('block_rss_client', $dataobject)) {
            error('There was an error trying to update rss feed with id:'. $rssid);
        }

        redirect($referrer, get_string('feedupdated', 'block_rss_client'));
/*        rss_display_feeds();
        rss_get_form($act, $dataobject->url, $rssid, $dataobject->preferredtitle);
*/
    } else if ($act == 'addfeed' ) {

        require_variable($url);            
        $dataobject->userid = $USER->id;
        $dataobject->description = '';
        $dataobject->title = '';
        $dataobject->url = addslashes($url);
        $dataobject->preferredtitle = addslashes($preferredtitle);

        $rssid = insert_record('block_rss_client', $dataobject);
        if (!$rssid) {
            error('There was an error trying to add a new rss feed:'. $url);
        }

        // By capturing the output from fetch_rss this way
        // error messages do not display and clutter up the moodle interface
        // however, we do lose out on seeing helpful messages like "cache hit", etc.
        ob_start();
        $rss = fetch_rss($url);
        $rsserror = ob_get_contents();
        ob_end_clean();
        
        if ($rss === false) {
            $message = 'There was an error loading this rss feed. You may want to verify the url you have specified before using it.'; //Daryl Hawes note: localize this line
        } else {

            $dataobject->id = $rssid;
            if (!empty($rss->channel['description'])) {
                $dataobject->description = addslashes(rss_unhtmlentities($rss->channel['description']));
            }
            if (!empty($rss->channel['title'])) {
                $dataobject->title = addslashes(rss_unhtmlentities($rss->channel['title']));
            } 
            if (!update_record('block_rss_client', $dataobject)) {
                error('There was an error trying to update rss feed with id:'. $rssid);
            }
            $message = get_string('feedadded', 'block_rss_client');
        }
        redirect($referrer, $message);
/*
        rss_display_feeds();
        rss_get_form($act, $dataobject->url, $dataobject->id, $dataobject->preferredtitle);
*/
    } else if ( $act == 'rss_edit') {
        
        $rss_record = get_record('block_rss_client', 'id', $rssid);
        $preferredtitle = stripslashes_safe($rss_record->preferredtitle);
        if (empty($preferredtitle)) {
            $preferredtitle = stripslashes_safe($rss_record->title);
        }
        $url = stripslashes_safe($rss_record->url);
        rss_display_feeds('', $rssid);
        rss_get_form($act, $url, $rssid, $preferredtitle);

    } else if ($act == 'delfeed') {
        
        $file = $CFG->dataroot .'/cache/rsscache/'. $rssid .'.xml';
        if (file_exists($file)) {
            unlink($file);
        }

        // echo "DEBUG: act = delfeed"; //debug
        //Daryl Hawes note: convert this sql statement to a moodle function call
        $sql = 'DELETE FROM '. $CFG->prefix .'block_rss_client WHERE id='. $rssid;
        $res= $db->Execute($sql);

        redirect($referrer, get_string('feeddeleted', 'block_rss_client') );

/*        rss_display_feeds();
        rss_get_form($act, $url, $rssid, $preferredtitle);
*/
    } else if ($act == 'view') {
        //              echo $sql; //debug
        //              print_object($res); //debug
        $rss_record = get_record('block_rss_client', 'id', $rssid);
        if (!$rss_record->id) {
            print '<strong>'. get_string('couldnotfindfeed', 'block_rss_client') .': '. $rssid .'</strong>';
        } else {
            // By capturing the output from fetch_rss this way
            // error messages do not display and clutter up the moodle interface
            // however, we do lose out on seeing helpful messages like "cache hit", etc.
            ob_start();
            $rss = fetch_rss($rss_record->url);
            $rsserror = ob_get_contents();
            ob_end_clean();
            
            if (empty($rss_record->preferredtitle)) {
                $feedtitle = stripslashes_safe($rss_record->preferredtitle);
            } else {
               $feedtitle =  stripslashes_safe(rss_unhtmlentities($rss->channel['title']));
            }
            print '<table align="center" width="50%" cellspacing="1">'."\n";
            print '<tr><td colspan="2"><strong>'. $feedtitle .'</strong></td></tr>'."\n";
            for($y=0; $y < count($rss->items); $y++) {
                $rss->items[$y]['title'] = stripslashes_safe(rss_unhtmlentities($rss->items[$y]['title']));
                $rss->items[$y]['description'] = stripslashes_safe(rss_unhtmlentities($rss->items[$y]['description']));
                if ($rss->items[$y]['link'] == '') {
                    $rss->items[$y]['link'] = $rss->items[$y]['guid'];
                }

                if ($rss->items[$y]['title'] == '') {
                    $rss->items[$y]['title'] = '&gt;&gt;';
                }

                print '<tr><td valign="middle">'."\n";
                print '<a href="'. $rss->items[$y]['link'] .'" target=_new><strong>'. $rss->items[$y]['title'];
                print '</strong></a>'."\n";
                print '</td>'."\n";
                if (file_exists($CFG->dirroot .'/blog/lib.php')) {
                    //Blog module is installed - provide "blog this" link
                    print '<td align="right">'."\n";
                    print '<img src="'. $CFG->pixpath .'/blog/blog.gif" alt="'. get_string('blogthis', 'blog').'" title="'. get_string('blogthis', 'blog') .'" border="0" align="middle" />'."\n";
                    print '<a href="'. $CFG->wwwroot .'/blog/blogthis.php?blogid='. $blogid .'&act=use&item='. $y .'&rssid='. $rssid .'"><small><strong>'. get_string('blogthis', 'blog') .'</strong></small></a>'."\n";
                } else {
                    print '<td>&nbsp;';
                }
                print '</td></tr>'."\n";
                print '<tr><td colspan=2><small>';
                print $rss->items[$y]['description'] .'</small></td></tr>'."\n";
            }
            print '</table>'."\n";
        }
    } else {
        rss_display_feeds();
        rss_get_form($act, $url, $rssid, $preferredtitle);
    }

    print_footer();
?>
