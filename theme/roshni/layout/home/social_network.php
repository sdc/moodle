<div class="stay-connected">
	<div class="container">
		<h2 class="header-b-2">STAY CONNECTED</h2>
		<?php
		$pluginname = 'theme_roshni';
		$social = 'social';
		$socialnetwork = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$social.'"');
		if($socialnetwork) {
		$fsocial = json_decode($socialnetwork->value,true);
		} else {
		$fsocial = '';
		}
		if(!empty($fsocial) && $fsocial["facebook"][0] != NULL) { ?>
			<div class="social-links">
			<?php
			foreach($fsocial as $key => $fscl) {
				foreach($fscl as $sites) {
					if(!empty($sites)) {
						?>
						<a href="<?php echo $sites; ?>"><i class="fa fa-<?php echo $key; ?>"></i></a>
						<?php
					}
				}
			}
			?>
			</div>
			<?php
    	} else { ?>
			<div class="social-links">
				<a href="javascript:void(0);"><i class="fa fa-facebook"></i></a>
				<a href="javascript:void(0);"><i class="fa fa-twitter"></i></a>
				<a href="javascript:void(0);"><i class="fa fa-linkedin"></i></a>
				<a href="javascript:void(0);"><i class="fa fa-google-plus"></i></a>
				<a href="javascript:void(0);"><i class="fa fa-dribbble"></i></a>
				<a href="javascript:void(0);"><i class="fa fa-youtube"></i></a>
				<a href="javascript:void(0);"><i class="fa fa-vimeo-square"></i></a>
				<a href="javascript:void(0);"><i class="fa fa-rss"></i></a>
				<a href="javascript:void(0);"><i class="fa fa-flickr"></i></a>
				<a href="javascript:void(0);"><i class="fa fa-pinterest"></i></a>
			</div>
		<?php } ?>	
	</div><!-- END of .container -->
</div><!-- END of .stay-connected -->



