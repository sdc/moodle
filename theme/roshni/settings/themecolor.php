<?php

global $CFG,$DB;



$pluginname = 'theme_roshni';

$fieldname = 'favcolor';

$buttoncolor = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$fieldname.'"');

if(!empty($buttoncolor->value)) {

?>

<style>

.usermenu .menubar a {
	background: <?php echo json_decode($buttoncolor->value); ?>;
}
.block .header h2:after {
	border: solid 1px <?php echo json_decode($buttoncolor->value); ?>;
}
div#page-navbar {
	border-bottom: 13px solid <?php echo json_decode($buttoncolor->value); ?>; /* #ffc908; */
}

aside#block-region-side-pre {
	border-right: 1px solid <?php echo json_decode($buttoncolor->value); ?>; /* #ffc908; */
}

aside#block-region-side-post {
  border-left: 1px solid <?php echo json_decode($buttoncolor->value); ?>;
}

.btn-1 {
	background: <?php echo json_decode($buttoncolor->value); ?> !important;
	border: 1px solid <?php echo json_decode($buttoncolor->value); ?> !important;
}

.av-course-item-cont,.header3.main-menu, .inner-header {
	background: <?php echo json_decode($buttoncolor->value); ?> !important;
}

.about-item:hover{
	background: <?php echo json_decode($buttoncolor->value); ?> !important;
}
.about-item:hover .about-item-img {
	background: none repeat scroll 0 0 <?php echo json_decode($buttoncolor->value); ?> !important;
    color: #000000;
}
/*.about-item p:hover {
	color: #ffffff;
}*/

.navbar .nav > li > a.active, .coursebox>.info>.coursename a, #region-main h2, #region-main a, .footer a, .block_news_items a, .searchform a, .breadcrumb a, .logininfo a, .block .content h3 a, .purgecaches a, .comment-area a, .inline-list a, .myprofileitem.email a, .block_section_links a, .minicalendar.calendartable a, .block_private_files a, .block_admin_bookmarks a, .block_messages a, .block_calendar_upcoming .footer a, .block_news_items a, .searchform a, .breadcrumb a, .logininfo a, .block .content h3 a, .loginbox a, .h-large span, .coursebox > .info > .coursename a, .block_recent_activity a, .ftoggler {
	color: <?php echo json_decode($buttoncolor->value); ?> !important;
}

.h-large span, .av-courses h2{
	color: <?php echo json_decode($buttoncolor->value); ?> !important;
}
.bx-wrapper .bx-pager.bx-default-pager a.active {
	background: <?php echo json_decode($buttoncolor->value); ?> !important;
}
.tabs-nav a.active {
	color: <?php echo json_decode($buttoncolor->value); ?> !important;
}

.partners {
	
	background-color: <?php echo json_decode($buttoncolor->value); ?> !important;
}

.categories-item-cont {
	background: <?php echo json_decode($buttoncolor->value); ?> !important;
}

.stay-connected i {
	background: <?php echo json_decode($buttoncolor->value); ?> !important;
}

.settingsform #setmegamenu, .loginbox a {
	color: <?php echo json_decode($buttoncolor->value); ?> !important;
}

.settingsform h2 {
	color: <?php echo json_decode($buttoncolor->value); ?> !important;
}

.block .header h2 {
	color: <?php echo json_decode($buttoncolor->value); ?> !important;
}

input[type="submit"], input#id_submitbutton, #searchform_button, .adminsearchform input[type="submit"], .btn-2, .login-information, #MegaNavbarID {
	background: <?php echo json_decode($buttoncolor->value); ?> !important;
	border: 1px solid <?php echo json_decode($buttoncolor->value); ?> !important;
}

</style> 

<?php

}

?>





<?php
$pluginname = 'theme_roshni';
$fieldname = 'favcolor2';

$buttontextcolor = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$fieldname.'"');

if(!empty($buttontextcolor->value)) {

?>



<style>
	.btn-1, input[type="submit"], #searchform_button, .adminsearchform input[type="submit"], .top-slide-content .btn-1, .stay-connected i{
	color: <?php echo json_decode($buttontextcolor->value); ?> !important;
	
}
</style>



<?php

}
$avcstrip = get_config("theme_roshni", "cstrip");
$avcstrips = json_decode($avcstrip);


if ($avcstrip == '"image"') {
	$avcstripbackgroundimg = get_config("theme_roshni","avcstripbackgrondimage");
	if($avcstripbackgroundimg != '""') {
		?>
			<style type="text/css">
			.av-courses .header-top {
				background: url(<?php echo $avcstripbackgroundimg; ?>) no-repeat center top;
				background-size: cover !important;
			}
			</style>
		<?php
	}
} else if($avcstrip == '"color"') {
	$avcstripbackgroundcolor = get_config("theme_roshni","avcstripbackgroundcolor");
	$avcstripbackgroundcolors = json_decode($avcstripbackgroundcolor);
	if(!empty($avcstripbackgroundcolors)) {
		?>
			<style type="text/css">
			.header-3 .av-courses .header-top {
				background: none !important;
				background-color: <?php echo $avcstripbackgroundcolors; ?> !important;
			}
			</style>
		<?php
	}
}

$logoimages = get_config("theme_roshni","logoimg");

if($logoimages != "") { ?>
	<style type="text/css">
		.logo {
			background: url(<?php echo $logoimages;?>) no-repeat left center !important;
			background-size: contain !important;
		}	
		.inner-logo {
			background: url(<?php echo $logoimages;?>) no-repeat left center !important;
			background-size: contain !important;
		}
	</style>
	
<?php }
?>



