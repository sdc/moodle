<div class="about">
	<div class="container">
		<?php
		$pluginname = 'theme_roshni';
		$aboutsite = 'abtsite';
		$aboutsite_blocks = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$aboutsite.'"');
		if(!empty($aboutsite_blocks)) { 
			$aboutsite_block = json_decode($aboutsite_blocks->value,true);
		} else {
			$aboutsite_block = '';
		}
		
		$aboutsiteArray=array();
		if(!empty($aboutsite_block) && $aboutsite_block["textone"][0] != null) {
			foreach ($aboutsite_block as $key => $aboutsite_blockval) {
				foreach($aboutsite_blockval as $aboutsite_blockvalValue) {
					$aboutsiteArray[$key] = $aboutsite_blockvalValue;
				}
			}
			
			if(!empty($aboutsiteArray['textone'])) { 
				if(str_word_count($aboutsiteArray['textone']) == 2) { 
					$lastwordstart = strrpos($aboutsiteArray['textone'], ' ') + 1; 
					$lastword = substr($aboutsiteArray['textone'], $lastwordstart);
					$zap = '';
					$firststring = str_replace($lastword, $zap, $aboutsiteArray['textone']);?>
					<h1 class="h-large"><?php echo $firststring; ?><span><?php echo $lastword?></span></h1>
				<?php } else { ?>
				<h1 class="h-large"><?php echo $aboutsiteArray['textone']; ?></h1>
				<?php } ?>
				<?php
			} else { ?>	
				<h1 class="h-large">NOBODY DOES IT LIKE US<span></span></h1>
			  <?php
			}	
			if(!empty($aboutsiteArray['texttwo'])) { ?>
				<h3 class="header-b-2"><?php echo $aboutsiteArray['texttwo']; ?></h3>
				<?php
			}	else { ?>
				<h3 class="header-b-2">Put In a Nice Little Piece Of Text That Describes Your USP</h3>
				<?php
			}
		} else { ?>
			<h1 class="h-large">NOBODY DOES IT LIKE US<span></span></h1>
			<h3 class="header-b-2">Put In a Nice Little Piece Of Text That Describes Your USP</h3>
			<?php
		}?>
		
		
		
		<!-- For blocks contents -->
		
		<?php
		$pluginname = 'theme_roshni';
		$addblock = 'addblock';
		$aboutsite_addblocks = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$addblock.'"');
		if(!empty($aboutsite_addblocks)) { 
			$aboutsite_addblock = json_decode($aboutsite_addblocks->value,true);
		} else {
			$aboutsite_addblock = '';
		}

		$addblockArray = array();
		if(!empty($aboutsite_addblock) && $aboutsite_addblock["blockicon"][0] != null) {
			foreach ($aboutsite_addblock as $key => $aboutsite_addblockval) {
				foreach($aboutsite_addblockval as $aboutsite_addblockvalKey => $aboutsite_addblockvalValue) {
					$addblockArray[$aboutsite_addblockvalKey][$key] = $aboutsite_addblockvalValue;
				}
			}

			?>
			<div class="clearfix"></div>
			<div class="about-items">
				<?php 
				foreach($addblockArray as $addblockArray_Value) {
					?>
					<div class="about-item">
						<div class="about-item-img-wr">
							<?php if(!empty($addblockArray_Value["blockicon"])) { ?>
							<div class="about-item-img">
								<a href="javascript:void(0);"><i class="fa <?php echo $addblockArray_Value["blockicon"]; ?>"></i></a>
							</div>
							<?php } else {?>
							<div class="about-item-img">
								<a href="javascript:void(0);"><i class="fa fa-book"></i></a>
							</div>
							<?php } ?>
						</div>
						
						<?php if(!empty($addblockArray_Value["blocktextsone"])) { ?>
						<h5><a href="<?php echo $addblockArray_Value["blocklink"]; ?>"><?php echo $addblockArray_Value["blocktextsone"]; ?></a></h5>
						<?php } else {?>
						<h5><a href="<?php echo $CFG->wwwroot; ?>/mod/forum/user.php?id=<?php echo $USER->id; ?>">Courses</a></h5>
						<?php } ?>
						
						<?php if(!empty($addblockArray_Value["blocktextstwo"])) { ?>
								<p><?php echo $addblockArray_Value["blocktextstwo"]; ?></p>
						<?php } else {?>
							<p>You can rename the content box names from the admin panel, and then add nifty descriptions for all the content boxes.</p>
						<?php } ?>
					</div><!-- END of .about-item -->
					<?php
				} ?>
			</div><!-- END of .about-items -->
			<?php
		} else { ?>
			<div class="clearfix"></div>
			<div class="about-items">
				<div class="about-item">
					<div class="about-item-img-wr">
						<div class="about-item-img">
							<a href="javascript:void(0);"><i class="fa fa-quote-right"></i></a>
						</div>
					</div>
					<h5><a href="<?php echo $CFG->wwwroot; ?>/mod/forum/user.php?id=<?php echo $USER->id; ?>">Our Blog</a></h5>
					<p>There's only one way to find out what life can be like at University of Utopia: dip into some of our students' uncut and uncensored blogs.</p>
				</div><!-- END of .about-item -->
				<div class="about-item">
					<div class="about-item-img-wr">
						<div class="about-item-img">
							<a href="javascript:void(0);"><i class="fa fa-book"></i></a>
						</div>
					</div>
					<h5><a href="<?php echo $CFG->wwwroot; ?>/course/index.php">Courses</a></h5>
					<p>You can rename the content box names from the admin panel, and then add nifty descriptions for all the content boxes.</p>
				</div><!-- END of .about-item -->
				<div class="about-item">
					<div class="about-item-img-wr">
						<div class="about-item-img">
							<a href="javascript:void(0);"><i class="fa fa-file-o"></i></a>
						</div>
					</div>
					<h5><a href="<?php echo $CFG->wwwroot; ?>/blog/index.php?userid=<?php echo $USER->id; ?>">Latest News</a></h5>
					<p>Wondering what is happening at you? A lot. And reading through this section will keep you updated about all the cutting edge research we are doing here!</p>
				</div><!-- END of .about-item -->
				<div class="about-item">
					<div class="about-item-img-wr">
						<div class="about-item-img">
							<a href="javascript:void(0);"><i class="fa fa-calendar"></i></a>
						</div>
					</div>
					<h5><a href="<?php echo $CFG->wwwroot; ?>/calendar/view.php">Upcoming Events</a></h5>
					<p>All these content boxes are completely editable. You can change the hover colors, icons, names and the description text. Cool, isn't it?</p>
				</div><!-- END of .about-item -->
			</div><!-- END of .about-items -->
			<?php
		}
		?>
	</div><!-- END of .container -->
</div><!-- END of .about -->



