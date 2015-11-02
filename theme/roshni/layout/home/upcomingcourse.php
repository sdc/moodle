<div class="upcoming-courses">
	<?php
	/*************** For the image position *****************/
	
	$imageposition = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="theme_roshni" and config.name="upcomingimg"');
	if($imageposition) {
		$viewimage = json_decode($imageposition->value);
	} else {
		$viewimage = '';
	}
	
	$pluginname = 'theme_roshni';
  $upcoming = 'upcoming';
  $upcomingcourses = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$upcoming.'"');
  if($upcomingcourses) {
    $upcomingcourse = json_decode($upcomingcourses->value,true);
  } else {
    $upcomingcourse = '';
  }
  $upcomingcourseArray=array();
	if(!empty($upcomingcourse) && $upcomingcourse["upctitle"][0] != null) {
		foreach ($upcomingcourse as $key => $upcomingcourseval) {
			foreach($upcomingcourseval as $upcomingcoursevalKey => $upcomingcoursevalValue) {
				$upcomingcourseArray[$upcomingcoursevalKey][$key] = $upcomingcoursevalValue;
			}
		}
		?>
		<div class="container">
			<?php if(!empty($upcomingcourseArray[0]['upctitle'])) { ?>
			<h2 class="header-b-2"><?php echo $upcomingcourseArray[0]['upctitle']; ?></h2>
			<?php } else { ?>
				<h2 class="header-b-2">Upcoming Courses</h2>
			<?php } ?>
			<ul class="feedback-slider-upcomingcourse">
				<?php foreach($upcomingcourseArray as $upcomingcourseArrayvalue) { ?>

				<li>
					<div class="upcoming-courses-item">
						<?php if(!empty($upcomingcourseArrayvalue['upcimage'])) { ?>
							<img src="<?php echo $upcomingcourseArrayvalue['upcimage']; ?>" alt="" <?php if($viewimage == "right") {?>style="float: right;"<?php } ?>>
						<?php } else { ?>	
							<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/u-1.jpg" alt="">
						<?php } ?>	
						
						<?php if(!empty($upcomingcourseArrayvalue['upcdetails'])) { ?>
							<div class="upcoming-courses-item-cont">
								<?php echo $upcomingcourseArrayvalue['upcdetails']; ?>
								<?php  if(!empty($upcomingcourseArrayvalue['upcbuttontext'])) { ?>
									<a href="<?php echo $upcomingcourseArrayvalue['upclink']; ?>" class="btn-1"><?php echo $upcomingcourseArrayvalue['upcbuttontext']; ?></a>
								<?php } ?> 
							</div>
						<?php } else { ?>		
							<div class="upcoming-courses-item-cont">
								<p>This is a image + text box and feel free to use this feature to showcase the best thing of your school, university, organization or test prep site. It is very important that choose the right set of information to highlight. If you highlight too many sections, importance of the individual sections will be diluted. On the other hand, trying to focus on too few points may mean you will miss out on leveraging some solid marketing platforms.</p>
								<p>You may use it to showcase your upcoming courses, your top faculties, your student success details, research grants. In case you are looking to build a short and sweet website, you may choose to altogether uncheck this section. In case you uncheck this section, it will no more be visible in the front end. You may find more description about the features of this block in the next block.</p>
								<a href="javascript:void(0);" class="btn-1">LET’S START</a>
							</div>
						<?php } ?>	
					</div>
				</li>
			<?php } ?>
			</ul>
		</div><!-- END of .container -->
    <?php
  	} else {?>
		<div class="container">
			<h2 class="header-b-2">Upcoming Courses</h2>
			<ul class="feedback-slider-upcomingcourse">
				<li>
				<div class="upcoming-courses-item">
					<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/upcoming-course-2.jpg" alt="">
					<div class="upcoming-courses-item-cont">
						<p>This is a image + text box and feel free to use this feature to showcase the best thing of your school, university, organization or test prep site. It is very important that choose the right set of information to highlight. If you highlight too many sections, importance of the individual sections will be diluted. On the other hand, trying to focus on too few points may mean you will miss out on leveraging some solid marketing platforms.</p>
						<p>You may use it to showcase your upcoming courses, your top faculties, your student success details, research grants. In case you are looking to build a short and sweet website, you may choose to altogether uncheck this section. In case you uncheck this section, it will no more be visible in the front end. You may find more description about the features of this block in the next block.</p>
						<a href="javascript:void(0);" class="btn-1">LET’S START</a>
					</div>
				</div>
				</li>
				<li>
				<div class="upcoming-courses-item">
					<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/u-1.jpg" alt="">
					<div class="upcoming-courses-item-cont">
						<p>You can use it as a slider(multiple boxes), or a single static content + image box. The best thing is, there is a TinyMCE editor at the back end. So, you can format it to your heart's content - you can add your own styling, including but not limited to, font size, font color etc. As it is the case with all the other sections of this theme, this section is completely customizable from the settings panel in the backend.</p>
						<p>Future plans - we plan to add a whole lot of features in future updates. For example, we will let you upload video files rather than just images, and let you decide if you want the text on the left side, or on the right side. With all these upcoming features, you can use Roshni for almost all kind of e-learning sites. Heck, Roshni will give most of the WordPress themes for it elegance and list of features.</p>
						<a href="javascript:void(0);" class="btn-1">LET’S START</a>
					</div>
					
				</div>
				</li>
				<li>
				<div class="upcoming-courses-item">
					<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/upcoming-course3.jpg" alt="">
					<div class="upcoming-courses-item-cont">
						<p>A slider with 3 slides looks more beautiful than 2 slides, so we thought we will add another slide. As they say, it adds to the symmetry.</p>
						<p>Interestingly, you can also change the location of this block. You can move it up, or down, or keep it in the same place.</p>
						<a href="javascript:void(0);" class="btn-1">LET’S START</a>
					</div>
				</div>
				</li>
			</ul>
		</div><!-- END of .container -->
	  <?php
	} ?>	
</div><!-- END of .upcoming-courses -->
