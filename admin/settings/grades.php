<?php // $Id$

// This file defines settingpages and externalpages under the "grades" section

if (has_capability('moodle/grade:manage', $systemcontext)
 or has_capability('moodle/grade:manageletters', $systemcontext)) { // speedup for non-admins, add all caps used on this page

// General settings

require_once $CFG->libdir.'/grade/constants.php';

$temp = new admin_settingpage('gradessettings', get_string('generalsettings', 'grades'), 'moodle/grade:manage');

// new CFG variable for gradebook (what roles to display)
$temp->add(new admin_setting_special_gradebookroles());

// enable outcomes checkbox
$temp->add(new admin_setting_configcheckbox('enableoutcomes', get_string('enableoutcomes', 'grades'), get_string('configenableoutcomes', 'grades'), 0, PARAM_INT));

$temp->add(new admin_setting_grade_profilereport());

$temp->add(new admin_setting_configselect('grade_aggregationposition', get_string('aggregationposition', 'grades'),
                                          get_string('configaggregationposition', 'grades'), GRADE_REPORT_AGGREGATION_POSITION_LAST,
                                          array(GRADE_REPORT_AGGREGATION_POSITION_FIRST => get_string('positionfirst', 'grades'),
                                                GRADE_REPORT_AGGREGATION_POSITION_LAST => get_string('positionlast', 'grades'))));

$temp->add(new admin_setting_configcheckbox('grade_hiddenasdate', get_string('hiddenasdate', 'grades'), get_string('confighiddenasdate', 'grades'), 0, PARAM_INT));

// enable publishing in exports/imports
$temp->add(new admin_setting_configcheckbox('gradepublishing', get_string('gradepublishing', 'grades'), get_string('configgradepublishing', 'grades'), 0, PARAM_INT));

$temp->add(new admin_setting_configselect('grade_export_displaytype', get_string('gradeexportdisplaytype', 'grades'),
                                          get_string('configgradeexportdisplaytype', 'grades'), GRADE_DISPLAY_TYPE_REAL,
                                          array(GRADE_DISPLAY_TYPE_REAL => get_string('real', 'grades'),
                                                GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades'),
                                                GRADE_DISPLAY_TYPE_LETTER => get_string('letter', 'grades'))));

$temp->add(new admin_setting_configselect('grade_export_decimalpoints', get_string('gradeexportdecimalpoints', 'grades'),
                                          get_string('configexportdecimalpoints', 'grades'), 2,
                                          array( '0' => '0',
                                                 '1' => '1',
                                                 '2' => '2',
                                                 '3' => '3',
                                                 '4' => '4',
                                                 '5' => '5')));

$temp->add(new admin_setting_special_gradeexport());
$ADMIN->add('grades', $temp);

/// Grade category settings
$temp = new admin_settingpage('gradecategorysettings', get_string('gradecategorysettings', 'grades'), 'moodle/grade:manage');

$temp->add(new admin_setting_configcheckbox('grade_hideforcedsettings', get_string('hideforcedsettings', 'grades'), get_string('confighideforcedsettings', 'grades'), 0, PARAM_INT));

$strnoforce = get_string('noforce', 'grades');

// Aggregation type
$options = array(GRADE_AGGREGATE_MEAN            =>get_string('aggregatemean', 'grades'),
                 GRADE_AGGREGATE_WEIGHTED_MEAN   =>get_string('aggregateweightedmean', 'grades'),
                 GRADE_AGGREGATE_WEIGHTED_MEAN2  =>get_string('aggregateweightedmean2', 'grades'),
                 GRADE_AGGREGATE_EXTRACREDIT_MEAN=>get_string('aggregateextracreditmean', 'grades'),
                 GRADE_AGGREGATE_MEDIAN          =>get_string('aggregatemedian', 'grades'),
                 GRADE_AGGREGATE_MIN             =>get_string('aggregatemin', 'grades'),
                 GRADE_AGGREGATE_MAX             =>get_string('aggregatemax', 'grades'),
                 GRADE_AGGREGATE_MODE            =>get_string('aggregatemode', 'grades'),
                 GRADE_AGGREGATE_SUM             =>get_string('aggregatesum', 'grades'));
$defaults = array('value'=>GRADE_AGGREGATE_MEAN, 'forced'=>false, 'adv'=>false);
$temp->add(new admin_setting_gradecat_combo('grade_aggregation', get_string('aggregation', 'grades'), get_string('aggregationhelp', 'grades'), $defaults, $options));

$options = array(0 => get_string('no'), 1 => get_string('yes'));

$defaults = array('value'=>0, 'forced'=>false, 'adv'=>true);
$temp->add(new admin_setting_gradecat_combo('grade_aggregateonlygraded', get_string('aggregateonlygraded', 'grades'),
            get_string('aggregateonlygradedhelp', 'grades'), $defaults, $options));
$temp->add(new admin_setting_gradecat_combo('grade_aggregateoutcomes', get_string('aggregateoutcomes', 'grades'),
            get_string('aggregateoutcomeshelp', 'grades'), $defaults, $options));
$temp->add(new admin_setting_gradecat_combo('grade_aggregatesubcats', get_string('aggregatesubcats', 'grades'),
            get_string('aggregatesubcatshelp', 'grades'), $defaults, $options));

$options = array(0 => get_string('none'));
for ($i=1; $i<=20; $i++) {
    $options[$i] = $i;
}

$temp->add(new admin_setting_gradecat_combo('grade_keephigh', get_string('keephigh', 'grades'),
            get_string('keephighhelp', 'grades'), $defaults, $options));
$temp->add(new admin_setting_gradecat_combo('grade_droplow', get_string('droplow', 'grades'),
            get_string('droplowhelp', 'grades'), $defaults, $options));

$ADMIN->add('grades', $temp);


/// Grade item settings
$temp = new admin_settingpage('gradeitemsettings', get_string('gradeitemsettings', 'grades'), 'moodle/grade:manage');


$temp->add(new admin_setting_configselect('grade_displaytype', get_string('gradedisplaytype', 'grades'),
                                          get_string('configgradedisplaytype', 'grades'), GRADE_DISPLAY_TYPE_REAL,
                                          array(GRADE_DISPLAY_TYPE_REAL => get_string('real', 'grades'),
                                                GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades'),
                                                GRADE_DISPLAY_TYPE_LETTER => get_string('letter', 'grades'))));

$temp->add(new admin_setting_configselect('grade_decimalpoints', get_string('decimalpoints', 'grades'),
                                          get_string('configdecimalpoints', 'grades'), 2,
                                          array( '0' => '0',
                                                 '1' => '1',
                                                 '2' => '2',
                                                 '3' => '3',
                                                 '4' => '4',
                                                 '5' => '5')));

$temp->add(new admin_setting_configmultiselect('grade_item_advanced', get_string('gradeitemadvanced', 'grades'), get_string('configgradeitemadvanced', 'grades'),
                                               array('iteminfo', 'idnumber', 'gradepass', 'plusfactor', 'multfactor', 'display', 'decimals', 'hiddenuntil', 'locktime'),
                                               array('iteminfo' => get_string('iteminfo', 'grades'),
                                                     'idnumber' => get_string('idnumber'),
                                                     'gradetype' => get_string('gradetype', 'grades'),
                                                     'scaleid' => get_string('scale'),
                                                     'grademin' => get_string('grademin', 'grades'),
                                                     'grademax' => get_string('grademax', 'grades'),
                                                     'gradepass' => get_string('gradepass', 'grades'),
                                                     'plusfactor' => get_string('plusfactor', 'grades'),
                                                     'multfactor' => get_string('multfactor', 'grades'),
                                                     'display' => get_string('gradedisplaytype', 'grades'),
                                                     'decimals' => get_string('decimalpoints', 'grades'),
                                                     'hidden' => get_string('hidden', 'grades'),
                                                     'hiddenuntil' => get_string('hiddenuntil', 'grades'),
                                                     'locked' => get_string('locked', 'grades'),
                                                     'locktime' => get_string('locktime', 'grades'),
                                                     'aggregationcoef' => get_string('aggregationcoef', 'grades'),
                                                     'parentcategory' => get_string('parentcategory', 'grades'))));

$ADMIN->add('grades', $temp);


/// Scales and outcomes

$scales = new admin_externalpage('scales', get_string('scales'), $CFG->wwwroot.'/grade/edit/scale/index.php', 'moodle/grade:manage');
$ADMIN->add('grades', $scales);
$outcomes = new admin_externalpage('outcomes', get_string('outcomes', 'grades'), $CFG->wwwroot.'/grade/edit/outcome/index.php', 'moodle/grade:manage');
$ADMIN->add('grades', $outcomes);
$letters = new admin_externalpage('letters', get_string('letters', 'grades'), $CFG->wwwroot.'/grade/edit/letter/edit.php', 'moodle/grade:manageletters');
$ADMIN->add('grades', $letters);

// The plugins must implement a settings.php file that adds their admin settings to the $settings object

// Reports

$first = true;
foreach (get_list_of_plugins('grade/report') as $plugin) {
 // Include all the settings commands for this plugin if there are any
    if ($first) {
        $ADMIN->add('grades', new admin_category('gradereports', get_string('reportsettings', 'grades')));
        $first = false;
    }

    if (file_exists($CFG->dirroot.'/grade/report/'.$plugin.'/settings.php')) {

        $settings = new admin_settingpage('gradereport'.$plugin, get_string('modulename', 'gradereport_'.$plugin), 'moodle/grade:manage');
        include($CFG->dirroot.'/grade/report/'.$plugin.'/settings.php');
        $ADMIN->add('gradereports', $settings);
    }
}

// Imports

$first = true;
foreach (get_list_of_plugins('grade/import') as $plugin) {

 // Include all the settings commands for this plugin if there are any
    if (file_exists($CFG->dirroot.'/grade/import/'.$plugin.'/settings.php')) {
        if ($first) {
            $ADMIN->add('grades', new admin_category('gradeimports', get_string('imports')));
            $first = false;
        }

        $settings = new admin_settingpage('gradeimport'.$plugin, get_string('modulename', 'gradeimport_'.$plugin), 'moodle/grade:manage');
        include($CFG->dirroot.'/grade/import/'.$plugin.'/settings.php');
        $ADMIN->add('gradeimports', $settings);
    }
}


// Exports

$first = true;
foreach (get_list_of_plugins('grade/export') as $plugin) {
 // Include all the settings commands for this plugin if there are any
    if (file_exists($CFG->dirroot.'/grade/export/'.$plugin.'/settings.php')) {
        if ($first) {
            $ADMIN->add('grades', new admin_category('gradeexports', get_string('exports')));
            $first = false;
        }

        $settings = new admin_settingpage('gradeexport'.$plugin, get_string('modulename', 'gradeexport_'.$plugin), 'moodle/grade:manage');
        include($CFG->dirroot.'/grade/export/'.$plugin.'/settings.php');
        $ADMIN->add('gradeexports', $settings);
    }
}

} // end of speedup

?>
