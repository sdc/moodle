<?php

// Get the HTML for the settings bits.
$html = theme_roshni_get_html_for_settings($OUTPUT, $PAGE);
GLOBAL $USER, $CFG, $DB;
// Set default (LTR) layout mark-up for a two column page (side-pre-only).
$regionmain = 'span9 pull-right two-column-main'; /* changes classname two-column-main */
$sidepre = 'span3 desktop-first-column two-column-sidebar'; /* changes classname two-column-sidebar */
// Reset layout mark-up for RTL languages.
if (right_to_left()) {
    $regionmain = 'span9 two-column-main'; /* changes classname */
    $sidepre = 'span3 pull-right two-column-sidebar'; /* changes classname */
}
$logosettings = get_config("theme_roshni","logostyle");
$logosetting = json_decode($logosettings, true);
$favicon = get_config("theme_roshni","faviconimg");

if ($favicon != '""') {
    $favicondetails = trim(stripcslashes($favicon),'"');
} else {
    $favicondetails = $CFG->wwwroot . '/theme/roshni/favicon.ico';
}
$pluginname = 'theme_roshni';
$headerstyle = 'header';
$headerstyles = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$headerstyle.'"');
if(!empty($headerstyles)) { 
    $headerdetails = json_decode($headerstyles->value,true);
} else {
    $headerdetails = '';
}
echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="image/x-icon" rel="shortcut icon" href="<?php echo $favicondetails;?>">
	<link rel="stylesheet" href="<?php echo $CFG->wwwroot ?>/theme/roshni/css/font-awesome.css">
	<link type="text/css" rel="Stylesheet" href="<?php echo $CFG->wwwroot ?>/theme/roshni/css/styles.css">
    <script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/jquery-2.1.4.js"></script>
	<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/bootstrap.min.js"></script>
	<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/jquery.bxslider.min.js"></script>
	<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/jquery.scroll.js"></script>
    <script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/nav.js"></script>
    <script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/backtop.js"></script>
	
    <?php
      include $CFG->dirroot . '/theme/roshni/settings/themecolor.php';
      include $CFG->dirroot . '/theme/roshni/settings/pagebackgroundlayout.php';
    ?>
</head>

<body <?php echo $OUTPUT->body_attributes('two-column'); ?>>

<?php include $CFG->dirroot . '/theme/roshni/analyticstracking.php';
echo $OUTPUT->standard_top_of_body_html() ?>

<header class="navbar navbar-fixed-top<?php echo $html->navbarclass ?> moodle-has-zindex">
    <?php require('headers.php');?>
</header>

<div id="page" <?php if(($headerdetails == "Style1") || ($headerdetails == "Style2")) {?>class = "custom-page-header"<?php } ?>>
    <?php if ($CFG->version >= 2015051100){
        echo $OUTPUT->full_header();
    } else { ?>
    <header id="page-header" class="clearfix">
        <div id="page-navbar" class="clearfix">
            <div class = "container">
                <div class="row">
                    <nav class="breadcrumb-nav"><?php echo $OUTPUT->navbar(); ?></nav>
                    <div class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></div>
                </div>
            </div>
        </div>
        <div id="course-header">
            <?php echo $OUTPUT->course_header(); ?>
        </div>
    </header>
    <?php } ?>
    <div id="page-content" class="row-fluid background-grey">
    	<div class="container">
        <section id="region-main" class="<?php echo $regionmain; ?> no-brodrer">
            <?php
            echo $OUTPUT->course_content_header();
            echo $OUTPUT->main_content();
            echo $OUTPUT->course_content_footer();
            ?>
        </section>
        <?php echo $OUTPUT->blocks('side-pre', $sidepre);?>
      </div> 
    </div>

    <?php require('footer.php'); echo $OUTPUT->standard_end_of_body_html() ?>

</div>
</body>
</html>
