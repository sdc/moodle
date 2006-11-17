<?php // Library functions for using AJAX with Moodle

/**
 * Print require statements for javascript libraries.
 * Takes in an array of either full paths or shortnames and it will translate
 * them to full paths.
 **/
function require_js($list) {
    global $CFG;
    $output = '';

    if (!ajaxenabled()) {
        return;
    }

    //list of shortname to filepath translations
    $translatelist = array(
            'yui_yahoo' => '/lib/yui/yahoo/yahoo-min.js',
            'yui_dom' => '/lib/yui/dom/dom-min.js',
            'yui_event' => '/lib/yui/event/event-min.js',
            'yui_dragdrop' => '/lib/yui/dragdrop/dragdrop-min.js',
            'yui_logger' => '/lib/yui/logger/logger-min.js',
            'yui_connection' => '/lib/yui/connection/connection-min.js',        
            'ajaxcourse_blocks' => '/lib/ajax/block_classes.js',
            'ajaxcourse_sections' => '/lib/ajax/section_classes.js',
            'ajaxcourse' => '/lib/ajax/ajaxcourse.js'
            );


    for ($i=0; $i<count($list); $i++) {
        if ($translatelist[$list[$i]]) {
            $output .= "<script type='text/javascript' src='".$CFG->wwwroot.''.$translatelist[$list[$i]]."'></script>\n";
            if ($translatelist[$list[$i]] == '/lib/yui/logger/logger-min.js') {
                // Special case. We need the css.
                $output .= "<link type='text/css' rel='stylesheet' href='{$CFG->wwwroot}/lib/yui/logger/assets/logger.css'>";
            }
        } else {
            $output .= "<script type='text/javascript' src='".$CFG->wwwroot.''.$list[$i]."'></script>\n";
        }
    }
    return $output;
}


/**
 * Returns whether ajax is enabled/allowed or not.
 */
function ajaxenabled() {

    global $CFG, $USER;

    if (!check_browser_version('MSIE', 6.0)
                && !check_browser_version('Firefox', 1.5)
                && !check_browser_version('Camino', '1.0.2')) {
        // We still have issues with AJAX in other browsers.
        return false;
    }

    if (!empty($CFG->enableajax) && !empty($USER->ajax)) {
        return true;
    } else {
        return false;
    }
}


/**
 * Used to create view of document to be passed to JavaScript on pageload.
 * We use this class to pass data from PHP to JavaScript.
 */
class jsportal {

    var $currentblocksection = null;
    var $blocks = array();


    /**
     * Takes id of block and adds it
     */
    function block_add($id, $hidden=false){
        $hidden_binary = 0;

        if ($hidden) {
            $hidden_binary = 1;
        }
        $this->blocks[count($this->blocks)] = array($this->currentblocksection, $id, $hidden_binary);
    }


    /**
     * Prints the JavaScript code needed to set up AJAX for the course.
     */
    function print_javascript($courseid, $return=false) {
        global $CFG;

        $blocksoutput = $output = '';
        for ($i=0; $i<count($this->blocks); $i++) {
            $blocksoutput .= "['".$this->blocks[$i][0]."',
                             '".$this->blocks[$i][1]."',
                             '".$this->blocks[$i][2]."']";

            if ($i != (count($this->blocks) - 1)) {
                $blocksoutput .= ',';
            }
        }
        $output .= "<script language='javascript'>\n";
        $output .= " 	main.portal.id = ".$courseid.";\n";
        $output .= "    main.portal.blocks = new Array(".$blocksoutput.");\n";
        $output .= "    main.portal.strings['wwwroot']='".$CFG->wwwroot."';\n";
        $output .= "    main.portal.strings['pixpath']='".$CFG->pixpath."';\n";
        $output .= "    main.portal.strings['update']='".get_string('update')."';\n";
        $output .= "    main.portal.strings['deletecheck']='".get_string('deletecheck','','_var_')."';\n";
        $output .= "    main.portal.strings['resource']='".get_string('resource')."';\n";
        $output .= "    main.portal.strings['activity']='".get_string('activity')."';\n";
        $output .= "    onloadobj.load();\n";
        $output .= "    main.process_blocks();\n";
        $output .= "</script>";
        if ($return) {
            return $output;
        } else {
            echo $output;
        }
    }

}

?>