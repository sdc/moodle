<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

// Institution Name

// Set status of Autohide functionality
$name = 'theme_rocket/autohide';
$title = get_string('autohide','theme_rocket');
$description = get_string('autohidedesc', 'theme_rocket');
$default = 'enable';
$choices = array(
	'enable' => get_string('enable', 'theme_rocket'),
	'disable' => get_string('disable', 'theme_rocket')
);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$settings->add($setting);

// Set status of Edit Toggle functionality
$name = 'theme_rocket/edittoggle';
$title = get_string('editmodetoggle','theme_rocket');
$description = get_string('edittoggledesc', 'theme_rocket');
$default = 'enable';
$choices = array(
	'enable' => get_string('enable', 'theme_rocket'),
	'disable' => get_string('disable', 'theme_rocket')
);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$settings->add($setting);

// Set status of Bootstrap functionality
$name = 'theme_rocket/bootstrap';
$title = get_string('bootstrap','theme_rocket');
$description = get_string('bootstrapdesc', 'theme_rocket');
$default = 'disable';
$choices = array(
	'enable' => get_string('enable', 'theme_rocket'),
	'disable' => get_string('disable', 'theme_rocket')
);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$settings->add($setting);

// Set terminology for dropdown couse list
$name = 'theme_rocket/mycoursetitle';
$title = get_string('mycoursetitle','theme_rocket');
$description = get_string('mycoursetitledesc', 'theme_rocket');
$default = 'course';
$choices = array(
	'course' => get_string('mycourses', 'theme_rocket'),
	'unit' => get_string('myunits', 'theme_rocket'),
	'class' => get_string('myclasses', 'theme_rocket'),
	'module' => get_string('mymodules', 'theme_rocket')
);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$settings->add($setting);
$default = 'rocket/pix/logo/rocket.png';

// Banner file setting
$default = 'rocket/pix/banner/default.png';

// Banner Height
$name = 'theme_rocket/bannerheight';
$title = get_string('bannerheight','theme_rocket');
$description = get_string('bannerheightdesc', 'theme_rocket');
$default = 255;
$choices = array(5=>get_string('nobanner', 'theme_rocket'), 55=>'50px', 105=>'100px',155=>'150px', 205=>'200px', 255=>'250px',  305=>'300px',355=>'350px');
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$settings->add($setting);

// Fullscreen Toggle
$name = 'theme_rocket/screenwidth';
$title = get_string('screenwidth','theme_rocket');
$description = get_string('screenwidthdesc', 'theme_rocket');
$default = 1000;
$choices = array(1000=>get_string('fixedwidth','theme_rocket'), 97=>get_string('variablewidth','theme_rocket'));
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$settings->add($setting);


// Main theme trim colour setting

// Menu colour setting

// Menu hover colour setting

// Menu trim colour setting

// Content link colour setting

// Block link colour setting

// Menu link colour setting
// Copyright Notice

// Custom CSS file
$name = 'theme_rocket/customcss';
$title = get_string('customcss','theme_rocket');
$description = get_string('customcssdesc', 'theme_rocket');
$setting = new admin_setting_configtextarea($name, $title, $description, '');
$settings->add($setting);

}