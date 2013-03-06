<?php // $Id: settings.php 915 2013-02-13 01:05:20Z malu $

defined('MOODLE_INTERNAL') || die();

require_once __DIR__.'/../../question/engine/bank.php';

if (!function_exists('block_sharing_cart_get_real_qtypes')) {
	function block_sharing_cart_get_real_qtypes()
	{
		$allqtypes = question_bank::get_all_qtypes();
		// some qtypes do not need workaround
		unset($allqtypes['missingtype']);
		unset($allqtypes['random']);
		// question_bank::sort_qtype_array() expects array(name => local_name)
		$qtypenames = array_map(function ($qtype) { return $qtype->local_name(); }, $allqtypes);
		$qtypenames = question_bank::sort_qtype_array($qtypenames);
		// sorts $allqtypes by $qtypenames
		return array_map(
			function ($name) use (&$allqtypes) { return $allqtypes[$name]; }, array_keys($qtypenames)
			);
	}
}

$settings->add(
	new admin_setting_configmulticheckbox(
		'block_sharing_cart/userdata_copyable_modtypes',
		get_string('conf:userdata_copyable_modtypes', 'block_sharing_cart'),
		get_string('conf:userdata_copyable_modtypes_desc', 'block_sharing_cart'),
		array('data' => 1, 'forum' => 1, 'glossary' => 1, 'wiki' => 1),
		array_reduce(
			$DB->get_records('modules', array(), 'name ASC'),
			function ($list, $module) use ($CFG, $OUTPUT)
			{
				$icon = $OUTPUT->pix_icon('icon', '', $module->name, array('class' => 'icon'));
				$list[$module->name] = ' ' . $icon . ' ' . get_string('modulename', $module->name);
				return $list;
			},
			array()
			)
	)
);

$settings->add(
	new admin_setting_configmulticheckbox(
		'block_sharing_cart/workaround_qtypes',
		get_string('conf:workaround_qtypes', 'block_sharing_cart'),
		get_string('conf:workaround_qtypes_desc', 'block_sharing_cart'),
		array(),
		array_reduce(
			block_sharing_cart_get_real_qtypes(),
			function ($list, $qtype) use ($CFG, $OUTPUT)
			{
				$icon = $OUTPUT->pix_icon('icon', '', $qtype->plugin_name());
				$list[$qtype->name()] = ' ' . $icon . ' ' . $qtype->local_name();
				return $list;
			},
			array()
			)
	)
);
