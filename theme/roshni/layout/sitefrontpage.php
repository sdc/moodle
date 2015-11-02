<?php
include($CFG->dirroot. '/theme/' . $CFG->theme . '/config.php'); 

// Get the HTML for the settings bits.
$html = theme_roshni_get_html_for_settings($OUTPUT, $PAGE);
 global $DB, $USER, $CFG;
if (right_to_left()) {
    $regionbsid = 'region-bs-main-and-post';
} else {
    $regionbsid = 'region-bs-main-and-pre';
}

echo $OUTPUT->doctype() ;

$pluginname = 'theme_roshni';
$headerstyle = 'header';
$headerstyles = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$headerstyle.'"');
if(!empty($headerstyles)) { 
	$headerdetails = json_decode($headerstyles->value,true);
} else {
	$headerdetails = '';
}

if($headerdetails == "Style1") {
  include("home.php");
} else if($headerdetails == "Style2") {
  include("home-2.php");
} else if($headerdetails == "Style3") {
  include("home-3.php");
} else {
  include("home-3.php");
}//End of if(header-style)

?>
