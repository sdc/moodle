<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_login();

if (!$site = get_site()) {
    redirect("index.php");
}

if (!isadmin()) {
    error("Only the admin can use this page");
}

/// get language strings
$str = get_strings(array('enrolments', 'users', 'administration', 'settings'));

print_header("$site->shortname: $str->enrolments", "$site->fullname",
              "<a href=\"../../admin/index.php\">$str->administration</a> -> 
               <a href=\"../../admin/users.php\">$str->users</a> -> 
               $str->enrolments -> IMS import");

require_once('enrol.php');

//echo "Creating the IMS Enterprise enroller object\n";
$enrol = new enrolment_plugin_imsenterprise();

?>
<p>Launching the IMS Enterprise "cron" function. The import log will appear below (giving details of any 
problems that might require attention).</p>
<pre style="margin:10px; padding: 2px; border: 1px solid black; background-color: white; color: black;"><?php
//error_reporting(E_ALL);
$enrol->cron();
?></pre><?php
print_footer();

exit;
?>