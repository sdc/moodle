<div class="categories">
	<?php
  $pluginname = 'theme_roshni';
  $catheadval = 'catheadval';
  $categoriesheading = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$catheadval.'"');
  if($categoriesheading) {
    $category_heading = json_decode($categoriesheading->value,true);
  } else {
    $category_heading = '';
  }
  
  if(!empty($category_heading) && $category_heading["catmhead"][0] != null) {
  	$category_headingArray = array();
		foreach ($category_heading as $category_headingkey => $category_headingval) {
			foreach($category_headingval as $category_headingvalValue) {
				$category_headingArray[$category_headingkey] = $category_headingvalValue;
			} 
		}
		?>
		<div class="container">
			<?php 
			if(!empty($category_headingArray["catmhead"])) { 
				if(str_word_count($category_headingArray['catmhead']) == 2) { 
					$lastwordstart = strrpos($category_headingArray['catmhead'], ' ') + 1; 
					$lastword = substr($category_headingArray['catmhead'], $lastwordstart);
					$zap = '';
					$firststring = str_replace($lastword, $zap, $category_headingArray['catmhead']);?>
					<h1 class="h-large"><?php echo $firststring; ?><span><?php echo $lastword?></span></h1>
				<?php } else { ?>
					<h1 class="h-large"><?php echo $category_headingArray["catmhead"]; ?></h1><?php } ?>
			<?php } else { ?>
			<h1 class="h-large">OUR <span>Courses</span></h1>
			<?php }
			if(!empty($category_headingArray["catshead"])) { ?>
			<h3 class="header-b-2"><?php echo $category_headingArray["catshead"]; ?></h3>
			<?php } else { ?>
			<h3 class="header-b-2">You Can Showcase All Your Courses In This Beautiful Masonry Block</h3>
			<?php } ?>
		</div><!-- END of .container -->
		<?php
	} else { ?>
		<div class="container">
			<h1 class="h-large">OUR <span>Courses</span></h1>
			<h3 class="header-b-2">You Can Showcase All Your Courses In This Beautiful Masonry Block</h3>
		</div><!-- END of .container -->
	<?php } ?>
	<div class="categories-items">
		<?php
		$pluginname = 'theme_roshni';
		$categories = 'categories';
		$course_categories = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$categories.'"');
		if($course_categories) {
			$course_category = json_decode($course_categories->value,true);
		} else {
			$course_category = '';
		}
		
		if(!empty($category_heading) && $category_heading["catmhead"][0] != null) {
			$course_categoryArray = array();
			foreach ($course_category as $course_categorykey => $course_categoryval) {
				foreach($course_categoryval as $course_categoryvalKey => $course_categoryvalValue) {
					$course_categoryArray[$course_categoryvalKey][$course_categorykey] = $course_categoryvalValue;
				} 
			}
			foreach($course_categoryArray as $course_categoryArraydetails) {
			?>
			<div class="categories-item view fourth-effect">
			<?php if(!empty($course_categoryArraydetails["catimage"])) { ?>
			<img src="<?php echo $course_categoryArraydetails["catimage"]; ?>" alt="">
			<?php } else { ?>
			<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/data/cat-1.jpg" alt="">
			<?php } ?>
			<div class="mask"></div>
			<div class="categories-item-cont">
				<?php if(!empty($course_categoryArraydetails["catname"])) { ?>
				<h5><a href="<?php echo $course_categoryArraydetails["catlnkpage"];?>;"><?php echo $course_categoryArraydetails["catname"];?></a></h5>
				<?php } else { ?>
				<h5><a href="javascript:void(0);">Quisque posuere lacus</a></h5>
				<?php } ?>
				<?php if(!empty($course_categoryArraydetails["subhead"])) { ?>
				<p><?php echo $course_categoryArraydetails["subhead"];?></p>
				<?php } else { ?>
				<p>Integer viverra ante</p>
				<?php } ?>
			</div>
			</div><!-- END of .categories-item -->
			<?php
			}
			?>
		
		<?php
		} else { ?>
			<div class="categories-item view fourth-effect">
				<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/data/cat-1.jpg" alt="">
				<div class="mask"></div>
				<div class="categories-item-cont">
					<h5><a href="javascript:void(0);">Egronomics</a></h5>
					<p>Integer viverra ante</p>
				</div>
			</div><!-- END of .categories-item -->
			<div class="categories-item view fourth-effect">
				<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/data/cat-2.jpg" alt="">
				<div class="mask"></div>
				<div class="categories-item-cont">
					<h5><a href="javascript:void(0);">History</a></h5>
					<p>Integer viverra ante</p>
				</div>
			</div><!-- END of .categories-item -->
			<div class="categories-item view fourth-effect">
				<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/data/cat-3.jpg" alt="">
				<div class="mask"></div>
				<div class="categories-item-cont">
					<h5><a href="javascript:void(0);">Cell Biology</a></h5>
					<p>Integer viverra ante</p>
				</div>
			</div><!-- END of .categories-item -->
			<div class="categories-item view fourth-effect">
				<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/data/cat-4.jpg" alt="">
				<div class="mask"></div>
				<div class="categories-item-cont">
					<h5><a href="javascript:void(0);">Structural Engineering</a></h5>
					<p>Integer viverra ante</p>
				</div>
			</div><!-- END of .categories-item -->
			<div class="categories-item view fourth-effect">
				<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/data/cat-5.jpg" alt="">
				<div class="mask"></div>
				<div class="categories-item-cont">
					<h5><a href="javascript:void(0);">Automotive Engineering</a></h5>
					<p>Integer viverra ante</p>
				</div>
			</div><!-- END of .categories-item -->
			<div class="categories-item view fourth-effect">
				<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/data/cat-6.jpg" alt="">
				<div class="mask"></div>
				<div class="categories-item-cont">
					<h5><a href="javascript:void(0);">Social Sciences</a></h5>
					<p>Integer viverra ante</p>
				</div>
			</div><!-- END of .categories-item -->
			<div class="categories-item view fourth-effect">
				<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/data/cat-7.jpg" alt="">
				<div class="mask"></div>
				<div class="categories-item-cont">
					<h5><a href="javascript:void(0);">Petrology</a></h5>
					<p>Integer viverra ante</p>
				</div>
			</div><!-- END of .categories-item -->
			<div class="categories-item view fourth-effect">
				<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/data/cat-8.jpg" alt="">
				<div class="mask"></div>
				<div class="categories-item-cont">
					<h5><a href="javascript:void(0);">Urban and City Planning</a></h5>
					<p>Integer viverra ante</p>
				</div>
			</div><!-- END of .categories-item -->
			<?php
		} ?>
	</div><!-- END of .categories-items -->
</div><!-- END of .categories -->




