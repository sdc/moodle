<?php

include($CFG->dirroot. '/theme/roshni/config.php'); 

// Get the HTML for the settings bits.
$html = theme_roshni_get_html_for_settings($OUTPUT, $PAGE);
 global $DB, $USER, $CFG;
if (right_to_left()) {
    $regionbsid = 'region-bs-main-and-post';
} else {
    $regionbsid = 'region-bs-main-and-pre';
}
$logosettings = get_config("theme_roshni","logostyle");
$logosetting = json_decode($logosettings, true);
$favicon = get_config("theme_roshni","faviconimg");
if ($favicon != "") {
    $favicondetails = trim(stripslashes($favicon),'"');
} else {
    $favicondetails = $CFG->wwwroot . '/theme/roshni/favicon.ico';
}
$menunav = get_config("theme_roshni","nav");
$menunavs = json_decode($menunav, true);
if(!empty($menunavs)) {
	$menuArray = $menuArray1 = $menuArray2 = array();
	$menuArray1 = $menunavs['headnav'];
	$menuArray2 = $menunavs['headnavlink'];

	for ($i=0; $i<count($menuArray1); $i++) {
		$menuArray[$menuArray1[$i]] = $menuArray2[$i];
	}
}

?>
<html  <?php echo $OUTPUT->htmlattributes(); ?>>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $OUTPUT->page_title(); ?></title>
		<link type="image/x-icon" rel="shortcut icon" href="<?php echo $favicondetails;?>">
		<link rel="stylesheet" href="<?php echo $CFG->wwwroot ?>/theme/roshni/css/font-awesome.css">
		
		<link type="text/css" rel="Stylesheet" href="<?php echo $CFG->wwwroot ?>/theme/roshni/css/styles.css">
		<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/jquery-2.1.4.js"></script>
		<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/bootstrap.min.js"></script>
		<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/jquery.bxslider.min.js"></script>
		<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/jquery.scroll.js"></script>
		<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/engine.js"></script>
		<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/backtop.js"></script>
		<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/jquery.flexisel.js"></script>
		<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/jquery.royalslider.min.js"></script>
		<script src="<?php echo $CFG->wwwroot ?>/theme/roshni/js/banbox.js"></script>
		<link href="<?php echo $CFG->wwwroot ?>/theme/roshni/css/imgthum.css" rel="stylesheet">
		<link href="<?php echo $CFG->wwwroot ?>/theme/roshni/css/royalsliddercss/royalslider.css" rel="stylesheet">
    	<link href="<?php echo $CFG->wwwroot ?>/theme/roshni/css/royalsliddercss/rs-default-inverted.css" rel="stylesheet">
    	<link href="<?php echo $CFG->wwwroot ?>/theme/roshni/css/royalsliddercss/rs2.css" rel="stylesheet">
    	<link href="<?php echo $CFG->wwwroot ?>/theme/roshni/css/banbox.css" rel="stylesheet">
    	<link href="<?php echo $CFG->wwwroot ?>/theme/roshni/css/autoplaypartner.css" rel="stylesheet">
    	<?php
	      include($CFG->dirroot . '/theme/roshni/settings/themecolor.php');
	      include($CFG->dirroot . '/theme/roshni/settings/hovereffect.php');
	      include($CFG->dirroot . '/theme/roshni/settings/themefont.php');
	    ?>
	    <style type="text/css">
	    	*[role="main"] {
	  			display: none;
			}
	    </style>
	</head>
	
	<body class="header-3">
		<?php include $CFG->dirroot . '/theme/roshni/analyticstracking.php';
		echo $OUTPUT->standard_top_of_body_html() ?>
		<header id="header">
				<div class="main-menu header3">
					<div class="container">
						<?php if($logosetting == "logostyle3") { ?>
							<a href="<?php echo $CFG->wwwroot;?>" class="logo logo-text"><?php echo $SITE->fullname; ?></a>
						<?php } else if($logosetting == "logostyle2") { ?>
							<a href="<?php echo $CFG->wwwroot;?>" class="logo only-text" style = "background: none !important;"><?php echo $SITE->fullname; ?></a>
						<?php } else if($logosetting == "logostyle1") { ?>
							<a href="<?php echo $CFG->wwwroot;?>" class="logo logo-img"></a>
						<?php } else { ?>
							<a href="<?php echo $CFG->wwwroot;?>" class="logo"></a>
						<?php } ?>
						<?php if(isguestuser()) {?>
							<div class="usermenu">
								<div>
										<ul class="menubar">
												<li>
														<a href="javascript:void(0);">
																<span class="userbutton">
																		<span>
																				<span class="avatar current">
																						<?php echo $OUTPUT->user_profile_picture(); ?>
																				</span>
																		</span>
																		<span>Hi, <?php echo $USER->firstname ." ". $USER->lastname ; ?></span>
																</span>
														</a>
												</li>
										</ul>
										<ul class="menu">
												<li>
														<a href="<?php echo $CFG->wwwroot; ?>/login/logout.php"><span>Logout</span></a>
												</li>
										</ul>
								</div>
							</div>
						<?php }  else if(isloggedin() and !isguestuser()){ ?>
									<div class="usermenu">
										<div>
											<ul class="menubar">
												<li>
													<a href="javascript:void(0);">
														<span class="userbutton">
															<span>
																<span class="avatar current">
																	<?php echo $OUTPUT->user_profile_picture(); ?>
																</span>
															</span>
															<span>Hi, <?php echo $USER->firstname ." ". $USER->lastname ; ?></span>
														</span>
													</a>
												</li>
											</ul>
											<ul class="menu">
												<li>
													<a href="<?php echo $CFG->wwwroot; ?>/user/edit.php"><span>Edit Profile</span></a>
												</li>
												<li>
													<a href="<?php echo $CFG->wwwroot.'/course/index.php';?>"><span>Course</span></a>
												</li>
												<li>
													<a href="<?php echo $CFG->wwwroot; ?>/login/logout.php"><span>Logout</span></a>
												</li>
											</ul>
										</div>
									</div>
								<?php } ?>
						<div class="navbar header3">
							<div class="navbar-inner">
								<div class="">
									<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
									</button>
									<div class="nav-collapse collapse">
										<ul class="nav">
											<?php
												$custommenu = get_config("theme_roshni","custmenu");
												$custommenus = json_decode($custommenu, true); 
												if($custommenus == "defaultmenu" or empty($custommenus)) {
											?>

											<li><a href="<?php echo $CFG->wwwroot;?>" class="active">Home</a></li>
											<li><a href="<?php echo $CFG->wwwroot.'/course/index.php';?>">Courses</a></li>
											<?php if ($menunavs["headnav"][0] != NULL) { foreach ($menuArray as $key => $menunavsval) { ?>
													<li><a href="<?php echo $menunavsval;?>"><?php echo $key; ?></a></li>
												<?php 
												} //End of for
											?>

											<?php } else { ?>
											<li><a href="<?php echo $CFG->wwwroot.'/blog/index.php?userid='.$USER->id;?>">Blogs</a></li>
											<li><a href="<?php echo $CFG->wwwroot.'/mod/forum/user.php?id='.$USER->id;?>">Forums</a></li>
											<?php }} else { ?>
											<?php if ($menunavs["headnav"][0] != NULL) { foreach ($menuArray as $key => $menunavsval) { ?>
												<li><a href="<?php echo $menunavsval;?>"><?php echo $key; ?></a></li>
											<?php 
											} /*End of for*/ }
											?>
											<?php } ?>
										</ul>
									</div><!--/.nav-collapse -->
								</div>
							</div><!-- END of .navbar-inner -->
						</div><!-- END of .navbar -->
					</div><!-- END of .container -->
				</div><!-- END of main-menu -->
			
			<?php if (!isloggedin()) { ?>
				<div class="header3-login">
					<div class="container">
						<form method="post" action="<?php echo $CFG->wwwroot; ?>/login/index.php?authldap_skipntlmsso=1">
							<input type="text" name="username" placeholder="Username:">
							<input type="password" name="password" placeholder="Password:">
							<input type="submit" value="LOG IN">
						</form>
					</div>
				</div>
			<?php } ?>
			<?php
			/**************** For section 1 ****************/	
				$plugin = 'theme_roshni';
				$section = 'formsection1';
				$checkenable = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$plugin.'" and config.name="'.$section.'"');
				if($checkenable) {
					$ifenablesection = json_decode($checkenable->value);
				} else {
				 $ifenablesection = ''; 
				}
				if(!empty($ifenablesection) && $ifenablesection !="none") {
					include($CFG->dirroot.'/theme/roshni/layout/home/'. $ifenablesection. '.php');
				} else if(!empty($ifenablesection) && $ifenablesection =="none") {
					//include($CFG->dirroot.'/theme/'.$CFG->theme.'/layout/home/firstslider.php');
				} else {
					include($CFG->dirroot.'/theme/roshni/layout/home/firstslider.php');
				}  // End of slider.
			/*************************************************/
			?>
		</header><!-- END of header -->

		<div class="content">
			<?php 
			/**************** For section 2 ****************/
			
			
			$plugin = 'theme_roshni';
			$section = 'formsection2';
			$checkenable = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$plugin.'" and config.name="'.$section.'"');
			
			if($checkenable) {
				$ifenablesection = json_decode($checkenable->value);
			} else {
				$ifenablesection = '';
			}
			
			
			if(!empty($ifenablesection) && $ifenablesection !="none") {
				include($CFG->dirroot.'/theme/roshni/layout/home/'. $ifenablesection. '.php');
			} else if(!empty($ifenablesection) && $ifenablesection =="none") {
				//include($CFG->dirroot.'/theme/'.$CFG->theme.'/layout/home/aboutsite.php');
			} else {
				include($CFG->dirroot.'/theme/roshni/layout/home/aboutsite.php');
			}
			
			/**********************************************/
			
			?>
			
			<?php 
			
			/**************** For section 3 ****************/
			
			$plugin = 'theme_roshni';
			$section = 'formsection3';
			$checkenable = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$plugin.'" and config.name="'.$section.'"');
			if($checkenable) {
				$ifenablesection = json_decode($checkenable->value);
			} else {
				$ifenablesection = '';
			}
			
			if(!empty($ifenablesection) && $ifenablesection !="none") {
				include($CFG->dirroot.'/theme/roshni/layout/home/'. $ifenablesection. '.php');
			} else if(!empty($ifenablesection) && $ifenablesection =="none") {
				//include($CFG->dirroot.'/theme/'.$CFG->theme.'/layout/home/availablecourse.php');
			} else {
				include($CFG->dirroot.'/theme/roshni/layout/home/availablecourse.php');
			}
			
			/**********************************************/
			?>

			<?php 
			/**************** For section 4 ****************/
			$plugin = 'theme_roshni';
			$section = 'formsection4';
			$checkenable = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$plugin.'" and config.name="'.$section.'"');
			if($checkenable) {
				$ifenablesection = json_decode($checkenable->value);
			} else {
				$ifenablesection = '';
			}
			
			if(!empty($ifenablesection) && $ifenablesection !="none") {
				include($CFG->dirroot.'/theme/roshni/layout/home/'. $ifenablesection. '.php');
			} else if(!empty($ifenablesection) && $ifenablesection =="none") {
				//include($CFG->dirroot.'/theme/'.$CFG->theme.'/layout/home/upcomingcourse.php');
			} else {
				include($CFG->dirroot.'/theme/roshni/layout/home/upcomingcourse.php');
			}
			
			/**********************************************/
			?>

			<?php 
			/**************** For section 5 ****************/
			
			$plugin = 'theme_roshni';
			$section = 'formsection5';
			$checkenable = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$plugin.'" and config.name="'.$section.'"');
			if($checkenable) {
				$ifenablesection = json_decode($checkenable->value);
			} else {
				$ifenablesection = '';
			}
			
			if(!empty($ifenablesection) && $ifenablesection !="none") {
				include($CFG->dirroot.'/theme/roshni/layout/home/'. $ifenablesection. '.php');
			} else if(!empty($ifenablesection) && $ifenablesection =="none") {
				//include($CFG->dirroot.'/theme/'.$CFG->theme.'/layout/home/about_site_details.php');
			} else {
				include($CFG->dirroot.'/theme/roshni/layout/home/about_site_details.php');
			}
			
			/**********************************************/
			?>
			
			<?php
			/**************** For section 6 ****************/
			
			$plugin = 'theme_roshni';
			$section = 'formsection6';
			$checkenable = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$plugin.'" and config.name="'.$section.'"');
			if($checkenable) {
				$ifenablesection = json_decode($checkenable->value);
			} else {
				$ifenablesection = '';
			}
			
			if(!empty($ifenablesection) && $ifenablesection !="none") {
				include($CFG->dirroot.'/theme/roshni/layout/home/'. $ifenablesection. '.php');
			} else if(!empty($ifenablesection) && $ifenablesection =="none") {
				//include($CFG->dirroot.'/theme/'.$CFG->theme.'/layout/home/partners.php');
			} else {
				include($CFG->dirroot.'/theme/roshni/layout/home/partners.php');
			}
			
			/**********************************************/
			?>

			<?php 
			/**************** For section 7 ****************/
			
			$plugin = 'theme_roshni';
			$section = 'formsection7';
			$checkenable = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$plugin.'" and config.name="'.$section.'"');
			if($checkenable) {
				$ifenablesection = json_decode($checkenable->value);
			} else {
				$ifenablesection = '';
			}
			
			if(!empty($ifenablesection) && $ifenablesection !="none") {
				include($CFG->dirroot.'/theme/roshni/layout/home/'. $ifenablesection. '.php');
			} else if(!empty($ifenablesection) && $ifenablesection =="none") {
				//include($CFG->dirroot.'/theme/'.$CFG->theme.'/layout/home/categories.php');
			} else {
				include($CFG->dirroot.'/theme/roshni/layout/home/categories.php');
			}
			
			/**********************************************/
			?>

			<?php 
			/**************** For section 8 ****************/
			$plugin = 'theme_roshni';
			$section = 'formsection8';
			$checkenable = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$plugin.'" and config.name="'.$section.'"');
			if($checkenable) {
				$ifenablesection = json_decode($checkenable->value);
			} else {
				$ifenablesection = '';
			}
			
			if(!empty($ifenablesection) && $ifenablesection !="none") {
				include($CFG->dirroot.'/theme/roshni/layout/home/'. $ifenablesection. '.php');
			} else if(!empty($ifenablesection) && $ifenablesection =="none") {
				//include($CFG->dirroot.'/theme/'.$CFG->theme.'/layout/home/testimonials.php');
			} else {
				include($CFG->dirroot.'/theme/roshni/layout/home/testimonials.php');
			}
			
			/**********************************************/
			?>
			
			
			<?php 
			/**************** For section 9 ****************/
			$plugin = 'theme_roshni';
			$section = 'formsection10';
			$checkenable = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$plugin.'" and config.name="'.$section.'"');
			if($checkenable) {
				$ifenablesection = json_decode($checkenable->value);
			} else {
				$ifenablesection = '';
			}
			
			if(!empty($ifenablesection) && $ifenablesection !="none") {
				include($CFG->dirroot.'/theme/roshni/layout/home/'. $ifenablesection. '.php');
			} else if(!empty($ifenablesection) && $ifenablesection =="none") {
				//include($CFG->dirroot.'/theme/'.$CFG->theme.'/layout/home/googlemaps.php');
			} else {
				include($CFG->dirroot.'/theme/roshni/layout/home/googlemaps.php');
			}
			
			/**********************************************/
			?>
			<?php 
			/**************** For section 10 ****************/
			$plugin = 'theme_roshni';
			$section = 'formsection9';
			$checkenable = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$plugin.'" and config.name="'.$section.'"');
			if($checkenable) {
				$ifenablesection = json_decode($checkenable->value);
			} else {
				$ifenablesection = '';
			}
			
			if(!empty($ifenablesection) && $ifenablesection !="none") {
				include($CFG->dirroot.'/theme/roshni/layout/home/'. $ifenablesection. '.php');
			} else if(!empty($ifenablesection) && $ifenablesection =="none") {
				//include($CFG->dirroot.'/theme/'.$CFG->theme.'/layout/home/contacts.php');
			} else {
				include($CFG->dirroot.'/theme/roshni/layout/home/contacts.php');
			}
			
			/**********************************************/
			?>
			<?php 
			/**************** For section 11 ****************/
			$plugin = 'theme_roshni';
			$section = 'formsection11';
			$checkenable = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$plugin.'" and config.name="'.$section.'"');
			
			if($checkenable) {
				$ifenablesection = json_decode($checkenable->value);
			} else {
				$ifenablesection = '';
			}
			
			
			if(!empty($ifenablesection) && $ifenablesection !="none") {
				include($CFG->dirroot.'/theme/roshni/layout/home/'. $ifenablesection. '.php');
			} else if(!empty($ifenablesection) && $ifenablesection =="none") {
				//include($CFG->dirroot.'/theme/'.$CFG->theme.'/layout/home/social_network.php');
			} else {
				include($CFG->dirroot.'/theme/roshni/layout/home/social_network.php');
			}
			/**********************************************/
			?>
		</div><!-- END of .content -->
		<?php
			echo $OUTPUT->main_content(); require('footer.php');
		?>
				
		<a href="#header" class="btn-to-top"><i class="fa fa-arrow-circle-up"></i></a>
		
	</body>
</html>
