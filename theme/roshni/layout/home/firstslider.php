<!-- If Not Full page slider -->

<?php 
	$pluginname = 'theme_roshni';
	$fullpageslider = 'sliderclass';
	$fullpagesliders = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$fullpageslider.'"');
	if(!empty($fullpagesliders)) { 
		$fullpageslideryes = json_decode($fullpagesliders->value,true);
	} else {
		$fullpageslideryes = '';
	}
	$fslider = get_config("theme_roshni","fslide");
	$fslide = json_decode($fslider, true);

	if ($fullpageslideryes == "no") {
		if ($fslide["link"][0] != null) {
	?>
		<ul class="top-slider"> 
			<?php for($slidercount = 0; $slidercount < count($fslide["link"]); $slidercount++) { ?>
			<li class="content-wrap">
				<?php if(!empty($fslide["link"])) { ?>
					<img src="<?php echo $fslide["link"][$slidercount]; ?>" alt="">
				<?php } else {?>
					<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/sl-1.jpg" alt="">
				<?php } ?>
				<div class="top-slide-content">
					<div class="customslider">
						<?php if(!empty($fslide["content"][$slidercount])) { echo $fslide["content"][$slidercount]; ?>
						<?php } else {?>
						<h2>THE TASK OF THE</h2>
						<h1>MODERN EDUCATOR</h1>
						<h3 class="header-b">IS NOT TO CUT DOWN JUNGLES, BUT TO IRRIGATE DESERTS</h3>
						<a href="javascript:void(0);" class="btn-1">GET STARTED</a>
						<?php } ?>
					</div>
				</div>
			</li>
			<?php } //end of for?>
		</ul>
		<!-- END of .top-slider -->
	<?php } else { ?>
		<ul class="top-slider"> 
			<li class="content-wrap">
				<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/sl-1.jpg" alt="">
				<div class="top-slide-content">
					<h2>THE TASK OF THE</h2>
					<h1>MODERN EDUCATOR</h1>
					<h3 class="header-b">IS NOT TO CUT DOWN JUNGLES, BUT TO IRRIGATE DESERTS</h3>
					<a href="javascript:void(0);" class="btn-1">GET STARTED</a>
				</div>
			</li>
			<li class="content-wrap">
				<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/sl-1.jpg" alt="">
				<div class="top-slide-content">
					<h2>THE TASK OF THE</h2>
					<h1>MODERN EDUCATOR</h1>
					<h3 class="header-b">IS NOT TO CUT DOWN JUNGLES, BUT TO IRRIGATE DESERTS</h3>
					<a href="javascript:void(0);" class="btn-1">GET STARTED</a>
				</div>
			</li>
			<li class="content-wrap">
				<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/sl-1.jpg" alt="">
				<div class="top-slide-content">
					<h2>THE TASK OF THE</h2>
					<h1>MODERN EDUCATOR</h1>
					<h3 class="header-b">IS NOT TO CUT DOWN JUNGLES, BUT TO IRRIGATE DESERTS</h3>
					<a href="javascript:void(0);" class="btn-1">GET STARTED</a>
				</div>
			</li>
		</ul>
	<?php }//End of fslide
	} else if ($fullpageslideryes == "yes") { if($fslide["link"][0] != null) {?>
		<div class="col span_4 fwImage span12" style="margin-left: 0;">
	  	<div class="royalSlider-preview">
	    <div class="royalSlider rsDefaultInv slider-in-laptop">
    		<?php for($slidercount = 0; $slidercount < count($fslide["link"]); $slidercount++) { ?>
    		<div class="showpic rsContent">
    			<?php if(!empty($fslide["link"])) { ?>
					<img src="<?php echo $fslide["link"][$slidercount]; ?>" alt="" class="rsImg" />
				<?php } else {?>
					<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/sl-1.jpg" class="rsImg" />
				<?php } ?>
				<div class="ban35 rs-inner"> 
					<div class="ban35bl content-wrap"> 
						<div class="ban35bltxt">
						<?php if(!empty($fslide["content"][$slidercount])) { echo $fslide["content"][$slidercount]; ?>
						</div>
						<?php } else { ?>
						<div class="ban35bltxt">
							<h2>THE TASK OF THE</h2>
							<h1>MODERN EDUCATOR</h1>
							<span class="banbox">IS NOT TO CUT DOWN JUNGLES, BUT TO IRRIGATE DESERTS</span>
						</div>
						<div class="ban35bltxt">
							<a href="<?php echo $CFG->wwwroot?>/course/index.php" class="btn-1">GET STARTED</a>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php } //end of for?>
		</div> <!-- End of slider-in-laptop -->
  	</div> <!-- End of royalSlider-preview-div -->
</div> <!-- End of fwImage-div -->
<?php } else { ?>
<!-- If Full page slider selected -->
<div class="col span_4 fwImage span12" style="margin-left: 0;">
  <div class="royalSlider-preview">
    <div class="royalSlider rsDefaultInv slider-in-laptop">
    		<div class="showpic rsContent">
				<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/sl-1.jpg" class="rsImg" />
				<div class="ban35 rs-inner"> 
					<div class="ban35bl content-wrap"> 
						<div class="ban35bltxt">
							<h2>THE TASK OF THE</h2>
							<h1>MODERN EDUCATOR</h1>
							<span class="banbox">IS NOT TO CUT DOWN JUNGLES, BUT TO IRRIGATE DESERTS</span>
						</div>
						<div class="ban35bltxt">
							<a href="<?php echo $CFG->wwwroot?>/course/index.php" class="btn-1">GET STARTED</a>
						</div>
					</div>
				</div>
			</div>
			<div class="showpic rsContent">
				<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/sl-1.jpg" class="rsImg" />
				<div class="ban35 rs-inner"> 
					<div class="ban35bl content-wrap"> 
						<div class="ban35bltxt">
							<h2>THE TASK OF THE</h2>
							<h1>MODERN EDUCATOR</h1>
							<span class="banbox">IS NOT TO CUT DOWN JUNGLES, BUT TO IRRIGATE DESERTS</span>
						</div>
						<div class="ban35bltxt">
							<a href="<?php echo $CFG->wwwroot?>/course/index.php" class="btn-1">GET STARTED</a>
						</div>
					</div>
				</div>
			</div>
			<div class="showpic rsContent">
				<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/sl-1.jpg" class="rsImg" />
				<div class="ban35 rs-inner"> 
					<div class="ban35bl content-wrap"> 
						<div class="ban35bltxt">
							<h2>THE TASK OF THE</h2>
							<h1>MODERN EDUCATOR</h1>
							<span class="banbox">IS NOT TO CUT DOWN JUNGLES, BUT TO IRRIGATE DESERTS</span>
						</div>
						<div class="ban35bltxt">
							<a href="<?php echo $CFG->wwwroot?>/course/index.php" class="btn-1">GET STARTED</a>
						</div>
					</div>
				</div>
			</div>
		</div> <!-- End of slider-in-laptop -->
  	</div> <!-- End of royalSlider-preview-div -->
</div> <!-- End of fwImage-div -->
<?php }

} else { ?>
	<ul class="top-slider"> 
			<li class="content-wrap">
				<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/sl-1.jpg" alt="">
				<div class="top-slide-content">
					<h2>THE TASK OF THE</h2>
					<h1>MODERN EDUCATOR</h1>
					<h3 class="header-b">IS NOT TO CUT DOWN JUNGLES, BUT TO IRRIGATE DESERTS</h3>
					<a href="javascript:void(0);" class="btn-1">GET STARTED</a>
				</div>
			</li>
			<li class="content-wrap">
				<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/sl-1.jpg" alt="">
				<div class="top-slide-content">
					<h2>THE TASK OF THE</h2>
					<h1>MODERN EDUCATOR</h1>
					<h3 class="header-b">IS NOT TO CUT DOWN JUNGLES, BUT TO IRRIGATE DESERTS</h3>
					<a href="javascript:void(0);" class="btn-1">GET STARTED</a>
				</div>
			</li>
			<li class="content-wrap">
				<img src="<?php echo $CFG->wwwroot ?>/theme/<?php echo $CFG->theme ?>/data/sl-1.jpg" alt="">
				<div class="top-slide-content">
					<h2>THE TASK OF THE</h2>
					<h1>MODERN EDUCATOR</h1>
					<h3 class="header-b">IS NOT TO CUT DOWN JUNGLES, BUT TO IRRIGATE DESERTS</h3>
					<a href="javascript:void(0);" class="btn-1">GET STARTED</a>
				</div>
			</li>
		</ul>
	<?php } ?>

