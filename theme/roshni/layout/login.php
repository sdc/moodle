<?php
// Get the HTML for the settings bits.

$favicon = get_config("theme_roshni","faviconimg");
$logosettings = get_config("theme_roshni", "logostyle");
$logosetting = json_decode($logosettings, true);
if ($favicon != "") {
    $favicondetails = trim(stripcslashes($favicon),'"');
} else {
    $favicondetails = $CFG->wwwroot . '/theme/roshni/favicon.ico';
}
echo $OUTPUT->doctype()
?>

<html <?php echo $OUTPUT->htmlattributes(); ?>>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $OUTPUT->page_title(); ?></title>
		<?php echo $OUTPUT->standard_head_html() ?>
		<link type="image/x-icon" rel="shortcut icon" href="<?php echo $favicondetails;?>">
		<link rel="stylesheet" href="<?php echo $CFG->wwwroot ?>/theme/roshni/css/font-awesome.css">	
		<link type="text/css" rel="Stylesheet" href="<?php echo $CFG->wwwroot ?>/theme/roshni/css/styles.css">

		<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/jquery-2.1.4.js"></script>
		<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/bootstrap.min.js"></script>
		<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/jquery.bxslider.min.js"></script>
		<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/jquery.scroll.js"></script>
		<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/engine.js"></script>
		<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/backtop.js"></script>
		<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/nav.js"></script>

		<?php 
			include($CFG->dirroot . '/theme/roshni/settings/themecolor.php');
			include $CFG->dirroot . '/theme/roshni/settings/pagebackgroundlayout.php';
			include($CFG->dirroot . '/theme/roshni/settings/themefont.php');
		?>
		<style type="text/css">
			#loginerrormessage {
				display: none;
			}
		</style>
	</head>

<body <?php include $CFG->dirroot . '/theme/roshni/analyticstracking.php';
	echo $OUTPUT->body_attributes(); ?>>
	<?php echo $OUTPUT->standard_top_of_body_html() ?>
	<div id="page" class="container-fluid login-page">
		<header id="page-header" class="clearfix row-fluid">
			<?php require('headers.php');?>
			<div id="course-header"></div>
		</header>
	
		<div id="page-content" class="row-fluid background-grey">
			<div class="login-information">
				<div class="container no-background">
					<div class="span6"><a href="javascript:void(0);"><?php echo get_string('loginsite');?></a></div>
					<div class="span6 right"><span><?php echo get_string('loggedinnot');?></span></div>
				</div>
			</div>
			<section id="region-main" class="span12 loginbox">
				<?php 
				echo $OUTPUT->course_content_header();
				?>
				<div class="container">
				<?php echo $OUTPUT->main_content(); ?>
				</div>
				<?php
				echo $OUTPUT->course_content_footer();
				?>
			</section>
		</div>
	</div>
	<?php require('footer.php'); echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>