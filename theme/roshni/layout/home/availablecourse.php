<div class = "clearfix"></div>
<div class="av-courses header2-nav-color">
	<?php
	$pluginname = 'theme_roshni';
	$avctitle = 'avctitle';
	$avctitles = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$avctitle.'"');
	$availablecourselinktitle = json_decode(get_config("theme_roshni","avlinkname"), true);
	$availablecourselink = json_decode(get_config("theme_roshni","avlink"), true);
	if(!empty($avctitles)) { 
		$avc_title = json_decode($avctitles->value,true);
	} else {
		$avc_title = '';
	}
	
	
	$avc_titlevalArray=array();
	if(!empty($avc_title) && $avc_title["title"][0] != null) {
		foreach ($avc_title as $key => $avc_titleval) {
			foreach($avc_titleval as $avc_titlevalValue) {
				$avc_titlevalArray[$key] = $avc_titlevalValue;
			}
		}
		if(!empty($avc_titlevalArray["title"])) {
		?>
			<div class="header-top">
				<h2 class="header-b"><?php echo $avc_titlevalArray["title"]; ?></h2>
			</div>
			<?php 
		}
	} else { ?>
		<div class="header-top">
			<h2 class="header-b">Departments</h2>
		</div>
		<?php
	} ?>
	
	
	<?php
	$pluginname = 'theme_roshni';
	$avlcourse = 'avlcourse';
	$available_courses = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$avlcourse.'"');
	if(!empty($available_courses)) { 
		$avl_course = json_decode($available_courses->value,true);
	} else {
		$avl_course = '';
	}
	$avl_courseArray = array();
	if(!empty($avl_course) && $avl_course["image"][0] != null) {
		foreach ($avl_course as $key => $avl_courseval) {
			foreach($avl_courseval as $avl_coursevalKey => $avl_coursevalValue) {
				$avl_courseArray[$avl_coursevalKey][$key] = $avl_coursevalValue;
			}
		} 
		?>
		<ul class="av-courses-slider">
			<?php foreach($avl_courseArray as $avl_courseArray_value) { ?>
				<li class = "view fourth-effect">
					<?php if(!empty($avl_courseArray_value["image"])) { ?> 
					<img src="<?php echo $avl_courseArray_value["image"]; ?>" alt="">
					<?php } else { ?>
					<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/c-1.jpg" alt="">
					<?php } ?>
					
					<?php if(!empty($avl_courseArray_value["textone"]) && !empty($avl_courseArray_value["texttwo"])) { ?> 
						<div class="mask"></div>
						<div class="av-course-item-cont">
							<h2><a href="javascript:void(0);"><?php echo $avl_courseArray_value["textone"]; ?></a></h2>
							<p><?php echo $avl_courseArray_value["texttwo"]; ?></p>
						</div>
					<?php } else { ?>
						<div class="mask"></div>
						<div class="av-course-item-cont">
							<h2><a href="javascript:void(0);">Course - Three</a></h2>
							<p>vehicula aliquam</p>
						</div>
					<?php } ?>
				</li>
			<?php } ?>
		</ul><!-- END of .av-courses-slider -->
		<?php 
	} else { ?>
		<ul class="av-courses-slider">
			<li>
				<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/c-1.jpg" alt="">
				<div class="av-course-item-cont">
					<h2><a href="javascript:void(0);">Architecture</a></h2>
					<p>School of Planning and Architecture</p>
				</div>
			</li>
			<li>
				<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/c-2.jpg" alt="">
				<div class="av-course-item-cont">
					<h2><a href="javascript:void(0);">Humanities & Arts</a></h2>
					<p>School of Humanities, Social Sciences and Arts</p>
				</div>
			</li>
			<li>
				<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/c-3.jpg" alt="">
				<div class="av-course-item-cont">
					<h2><a href="javascript:void(0);">Basic Sciences</a></h2>
					<p>School of Basic Sciences</p>
				</div>
			</li>
			<li>
				<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/c-4.jpg" alt="">
				<div class="av-course-item-cont">
					<h2><a href="javascript:void(0);">Entrepreneurship</a></h2>
					<p>School of Entrepreneurship and Business</p>
				</div>
			</li>
			<li>
				<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/c-5.jpg" alt="">
				<div class="av-course-item-cont">
					<h2><a href="javascript:void(0);">Multidisciplinary</a></h2>
					<p>Multidisciplinary Courses</p>
				</div>
			</li>
		</ul><!-- END of .av-courses-slider -->
		<?php
	} ?>
	
	<div>
		<a href="<?php if($availablecourselink) { echo $availablecourselink; } else { ?><?php echo $CFG->wwwroot ?>/course/index.php<?php } ?>" class="btn-view-all"><?php if(!empty($availablecourselinktitle)) { echo trim($availablecourselinktitle ,'"'); } else { ?>View All Courses<?php } ?></a>
	</div>
</div><!-- END of .av-courses -->

