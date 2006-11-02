<?php //$Id$

    /**
     * Library of functions and constants for blog
     */
    require_once($CFG->libdir .'/blocklib.php');
    require_once($CFG->libdir .'/pagelib.php');
    require_once($CFG->dirroot .'/blog/rsslib.php');
    require_once($CFG->dirroot .'/blog/blogpage.php');


    /**
     * Blog access level constant declaration
     */
    define ('BLOG_USER_LEVEL', 1);
    define ('BLOG_GROUP_LEVEL', 2);
    define ('BLOG_COURSE_LEVEL', 3);
    define ('BLOG_SITE_LEVEL', 4);
    define ('BLOG_GLOBAL_LEVEL', 5);


    /**
     * Definition of blogcourse page type (blog page with course id present).
     */
    //not used at the moment, and may not need to be
    define('PAGE_BLOG_COURSE_VIEW', 'blog_course-view');


    /**
     * Checks to see if user has visited blogpages before, if not, install 2
     * default blocks (blog_menu and blog_tags).
     */
    function blog_check_and_install_blocks() {
        global $USER;
        if (isloggedin() && !isguest()) {
            // if this user has not visited this page before
            if (!get_user_preferences('blogpagesize')) {
                // find the correct ids for blog_menu and blog_from blocks
                $menublock = get_record('block','name','blog_menu');
                $tagsblock = get_record('block','name','blog_tags');
                // add those 2 into block_instance page

                // add blog_menu block
                $newblock = new object();
                $newblock -> blockid  = $menublock->id;
                $newblock -> pageid   = $USER->id;
                $newblock -> pagetype = 'blog-view';
                $newblock -> position = 'r';
                $newblock -> weight   = 0;
                $newblock -> visible  = 1;
                insert_record('block_instance', $newblock);

                // add blog_tags menu
                $newblock -> blockid = $tagsblock->id;
                $newblock -> weight  = 1;
                insert_record('block_instance', $newblock);

                // finally we set the page size pref
                set_user_preference('blogpagesize', 10);
            }
        }
    }


    /**
     * Adaptation of isediting in moodlelib.php for blog module
     * @return bool
     */
    function blog_isediting() {
        global $SESSION;

        return !empty($SESSION->blog_editing_enabled);
    }


    /**
     *  This function is in lib and not in BlogInfo because entries being searched
     *   might be found in any number of blogs rather than just one.
     *
     *   $@param ...
     */
    function blog_print_html_formatted_entries($postid, $filtertype, $filterselect, $tagid, $tag) {

        global $CFG, $USER;

        $blogpage  = optional_param('blogpage', 0, PARAM_INT);
        $bloglimit = optional_param('limit', get_user_preferences('blogpagesize', 10), PARAM_INT);
        $start     = $blogpage * $bloglimit;

        $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);

        $morelink = '<br />&nbsp;&nbsp;';
    
        $totalentries = get_viewable_entry_count($postid, $bloglimit, $start, $filtertype, $filterselect, $tagid, $tag, $sort='lastmodified DESC');
        $blogEntries = fetch_entries($postid, $bloglimit, $start, $filtertype, $filterselect, $tagid, $tag, $sort='lastmodified DESC', true);

        print_paging_bar($totalentries, $blogpage, $bloglimit, get_baseurl($filtertype, $filterselect), 'blogpage');

        if ($CFG->enablerssfeeds) {
            blog_rss_print_link($filtertype, $filterselect, $tag);
        }

        if (has_capability('moodle/blog:create', $sitecontext)) {
            //the user's blog is enabled and they are viewing their own blog
            $addlink = '<div align="center">';
            $addlink .= '<a href="'.$CFG->wwwroot .'/blog/edit.php?action=add'.'">'. get_string('addnewentry', 'blog').'</a>';
            $addlink .= '</div>';
            echo $addlink;
        }

        if ($blogEntries) {

            $count = 0;
            foreach ($blogEntries as $blogEntry) {
                blog_print_entry($blogEntry, 'list', $filtertype, $filterselect); //print this entry.
                $count++;
            }
            
            print_paging_bar($totalentries, $blogpage, $bloglimit, get_baseurl($filtertype, $filterselect), 'blogpage');
            
            if (!$count) {
                print '<br /><center>'. get_string('noentriesyet', 'blog') .'</center><br />';

            }
                       
            print $morelink.'<br />'."\n";
            return;
        }

        $output = '<br /><center>'. get_string('noentriesyet', 'blog') .'</center><br />';
        
        print $output;       

    }


    /**
     * This function is in lib and not in BlogInfo because entries being searched
     * might be found in any number of blogs rather than just one.
     *
     * This function builds an array which can be used by the included
     * template file, making predefined and nicely formatted variables available
     * to the template. Template creators will not need to become intimate
     * with the internal objects and vars of moodle blog nor will they need to worry
     * about properly formatting their data
     *
     *   @param BlogEntry blogEntry - a hopefully fully populated BlogEntry object
     *   @param string viewtype Default is 'full'. If 'full' then display this blog entry
     *     in its complete form (eg. archive page). If anything other than 'full'
     *     display the entry in its abbreviated format (eg. index page)
     */
    function blog_print_entry($blogEntry, $viewtype='full', $filtertype='', $filterselect='', $mode='loud') {

        global $USER, $CFG, $COURSE, $ME;

        $template['body'] = format_text($blogEntry->summary, $blogEntry->format);
        $template['title'] = '<a name="'. $blogEntry->subject .'"></a>';
        //enclose the title in nolink tags so that moodle formatting doesn't autolink the text
        $template['title'] .= '<span class="nolink">'.$blogEntry->subject.'</span>';
        $template['userid'] = $blogEntry->userid;
        $template['author'] = fullname(get_record('user','id',$blogEntry->userid));
        $template['lastmod'] = userdate($blogEntry->lastmodified);
        $template['created'] = userdate($blogEntry->created);
        $template['publishstate'] = $blogEntry->publishstate;

        /// preventing user to browse blogs that they aren't supposed to see
        /// This might not be too good since there are multiple calls per page

        /*
        if (!blog_user_can_view_user_post($template['userid'])) {
            error ('you can not view this post');
        }*/

        $stredit = get_string('edit');
        $strdelete = get_string('delete');

        $user = get_record('user','id',$template['userid']);

        /// Start printing of the blog

        echo '<table cellspacing="0" class="forumpost blogpost blog'.$template['publishstate'].'" width="100%">';

        echo '<tr class="header"><td class="picture left">';
        print_user_picture($template['userid'], SITEID, $user->picture);
        echo '</td>';

        echo '<td class="topic starter"><div class="subject">'.$template['title'].'</div><div class="author">';
        $fullname = fullname($user, $template['userid']);
        $by = new object();
        $by->name =  '<a href="'.$CFG->wwwroot.'/user/view.php?id='.
                    $user->id.'&amp;course='.$COURSE->id.'">'.$fullname.'</a>';
        $by->date = $template['lastmod'];
        print_string('bynameondate', 'forum', $by);
        echo '</div></td></tr>';

        echo '<tr><td class="left side">';

    /// Actual content

        echo '</td><td class="content">'."\n";

        switch ($template['publishstate']) {
            case 'draft':
                $blogtype = get_string('publishtonoone', 'blog');
            break;
            case 'site':
                $blogtype = get_string('publishtosite', 'blog');
            break;
            case 'public':
                $blogtype = get_string('publishtoworld', 'blog');
            break;
            default:
                $blogtype = '';
            break;

        }

        echo '<div class="audience">'.$blogtype.'</div>';

        // Print whole message
        echo format_text($template['body']);

    /// Links to tags

        if ($blogtags = get_records_sql('SELECT t.* FROM '.$CFG->prefix.'tags t, '.$CFG->prefix.'blog_tag_instance ti
                                     WHERE t.id = ti.tagid
                                     AND ti.entryid = '.$blogEntry->id)) {
            echo '<div class="tags">';
            if ($blogtags) {
                print_string('tags');
                echo ': ';
                foreach ($blogtags as $key => $blogtag) {
                    $taglist[] = '<a href="index.php?filtertype='.$filtertype.'&amp;filterselect='.$filterselect.'&amp;tagid='.$blogtag->id.'">'.$blogtag->text.'</a>';
                }
                echo implode(', ', $taglist);
            }
            echo '</div>';
        }

    /// Commands

        echo '<div class="commands">';

        if (blog_user_can_edit_post($blogEntry)) {
            echo '<a href="'.$CFG->wwwroot.'/blog/edit.php?action=edit&amp;id='.$blogEntry->id.'">'.$stredit.'</a>';
            echo '| <a href="'.$CFG->wwwroot.'/blog/edit.php?action=delete&amp;id='.$blogEntry->id.'">'.$strdelete.'</a>';
        }

        echo '</div>';

        echo '</td></tr></table>'."\n\n";

    }


    /**
     * Use this function to retrieve a list of publish states available for
     * the currently logged in user.
     *
     * @return array This function returns an array ideal for sending to moodles'
     *                choose_from_menu function.
     */
    function blog_applicable_publish_states($courseid='') {

        global $CFG;

        // everyone gets draft access
        if ($CFG->bloglevel >= BLOG_USER_LEVEL) {
            $options = array ( 'draft' => get_string('publishtonoone', 'blog') );
        }
        
        if ($CFG->bloglevel > BLOG_USER_LEVEL) {
            $options['site'] = get_string('publishtosite', 'blog');
        }
        
        if ($CFG->bloglevel >= BLOG_GLOBAL_LEVEL) {
            $options['public'] = get_string('publishtoworld', 'blog');
        }

        return $options;
    }


    /**
     * User can edit a blog entry if this is their own blog post and they have
     * the capability moodle/blog:create, or if they have the capability
     * moodle/blog:manageentries.
     *
     * This also applies to deleting of posts.
     */
    function blog_user_can_edit_post($blogEntry) {

        global $CFG, $USER;

        $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);

        if (has_capability('moodle/blog:manageentries', $sitecontext)) {
            return true; // can edit any blog post
        }

        if ($blogEntry->userid == $USER->id
          and has_capability('moodle/blog:create', $sitecontext)) {
            return true; // can edit own when having blog:create capability
        }

        return false;
    }


    /**
     * Checks to see if a user can view the blogs of another user.
     * Only blog level is checked here, the capabilities are enforced
     * in blog/index.php
     */
    function blog_user_can_view_user_post($targetuserid, $blogEntry=null) {
        global $CFG, $USER;

        if (empty($CFG->bloglevel)) {
            return false; // blog system disabled
        }

        if (!empty($USER->id) and $USER->id == $targetuserid) {
            return true; // can view own posts in any case
        }

        $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
        if (has_capability('moodle/blog:manageentries', $sitecontext)) {
            return true; // can manage all posts
        }

        if ($blogEntry and $blogEntry->publishstate == 'draft') {
            return false;  // can not view draft of others
        }

        switch ($CFG->bloglevel) {
            case BLOG_GLOBAL_LEVEL:
                return true;
            break;

            case BLOG_SITE_LEVEL:
                if (!empty($USER->id)) { // not logged in viewers forbidden
                    return true;
                }
                return false;
            break;

            case BLOG_COURSE_LEVEL:
                $mycourses = array_keys(get_my_courses($targetuserid));
                $usercourses = array_keys(get_my_courses($targetuserid));
                $shared = array_intersect($mycourses, $usercourses);
                if (!empty($shared)) {
                    return true;
                }
                return false;
            break;

            case BLOG_GROUP_LEVEL:
                $mycourses = array_keys(get_my_courses($targetuserid));
                $usercourses = array_keys(get_my_courses($targetuserid));
                $shared = array_intersect($mycourses, $usercourses);
                foreach ($shared as $courseid) {
                    $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
                    if (has_capability('moodle/site:accessallgroups', $coursecontext)
                      or groupmode($courseid) != SEPARATEGROUPS) {
                        return true;
                    } else {
                        if ($usergroups = user_group($courseid, $targetuserid)) {
                            foreach ($usergroups as $usergroup) {
                                if (ismember($usergroup->id)) {
                                    return true;
                                }
                            }
                        }
                    }
                }
                return false;
            break;

            case BLOG_USER_LEVEL:
            default:
                $personalcontext = get_context_instance(CONTEXT_USER, $targetuserid);
                return has_capability('moodle/user:readuserblogs', $personalcontext);
            break;

        }
    }


    /**
     * Main filter function.
     */
    function fetch_entries($postid='', $fetchlimit=10, $fetchstart='', $filtertype='', $filterselect='', $tagid='', $tag ='', $sort='lastmodified DESC', $limit=true) {

        global $CFG, $USER;

        /// set the tag id for searching
        if ($tagid) {
            $tag = $tagid;
        } else if ($tag) {
            if ($tagrec = get_record_sql('SELECT * FROM '.$CFG->prefix.'tags WHERE text LIKE "'.$tag.'"')) {
                $tag = $tagrec->id;
            } else {
                $tag = -1;    //no records found
            }
        }

        // If we have specified an ID
        // Just return 1 entry

        if ($postid) {

            if ($post = get_record('post', 'id', $postid)) {

                if (blog_user_can_view_user_post($post->userid)) {

                    if ($user = get_record('user', 'id', $post->userid)) {
                        $post->email = $user->email;
                        $post->firstname = $user->firstname;
                        $post->lastname = $user->lastname;
                    }
                    $retarray[] = $post;
                    return $retarray;
                } else {
                    return null;
                }

            } else { // bad postid
                return null;
            }
        }

        if ($tag) {
            $tagtablesql = $CFG->prefix.'blog_tag_instance bt, ';
            $tagquerysql = ' AND bt.entryid = p.id AND bt.tagid = '.$tag.' ';
        } else {
            $tagtablesql = '';
            $tagquerysql = '';
        }


        /****************************************
         * depending on the type, there are 4   *
         * different possible sqls              *
         ****************************************/

        $requiredfields = 'p.*, u.firstname,u.lastname,u.email';

        switch ($filtertype) {

            case 'site':

                if (isloggedin()) {

                    $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                            .$CFG->prefix.'user u
                            WHERE p.userid = u.id '.$tagquerysql.'
                            AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.')
                            AND u.deleted = 0';

                } else {

                    $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                            .$CFG->prefix.'user u
                            WHERE p.userid = u.id '.$tagquerysql.'
                            AND p.publishstate = \'public\'
                            AND u.deleted = 0';
                }

            break;

            case 'course':
                if ($filterselect != SITEID) {

                    // all users with a role assigned
                    $context = get_context_instance(CONTEXT_COURSE, $filterselect);

                    $SQL = '(SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                            .$CFG->prefix.'role_assignments ra, '.$CFG->prefix.'user u
                            WHERE p.userid = ra.userid '.$tagquerysql.'
                            AND ra.contextid '.get_related_contexts_string($context).'
                            AND u.id = p.userid
                            AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.'))';
                } else {

                    if (isloggedin()) {

                        $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                                .$CFG->prefix.'user u
                                WHERE p.userid = u.id '.$tagquerysql.'
                                AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.')
                                AND u.deleted = 0';

                    } else {

                        $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                                .$CFG->prefix.'user u
                                WHERE p.userid = u.id '.$tagquerysql.'
                                AND p.publishstate = \'public\'
                                AND u.deleted = 0';
                    }

                }

            break;

            case 'group':

                $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                        .$CFG->prefix.'groups_members m, '.$CFG->prefix.'user u
                        WHERE p.userid = m.userid '.$tagquerysql.'
                        AND u.id = p.userid
                        AND m.groupid = '.$filterselect.'
                        AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.')';

            break;

            case 'user':

                if (isloggedin()) {

                    $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                            .$CFG->prefix.'user u
                            WHERE p.userid = u.id '.$tagquerysql.'
                            AND u.id = '.$filterselect.'
                            AND (p.publishstate = \'site\' OR p.publishstate = \'public\' OR p.userid = '.$USER->id.')';
                } else {

                    $SQL = 'SELECT '.$requiredfields.' FROM '.$CFG->prefix.'post p, '.$tagtablesql
                            .$CFG->prefix.'user u
                            WHERE p.userid = u.id '.$tagquerysql.'
                            AND u.id = '.$filterselect.'
                            AND p.publishstate = \'public\'';

                }

            break;


        }

        $limitfrom = 0;
        $limitnum = 0;

        if ($fetchstart !== '' && $limit) {
            $limitfrom = $fetchstart;
            $limitnum = $fetchlimit;
        }

        $orderby = ' ORDER BY '. $sort .' ';

        //echo 'Debug: BlogFilter fetch_entries() sql="'. $SQL . $orderby . $limit .'"<br />'. $this->categoryid; //debug
        $records = get_records_sql($SQL . $orderby, $limitfrom, $limitnum);

//        print_object($records); //debug

        if (empty($records)) {
            return array();
        }

        return $records;
    }


    /**
     * get the count of viewable entries, easiest way is to count fetch_entries
     * this is used for print_paging_bar
     * this is not ideal, but because of the UNION in the sql in fetch_entries,
     * it is hard to use count_records_sql
     */
    function get_viewable_entry_count($postid='', $fetchlimit=10,
                $fetchstart='', $filtertype='', $filterselect='', $tagid='',
                $tag ='', $sort='lastmodified DESC') {

        $blogEntries = fetch_entries($postid, $fetchlimit,
                $fetchstart, $filtertype, $filterselect, $tagid, $tag,
                $sort='lastmodified DESC', false);

        return count($blogEntries);
    }


    /// Find the base url from $_GET variables, for print_paging_bar
    function get_baseurl($filtertype, $filterselect) {

        $getcopy  = $_GET;

        unset($getcopy['blogpage']);

        $strippedurl = strip_querystring(qualified_me());
        if(!empty($getcopy)) {
            $first = false;
            $querystring = '';
            foreach($getcopy as $var => $val) {
                if(!$first) {
                    $first = true;
                    if ($var != 'filterselect' && $var != 'filtertype') {
                        $querystring .= '?'.$var.'='.$val;
                        $hasparam = true;
                    } else {
                        $querystring .= '?';
                    }
                } else {
                    if ($var != 'filterselect' && $var != 'filtertype') {
                    $querystring .= '&amp;'.$var.'='.$val;
                    $hasparam = true;
                    }
                }
            }
            if (isset($hasparam)) {
                $querystring .= '&amp;';
            } else {
                $querystring = '?';
            }
        } else {
            $querystring = '?';
        }

        return strip_querystring(qualified_me()) . $querystring. 'filtertype='.
                $filtertype.'&amp;filterselect='.$filterselect.'&amp;';

    }
?>
