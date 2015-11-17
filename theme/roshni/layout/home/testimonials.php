<div class="testimonials">
	<div class="container">
		<?php
		$pluginname = 'theme_roshni';
		$testimonialhead = 'testimonialhead';
		$testimonial_heads = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$testimonialhead.'"');
		if(!empty($testimonial_heads)) { 
			$testimonial_head = json_decode($testimonial_heads->value,true);
		} else {
			$testimonial_head = '';
		}
		
		$testimonialheadsArray=array();
		if(!empty($testimonial_head) && $testimonial_head["testhead"][0] != null) {
			foreach ($testimonial_head as $key => $testimonial_headval) {
				foreach($testimonial_headval as $atestimonial_headvalValue) {
					$testimonialheadsArray[$key] = $atestimonial_headvalValue;
				}
			}
			
			if(!empty($testimonialheadsArray['testhead'])) { ?>
				<h1 class="header-b-2"><?php echo $testimonialheadsArray['testhead']; ?></h1>
				<?php
			} else { ?>	
				<h1 class="header-b-2">TESTIMONIALS</h1>
			  <?php
			}	
		?>			
		<?php
		} else {
		?>
		<h1 class="header-b-2">TESTIMONIALS</h1>
		<?php
			}
		?>

		<ul class="feedback-slider">
			<?php
				$testimonialslide = 'testimonials';
				$testimonial_slides = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$testimonialslide.'"');

				if(!empty($testimonial_slides)) { 
					$testimonialslide = json_decode($testimonial_slides->value,true);
				} else {
					$testimonialslide = '';
				}
				//print_r($testimonialslide);
				$testimonialslideArray=array();
				if(!empty($testimonialslide) && $testimonialslide["detailsleft"][0] != null) {
					foreach ($testimonialslide as $key => $testimonialslideval) {
						foreach($testimonialslideval as $testimonialslidevalKey => $testimonialslidevalValue) {
							$testimonialslideArray[$testimonialslidevalKey][$key] = $testimonialslidevalValue;
						}
					}
					//print_r($testimonialslideArray);
					foreach($testimonialslideArray as $testimonialslideArray_Value) {
						//echo $testimonialslideArray_Value["detailsleft"];
						?>
						<li>
							<div class="feedback-item">
								<div class="feedback-item-text">
									<?php if(!empty($testimonialslideArray_Value["detailsleft"])) { ?>
									<p><?php echo $testimonialslideArray_Value["detailsleft"];?></p>
									<?php } else { ?>
									<p>While this is a testimonial block, you may also use it as "faculty speak", student testimonials or even parent feedback. Wonderful!</p>
									<?php } ?>
								</div>
								<div class="feedback-item-user">
									<?php if(!empty($testimonialslideArray_Value["imageleft"])) { ?>
									<img src="<?php echo $testimonialslideArray_Value["imageleft"];?>" alt="">
									<h4><?php echo $testimonialslideArray_Value["userleft"];?></h4>
									<?php } else { ?>
									<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/i-user-1.png" alt="">
									<h4>Laali Debi, Director, University of Utopia</h4>
									<?php } ?>
								</div>
							</div><!-- END of .feedback-item -->
						</li>
						<?php
					}
					
			?>
			<?php
			} else {
			?>
			<li>
				<div class="feedback-item">
					<div class="feedback-item-text">
						<p>While this is a testimonial block, you may also use it as "faculty speak", student testimonials or even parent feedback. Wonderful!</p>
					</div>
					<div class="feedback-item-user">
						<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/i-user-1.png" alt="">
						<h4>Laali Debi, Director, University of Utopia</h4>
						
					</div>
				</div><!-- END of .feedback-item -->
			</li>
			<li>
				<div class="feedback-item">
					<div class="feedback-item-text">
						<p>The customary John Doe testimonial. Let this block content all the beautiful words your stakeholders have about you.</p>
					</div>
					<div class="feedback-item-user">
						<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/i-user-2.png" alt="">
						<h4>John Doe, Electrical Engineering, Class of 2015</h4>
						
					</div>
				</div><!-- END of .feedback-item -->
			</li>
			<li>
				<div class="feedback-item">
					<div class="feedback-item-text">
						<p>While this is a testimonial block, you may also use it as "faculty speak", student testimonials or even parent feedback. Wonderful!</p>
					</div>
					<div class="feedback-item-user">
						<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/i-user-1.png" alt="">
						<h4>Laali Debi, Director, University of Utopia</h4>
						
					</div>
				</div><!-- END of .feedback-item -->
			</li>
			<li>
				<div class="feedback-item">
					<div class="feedback-item-text">
						<p>The customary John Doe testimonial. Let this block content all the beautiful words your stakeholders have about you.</p>
					</div>
					<div class="feedback-item-user">
						<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/i-user-1.png" alt="">
						<h4>John Doe, Electrical Engineering, Class of 2015</h4>
						
					</div>
				</div><!-- END of .feedback-item -->
			</li>
			<li>
				<div class="feedback-item">
					<div class="feedback-item-text">
						<p>While this is a testimonial block, you may also use it as "faculty speak", student testimonials or even parent feedback. Wonderful!</p>
					</div>
					<div class="feedback-item-user">
						<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/i-user-2.png" alt="">
						<h4>Laali Debi, Director, University of Utopia</h4>
						
					</div>
				</div><!-- END of .feedback-item -->
			</li>
			<li>
				<div class="feedback-item">
					<div class="feedback-item-text">
						<p>The customary John Doe testimonial. Let this block content all the beautiful words your stakeholders have about you.</p>
					</div>
					<div class="feedback-item-user">
						
						<img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/i-user-2.png" alt="">
						<h4>John Doe, Electrical Engineering, Class of 2015</h4>
						
					</div>
				</div><!-- END of .feedback-item -->
			</li>
			<?php
			}
			?>
		</ul>
	</div><!-- END of .container -->
</div><!-- END of .testimonials -->
