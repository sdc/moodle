<?PHP  // $Id$
/// Extended by Michael Schneider
/// This page prints a particular instance of wiki

    global $CFG;

    require_once("../../config.php");
    require_once("lib.php");
#    require_once("$CFG->dirroot/course/lib.php"); // For side-blocks

    optional_variable($ewiki_action,"");     // Action on Wiki-Page
    optional_variable($id);     // Course Module ID, or
    optional_variable($wid);    // Wiki ID
    optional_variable($wikipage, false);     // Wiki Page Name
    optional_variable($q,"");    // Search Context
    optional_variable($userid);     // User wiki.
    optional_variable($groupid);    // Group wiki.
    optional_variable($canceledit,"");    // Editing has been cancelled
    if($canceledit) {
      $wikipage=$ewiki_id;
    }

    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }

        if (! $wiki = get_record("wiki", "id", $cm->instance)) {
            error("Course module is incorrect");
        }

    } else {
        if (! $wiki = get_record("wiki", "id", $wid)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $wiki->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("wiki", $wiki->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
        $id = $cm->id;
        $_REQUEST["id"] = $id;
    }

    if ($course->category or !empty($CFG->forcelogin)) {
        require_login($course->id);
    }

    /// Add the course module 'groupmode' to the wiki object, for easy access.
    $wiki->groupmode = $cm->groupmode;

    /// Default format:
    $moodle_format=FORMAT_MOODLE;

    ### SAVE ID from Moodle
    $moodleID=@$_REQUEST["id"];

/// Globally disable CamelCase, if the option is selected for this wiki.
    $moodle_disable_camel_case = ($wiki->disablecamelcase == 1);

    if (($wiki_entry = wiki_get_default_entry($wiki, $course, $userid, $groupid))) {

///     ################# EWIKI Part ###########################
///     The wiki_entry->pagename is set to the specified value of the wiki,
///     or the default value in the 'lang' file if the specified value was empty.
        define("EWIKI_PAGE_INDEX",$wiki_entry->pagename);

        $wikipage = ($wikipage === false) ?  EWIKI_PAGE_INDEX: $wikipage;

///     ### Prevent ewiki getting id as PageID...
        unset($_REQUEST["id"]);
        unset($_GET["id"]);
        unset($_POST["id"]);
        unset($_POST["id"]);
        unset($_SERVER["QUERY_STRING"]);
        unset($HTTP_GET_VARS["id"]);
        unset($HTTP_POST_VARS["id"]);
        global $ewiki_title;

///     #-- predefine some of the configuration constants
        
        
        /// EWIKI_NAME is defined in ewikimoodlelibs, so that also admin.php can use this
        #define("EWIKI_NAME", $wiki_entry->pagename);

        /// Search Hilighting
        if($ewiki_title=="SearchPages") {
            $qArgument="&q=".urlencode($q);
        }
 
        /// Build the ewsiki script constant
        /// ewbase will also be needed by EWIKI_SCRIPT_BINARY
        $ewbase = $ME.'?id='.$moodleID;
        if (isset($userid)) $ewbase .= '&userid='.$userid;
        if (isset($groupid)) $ewbase .= '&groupid='.$groupid;
        $ewscript = $ewbase.'&wikipage=';
        define("EWIKI_SCRIPT", $ewscript);
        define("EWIKI_SCRIPT_URL", $ewscript);

        /// # Settings for this specific Wiki
        define("EWIKI_PRINT_TITLE", $wiki->ewikiprinttitle);

        define("EWIKI_INIT_PAGES", wiki_content_dir($wiki));

///     # fix broken PHP setup
        if (!function_exists("get_magic_quotes_gpc") || get_magic_quotes_gpc()) {
            include($CFG->dirroot."/mod/wiki/ewiki/fragments/strip_wonderful_slashes.php");
        }
        if (ini_get("register_globals")) {
            #    include($CFG->dirroot."/mod/wiki/ewiki/fragments/strike_register_globals.php");
        }

        # Database Handler
        include_once($CFG->dirroot."/mod/wiki/ewikimoodlelib.php");
        # Plugins
        //include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/email_protect.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/patchsaving.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/notify.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/feature/imgresize_gd.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/moodle_highlight.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/f_fixhtml.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/sitemap.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/moodle_wikidump.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/aview/backlinks.php");
        #include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/markup/css.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/markup/footnotes.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/diff.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/page/pageindex.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/page/orphanedpages.php");
        include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/wantedpages.php");

        # Binary Handling
        if($wiki->ewikiacceptbinary) {
            define("EWIKI_UPLOAD_MAXSIZE", get_max_upload_file_size());
            define("EWIKI_SCRIPT_BINARY", $ewbase."&binary=");
            define("EWIKI_ALLOW_BINARY",1);
            define("EWIKI_IMAGE_CACHING",1);
            #define("EWIKI_AUTOVIEW",1);
            include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/lib/mime_magic.php");
            include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/aview/downloads.php");
            include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/downloads.php");
            #include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/db/binary_store.php");
            include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/moodle_binary_store.php");
        } else {
            define("EWIKI_SCRIPT_BINARY", 0);
            define("EWIKI_ALLOW_BINARY",0);
        }

        # The mighty Wiki itself
        include_once($CFG->dirroot."/mod/wiki/ewiki/ewiki.php");

        # Language-stuff: eWiki gets language from Browser. Lets correct it. Empty arrayelements do no harm
        $ewiki_t["languages"]=array(current_language(), $course->lang, $CFG->lang,"en","c");

        # Check Access Rights
        $canedit = wiki_can_edit_entry($wiki_entry, $wiki, $USER, $course);
        if (!$canedit) {
            # Protected Mode
            unset($ewiki_plugins["action"]["edit"]);
            unset($ewiki_plugins["action"]["info"]);
        }

        # HTML Handling
        $ewiki_use_editor=0;
        if($wiki->htmlmode == 0) {
            # No HTML
            $ewiki_config["htmlentities"]=array(); // HTML is managed by moodle
            $moodle_format=FORMAT_TEXT;
        }
        if($wiki->htmlmode == 1) {
            # Safe HTML
            include_once($CFG->dirroot."/mod/wiki/ewiki/plugins/moodle/moodle_rescue_html.php");
            $moodle_format=FORMAT_HTML;
        }
        if($wiki->htmlmode == 2) {
            # HTML Only 
            $moodle_format=FORMAT_HTML;
            $ewiki_use_editor=1;
            $ewiki_config["htmlentities"]=array(); // HTML is allowed
            $ewiki_config["wiki_link_regex"] = "\007 [!~]?(
                        \#?\[[^<>\[\]\n]+\] |
                        \^[-".EWIKI_CHARS_U.EWIKI_CHARS_L."]{3,} |
                        \b([\w]{3,}:)*([".EWIKI_CHARS_U."]+[".EWIKI_CHARS_L."]+){2,}\#?[\w\d]* |
                        \w[-_.+\w]+@(\w[-_\w]+[.])+\w{2,}   ) \007x";
        }

        global $ewiki_author, $USER;
        $ewiki_author=fullname($USER);
        $content=ewiki_page($wikipage);
        $content2='';

        ### RESTORE ID from Moodle
        $_REQUEST["id"]=$moodleID;
        $id=$moodleID;
///     ################# EWIKI Part ###########################
    }
    else {
        $content = '';
        $content2 = '<div align="center">'.get_string('nowikicreated', 'wiki').'</div>';
    }


    # Group wiki, ...: No wikipage and no ewiki_title
    if(!isset($ewiki_title)) {
          $ewiki_title="";
    }
        
/// Moodle Log
    add_to_log($course->id, "wiki", $ewiki_action, "view.php?id=$cm->id&groupid=$groupid&userid=$userid&wikipage=$wikipage", $wiki->name." ".$ewiki_title);


/// Print the page header

    $strwikis = get_string("modulenameplural", "wiki");
    $strwiki  = get_string("modulename", "wiki");

    print_header_simple(($ewiki_title?$ewiki_title:$wiki->name), "",
                "<A HREF=\"index.php?id=$course->id\">$strwikis</A> -> <A HREF=\"view.php?id=$moodleID\">$wiki->name</a>".($ewiki_title?" -> $ewiki_title":""),
                "", "", true, update_module_button($cm->id, $course->id, $strwiki),
                navmenu($course, $cm));


    /// Print Page

    /// The top row contains links to other wikis, if applicable.
    if ($wiki_list = wiki_get_other_wikis($wiki, $USER, $course, $wiki_entry->id)) {
        $selected="";
        if (isset($wiki_list['selected'])) {
            $selected = $wiki_list['selected'];
            unset($wiki_list['selected']);
        }
        echo '<tr><td colspan="2">';

        echo '<form name="otherwikis" action="'.$CFG->wwwroot.'/mod/wiki/view.php">';
        echo '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>';
        echo '<td class="sideblockheading" bgcolor="'.$THEME->cellheading.'">&nbsp;'
            .$WIKI_TYPES[$wiki->wtype].' '
            .get_string('modulename', 'wiki')." ".get_string('for',"wiki")." "
            .wiki_get_owner($wiki_entry).':</td>';

        echo '<td class="sideblockheading" bgcolor="'.$THEME->cellheading.'" align="right">'
            .get_string('otherwikis', 'wiki').':&nbsp;&nbsp;';
        $script = 'self.location=document.otherwikis.wikiselect.options[document.otherwikis.wikiselect.selectedIndex].value';
        choose_from_menu($wiki_list, "wikiselect", $selected, "choose", $script);
        echo '</td>';        
        echo '</tr></table>';
        echo '</form>';

        echo '</td>';
        echo '</tr>';
    }

    if ($wiki_entry) {
        $specialpages=array("WikiExport", "SiteMap", "SearchPages", "PageIndex","NewestPages","MostVisitedPages","MostOftenChangedPages","UpdatedPages","FileDownload","FileUpload","OrphanedPages","WantedPages");
    /// Page Actions
        echo '<table border="0" width="100%">';
        echo '<tr>';
        
        /// Searchform
        echo '<td align="center">';    
        wiki_print_search_form($cm->id, $q, $userid, $groupid, false);
        echo '</td>';
    
        /// Internal Wikilinks
        echo '<td align="center">';
        wiki_print_wikilinks_block($cm->id,  $wiki->ewikiacceptbinary);
        echo '</td>';
    
        /// Administrative Links
        if($canedit) {
          echo '<td align="center">';          
          wiki_print_administration_actions($wiki, $cm->id, $userid, $groupid, $ewiki_title, $wiki->htmlmode!=2, $course);
          echo '</td>';
        }
        
        /// Formatting Rules
        echo '<td align="right">';          
        helpbutton('howtowiki', get_string('howtowiki', 'wiki'), 'wiki');
        echo '</td>';
        
        echo '</tr></table>';
    }

    if($ewiki_title==$wiki_entry->pagename && !empty($wiki->summary)) {
      if (trim(strip_tags($wiki->summary))) {
          print "<br>";
          print_simple_box(format_text($wiki->summary, FORMAT_MOODLE), "center");
          print "<br>";
      }
    }
    
    // The wiki Contents

    if ($canedit) {   /// Print tabs with commands for this page
        $tabstyle = ' style="padding-left: 5px;padding-right: 5px" ';

        echo '<table border="0">';
        echo "<tr>";
        $tabs = array('view', 'edit','links','info');
        if ($binary) {
            $tabs[] = 'attachments';
        }
        foreach ($tabs as $tab) {
            $tabname = get_string("tab$tab", 'wiki');
            if ($ewiki_action != "$tab" && !in_array($wikipage, $specialpages)) {          
                echo '<td class="generaltab" '.$tabstyle.' bgcolor="'.$THEME->cellheading.'">';
                echo '<a href="'.$ewbase.'&wikipage='.$tab.'/'.$ewiki_id.'">'.$tabname.'</a>';
                echo '</td>';
            } else {
                echo '<td class="generaltabselected" '.$tabstyle.' bgcolor="'.$THEME->cellcontent.'">'.$tabname.'</td>';
            }
        }
        echo "</tr>";
        echo "</table>";
    }
    print_simple_box_start( "right", "100%", "$THEME->cellcontent", "20");
    if($ewiki_action=="edit") {
      # When editing, the filters shall not interfere the wiki-source
      print $content.$content2;
    } else {
      //print(format_text($content, $moodle_format));    /// DISABLED UNTIL IT CAN BE FIXED
      print $content;
      print $content2;
    }
    print_simple_box_end();
    echo "<br clear=all />";

/// Finish the page
    print_footer($course);
?>
