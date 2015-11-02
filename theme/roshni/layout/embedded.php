<?php

$favicon = get_config("theme_roshni","faviconimg");
if ($favicon != "") {
    $favicondetails = trim(stripcslashes($favicon),'"');
} else {
    $favicondetails = $CFG->wwwroot . '/theme/' . $CFG->theme . '/favicon.ico';
}
echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="image/x-icon" rel="shortcut icon" href="<?php echo $favicondetails;?>">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css">
	<link type="text/css" rel="Stylesheet" href="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/css/styles.css">
    <script src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/js/jquery-1.11.1.min.js"></script>
	<script src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/js/bootstrap.min.js"></script>
	<script src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/js/jquery.bxslider.min.js"></script>
	<script src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/js/jquery.scroll.js"></script>
	<script src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/js/engine.js"></script>
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>
<?php echo $OUTPUT->standard_top_of_body_html() ?>

<div id="page">
    <div id="page-content" class="clearfix background-grey">
        <?php echo $OUTPUT->main_content(); ?>
    </div>
</div>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>
