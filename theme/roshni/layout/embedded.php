<?php

$favicon = get_config("theme_roshni","faviconimg");
if ($favicon != "") {
    $favicondetails = trim(stripcslashes($favicon),'"');
} else {
    $favicondetails = $CFG->wwwroot . '/theme/roshni/favicon.ico';
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
    <script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/jquery-1.11.1.min.js"></script>
	<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/bootstrap.min.js"></script>
	<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/jquery.bxslider.min.js"></script>
	<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/jquery.scroll.js"></script>
	<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/engine.js"></script>
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
