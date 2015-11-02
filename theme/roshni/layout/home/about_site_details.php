<div class="about">
	<div class="container">
		<h2 class="header-b-2">About University Of Utopia</h2>
		<!-- site details part -->		
		<?php
		$pluginname = 'theme_roshni';
		$sitedetails = 'sitedetails';
		$site_details = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$sitedetails.'"');
		if(!empty($site_details)) { 
			$site_detail = json_decode($site_details->value,true);
		} else {
			$site_detail = '';
		}
		
		
		$site_detailArray = array();
		if(!empty($site_detail)) {
			foreach ($site_detail as $key => $site_detailval) {
				foreach($site_detailval as $site_detailvalKey => $site_detailvalValue) {
					$site_detailArray[$site_detailvalKey][$key] = $site_detailvalValue;
				}
			}
			
			?>

			<div class="tabs-container about-tabs">
				<div class="tabs-nav">
					<?php 
					$count1 = 0;
					$divider = 0;
					foreach($site_detailArray as $site_detailArray_Value) { 
						if($count1 == 0) { $countclass= 'active'; } else { $countclass= '';}
						?>
						<?php if(!empty($site_detailArray_Value["sitehead"])) { ?>
								<?php if($divider > 0) { ?>
								<span>|</span>
								<?php } ?>
								<a href="javascript:void(0);" data-label="<?php echo $count1; ?>" class="<?php echo $countclass; ?>"><?php echo $site_detailArray_Value["sitehead"]; ?></a>
								
							<?php 
						} else { ?>
							<a href="javascript:void(0);" data-label="0" class="active">Rich Legacy</a>
							<span>|</span>
							<a href="javascript:void(0);" data-label="1">Future Roadmap</a>
							<?php 
						} 
						$count1++;
						$divider = $divider + 1;
					} ?>
				</div>
				
				<?php
				$count2 = 0;
				foreach($site_detailArray as $site_detailArray_Value) { 
					//print_r($site_detailArray_Value);
					if($count2 == 0) { $counthiddenclass= ''; } else { $counthiddenclass= ' hidden';}
					if(!empty($site_detailArray_Value["sitetext"])) { ?>
						<div class="tabs-content-item <?php echo $counthiddenclass; ?>" data-target="<?php echo $count2; ?>">
							<p><?php echo $site_detailArray_Value["sitetext"]; ?></p>
							<?php if(!empty($site_detailArray_Value["setpagelink"])) { ?>
							<a href="<?php echo $site_detailArray_Value["setpagelink"]; ?>" class="btn-1"><?php if(!empty($site_detailArray_Value["setpagetext"])) { echo $site_detailArray_Value["setpagetext"]; } else { ?>READ MORE<?php } ?></a>
							<?php } ?>
						</div>
						<?php 
					} else { ?>
						<div class="tabs-content-item " data-target="0">
							<p>Just like all the sections, this section too is highly customizable. You can add tabs, name them as per your wish, add content and change the button text. You may use this section block to promote your USP, highlight your strength areas or simply showcase you rich history. Lot of our beta testers felt that this is one of the most useful content blocks of this theme and they used this block with great impact to visitors. We have more plans about this block for future updates. For example, you will be able to change the location of the tab headers. In future editions of this theme, you can place the tab headers as left aligned, centre aligned or right aligned. Undoubtedly, this kind of a block is not present in any Moodle theme released till date. With Roshni, the capabilities are limited only by your imagination. Go ahead and make your Moodle based theme the most beautiful website in the world.</p>
							<p>The best thing is, we at DualCube are here to help you in whatever way we can. While we will provide free support for any kind of theme related issues, we can also help you with drastic customization of this theme. Just give us a shout and we will be happy to help. We will off course required a detailed brief about the changes you want. Once you have given us the brief, you may just sit back and relax and see the magic!</p>
							<a href="javascript:void(0);" class="btn-1">READ MORE</a>
						</div>
						<div class="tabs-content-item" data-target="1" style="display: none;">
							<p>Moodle themes are too "Moodly", strictly restricted by the rigid framework of Moodle. Roshni is an honest endeavor towards helping Moodle based websites keep abreast with the latest web design trends. Substance is the key, no doubt. However, in this era of fleeting attention spans and multiple options, form is as important, if not more important, that substance. A great website with a lot of content and dated design may end up being the second in the two man race. As you may have realized by now, Roshni despite being a Moodle theme, hardly looks or behaves like a typical Moodle theme. Keep an eye on Roshni, as we will be adding new features in future that will make this theme all the more versatile. Our aim is to make Roshni the most popular unmoodle like Moodle theme.</p>
							<p>Just as you are wondering who we are, well, we are a Kolkata, India based web development agency with more than 6 years of experience in Moodle. We have written plugin, modes and designed and developed free Moodle themes which have been published in Moodle.org. In fact, our free themes and plugins have been downloaded more than 50,000 times in Moodle.org.</p>
							<a href="javascript:void(0);" class="btn-1">READ MORE</a>
						</div>
						<?php
					}
					$count2++;
				} ?>
			</div>
			<?php 
		} else { ?>
			<div class="tabs-container about-tabs">
				<div class="tabs-nav">
					<a href="javascript:void(0);" data-label="0" class="active">Rich Legacy</a>
					<span>|</span>
					<a href="javascript:void(0);" data-label="1">Future Roadmap</a>
				</div>
				<div class="tabs-content-item" data-target="0">
					<p>Just like all the sections, this section too is highly customizable. You can add tabs, name them as per your wish, add content and change the button text. You may use this section block to promote your USP, highlight your strength areas or simply showcase you rich history. Lot of our beta testers felt that this is one of the most useful content blocks of this theme and they used this block with great impact to visitors. We have more plans about this block for future updates. For example, you will be able to change the location of the tab headers. In future editions of this theme, you can place the tab headers as left aligned, centre aligned or right aligned. Undoubtedly, this kind of a block is not present in any Moodle theme released till date. With Roshni, the capabilities are limited only by your imagination. Go ahead and make your Moodle based theme the most beautiful website in the world.</p>
					<p>The best thing is, we at DualCube are here to help you in whatever way we can. While we will provide free support for any kind of theme related issues, we can also help you with drastic customization of this theme. Just give us a shout and we will be happy to help. We will off course required a detailed brief about the changes you want. Once you have given us the brief, you may just sit back and relax and see the magic!</p>
					<a href="javascript:void(0);" class="btn-1">READ MORE</a>
				</div>
				<div class="tabs-content-item hidden" data-target="1">
					<p>Moodle themes are too "Moodly", strictly restricted by the rigid framework of Moodle. Roshni is an honest endeavor towards helping Moodle based websites keep abreast with the latest web design trends. Substance is the key, no doubt. However, in this era of fleeting attention spans and multiple options, form is as important, if not more important, that substance. A great website with a lot of content and dated design may end up being the second in the two man race. As you may have realized by now, Roshni despite being a Moodle theme, hardly looks or behaves like a typical Moodle theme. Keep an eye on Roshni, as we will be adding new features in future that will make this theme all the more versatile. Our aim is to make Roshni the most popular unmoodle like Moodle theme.</p>
					<p>Just as you are wondering who we are, well, we are a Kolkata, India based web development agency with more than 6 years of experience in Moodle. We have written plugin, modes and designed and developed free Moodle themes which have been published in Moodle.org. In fact, our free themes and plugins have been downloaded more than 50,000 times in Moodle.org.</p>
					<a href="javascript:void(0);" class="btn-1">READ MORE</a>
				</div>
			</div>
			<?php 
		} ?>
	</div>
</div>
	