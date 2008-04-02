<?php
/**
* Global Search Engine for Moodle
* Michael Champanis (mchampan) [cynnical@gmail.com]
* review 1.8+ : Valery Fremaux [valery.fremaux@club-internet.fr] 
* 2007/08/02
*
* This file serves as a splash-screen (entry page) to the indexer script -
* it is in place to prevent accidental reindexing which can lead to a loss
* of time, amongst other things.
**/

require_once('../config.php');
require_once("$CFG->dirroot/search/lib.php");

require_login();

if (empty($CFG->enableglobalsearch)) {
    print_error('globalsearchdisabled', 'search');
}

if (!isadmin()) {
    print_error('beadmin', 'search', "$CFG->wwwroot/login/index.php");
} 

//check for php5 (lib.php)
if (!search_check_php5()) {
    $phpversion = phpversion();
    mtrace("Sorry, global search requires PHP 5.0.0 or later (currently using version $phpversion)");
    exit(0);
}

require_once("$CFG->dirroot/search/indexlib.php");
$indexinfo = new IndexInfo();

if ($indexinfo->valid()) {
    $strsearch = get_string('search', 'search');
    $strquery  = get_string('stats');
    
    $navlinks[] = array('name' => $strsearch, 'link' => "index.php", 'type' => 'misc');
    $navlinks[] = array('name' => $strquery, 'link' => "stats.php", 'type' => 'misc');
    $navlinks[] = array('name' => get_string('runindexer','search'), 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    $site = get_site();
    print_header("$strsearch", "$site->fullname" , $navigation, "", "", true, "&nbsp;", navmenu($site));
 
    mtrace("<pre>The data directory ($indexinfo->path) contains $indexinfo->filecount files, and\n"
          ."there are ".$indexinfo->dbcount." records in the <em>block_search_documents</em> table.\n"
          ."\n"
          ."This indicates that you have already succesfully indexed this site. Follow the link\n"
          ."if you are sure that you want to continue indexing - this will replace any existing\n"
          ."index data (no Moodle data is affected).\n"
          ."\n"
          ."You are encouraged to use the 'Test indexing' script before continuing onto\n"
          ."indexing - this will check if the modules are set up correctly. Please correct\n"
          ."any errors before proceeding.\n"
          ."\n"
          ."<a href='tests/index.php'>Test indexing</a> or "
          ."<a href='indexer.php?areyousure=yes'>Continue indexing</a> or <a href='index.php'>Back to query page</a>."
          ."</pre>");
    print_footer();
} 
else {
    header('Location: indexer.php?areyousure=yes');
}
?>
