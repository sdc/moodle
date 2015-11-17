<?php

include("../../../config.php");
require_once("../locallib.php");

global $OUTPUT,$CFG;


$PAGE->set_pagelayout('admin');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Settings');
$PAGE->set_url($CFG->wwwroot . "/theme/roshni/settings/index.php");
$PAGE->navbar->ignore_active();
$PAGE->navbar->add("Site administration");
$PAGE->navbar->add("Appearance");
$PAGE->navbar->add("Themes");
$PAGE->navbar->add("Roshni");
echo $OUTPUT->header();
if (!isloggedin() or isguestuser()) {
	require_login();
} else {
$faviconimage = get_config("theme_roshni","faviconimg");
$faviconimages = json_decode($faviconimage);   

$logoimage = get_config("theme_roshni","logoimg");
$logoimages = json_decode($logoimage);  

$logostyle = get_config("theme_roshni","logostyle");
$logostyles = json_decode($logostyle);  

$pageheader = get_config("theme_roshni","header");
$fpageheader = json_decode($pageheader);

$hovereffect = get_config("theme_roshni","effect");
$hovereffects = json_decode($hovereffect);

$pagelayout = get_config("theme_roshni","playout");
$pagelayouts = json_decode($pagelayout);

$backgroundimage = get_config("theme_roshni","backgrondimage");    

$backgroundlayoutstyle = get_config("theme_roshni","backgroundlayoutstyles");

$backgroundcolor = get_config("theme_roshni","backgroundcolor");
$backgroundcolors = json_decode($backgroundcolor);

$pagecontentbackgroundcolor = get_config("theme_roshni","pagebackgroundcolor");
$pagecontentbackgroundcolors = json_decode($pagecontentbackgroundcolor);

$avcstrip = get_config("theme_roshni", "cstrip");
$avcstrips = json_decode($avcstrip);

$avcstripbackgroundcolor = get_config("theme_roshni","avcstripbackgroundcolor");
$avcstripbackgroundcolors = json_decode($avcstripbackgroundcolor);

$avcstripbackgroundimg = get_config("theme_roshni","avcstripbackgrondimage");
$avcstripbackgroundimgs = json_decode($avcstripbackgroundimg);


$fullpageslider = get_config("theme_roshni","sliderclass");
$fullslider = json_decode($fullpageslider);

$choosemenubar = get_config("theme_roshni","navmenubar");
$choosemenu = json_decode($choosemenubar);

$fslide = get_config("theme_roshni","fslide");
$fslides = json_decode($fslide);


?>

<link rel="stylesheet" type="text/css" href="<?php echo $CFG->wwwroot ?>/theme/roshni/css/settingsstyles.css"/>
<script src="<?php echo $CFG->wwwroot; ?>/theme/roshni/js/tinymce/tinymce.min.js"></script>
<script src="<?php echo $CFG->wwwroot; ?>/theme/roshni/settings/jscolor/jscolor.js"></script>
<script src="<?php echo $CFG->wwwroot; ?>/theme/roshni/settings/addremove.js"></script>


<?php
include($CFG->dirroot . '/theme/roshni/settings/themecolor.php');
include($CFG->dirroot . '/theme/roshni/settings/pagebackgroundlayout.php');
$sections = array("firstslider" => "First Slider", 
	"availablecourse" => "Info Blurb",
	"aboutsite" => "Icon + Content Blurb", 
	"testimonials" => "Text Bubble", 
	"categories" => "Masonary", 
	"upcomingcourse" => "Text + Image Slider", 
	"partners" => "Thin Strip", 
	"googlemaps" => "Google Maps", 
	"contacts" => "Contact Details", 
	"social_network" => "Social Networks", 
	"about_site_details" => "Tabbed Doc");
$formsections = array();    
if(!empty($sections)) {
  for($i = 1; $i <= count($sections); $i++) {
    $formsection = get_config("theme_roshni", "formsection$i", true);
    $formsections[$i] = json_decode($formsection); 
  }
}
?>
<form action="save.php" method="POST" id="adminsettings" enctype="multipart/form-data">
	<div class="settingsform clearfix">
		<h2>Roshni Settings</h2>
		<fieldset>
			<div class="clearer"><!-- --></div>
			
			<!-- GENERAL SETTINGS //-->
			<!-- favicon //-->
			<div class="form-item clearfix">
				<h3>Set Favicon</h3>
				<h4>Set Image URL</h4>
				<div class="upload-img-text">
					<input type="url" value = "<?php if(!empty($faviconimage)) { echo trim(stripslashes($faviconimage),'"'); } else { echo $CFG->wwwroot."/theme/roshni/favicon.ico"; }?>" name="faviconimg" class="faviconimage">
					<label for="faviconup" class="btn-4">Upload</label>
					<input type="file" name="uploadfavicon[]" accept="image/*" id="faviconup">
				</div>
			</div>
			<!-- logo //-->
			<div class="form-item clearfix">
				<h3>Set Logo Image</h3>
				<input type="radio" id="l-s-1" name="logostyle" value="logostyle1" class="style1" <?php echo ((isset($logostyles) && $logostyles=="logostyle1")?'checked="checked"':''); ?>>
				<label for="l-s-1">Logo</label>
				
				<input type="radio" id="l-s-2" name="logostyle" value="logostyle2" class="style2" <?php echo ((isset($logostyles) && $logostyles=="logostyle2")?'checked="checked"':''); ?>> 
				<label for="l-s-2">Sitemane</label>
				
				<input type="radio" id="l-s-3" name="logostyle" value="logostyle3" class="style3" <?php echo ((isset($logostyles) && $logostyles=="logostyle3")?'checked="checked"':''); ?>> 
				<label for="l-s-3">Icon and Sitename Both</label>
				<div class="clearfix"></div>
				<h4>Set Image URL</h4>
				<div class="upload-img-text">
					<input type="url" value = "<?php if(!empty($logoimage)) { echo trim(stripslashes($logoimage),'"');} else { echo $CFG->wwwroot."/theme/roshni/css/img/logo.png"; } ?>" name="logoimg" class="logoimage">
					<!-- image upload -->
					<label for="logoup" class="btn-4">Upload</label>
					<input type="file" name="uploadlogo[]" accept="image/*" id="logoup">
					<span class="form-shortname">Preffered image size is - 125 x 35 (in pixel)</span>
				</div>
			</div>
			
			<!-- for frontpage header // -->
			
			<div class="form-item clearfix">
				<h3>Home Page Header Style</h3>
				<input type="radio" id="h-s-1" name="header" value="Style1" class="style1" <?php echo ((isset($fpageheader) && $fpageheader=="Style1")?'checked="checked"':''); ?>><!-- <input type="radio" id="h-s-1" name="h-s" value="h-s-1" checked> -->
				<label for="h-s-1">Header Style 1</label>
				
				<input type="radio" id="h-s-2" name="header" value="Style2" class="style2" <?php echo ((isset($fpageheader) && $fpageheader=="Style2")?'checked="checked"':''); ?>> <!-- name="h-s" value="h-s-2" -->
				<label for="h-s-2">Header Style 2</label>
				
				<input type="radio" id="h-s-3" name="header" value="Style3" class="style3" <?php echo ((isset($fpageheader) && $fpageheader=="Style3")?'checked="checked"':''); ?>> <!-- name="h-s" value="h-s-3" -->
				<label for="h-s-3">Header Style 3</label>
			</div>

			<!-- for hover effect // -->
			
			<div class="form-item clearfix">
				<h3>Home Page Hover Effect</h3>
				<input type="radio" id="h-e-1" name="effect" value="effect1" class="hovereffect1" <?php echo ((isset($hovereffects) && $hovereffects=="effect1")?'checked="checked"':''); ?>><!-- <input type="radio" id="h-s-1" name="h-s" value="h-s-1" checked> -->
				<label for="h-e-1">Hover Effect 1</label>
				
				<input type="radio" id="h-e-2" name="effect" value="effect2" class="hovereffect2" <?php echo ((isset($hovereffects) && $hovereffects=="effect2")?'checked="checked"':''); ?>> <!-- name="h-s" value="h-s-2" -->
				<label for="h-e-2">Hover Effect 2</label>
				
				<input type="radio" id="h-e-3" name="effect" value="effect3" class="hovereffect3" <?php echo ((isset($hovereffects) && $hovereffects=="effect3")?'checked="checked"':''); ?>> <!-- name="h-s" value="h-s-3" -->
				<label for="h-e-3">Hover Effect 3</label>

				<input type="radio" id="h-e-4" name="effect" value="effect4" class="hovereffect4" <?php echo ((isset($hovereffects) && $hovereffects=="effect4")?'checked="checked"':''); ?>> <!-- name="h-s" value="h-s-3" -->
				<label for="h-e-4">Hover Effect 4</label>

				<input type="radio" id="h-e-5" name="effect" value="effect5" class="hovereffect5" <?php echo ((isset($hovereffects) && $hovereffects=="effect5")?'checked="checked"':''); ?>> <!-- name="h-s" value="h-s-3" -->
				<label for="h-e-5">Hover Effect 5</label>
			</div>
			
			<!-- Full page slider -->
			
			<div class="form-item clearfix">
				<h3>Set Home Page Full Page Slider</h3>
				<input type="radio" id="f-s-1" name="sliderclass" class="fullslideryes" value="yes" <?php echo ((isset($fullslider) && $fullslider=="yes")?'checked="checked"':''); ?>><!-- name="f-s" value="f-s-1" -->
				<label for="f-s-1">Yes</label>
				<input type="radio" id="f-s-2" name="sliderclass"  class="fullsliderno" value="no" <?php echo ((isset($fullslider) && $fullslider=="no")?'checked="checked"':''); ?>><!-- name="f-s" value="f-s-2" -->
				<label for="f-s-2">No</label>
			</div>

			<!-- page Layout -->
			
			<div class="form-item clearfix">
				<h3>Set Page Background</h3>
				<input type="radio" id="p-l-1" name="playout" class="pagelayoutcolor" value="color" <?php echo ((isset($pagelayouts) && $pagelayouts=="color")?'checked="checked"':''); ?>><!-- name="f-s" value="f-s-1" -->
				<label for="p-l-1" class="back-col-op">Background Color</label>
				<div class="select-color plcolor1" style="display: none !important;">
					<h4>Choose Page Background Color</h4>
					<div>
						<div>
							<input type="text" name="backgroundcolor" value=<?php if(isset($backgroundcolor) && !empty($backgroundcolor)){ echo $backgroundcolor;} else { echo "#e84c3d"; } ?> id="btn-color" class="colorpicker color">
						</div>
					</div>
					<span class="form-shortname">Click on the field to choose the color</span>
				</div>
				<div class="clearfix"></div>
				<input type="radio" id="p-l-2" name="playout" class="pagelayoutimg" value="image" <?php echo ((isset($pagelayouts) && $pagelayouts=="image")?'checked="checked"':''); ?>>
				<label for="p-l-2" class="back-col-img">Background Image/ Pattern</label>
				<div class="span5 plimage1" style="display: none !important;">
					<h4>Background Image</h4>
					<div class="upload-img-text">
						<input type="url" value="<?php if(isset($backgroundimage) && !empty($backgroundimage)){ echo trim(stripslashes($backgroundimage),'"');} ?>" name="backgrondimage" class="link">
						<!-- image upload -->
						<label for="page-layout" class="btn-4">Upload</label>
						<input type="file" name="uploadpl[]" id="page-layout">
					</div>
				</div>
				<div class="clearfix"></div>

				<input type="radio" id="b-s-1" name="backgroundlayoutstyles" class="style1" value="backgroundlayoutstyles1" <?php echo ((isset($backgroundlayoutstyle) && $backgroundlayoutstyle=='"backgroundlayoutstyles1"')?'checked="checked"':''); ?>>
				<label for="b-s-1" class = "b-s-class">Repeat and Not Fixed</label>
				
				<input type="radio" id="b-s-2" name="backgroundlayoutstyles" class="style2" value="backgroundlayoutstyles2" <?php echo ((isset($backgroundlayoutstyle) && $backgroundlayoutstyle=='"backgroundlayoutstyles2"')?'checked="checked"':''); ?>> 
				<label for="b-s-2" class = "b-s-class">Fixed and Repeat</label>

				<input type="radio" id="b-s-3" name="backgroundlayoutstyles" class="style3" value="backgroundlayoutstyles3" <?php echo ((isset($backgroundlayoutstyle) && $backgroundlayoutstyle=='"backgroundlayoutstyles3"')?'checked="checked"':''); ?>>
				<label for="b-s-3" class = "b-s-class">No Repeat and Fixed</label>
		
				<div class="clearfix"></div>
				<div class="select-color plcontentcolor">
					<h4>Choose Page Content Background Color</h4>
					<div>
						<div>
							<input type="text" name="pagebackgroundcolor" value="<?php if(isset($pagecontentbackgroundcolors) && !empty($pagecontentbackgroundcolors)){ echo $pagecontentbackgroundcolors;} else { echo "#f5f5dc"; } ?>" id="btn-color" class="colorpicker color">
						</div>
					</div>
					<span class="form-shortname">Click on the field to choose the color</span>
				</div>
			</div>
			<!-- Theme color -->
			
			<div class="form-item clearfix">
				<h3>Select Theme Color</h3>
				<?php
				$buttoncolour = get_config("theme_roshni","favcolor");
				$btncolour = json_decode($buttoncolour);
				$buttontextcolour = get_config("theme_roshni","favcolor2");
				$btntxtcolour = json_decode($buttontextcolour);
				?>
				<div class="select-color">
					<h4>Choose Button and Theme Color</h4>
					<div>
						<div>
							<input type="text" name="favcolor" value="<?php if(isset($btncolour) && !empty($btncolour)){ echo $btncolour;} else { echo "#e84c3d"; } ?>" id="btn-color" class="colorpicker color">
						</div>
					</div>
					<span class="form-shortname">Click on the field to choose the color</span>
				</div>
				
				<div class="select-color">
					<h4>Choose Button-Text Color</h4>
					<div>
						<div>
							<input type="text" name="favcolor2" value="<?php if(isset($btntxtcolour) && !empty($btntxtcolour)){ echo $btntxtcolour;} else { echo "#fff"; } ?>" id="btn-t-color" class="colorpicker color">
						</div>
					</div>
					<span class="form-shortname">Click on the field to choose the color</span>
				</div>
			</div>
			
			<!-- Menu type -->
			
			
			<input type="radio" id="m-t-2" name="navmenubar" value="sitemenu" checked="checked" class="sitemenu" style="display:none">
				
			
			<!-- Set Page Sections -->
			
			<div class="form-item clearfix blocks-positions-wr">
				<h3>Set Home Page Sections</h3>
				<div class="span6">
					<?php
            if(!empty($sections)) {
              for($i = 1; $i <= count($sections); $i++) { 
                ?>
                <div>
                  	<h4 style="width: 100px; display: inline-block;">Position <?php echo $i; ?>:</h4>
                  	<div class="sel-wr">
						<select id="section<?php echo $i; ?>" class="unique-value" name="formsection<?php echo $i; ?>">
							<option value="none">Choose section...</option>
							<?php
							foreach($sections as $section => $section_label) {
								echo '<option value="' . $section . '"';
								if(isset($formsections[$i]) && ($formsections[$i] == $section)) echo 'selected="selected"';
								if(!empty($formsections[$i])) if(in_array($section, $formsections)) echo 'style="display:none"';
								echo '>' . $section_label . '</option>';
							}
							?>
						</select>
					</div>
                </div>
              <?php
              }
            }
          ?>
				</div> 
				<div class="span6">
					<div class="blocks-positions">
						<header></header>
						<div>Position 1</div>
						<div>Position 2</div>
						<div>Position 3</div>
						<div>Position 4</div>
						<div>Position 5</div>
						<div>Position 6</div>
						<div>Position 7</div>
						<div>Position 8</div>
						<div>Position 9</div>
						<div>Position 10</div>
						<div>Position 11</div>
					</div>
				</div>
			</div>			
			
			<div class="form-item clearfix slider_wrap">
				<h3>Home Page Slider Settings</h3>
				<div class="slider-settings">
					<?php 
					$fslider = get_config("theme_roshni","fslide");
					$fslide = json_decode($fslider, true);
					
					if($fslide["link"][0] != null) {
						$fslideRow = count($fslide["link"]);
						?>
						<div class="slider-settings-header slider_head_content">
							<?php
							for($i = 1; $i <= $fslideRow; $i++) {
								if($i ==  1) {
									$abc = "active slider_tab";
								} else if($i ==  $fslideRow) {
									$abc = 'jeet_abc';
								} else {
									$abc = '';
								}
								?>	
								<a href="javascript:void(0);" data-slide-label="<?php echo $i; ?>" class="<?php echo $abc; ?> tab_toggle" id="fisrt_tab_<?php echo $i; ?>">Slide <?php echo $i; ?></a>
								<?php
							} ?>
							<a href="javascript:void(0);" class="btn-5 add-btn slider_add_field_button"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-add.png" alt=""></a>
						</div>
						<div class="slider-settings-body slider_body_content">
						<?php
						for($i = 1; $i <= $fslideRow; $i++) {
							if($i ==  1) {
								$pqr = "slider_body_content";
								$xyz = "slider_body";
								$slider_remove_field = "";
							} else {
								$pqr = '';
								$xyz = '';
								$slider_remove_field = "slider_remove_field_show";
							}
						?>
						<div class="slider-settings-item <?php echo $xyz; ?> fisrt_tab_<?php echo $i; ?>" data-slide-number="<?php echo $i; ?>">
							<a href="javascript:void(0);" class="btn-5 delete-btn slider_remove_field <?php echo $slider_remove_field; ?>"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png" alt="">Delete This Slide</a>
							<div class="clearfix">
								<div class="span5">
									<h4>Background Image</h4>
									<div class="upload-img-text">
										<input type="url" value="<?php echo $fslide["link"][$i-1]; ?>" name="fslide[link][]" class="link">
										<span class="form-shortname">Preffered image size is - 1920 x 640 (in pixel)</span>
										<span class="form-shortname">Please use "&lt;h2&gt; .. &lt;/h2&gt;" for heading purpose.</span>
									</div>
								</div>
								<div class="span7">
									<h4>Background Content</h4>
									<div class="html-editor"><textarea rows="3" cols="30" name="fslide[content][]" id="textarea<?php echo $i;?>"><?php echo $fslide["content"][$i-1]; ?></textarea></div>
								</div>
							</div>
						</div>
						<?php							
						}
						echo "</div>";
					} else {
					?>
					<div class="slider-settings-header slider_head_content">
						<a href="javascript:void(0);" data-slide-label="1" class="active slider_tab tab_toggle" id="fisrt_tab_1">Slide 1</a>
						<a href="javascript:void(0);" class="btn-5 add-btn slider_add_field_button"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-add.png" alt=""></a>
					</div>
					<div class="slider-settings-body slider_body_content">
						<div class="slider-settings-item slider_body fisrt_tab_1" data-slide-number="1">
							<a href="javascript:void(0);" class="btn-5 delete-btn slider_remove_field"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png" alt="">Delete This Slide</a>
							<div class="clearfix">
								<div class="span5">
									<h4>Background Image</h4>
									<div class="upload-img-text">
										<input type="url" value="" name="fslide[link][]" class="link">
										<span class="form-shortname">Preffered image size is - 1920 x 640 (in pixel)</span>
										 <span class="form-shortname">Please use "&lt;h2&gt; .. &lt;/h2&gt;" for heading purpose.</span>
									</div>
								</div>
								<div class="span7">
									<h4>Background Content</h4>
									<div class="html-editor"><textarea rows="3" cols="30" name="fslide[content][]" id="textarea1"></textarea></div>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
			
			
			<!-- Header Menu -->
			
			<div class="form-item clearfix hm_wrap">
				<h3>Home Page Header Menu</h3>
				<?php $custommenu = get_config("theme_roshni","custmenu");
				$custommenus = json_decode($custommenu, true);
				?>
				<input type="radio" id="c-m-1" name="custmenu" class="headnavmenu" value="defaultmenu" <?php echo ((isset($custommenus) && $custommenus=="defaultmenu")?'checked="checked"':''); ?>>
				<label for="c-m-1">Default Menu</label>
				<input type="radio" id="c-m-2" name="custmenu" class="headnavmenu" value="custommenu" <?php echo ((isset($custommenus) && $custommenus=="custommenu")?'checked="checked"':''); ?>>
				<label for="c-m-2">Only Custom Menu</label>
				<div class="clearfix"></div>
				<a href="javascript:void(0);" class="hm_add_field_button"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-add.png" alt=""></a>
				<?php 
				$nav = get_config("theme_roshni","nav");
				$navs = json_decode($nav, true);
				
				if($navs["headnav"][0] != null) { //checking if not empty the first field.
					$navRow = count($navs["headnav"]);
					for($i = 1; $i <= $navRow; $i++) {
						if($i ==  1) {
							//$jeet = "ac_content";
							$hmcontent = "hm_content";
							$hm_remove_field = "";
						} else {
							$hmcontent = 'remove_hm_details';
							$hm_remove_field = 'hm_remove_field_show';
						}
					  ?>

						<div class="row inputs-row <?php echo $hmcontent; ?>">
							<div class="span3">
								<h4>Title</h4>
								<input type="text" value="<?php echo $navs["headnav"][$i-1]; ?>" name="nav[headnav][]" data-field="nav[headnav][]" class="headnav small">
							</div>
							<div class="span3">
								<h4>Page URL</h4>
								<input type="url" value="<?php echo $navs["headnavlink"][$i-1]; ?>" name="nav[headnavlink][]" data-field="nav[headnavlink][]" class="headnavlink small">
							</div>
							<div class="span3">
								<a class="btn-5 delete-btn hm_remove_field <?php echo $hm_remove_field; ?>" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
							</div>
						</div>
						<?php
					}
				} else {
				?>
				<div class="row inputs-row hm_content">
					<div class="span3">
						<h4>Title</h4>
						<input type="text" value="" name="nav[headnav][]" data-field="nav[headnav][]" class="headnav small">
					</div>
					<div class="span3">
						<h4>Page URL</h4>
						<input type="url" value="" name="nav[headnavlink][]" data-field="nav[headnavlink][]" class="headnavlink small">
					</div>
					<div class="span3">
						<a class="btn-5 delete-btn hm_remove_field" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
					</div>
				</div>
				<?php } ?>
			</div>
			
			
			
			<!-- Available Course  -->
			
			<div class="form-item clearfix ac_wrap">

				<h3>Info Blurb Settings</h3>

				<?php 
				$availablecoursetitles = get_config("theme_roshni","avctitle");
				$availablecoursetitle = json_decode($availablecoursetitles, true);
				$availablecourselinktitle = get_config("theme_roshni","avlinkname");
				$availablecourselink = get_config("theme_roshni","avlink"); ?>
				<div class="span3">
					<h4>Link Title</h4>
					<input type="text" value="<?php echo trim($availablecourselinktitle,'"'); ?>" name="avlinkname" data-field="avlinkname" class="avlikename">
				</div>
				
				<div class="span3">
					<h4>Link</h4>
					<input type="text" value="<?php echo trim(stripcslashes($availablecourselink),'"'); ?>" name="avlink" data-field="avlink" class="avlink">
				</div>
				<div class="clearfix"></div>
				<?php if($availablecoursetitle["title"][0] != null) { //checking if not empty the first field.
					$jcRow = count($availablecoursetitle["title"]);
					for($i = 1; $i <= $jcRow; $i++) { ?>
						<div class="row inputs-row">
							<div class="span3">
								<h4>Title</h4>
								<input type="text" value="<?php  echo $availablecoursetitle["title"][$i-1]; ?>" name="avctitle[title][]" data-field="avctitle[title][]" class="title">
							</div>
							<input type="radio" id="a-c-1" name="cstrip" class="avcstripcolor" value="color" <?php echo ((isset($avcstrips) && $avcstrips=="color")?'checked="checked"':''); ?>><!-- name="f-s" value="f-s-1" -->
							<label for="a-c-1">Background Color</label>
							<div class="select-color plcolor" style="display: none">
								<h4>Choose Strip Color</h4>
								<div>
									<div>
										<input type="text" value="<?php if(isset($avcstripbackgroundcolors) && !empty($avcstripbackgroundcolors)){ echo $avcstripbackgroundcolors;} else { echo "#e84c3d"; } ?>" id="btn-color" name="avcstripbackgroundcolor" class="colorpicker color">
									</div>
								</div>
								<span class="form-shortname">Click on the field to choose the color</span>
							</div>
							<div class="clearfix"></div>
							<input type="radio" id="a-c-2" name="cstrip" class="avcstripimg" value="image" <?php echo ((isset($avcstrips) && $avcstrips=="image")?'checked="checked"':''); ?>><!-- name="f-s" value="f-s-2" -->
							<label for="a-c-2">Background Image/ Pattern</label>
							<div class="span5 plimage" style="display: none">
								<h4>Choose Strip Background Image</h4>
								<div class="upload-img-text">
									<input type="url" value="<?php if(isset($avcstripbackgroundimgs) && !empty($avcstripbackgroundimgs)){ echo $avcstripbackgroundimgs;} else { echo $CFG->wwwroot."/theme/".$CFG->theme."/css/img/b-1.jpg"; } ?>" name="avcstripbackgrondimage" class="link">
									<!-- image upload -->
									<label for="sbimage" class="btn-4">Upload</label>
									<input type="file" name="uploadsb[]" accept="image/*" id="sbimage">
									<span class="form-shortname">Preffered image size is - 1920 x 640 (in pixel)</span>
								</div>
							</div>
						</div>
						<?php
					}
				} else {
				?>
				<div class="row inputs-row">
					<div class="span3">
						<h4>Title</h4>
						<input type="text" value="" name="avctitle[title][]" data-field="avctitle[title][]" class="title">
					</div>
					<input type="radio" id="a-c-1" name="cstrip" class="avcstripcolor" value="color" <?php echo ((isset($avcstrips) && $avcstrips=="color")?'checked="checked"':''); ?>><!-- name="f-s" value="f-s-1" -->
					<label for="a-c-1">Background Color</label>
					<input type="radio" id="a-c-2" name="cstrip" class="avcstripimg" value="image" <?php echo ((isset($avcstrips) && $avcstrips=="image")?'checked="checked"':''); ?>><!-- name="f-s" value="f-s-2" -->
					<label for="a-c-2">Background Image/ Pattern</label>
					<div class="select-color plcolor" style="display: none">
						<h4>Choose Strip Color</h4>
						<div>
							<div>
								<input type="text" value="<?php if(isset($avcstripbackgroundcolors) && !empty($avcstripbackgroundcolors)){ echo $avcstripbackgroundcolors;} else { echo "#e84c3d"; } ?>" id="btn-color" name="avcstripbackgroundcolor" class="colorpicker color">
							</div>
						</div>
						<span class="form-shortname">Click on the field to choose the color</span>
					</div>
					<div class="span5 plimage" style="display: none">
						<h4>Choose Strip Background Image</h4>
						<div class="upload-img-text">
							<input type="url" value="<?php if(isset($avcstripbackgroundimgs) && !empty($avcstripbackgroundimgs)){ echo $avcstripbackgroundimgs;} else { echo $CFG->wwwroot."/theme/".$CFG->theme."/css/img/b-1.jpg"; } ?>" name="avcstripbackgrondimage" class="link">
							<!-- image upload -->
							<label for="sbimage" class="btn-4">Upload</label>
							<input type="file" name="uploadsb[]" accept="image/*" id="sbimage">
							<span class="form-shortname">Preffered image size is - 1920 x 640 (in pixel)</span>
						</div>
					</div>
				</div>
				<?php } ?>
				
				
				<h3>Add Info Blurb Details <span class="small-setting">(Home Page)</span></h3>
				<a href="javascript:void(0);" class="ac_add_field_button"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-add.png" alt=""></a>
				<?php 
				$availablecourse = get_config("theme_roshni","avlcourse");
				$avlcourses = json_decode($availablecourse, true);
				if(!empty($avlcourses) && $avlcourses["image"][0] != null) { //checking if not empty the first field.
					$jcRow = count($avlcourses["image"]);
					for($i = 1; $i <= $jcRow; $i++) {
						if($i ==  1) {
							$content = "ac_content";
							$ac_remove_field = "ac_remove_field";
						} else {
							$content = 'remove_ac_details';
							$ac_remove_field = 'ac_remove_field_show';
						}
					?>
					<div class="row inputs-row <?php echo $content; ?>">
						<div class="inputs-subrow">
							<h4>Image URL</h4>
							<input type="url" value="<?php  echo $avlcourses["image"][$i-1]; ?>" name="avlcourse[image][]" data-field="avlcourse[0][image]" class="image">
							<span class="form-shortname">Preffered Size is - 480 x 250 ( px )</span>
						</div>
						<div class="inputs-subrow">
							<h4>Title</h4>
							<input type="text" value="<?php  echo $avlcourses["textone"][$i-1]; ?>" name="avlcourse[textone][]" data-field="avlcourse[0][textone]" class="textone">
						</div>
						<div class="inputs-subrow">
							<h4>Sub Title</h4>
							<input type="text" value="<?php  echo $avlcourses["texttwo"][$i-1]; ?>" name="avlcourse[texttwo][]" data-field="avlcourse[0][texttwo]" class="texttwo">
						</div>
						<div class="inputs-subrow">
							<h4>Course URL</h4>
							<input type="url" value="<?php  echo $avlcourses["pagelink"][$i-1]; ?>" name="avlcourse[pagelink][]" data-field="avlcourse[0][pagelink]" class="pagelink">
						</div>
						<div class="inputs-subrow">
							<a class="btn-5 delete-btn ac_remove_field <?php echo $ac_remove_field; ?>" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
						</div>
					</div>
						
					<?php
					}
				} else {
				?>
				<div class="row inputs-row ac_content">
					<div class="inputs-subrow">
						<h4>Image URL</h4>
						<input type="url" value="" name="avlcourse[image][]" data-field="avlcourse[0][image]" class="image">
						<span class="form-shortname">Preffered Size is - 480 x 250 ( px )</span>
					</div>
					<div class="inputs-subrow">
						<h4>Title</h4>
						<input type="text" value="" name="avlcourse[textone][]" data-field="avlcourse[0][textone]" class="textone">
					</div>
					<div class="inputs-subrow">
						<h4>Sub Title</h4>
						<input type="text" value="" name="avlcourse[texttwo][]" data-field="avlcourse[0][texttwo]" class="texttwo">
					</div>
					<div class="inputs-subrow">
						<h4>Course URL</h4>
						<input type="url" value="" name="avlcourse[pagelink][]" data-field="avlcourse[0][pagelink]" class="pagelink">
					</div>
					<div class="inputs-subrow">
						<a class="btn-5 delete-btn ac_remove_field" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
					</div>
				</div>
				<?php } ?>
				
			</div>
											
						
			
			
			<!-- About Site Settings -->
			
			<div class="form-item clearfix as_wrap">
				<h3>Icon + Content Blurb Settings <span class="small-setting">(Home Page)</span></h3>
				<?php 
				$aboutsite = get_config("theme_roshni","abtsite");
				$aboutsitedetails = json_decode($aboutsite, true);
				if($aboutsitedetails["textone"][0] != null) { //checking if not empty the first field.
					$aboutsiteRow = count($aboutsitedetails["textone"]);
					for($i = 1; $i <= $aboutsiteRow; $i++) { ?>
						<div class="row inputs-row">
							<div class="span3">
								<h4>Title</h4>
								<input type="text" value="<?php  echo $aboutsitedetails["textone"][$i-1]; ?>" name="abtsite[textone][]" class="textone">
							</div>
							<div class="span3">
								<h4>Sub Title</h4>
								<input type="text" value="<?php  echo $aboutsitedetails["texttwo"][$i-1]; ?>" name="abtsite[texttwo][]" class="texttwo">
							</div>
						</div>
						<?php  
					}
				} else { ?>
					<div class="row inputs-row">
						<div class="span3">
							<h4>Title</h4>
							<input type="text" value="" name="abtsite[textone][]" class="textone">
						</div>
						<div class="span3">
							<h4>Sub Title</h4>
							<input type="text" value="" name="abtsite[texttwo][]" class="texttwo">
						</div>
					</div>
				<?php } ?>
				
				<a href="javascript:void(0);" class="as_add_field_button"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-add.png" alt=""></a>
				<?php 
				$aboutsiteaddblocks = get_config("theme_roshni","addblock");
				$aboutsiteaddblock = json_decode($aboutsiteaddblocks, true);
				if($aboutsiteaddblock["blocklink"][0] != null) { //checking if not empty the first field.
					$aboutsiteblockRow = count($aboutsiteaddblock["blocklink"]);
					for($i = 1; $i <= $aboutsiteblockRow; $i++) {
						if($i ==  1) {
							
							$ascontent = "as_content";
						} else {
							$ascontent = 'remove_as_details';
							$asbutton = 'as_remove_field_show';
						}
					  ?>
						<div class="row inputs-row <?php echo $ascontent; ?>">
							<div class="inputs-subrow">
								<h4>Block Icon</h4>
								<?php 
									$settings = array(
											'param_name' => 'addblock[blockicon][]',
											'class' => 'blockicon option_home'
										);
									
									$blockicon = $aboutsiteaddblock["blockicon"][$i-1];;
									iconpicker_settings_field($settings, $blockicon); 
								?>
							</div>
							<div class="inputs-subrow">
								<h4>Page URL</h4>
								<input type="url" value="<?php  echo $aboutsiteaddblock["blocklink"][$i-1]; ?>" name="addblock[blocklink][]" data-field="addblock[0][blocklink]" class="blocklink">
							</div>
							<div class="inputs-subrow">
								<h4>Title</h4>
								<input type="text" value="<?php  echo $aboutsiteaddblock["blocktextsone"][$i-1]; ?>" name="addblock[blocktextsone][]" data-field="addblock[0][blocktextsone]" class="blocktextsone">
							</div>
							<div class="inputs-subrow">
								<h4>Sub Title</h4>
								<input type="text" value="<?php  echo $aboutsiteaddblock["blocktextstwo"][$i-1]; ?>" name="addblock[blocktextstwo][]" data-field="addblock[0][blocktextstwo]" class="blocktextstwo">
							</div>
							<div class="inputs-subrow">
								<a class="btn-5 delete-btn as_remove_field <?php echo $asbutton; ?>" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
							</div>
						</div>
						<?php
					}
				} else {
				?>
				<div class="row inputs-row as_content">
					<div class="inputs-subrow">
						<h4>Block Icon</h4>
						<?php 
							$settings = array(
									'param_name' => 'addblock[blockicon][]',
									'class' => 'blockicon option_home'
								);
							
							$blockicon = '';
							iconpicker_settings_field($settings, $blockicon ); 
						?>
					</div>
					<div class="inputs-subrow">
						<h4>Page URL</h4>
						<input type="url" value="" name="addblock[blocklink][]" data-field="addblock[0][blocklink]" class="blocklink">
					</div>
					<div class="inputs-subrow">
						<h4>Title</h4>
						<input type="text" value="" name="addblock[blocktextsone][]" data-field="addblock[0][blocktextsone]" class="blocktextsone">
					</div>
					<div class="inputs-subrow">
						<h4>Sub Title</h4>
						<input type="text" value="" name="addblock[blocktextstwo][]" data-field="addblock[0][blocktextstwo]" class="blocktextstwo">
					</div>
					<div class="inputs-subrow">
						<a class="btn-5 delete-btn as_remove_field" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
					</div>
				</div>
			  <?php } ?>
			</div>
			
			
			
			
			<!-- Site Details (joined with about site block)-->
			
			<div class="form-item clearfix site_details_wrap">
				<h3>Tabbed Doc Details <span class="small-setting">(Home Page)</span></h3>
				<a href="javascript:void(0);" class="site_details_add_field_button"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-add.png" alt=""></a>
				<?php 
					$sitedetailheadings = get_config("theme_roshni","sitedetailshrading");
					$sitedetailheading = json_decode($sitedetailheadings, true);
				?>
				<div class="row inputs-row">
					<div class="span3">
						<h4>Title</h4>
						<input type="text" value="<?php if(!empty($sitedetailheading)) { echo $sitedetailheading; }?>" name="sitedetailshrading" class="tabdocheading">
					</div>	
				</div>
				<?php 
				$sitedetails = get_config("theme_roshni","sitedetails");
				$sitedetail = json_decode($sitedetails, true);
				if($sitedetail["sitehead"][0] != null) { //checking if not empty the first field.
					$sitedetailRow = count($sitedetail["sitehead"]);
					for($i = 1; $i <= $sitedetailRow; $i++) {
						if($i ==  1) {
							
							$site_details_content = "site_details_content".' sdcla sd_class_'.$i;
							$site_details_remove_field = "site_details_remove_field";
						} else {
							$site_details_content = 'remove_site_details'.' sdcla sd_class_'.$i;
							$site_details_remove_field = 'site_details_remove_field_show';
						}
					  ?>
						<div class="row inputs-row <?php echo $site_details_content ?>" sd-data-element = "<?php echo $sitedetailRow; ?>">
							<div class="inputs-subrow">
								<h4>Title</h4>
								<input type="text" value="<?php  echo $sitedetail["sitehead"][$i-1]; ?>" name="sitedetails[sitehead][]" class="sitehead">
							</div>
							<div class="inputs-subrow">
								<h4>Details</h4>
								<div class="html-editor"><textarea rows="3" cols="30" name="sitedetails[sitetext][]" class="sitetext sdtextarea" id="sdtextarea<?php echo $i; ?>"><?php  echo $sitedetail["sitetext"][$i-1]; ?></textarea></div>
								<!-- <input type="text" value=""> -->
							</div>
							<div class="inputs-subrow">
								<h4>Page URL</h4>
								<input type="url" value="<?php  echo $sitedetail["setpagelink"][$i-1]; ?>" name="sitedetails[setpagelink][]"  class="setpagelink">
							</div>
							<div class="inputs-subrow">
								<h4>Button Text</h4>
								<input type="text" value="<?php  echo $sitedetail["setpagetext"][$i-1]; ?>" name="sitedetails[setpagetext][]"  class="setpagetext">
							</div>
							<div class="inputs-subrow">
								<a class="btn-5 delete-btn site_details_remove_field <?php echo $site_details_remove_field; ?>" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
							</div>
						</div>
						<?php 
					}
				} else {
				?>
				<div class="row inputs-row site_details_content" sd-data-element = "<?php echo $sitedetailRow; ?>">
					<div class="inputs-subrow">
						<h4>Title</h4>
						<input type="text" value="" name="sitedetails[sitehead][]" data-field="sitedetails[0][sitehead]" class="sitehead">
					</div>
					<div class="inputs-subrow">
						<h4>Details</h4>
						<div class="html-editor"><textarea rows="3" cols="30" name="sitedetails[sitetext][]" data-field="sitedetails[0][sitehead]" class="sitetext sdtextarea" id="sdtextarea1"></textarea></div>
					</div>
					<div class="inputs-subrow">
						<h4>Page URL</h4>
						<input type="url" value="" name="sitedetails[setpagelink][]" data-field=="sitedetails[0][sitehead]" class="setpagelink">
					</div>
					<div class="inputs-subrow">
						<h4>Button Text</h4>
						<input type="text" value="" name="sitedetails[setpagetext][]"  class="setpagetext">
					</div>
					<div class="inputs-subrow">
						<a class="btn-5 delete-btn site_details_remove_field" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
					</div>
				</div>
				<?php } ?>
			</div>
											
			
			
			
			
			<!-- Testimonials -->
			
			<div class="form-item clearfix testimonial_wrap">
				<h3>Text Bubble Settings <span class="small-setting">(Home Page)</span></h3>
				<?php 
				$testimonialheads = get_config("theme_roshni","testimonialhead");
				$testimonialhead = json_decode($testimonialheads, true);
				if($testimonialhead["testhead"][0] != null) { //checking if not empty the first field.
					$testheadRow = count($testimonialhead["testhead"]);
					for($i = 1; $i <= $testheadRow; $i++) { ?>
						<div class="row inputs-row">
							<div class="span3">
								<h4>Title</h4>
								<input type="text" value="<?php echo $testimonialhead["testhead"][$i-1]; ?>" name="testimonialhead[testhead][]" class="testhead">
							</div>
						</div>
						<?php
					}
				} else { ?>
					<div class="row inputs-row">
						<div class="span3">
							<h4>Title</h4>
							<input type="text" value="" name="testimonialhead[testhead][]" class="testhead">
						</div>
					</div>
				<?php } ?>
				
				<a href="javascript:void(0);" class="testimonial_add_field_button"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-add.png" alt=""></a>
				
				<?php 
				$testimonials = get_config("theme_roshni","testimonials");
				$testimonial = json_decode($testimonials, true);
				
				if($testimonial["detailsleft"][0] != null) { //checking if not empty the first field.
					$testimonialRow = count($testimonial["detailsleft"]);
					for($i = 1; $i <= $testimonialRow; $i++) {
						if($i ==  1) {
							
							$testimonial_content = "testimonial_content".' tdcla td_class_'.$i;
							$testimonial_remove_field = "testimonial_remove_field";
						} else {
							$testimonial_content = 'remove_testimonial_details'.' tdcla td_class_'.$i;
							$testimonial_remove_field = 'testimonial_remove_field_show';
						}
					  ?>
						<div class="row inputs-row <?php echo $testimonial_content; ?>" td-data-element = "<?php echo $testimonialRow; ?>">
							<div class="inputs-subrow">
								<h4>Text</h4>
								<div class="html-editor"><textarea rows="3" cols="30" name="testimonials[detailsleft][]" id="tdtextarea<?php echo $i; ?>" class="detailsleft option_home"><?php echo $testimonial["detailsleft"][$i-1]; ?></textarea></div>
							</div>
							<div class="inputs-subrow">
								<h4>Image</h4>
								<input type="url" value="<?php echo $testimonial["imageleft"][$i-1]; ?>" name="testimonials[imageleft][]" class="imageleft">
								<span class="form-shortname">Preffered size is - 66 x 66 ( px )</span>
							</div>
							<div class="inputs-subrow">
								<h4>User Details</h4>
								<textarea rows="3" cols="30" name="testimonials[userleft][]" class="userleft option_home"><?php echo $testimonial["userleft"][$i-1]; ?></textarea>
							</div>
							<div class="inputs-subrow">
								<a class="btn-5 delete-btn testimonial_remove_field <?php echo $testimonial_remove_field; ?>" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
							</div>
						</div>
						<?php
					}
				} else { ?>
					<div class="row inputs-row testimonial_content" td-data-element = "<?php echo $sitedetailRow; ?>">
						<div class="inputs-subrow">
							<h4>Text</h4>
							<div class="html-editor"><textarea rows="3" cols="30" name="testimonials[detailsleft][]"  class="detailsleft option_home tdtextarea" id="tdtextarea1"></textarea></div>
						</div>
						<div class="inputs-subrow">
							<h4>Image</h4>
							<input type="url" value="" name="testimonials[imageleft][]" class="imageleft">
							<span class="form-shortname">Preffered size is - 66 x 66 ( px )</span>
						</div>
						<div class="inputs-subrow">
							<h4>User Details</h4>
							<textarea rows="3" cols="30" name="testimonials[userleft][]" class="userleft option_home"></textarea>
						</div>
						<div class="inputs-subrow">
							<a class="btn-5 delete-btn testimonial_remove_field" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
						</div>
					</div>
				  <?php 
				} ?>
			</div>
			
			
			
			<!-- Course Categories -->
			
			<div class="form-item clearfix cc_wrap">
				<h3>Masonary Settings <span class="small-setting">(Home Page)</span></h3>
				<?php 
				$catheadvals = get_config("theme_roshni","catheadval");
				$catheadval = json_decode($catheadvals, true);
				if($catheadval["catmhead"][0] != null) { //checking if not empty the first field.
					$catheadvalRow = count($catheadval["catmhead"]);
					for($i = 1; $i <= $catheadvalRow; $i++) {?>
						<div class="row inputs-row">
							<div class="span3">
								<h4>Title</h4>
								<input type="text" value="<?php echo $catheadval["catmhead"][$i-1]; ?>" name="catheadval[catmhead][]" class="catmhead">
							</div>
							<div class="span3">
								<h4>Sub Title</h4>
								<input type="text" value="<?php echo $catheadval["catshead"][$i-1]; ?>" name="catheadval[catshead][]" class="catshead">
							</div>
						</div>
						<?php
					}
				} else { ?>
					<div class="row inputs-row">
						<div class="span3">
							<h4>Title</h4>
							<input type="text" value="" name="catheadval[catmhead][]" class="catmhead">
						</div>
						<div class="span3">
							<h4>Sub Title</h4>
							<input type="text" value="" name="catheadval[catshead][]" class="catshead">
						</div>
					</div>
				<?php 
				} ?>	
				
				<a href="javascript:void(0);" class="cc_add_field_button"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-add.png" alt=""></a>
				
				<?php 
				$categories = get_config("theme_roshni","categories");
				$category = json_decode($categories, true);
				if($category["catimage"][0] != null) { //checking if not empty the first field.
					$categoryRow = count($category["catimage"]);
					for($i = 1; $i <= $categoryRow; $i++) {
						if($i ==  1) {
							//$jeet = "ac_content";
							$cc_content = "cc_content";
							$cc_remove_field = "cc_remove_field";
						} else {
							$cc_content = 'remove_cc_details';
							$cc_remove_field = 'cc_remove_field_show';
						}
					  ?>
						<div class="row inputs-row <?php echo $cc_content; ?>">
							<div class="inputs-subrow">
								<h4>Category Image</h4>
								<input type="url" value="<?php echo $category["catimage"][$i-1]; ?>" name="categories[catimage][]" class="catimage">
								<span class="form-shortname">Preffered size is - 379 x 237 ( px )</span>
							</div>
							<div class="inputs-subrow">
								<h4>Category Title</h4>
								<input type="text" value="<?php echo $category["catname"][$i-1]; ?>" name="categories[catname][]"  class="catname">
							</div>
							<div class="inputs-subrow">
								<h4>Sub Heading</h4>
								<input type="text" value="<?php echo $category["subhead"][$i-1]; ?>" name="categories[subhead][]" class="subhead">
							</div>
							<div class="inputs-subrow">
								<h4>Page URL</h4>
								<input type="text" value="<?php echo $category["catlnkpage"][$i-1]; ?>" name="categories[catlnkpage][]" class="catlnkpage">
							</div>
							<div class="inputs-subrow">
								<a class="btn-5 delete-btn cc_remove_field <?php echo $cc_remove_field; ?>" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
							</div>
						</div>
						<?php
					}
				} else { ?>
					<div class="row inputs-row cc_content">
						<div class="inputs-subrow">
							<h4>Category Image</h4>
							<input type="url" value="" name="categories[catimage][]" class="catimage">
							<span class="form-shortname">Preffered size is - 379 x 237 ( px )</span>
						</div>
						<div class="inputs-subrow">
							<h4>Category Title</h4>
							<input type="text" value="" name="categories[catname][]"  class="catname">
						</div>
						<div class="inputs-subrow">
							<h4>Sub Heading</h4>
							<input type="text" value="" name="categories[subhead][]" class="subhead">
						</div>
						<div class="inputs-subrow">
							<h4>Page URL</h4>
							<input type="text" value="" name="categories[catlnkpage][]" class="catlnkpage">
						</div>
						<div class="inputs-subrow">
							<a class="btn-5 delete-btn cc_remove_field" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
						</div>
					</div>
				<?php 
				} ?>	
			</div>
			
			
			
			<!-- Up Coming Course -->
			
			<div class="form-item clearfix uc_wrap">
				<h3>Text + Image Slider Settings <span class="small-setting">(Home Page)</span></h3>
				<?php
				$chooseposition = get_config("theme_roshni","upcomingimg");
				$choosepos = json_decode($chooseposition,true);
				
				?>
				<input type="radio" id="m-i-1" name="upcomingimg" value="left" <?php echo ((isset($choosepos) && $choosepos=="left")?'checked="checked"':''); ?> class="imgleft">           <!-- name="m-i" value="m-i-1" -->
				<label for="m-i-1">Move Image Left</label>
				
				<input type="radio" id="m-i-2" name="upcomingimg" value="right" <?php echo ((isset($choosepos) && $choosepos=="right")?'checked="checked"':''); ?> class="imgright">        <!-- name="m-i" value="m-i-2" -->
				<label for="m-i-2">Move Image Right</label>
				
				
				<div class="row inputs-row"></div>
				<a href="javascript:void(0);" class="uc_add_field_button"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-add.png" alt=""></a>
				<?php 
				$upcomingcourses = get_config("theme_roshni","upcoming");
				$upcomingcourse = json_decode($upcomingcourses, true);
				if($upcomingcourse["upctitle"][0] != null) { //checking if not empty the first field.
					$upcomingRow = count($upcomingcourse["upctitle"]);
					for($i = 1; $i <= $upcomingRow; $i++) {
						if($i ==  1) {
							
							$uc_content = "uc_content".' uccla uc_class_'.$i;
							$uc_remove_field = "uc_remove_field";
						} else {
							$uc_content = 'remove_uc_details'.' uccla uc_class_'.$i;
							$uc_remove_field = 'uc_remove_field_show';
						}
					  ?>
					  	<div class="inputs-subrow">
							<h4>Title</h4>
							<input type="text" value="<?php echo $upcomingcourse["upctitle"][$i-1]; ?>" name="upcoming[upctitle][]" class="upctitle">
						</div>
						<div class="clearfix"></div>
						<div class="row inputs-row <?php echo $uc_content; ?>" uc-data-element = "<?php echo $upcomingRow; ?>">
							
							<div class="inputs-subrow">
								<h4>Add Image</h4>
								<input type="url" value="<?php echo $upcomingcourse["upcimage"][$i-1]; ?>" name="upcoming[upcimage][]" class="upcimage">
								<span class="form-shortname">Preffered size is - 379 x 237 ( px )</span>
							</div>
							<div class="inputs-subrow">
								<h4>Description</h4>
								<div class="html-editor"><textarea rows="3" cols="30" name="upcoming[upcdetails][]" class="uctextarea upcdetails" id="uctextarea<?php echo $i; ?>"><?php echo $upcomingcourse["upcdetails"][$i-1]; ?></textarea></div>
							</div>
							<div class="inputs-subrow">
								<h4>Button Text</h4>
								<input type="text" value="<?php echo $upcomingcourse["upcbuttontext"][$i-1]; ?>" name="upcoming[upcbuttontext][]" class="upcbuttontext">
							</div>
							<div class="inputs-subrow">
								<h4>Page URL</h4>
								<input type="text" value="<?php echo $upcomingcourse["upclink"][$i-1]; ?>" name="upcoming[upclink][]" class="upclink">
							</div>
							<div class="inputs-subrow">
								<a class="btn-5 delete-btn uc_remove_field <?php echo $uc_remove_field; ?>" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
							</div>
						</div>
						<?php
					}
				} else { ?>
					<div class="inputs-subrow">
						<h4>Title</h4>
						<input type="text" value="" name="upcoming[upctitle][]" class="upctitle">
					</div>
					<div class="clearfix"></div>
					<div class="row inputs-row uc_content">
						
						<div class="inputs-subrow">
							<h4>Add Image</h4>
							<input type="url" value="" name="upcoming[upcimage][]" class="upcimage">
							<span class="form-shortname">Preffered size is - 379 x 237 ( px )</span>
						</div>
						<div class="inputs-subrow">
							<h4>Description</h4>
							<div class="html-editor"><textarea rows="3" cols="30" name="upcoming[upcdetails][]" class="uctextarea upcdetails" id="uctextarea1"></textarea></div>
						</div>
						<div class="inputs-subrow">
							<h4>Button Text</h4>
							<input type="text" value="" name="upcoming[upcbuttontext][]" class="upcbuttontext">
						</div>
						<div class="inputs-subrow">
							<h4>Page URL</h4>
							<input type="text" value="" name="upcoming[upclink][]" class="upclink">
						</div>
						<div class="inputs-subrow">
							<a class="btn-5 delete-btn uc_remove_field" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
						</div>
					</div>
				<?php } ?>
			</div>
			
			
			
			
			
			<!-- Add Partners -->
			
			<div class="form-item clearfix partner_wrap">
				<h3>Add Thin strip <span class="small-setting">(Home Page)</span></h3>
				<span class="form-shortname">Preffered size is - 100 x 100 ( px )</span>
				<div class="add-logos-wr">
					<div class="add-logos-item">
						<a href="javascript:void(0);"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-close-2.png" alt=""></a>
						<span>LOGO</span>
					</div>
					<div class="add-logos-item">
						<a href="javascript:void(0);"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-close-2.png" alt=""></a>
						<span>LOGO</span>
					</div>
					<div class="add-logos-item">
						<a href="javascript:void(0);"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-close-2.png" alt=""></a>
						<span>LOGO</span>
					</div>
				</div>
				
				<a href="javascript:void(0);" class="partner_add_field_button"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-add.png" alt=""></a>
				<?php 
				$partners = get_config("theme_roshni","partners");
				$partner = json_decode($partners, true);
				if($partner["particon"][0] != null) { //checking if not empty the first field.
					$partnerRow = count($partner["particon"]);
					for($i = 1; $i <= $partnerRow; $i++) {
						if($i ==  1) {
							$partner_content = "partner_content";
							$partner_remove_field = "partner_remove_field";
						} else {
							$partner_content = 'remove_partner_details';
							$partner_remove_field = 'partner_remove_field_show';
						}
					  ?>
						<div class="row inputs-row <?php echo $partner_content; ?>">
							<div class="inputs-subrow">
								<h4>Add Logo</h4>
								<input type="text" value="<?php echo $partner["particon"][$i-1]; ?>" name="partners[particon][]" class="particon option_home">
							</div>
							<div class="inputs-subrow">
								<a class="btn-5 delete-btn partner_remove_field <?php echo $partner_remove_field; ?>" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
							</div>
						</div>
						<?php
					}
				} else { ?>
					<div class="row inputs-row partner_content">
						<div class="inputs-subrow">
							<h4>Add Logo</h4>
							<input type="text" value="" name="partners[particon][]" class="particon option_home">
						</div>
						<div class="inputs-subrow">
							<a class="btn-5 delete-btn partner_remove_field" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
						</div>
					</div>
				<?php } ?>	
			</div>
			
			<!-- Google Map Settings -->
			
			<div class="form-item clearfix">
				<h3>Google Map Settings <span class="small-setting">(Home Page)</span></h3>
				
				<?php 
				$contactsus = get_config("theme_roshni","contactus");
				$contactus = json_decode($contactsus, true);

				if(!empty($contactus)) { //checking if not empty the first field.
					?>
					<div class="row inputs-row">
						<div class="inputs-subrow">
							<h4>Title</h4>
							<input type="text" value="<?php if($contactus["mapmhead"][0] != null) { echo $contactus["mapmhead"][0]; } else { echo ""; } ?>" name="contactus[mapmhead][]" class="mapmhead">
						</div>
						<div class="inputs-subrow">
							<h4>Sub Title</h4>
							<input type="text" value="<?php if($contactus["mapshead"][0] != null) { echo $contactus["mapshead"][0]; } else { echo ""; } ?>" name="contactus[mapshead][]" class="mapshead">
						</div>
						
						<div class="inputs-subrow">
							<h4>Place</h4>
							<input type="text" value="<?php if($contactus["place"][0] != null) { echo $contactus["place"][0]; } else { echo ""; } ?>" name="contactus[place][]" class="place">
						</div>
						<div class="inputs-subrow">
							<h4>Country</h4>
							<input type="text" value="<?php if($contactus["country"][0] != null) { echo $contactus["country"][0]; } else { echo ""; } ?>" name="contactus[country][]" class="country">
						</div>
					</div>
				<?php } else { ?>
					<div class="row inputs-row">
						<div class="inputs-subrow">
							<h4>Title</h4>
							<input type="text" value="<?php if($contactus["mapmhead"][0] != null) { echo $contactus["mapmhead"][0]; } else { echo ""; } ?>" name="contactus[mapmhead][]" class="mapmhead">
						</div>
						<div class="inputs-subrow">
							<h4>Sub Title</h4>
							<input type="text" value="<?php if($contactus["mapshead"][0] != null) { echo $contactus["mapshead"][0]; } else { echo ""; } ?>" name="contactus[mapshead][]" class="mapshead">
						</div>
						
						<div class="inputs-subrow">
							<h4>Place</h4>
							<input type="text" value="<?php if($contactus["place"][0] != null) { echo $contactus["place"][0]; } else { echo ""; } ?>" name="contactus[place][]" class="place">
						</div>
						<div class="inputs-subrow">
							<h4>Country</h4>
							<input type="text" value="<?php if($contactus["country"][0] != null) { echo $contactus["country"][0]; } else { echo ""; } ?>" name="contactus[country][]" class="country">
						</div>
					</div>
				<?php } ?>	
			</div>
			
			<!-- Contact Details -->
			
			<div class="form-item clearfix contact_wrap">
				<h3>Contact Details <span class="small-setting">(Home Page)</span></h3>
				<a href="javascript:void(0);" class="contact_add_field_button"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-add.png" alt=""></a>
				<?php 
				$contacts = get_config("theme_roshni","contacts");
				$contact = json_decode($contacts, true);
				if($contact["contacticon"][0] != null) { //checking if not empty the first field.
					$contactRow = count($contact["contacticon"]);
					for($i = 1; $i <= $contactRow; $i++) {
						if($i ==  1) {
							$contact_content = "contact_content";
							$contact_remove_field = "contact_remove_field";
						} else {
							$contact_content = 'remove_contact_details';
							$contact_remove_field = 'contact_remove_field_show';
						}
					  ?>
						<div class="row inputs-row <?php echo $contact_content; ?>">
							<div class="inputs-subrow">
								<h4>Block Icon</h4>
								<?php 
									$settings = array(
											'param_name' => 'contacts[contacticon][]',
											'class' => 'contacticon'
									); 
									
									$contacticon = $contact["contacticon"][$i-1];
									
									iconpicker_settings_field($settings, $contacticon); 
								?>
							</div>
							<div class="inputs-subrow">
								<h4>Title</h4>
								<input type="text" value="<?php echo $contact["contacttype"][$i-1]; ?>" name="contacts[contacttype][]" class="contacttype">
							</div>
							<div class="inputs-subrow">
								<h4>Details</h4>
								<input type="text" value="<?php echo $contact["contactdetails"][$i-1]; ?>" name="contacts[contactdetails][]" class="contactdetails">
							</div>
							<div class="inputs-subrow">
								<a class="btn-5 delete-btn contact_remove_field <?php echo $contact_remove_field; ?>" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
							</div>
						</div>
						<?php
					}
				} else { ?>
					<div class="row inputs-row contact_content">
						<div class="inputs-subrow">
							<h4>Block Icon</h4>
							<?php 
								$settings = array(
										'param_name' => 'contacts[contacticon][]',
										'class' => 'contacticon'
								); 
								
								$contacticon = '';
								iconpicker_settings_field($settings, $contacticon); 
							?>
						</div>
						<div class="inputs-subrow">
							<h4>Title</h4>
							<input type="text" value="" name="contacts[contacttype][]" class="contacttype">
						</div>
						<div class="inputs-subrow">
							<h4>Details</h4>
							<input type="text" value="" name="contacts[contactdetails][]" class="contactdetails">
						</div>
						<div class="inputs-subrow">
							<a class="btn-5 delete-btn contact_remove_field" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
						</div>
					</div>	
				<?php } ?>	
			</div>
			
			
			<!-- Create header Menu Pages -->
			
			<div class="form-item clearfix header_menu_wrap">
				<h3>Create header Menu Pages <span class="small-setting">(Home Page)</span></h3>
				<p>Please give the page-link created to ‘Page-URL’ field or ‘Header Menu’</p>
				<a href="javascript:void(0);" class="header_menu_add_field_button"><img src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-add.png" alt=""></a>
				<?php 
				$dynamic_pages = get_config("theme_roshni","dynamic_page");
				$dynamic_page = json_decode($dynamic_pages, true);
				if($dynamic_page["text"][0] != null) { //checking if not empty the first field.
					$dynamic_pageRow = count($dynamic_page["text"]);
					
					for($i = 1; $i <= $dynamic_pageRow; $i++) {
						if($i ==  1) {
							$header_menu_content = "header_menu_content".' dynamiccla dynamic_class_'.$i;
							$header_menu_remove_field = "header_menu_remove_field";
							
						} else {
							$header_menu_content = 'remove_header_menu_details'.' dynamiccla dynamic_class_'.$i;
							$header_menu_remove_field = 'header_menu_remove_field_show';
						}
					  ?>
						<div class="row inputs-row <?php echo $header_menu_content; ?>" data-element = "<?php echo $dynamic_pageRow; ?>">
							<div class="span3 inputs-subrow">
								<h4>Page Title</h4>
								<input type="text" value="<?php echo $dynamic_page["text"][$i-1]; ?>" name="dynamic_page[text][]" class="texts">
							</div>
							<div class="span7 inputs-subrow">
								<h4>Page Content</h4>
								<div class="html-editor"><textarea class = "textarea" rows="3" cols="30" name="dynamic_page[textarea][]" id="dtextarea<?php echo $i; ?>"><?php echo $dynamic_page["textarea"][$i-1]; ?></textarea></div>
							</div>
							<div class="inputs-subrow">
								<a class="btn-5 delete-btn header_menu_remove_field <?php echo $header_menu_remove_field; ?>" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
							</div>
							<div class="clearfix"></div>
							<div class="clearfix"></div>
							<div>Menu Link :-<?php echo $CFG->wwwroot."/theme/roshni/page.php?page=".str_replace(" ", "_", $dynamic_page["text"][$i-1]);?></div>
						</div>
						<?php
					}
				} else { ?>
					<div class="row inputs-row header_menu_content">
						<div class="span3 inputs-subrow">
							<h4>Page Title</h4>
							<input type="text" value="" name="dynamic_page[text][]" class="texts">
						</div>

						<div class="span7 inputs-subrow">
							<h4>Page Content</h4>
							<div class="html-editor"><textarea class = "textarea" rows="3" cols="30" name="dynamic_page[textarea][]" id="dtextarea1"></textarea></div>
						</div>
						<div class="inputs-subrow">
							<a class="btn-5 delete-btn header_menu_remove_field" href="javascript:void(0);"><img alt="" src="<?php echo $CFG->wwwroot ?>/theme/roshni/css/img/admin-option/i-delete.png"></a>
						</div>
						<div class="clearfix"></div>
							<div class="clearfix"></div>
							
					</div>
				<?php } ?>
			</div>
			
			
			
			<!-- Add Social Networks -->
			
			<div class="form-item clearfix">
				<h3>Add Social Networks <span class="small-setting">(Home Page)</span></h3>
				<?php 
				$socials = get_config("theme_roshni","social");
				$social = json_decode($socials, true);
				
				if(!empty($social)) { //checking if not empty the first field.
					?>
					<div class="row inputs-row">
						<div class="inputs-subrow">
							<h4>Facebook</h4>
							<input type="url" id="fburl" value="<?php if($social["facebook"][0] != null) { echo $social["facebook"][0]; } else { echo ""; } ?>" name="social[facebook][]" class="facebook">
						</div>
						<div class="inputs-subrow">
							<h4>Twitter</h4>
							<input type="url" id="twturl" value="<?php if($social["twitter"][0] != null) { echo $social["twitter"][0]; } else { echo ""; } ?>" name="social[twitter][]" class="twitter">
						</div>
						<div class="inputs-subrow">
							<h4>Linkedin</h4>
							<input type="url" id="linkdnurl" value="<?php if($social["linkedin"][0] != null) { echo $social["linkedin"][0]; } else { echo ""; } ?>" name="social[linkedin][]" class="linkedin">
						</div>
						<div class="inputs-subrow">
							<h4>Google Plus</h4>
							<input type="url" id="googleplusurl" value="<?php if($social["google-plus"][0] != null) { echo $social["google-plus"][0]; } else { echo ""; } ?>" name="social[google-plus][]" class="googleplus">
						</div>
						<div class="inputs-subrow">
							<h4>Dribbble</h4>
							<input type="url" id="drbleurl" value="<?php if($social["dribbble"][0] != null) { echo $social["dribbble"][0]; } else { echo ""; } ?>" name="social[dribbble][]" class="dribbble">
						</div>
						<div class="inputs-subrow">
							<h4>Youtube</h4>
							<input type="url" id="youtubeurl" value="<?php if($social["youtube"][0] != null) { echo $social["youtube"][0]; } else { echo ""; } ?>" name="social[youtube][]" class="youtube">
						</div>
						<div class="inputs-subrow">
							<h4>Vimeo</h4>
							<input type="url" id="vimeourl" value="<?php if($social["vimeo-square"][0] != null) { echo $social["vimeo-square"][0]; } else { echo ""; } ?>" name="social[vimeo-square][]" class="vimeo">
						</div>
						<div class="inputs-subrow">
							<h4>RSS</h4>
							<input type="url" id="rssurl" value="<?php if($social["rss"][0] != null) { echo $social["rss"][0]; } else { echo ""; } ?>" name="social[rss][]" class="rss">
						</div>
						<div class="inputs-subrow">
							<h4>Flicker</h4>
							<input type="url" id="flickerurl" value="<?php if($social["flickr"][0] != null) { echo $social["flickr"][0]; } else { echo ""; } ?>" name="social[flickr][]" class="flicker">
						</div>
						<div class="inputs-subrow">
							<h4>Pinterest</h4>
							<input type="url" id="pinteresturl" value="<?php if($social["pinterest"][0] != null) { echo $social["pinterest"][0]; } else { echo ""; } ?>" name="social[pinterest][]" class="pinterest">
						</div>
					</div>
						<?php
				} else { ?>
					<div class="row inputs-row">
						<div class="inputs-subrow">
							<h4>Facebook</h4>
							<input type="url" id="fburl" value="<?php if($social["facebook"][0] != null) { echo $social["facebook"][0]; } else { echo ""; } ?>" name="social[facebook][]" class="facebook">
						</div>
						<div class="inputs-subrow">
							<h4>Twitter</h4>
							<input type="url" id="twturl" value="<?php if($social["twitter"][0] != null) { echo $social["twitter"][0]; } else { echo ""; } ?>" name="social[twitter][]" class="twitter">
						</div>
						<div class="inputs-subrow">
							<h4>Linkedin</h4>
							<input type="url" id="linkdnurl" value="<?php if($social["linkedin"][0] != null) { echo $social["linkedin"][0]; } else { echo ""; } ?>" name="social[linkedin][]" class="linkedin">
						</div>
						<div class="inputs-subrow">
							<h4>Google Plus</h4>
							<input type="url" id="googleplusurl" value="<?php if($social["google-plus"][0] != null) { echo $social["google-plus"][0]; } else { echo ""; } ?>" name="social[google-plus][]" class="googleplus">
						</div>
						<div class="inputs-subrow">
							<h4>Dribbble</h4>
							<input type="url" id="drbleurl" value="<?php if($social["dribbble"][0] != null) { echo $social["dribbble"][0]; } else { echo ""; } ?>" name="social[dribbble][]" class="dribbble">
						</div>
						<div class="inputs-subrow">
							<h4>Youtube</h4>
							<input type="url" id="youtubeurl" value="<?php if($social["youtube"][0] != null) { echo $social["youtube"][0]; } else { echo ""; } ?>" name="social[youtube][]" class="youtube">
						</div>
						<div class="inputs-subrow">
							<h4>Vimeo</h4>
							<input type="url" id="vimeourl" value="<?php if($social["vimeo-square"][0] != null) { echo $social["vimeo-square"][0]; } else { echo ""; } ?>" name="social[vimeo-square][]" class="vimeo">
						</div>
						<div class="inputs-subrow">
							<h4>RSS</h4>
							<input type="url" id="rssurl" value="<?php if($social["rss"][0] != null) { echo $social["rss"][0]; } else { echo ""; } ?>" name="social[rss][]" class="rss">
						</div>
						<div class="inputs-subrow">
							<h4>Flicker</h4>
							<input type="url" id="flickerurl" value="<?php if($social["flickr"][0] != null) { echo $social["flickr"][0]; } else { echo ""; } ?>" name="social[flickr][]" class="flicker">
						</div>
						<div class="inputs-subrow">
							<h4>Pinterest</h4>
							<input type="url" id="pinteresturl" value="<?php if($social["pinterest"][0] != null) { echo $social["pinterest"][0]; } else { echo ""; } ?>" name="social[pinterest][]" class="pinterest">
						</div>
					</div>
					
				<?php } ?>	
			</div>
			<!-- Google analytics -->

			<div class="form-item clearfix">
				<h3>Add Google Analytics</h3>
				<?php 
				$googleanalytics = get_config("theme_roshni","ganalytics");
				$ganalytics = json_decode($googleanalytics, true);
				
				if(!empty($ganalytics)) { //checking if not empty the first field.
					?>
					<div class="row inputs-row">
						<h4>Enter Tracking ID</h4>
						<input type="text" id="trackingid" value="<?php if($ganalytics["trackingid"][0] != null) { echo $ganalytics["trackingid"][0]; } else { echo "UA-XXXXXXXX-X"; } ?>" name="ganalytics[trackingid][]" class="googleanalytics">
					</div>
					<div class="span3 inputs-subrow">
						<h4>Tracking Code</h4>
						<textarea rows="6" cols="30" name="ganalytics[trackingcode][]" value="<?php if($ganalytics["trackingcode"][0] != null) { echo $ganalytics["trackingcode"][0]; } else { echo " "; } ?>" class="textarea"><?php echo $ganalytics["trackingcode"][0]; ?></textarea>
					</div>
				<?php } else { ?>
					<div class="row inputs-row">
						<h4>Enter Tracking ID</h4>
						<input type="text" id="trackingid" value="<?php if($ganalytics["trackingcode"][0] != null) { echo $ganalytics["trackingcode"][0]; } else { echo "UA-XXXXXXXX-X"; } ?>" name="ganalytics[trackingid][]" class="googleanalytics">
					</div>
					<div class="span3 inputs-subrow">
						<h4>Tracking Code</h4>
						<textarea rows="6" cols="30" name="ganalytics[trackingcode][]" class="textarea"><?php echo $ganalytics["trackingcode"][0]; ?></textarea>
					</div>
				<?php } ?>
			</div>

			<!-- Add footer -->
			<div class="form-item clearfix">
				<h3>Add Your Footer</h3>
				<?php 
				$footer = get_config("theme_roshni","footer");
				$footers = json_decode($footer, true);
				if(!empty($footers)) { //checking if not empty the field.
					?>
					<div class="span3 inputs-subrow">
						<h4>Footer Text</h4>
						<textarea rows="20" cols="30" name="footer" value="<?php if($footers != null) { echo $footers; } else { echo " "; } ?>" class="textarea"><?php if($footers != null) { echo $footers; } else { echo " "; } ?></textarea>
					</div>
				<?php } else { ?>
					<div class="span3 inputs-subrow">
						<h4>Footer Text</h4>
						<textarea rows="20" cols="30" name="footer" class="textarea"><?php echo $footers; ?></textarea>
					</div>
				<?php } ?>
			</div>
			<div class="form-buttons">
				<input class="form-submit btn-3" type="submit" value="Save changes">
			</div>
		</fieldset>
	</div>
</form>

<a href="#page" class="btn-to-top">To top</a>

<?php 
}
echo $OUTPUT->footer(); 
   

?>
