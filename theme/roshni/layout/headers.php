<?php
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
	$pluginname = 'theme_roshni';
	$headerstyle = 'header';
	$headerstyles = $DB->get_record_sql('select config.value from {config_plugins} config where config.plugin="'.$pluginname.'" and config.name="'.$headerstyle.'"');
	if(!empty($headerstyles)) { 
		$headerdetails = json_decode($headerstyles->value,true);
	} else {
		$headerdetails = '';
	}

	if($headerdetails == "Style1") { ?>
	  	<div class="container">
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
			<?php } else if(isloggedin() and !isguestuser()){  ?>
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
		</div><!-- END of .container -->
		<div class="header2 main-menu">
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
				<div class="navbar">
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
	<?php } else if ($headerdetails == "Style2") { ?>
	  	<div class="container">
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
			<?php } else if(isloggedin() and !isguestuser()){ ?>
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
		</div><!-- END of .container -->
		<div class="header2 main-menu">
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
				<div class="navbar">
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
	<?php } else if($headerdetails == "Style3") { ?>
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
						<button type="button" class="btn btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
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
	<?php } else { ?>
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
	<?php } ?>
