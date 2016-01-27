<div class = "clearfix"></div>
<div class="stay-connected">
	<div class="container">
		<?php 
			$socialheadings = get_config("theme_roshni","socialheading");
			$socialheading = json_decode($socialheadings, true);
			if(!empty($socialheading)) { 
		?>
		<h2 class="header-b-2"><?php echo $socialheading;?></h2>
		<?php } else { ?>
		<h2 class="header-b-2">STAY CONNECTED</h2>
		<?php } ?>
		<?php
		$pluginname = 'theme_roshni';
		$social = 'social';
		$socialnetwork = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$social.'"');
		if($socialnetwork) {
		$fsocial = json_decode($socialnetwork->value,true);
		} else {
		$fsocial = '';
		}
		if(!empty($fsocial)) { ?>
			<div class="social-links">
			<?php
			if (empty($fsocial[0]) && empty($fsocial[1]) && empty($fsocial[2]) && empty($fsocial[3]) && empty($fsocial[4]) && empty($fsocial[5]) && empty($fsocial[6]) && empty($fsocial[7]) && empty($fsocial[8]) && empty($fsocial[9])) { ?>
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
			<?php 
			} else {
			foreach($fsocial as $key => $fscl) {
			if (!empty($fscl)) {
			switch ($key) {
			    case "0":
			    	$faicon	= "facebook";
			        break;
			    case "1":
			        $faicon = "twitter";
			        break;
			    case "2":
			        $faicon = "linkedin";
			        break;
			    case "3":
			        $faicon = "google-plus";
			        break;
			    case "4":
			        $faicon = "dribbble";
			        break; 
			    case "5":
			        $faicon = "youtube";
			        break;  
			    case "6":
			        $faicon = "vimeo-square";
			        break; 
			    case "7":
			        $faicon = "rss";
			        break;
			    case "8":
			        $faicon = "flickr";
			        break;
			    case "9":
			        $faicon = "pinterest";
			        break;
			} 
			?>
			<a href="<?php echo $fscl; ?>"><i class="fa fa-<?php echo $faicon; ?>"></i></a>
					
			<?php }
			}
			}
			?>
			</div>
			<?php
    	} ?>	
	</div><!-- END of .container -->
</div><!-- END of .stay-connected -->



