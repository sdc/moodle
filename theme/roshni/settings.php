<?php

$settings = null;
defined('MOODLE_INTERNAL') || die;
 
if (is_siteadmin()) {
	$ADMIN->add('themes', new admin_category('theme_roshni', 'Roshni'));  
	
	/************ Font temp **************/
	
	$temp = new admin_settingpage('theme_roshni_font', get_string('generalsettings', 'theme_roshni'));
	$temp->add(new admin_setting_heading('theme_roshni_font', get_string('generalsettings', 'theme_roshni'),
					format_text(get_string('standarddesc', 'theme_roshni'), FORMAT_MARKDOWN)));
	$ADMIN->add('theme_roshni', $temp);


	// Custom CSS file.
    $name = 'theme_roshni/customcss';
    $title = get_string('customcss', 'theme_roshni');
    $description = get_string('customcssdesc', 'theme_roshni');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

	$name = 'theme_roshni/fontselect';
	$title = get_string('fontselect', 'theme_roshni');
	$description = get_string('fontselectdesc', 'theme_roshni');
	$default = 1;
	$choices = array(
			1 => get_string('fonttypestandard', 'theme_roshni'),
			2 => get_string('fonttypecustom', 'theme_roshni'),
	);
	$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
	$setting->set_updatedcallback('theme_reset_all_caches');
	$temp->add($setting);
	
	
	// Heading font name
	$name = 'theme_roshni/fontnameheading';
	$title = get_string('fontnameheading', 'theme_roshni');
	$description = get_string('fontnameheadingdesc', 'theme_roshni');
	$default = 'Raleway';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$setting->set_updatedcallback('theme_reset_all_caches');
	$temp->add($setting);
	
	// Text font name
	
	
	$name = 'theme_roshni/fontnamebody';
	$title = get_string('fontnamebody', 'theme_roshni');
	$description = get_string('fontnamebodydesc', 'theme_roshni');
	$default = 'Raleway';
	$setting = new admin_setting_configtext($name, $title, $description, $default);
	$setting->set_updatedcallback('theme_reset_all_caches');
	$temp->add($setting);

	if(get_config('theme_roshni', 'fontselect') === "2") {
	
			if (floatval($CFG->version) >= 2014111005.01) { // 2.8.5+ (Build: 20150313) which has MDL-49074 integrated into it.
					$woff2 = true;
			} else {
					$woff2 = false;
			}
	
			// This is the descriptor for the font files
			$name = 'theme_roshni/fontfiles';
			$heading = get_string('fontfiles', 'theme_roshni');
			$information = get_string('fontfilesdesc', 'theme_roshni');
			$setting = new admin_setting_heading($name, $heading, $information);
			$temp->add($setting);
	
			// Heading Fonts.
			// TTF Font.
			$name = 'theme_roshni/fontfilettfheading';
			$title = get_string('fontfilettfheading', 'theme_roshni');
			$description = '';
			$setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilettfheading');
			$setting->set_updatedcallback('theme_reset_all_caches');
			$temp->add($setting);
	
			// OTF Font.
			$name = 'theme_roshni/fontfileotfheading';
			$title = get_string('fontfileotfheading', 'theme_roshni');
			$description = '';
			$setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfileotfheading');
			$setting->set_updatedcallback('theme_reset_all_caches');
			$temp->add($setting);
	
			// WOFF Font.
			$name = 'theme_roshni/fontfilewoffheading';
			$title = get_string('fontfilewoffheading', 'theme_roshni');
			$description = '';
			$setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilewoffheading');
			$setting->set_updatedcallback('theme_reset_all_caches');
			$temp->add($setting);
	
			if ($woff2) {
					// WOFF2 Font.
					$name = 'theme_roshni/fontfilewofftwoheading';
					$title = get_string('fontfilewofftwoheading', 'theme_roshni');
					$description = '';
					$setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilewofftwoheading');
					$setting->set_updatedcallback('theme_reset_all_caches');
					$temp->add($setting);
			}
	
			// EOT Font.
			$name = 'theme_roshni/fontfileeotheading';
			$title = get_string('fontfileeotheading', 'theme_roshni');
			$description = '';
			$setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfileweotheading');
			$setting->set_updatedcallback('theme_reset_all_caches');
			$temp->add($setting);
	
			// SVG Font.
			$name = 'theme_roshni/fontfilesvgheading';
			$title = get_string('fontfilesvgheading', 'theme_roshni');
			$description = '';
			$setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilesvgheading');
			$setting->set_updatedcallback('theme_reset_all_caches');
			$temp->add($setting);
	
			// Body fonts.
			// TTF Font.
			$name = 'theme_roshni/fontfilettfbody';
			$title = get_string('fontfilettfbody', 'theme_roshni');
			$description = '';
			$setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilettfbody');
			$setting->set_updatedcallback('theme_reset_all_caches');
			$temp->add($setting);
	
			// OTF Font.
			$name = 'theme_roshni/fontfileotfbody';
			$title = get_string('fontfileotfbody', 'theme_roshni');
			$description = '';
			$setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfileotfbody');
			$setting->set_updatedcallback('theme_reset_all_caches');
			$temp->add($setting);
	
			// WOFF Font.
			$name = 'theme_roshni/fontfilewoffbody';
			$title = get_string('fontfilewoffbody', 'theme_roshni');
			$description = '';
			$setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilewoffbody');
			$setting->set_updatedcallback('theme_reset_all_caches');
			$temp->add($setting);
	
			if ($woff2) {
					// WOFF2 Font.
					$name = 'theme_roshni/fontfilewofftwobody';
					$title = get_string('fontfilewofftwobody', 'theme_roshni');
					$description = '';
					$setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilewofftwobody');
					$setting->set_updatedcallback('theme_reset_all_caches');
					$temp->add($setting);
			}
	
			// EOT Font.
			$name = 'theme_roshni/fontfileeotbody';
			$title = get_string('fontfileeotbody', 'theme_roshni');
			$description = '';
			$setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfileweotbody');
			$setting->set_updatedcallback('theme_reset_all_caches');
			$temp->add($setting);
	
			// SVG Font.
			$name = 'theme_roshni/fontfilesvgbody';
			$title = get_string('fontfilesvgbody', 'theme_roshni');
			$description = '';
			$setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilesvgbody');
			$setting->set_updatedcallback('theme_reset_all_caches');
			$temp->add($setting);
	} //End of if(get_config()) */


  /*****************************************/




	/************ Custom temp ************/
	
	$temp = new admin_settingpage('theme_roshni_customsettings', get_string('customsettings', 'theme_roshni'));
	$temp->add(new admin_setting_heading('theme_roshni_customsettings', get_string('customsettings', 'theme_roshni'),
					format_text(get_string('customdesc', 'theme_roshni'), FORMAT_MARKDOWN)));
	$ADMIN->add('theme_roshni', $temp);
	
	if(isset($_SERVER['QUERY_STRING']) && trim($_SERVER['QUERY_STRING']) == 'section=theme_roshni_customsettings') {
			redirect ($CFG->wwwroot.'/theme/roshni/settings/index.php');
	}
	
	/**************************************/

} //End of if(site_admin()).




