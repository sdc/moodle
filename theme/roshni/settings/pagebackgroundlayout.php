<?php

global $CFG,$DB;
$pluginname = 'theme_roshni';
$fieldname = 'playout';
$pagelayout = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$fieldname.'"');

if (!empty($pagelayout) && $pagelayout->value != NULL) {
	if ($pagelayout->value == '"color"') {
		$iscolor = get_config($pluginname, "backgroundcolor");
?>
<style>
	body {
		background-color :<?php echo trim($iscolor, '"'); ?> !important;
		margin: 0 auto !important;
	}
</style>
<?php	
 	} else if ($pagelayout->value == '"image"') {
 		$isiamge = get_config($pluginname, "backgrondimage");
 		$backgroundlayoutstyle = get_config("theme_roshni","backgroundlayoutstyles");
 		if($backgroundlayoutstyle == '"backgroundlayoutstyles1"') { ?>
	 		<style>
				body {
					background-image: url(<?php echo $isiamge; ?>);
					background-repeat: repeat;
					/*background-attachment: fixed;*/
					margin: 0 auto !important;
				}
			</style>
 		<?php } else if ($backgroundlayoutstyle == '"backgroundlayoutstyles2"') { ?>
 				<style>
				body {
					background-image: url(<?php echo $isiamge; ?>);
					background-repeat: repeat;
					background-attachment: fixed;
					margin: 0 auto !important;
				}
			</style>
		<?php } else if ($backgroundlayoutstyle == '"backgroundlayoutstyles3"') { ?>
 				<style>
					body {
						background-image: url(<?php echo $isiamge; ?>);
						background-repeat: no-repeat;
						background-attachment: fixed;
						margin: 0 auto !important;
					}
				</style>
 		<?php } else { ?>
 		<style>
			body {
				background-image: url(<?php echo $isiamge; ?>);
				background-repeat: no-repeat;
				background-attachment: fixed;
				margin: 0 auto !important;
			}
		</style>
		<?php } ?>
		<?php
 	}
} 
$pagecontentlayout = $DB->get_record_sql('select config.value from {config_plugins} config 
	where config.plugin="'.$pluginname.'" and config.name="pagebackgroundcolor"');
if (!empty($pagecontentlayout) && $pagecontentlayout->value != NULL) {
	?>
	<style>
		#page-content div.container {
			background-color: <?php echo trim($pagecontentlayout->value, '"');?> !important;
		}
	</style>
	<?php

}
?>