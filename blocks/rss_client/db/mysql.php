<?php //$Id$

function block_rss_client_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2003111500) {
       # Do something ...
    }

    return true;
}

?>
