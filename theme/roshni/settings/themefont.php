<?php

global $CFG,$DB;

$fieldnameheading = 'fontnameheading';

$headingfont = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$fieldnameheading.'"');
if(!empty($headingfont->value)) { ?>
<style>
	h1,
	h2 {
	  font-family: <?php echo $headingfont->value; ?> !important; 
	}
	h1,
	h2,
	h3,
	h4,
	h5,
	h6,
</style>
<?php } 

$pluginname = 'theme_roshni';

$fieldnamebody = 'fontnamebody';


$bodyfont = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$fieldnamebody.'"');
if(!empty($bodyfont->value)) { ?>
<style>
p,
body,
input[type="text"], 
input[type="url"],
textarea,
input[type="submit"],
input.srch-fld,
select, input[type="file"],
html{
    font-family:  <?php echo $bodyfont->value; ?> !important; 
    font-style: normal;
    font-weight: 400;
}

</style>

<?php } 


?>
