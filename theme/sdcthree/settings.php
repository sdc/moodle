<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

// Graphic Wrap (Background Image)
$name = 'theme_sdcthree/graphicwrap';
$title=get_string('graphicwrap','theme_sdcthree');
$description = get_string('graphicwrapdesc', 'theme_sdcthree');
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$settings->add($setting);

// Logo file setting
$name = 'theme_sdcthree/logo';
$title = get_string('logo','theme_sdcthree');
$description = get_string('logodesc', 'theme_sdcthree');
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$settings->add($setting);

// Menu select background colour setting
$name = 'theme_sdcthree/menuhovercolor';
$title = get_string('menuhovercolor','theme_sdcthree');
$description = get_string('menuhovercolordesc', 'theme_sdcthree');
$default = '#fd7f11';
$previewconfig = NULL;
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
$settings->add($setting);

// Email url setting
$name = 'theme_sdcthree/emailurl';
$title = get_string('emailurl','theme_sdcthree');
$description = get_string('emailurldesc', 'theme_sdcthree');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$settings->add($setting);

// Foot note setting
$name = 'theme_sdcthree/footnote';
$title = get_string('footnote','theme_sdcthree');
$description = get_string('footnotedesc', 'theme_sdcthree');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$settings->add($setting);

}