<?php
/**
 *  Sharing Cart block
 *  
 *  @author  VERSION2, Inc.
 *  @version $Id: block_sharing_cart.php 914 2013-02-13 01:01:45Z malu $
 */

require_once __DIR__.'/classes/controller.php';

class block_sharing_cart extends block_base
{
	public function init()
	{
		$this->title   = get_string('pluginname', __CLASS__);
		$this->version = 2013021300;
	}

	public function applicable_formats()
	{
		return array('course' => true, 'course-category' => false);
	}

	public function instance_can_be_docked()
	{
		return false; // AJAX won't work with Dock
	}

	public function has_config()
	{
		return true;
	}

	/**
	 *  Get the block content
	 *  
	 *  @global object $CFG
	 *  @global object $USER
	 *  @return object|string
	 */
	public function get_content()
	{
		global $CFG, $USER;
		
		if ($this->content !== null)
			return $this->content;
		
		if (!$this->page->user_is_editing())
			return $this->content = '';
		
		$context = context_course::instance($this->page->course->id);
		if (!has_capability('moodle/backup:backupactivity', $context))
			return $this->content = '';
		
		$controller = new sharing_cart\controller();
		$html = $controller->render_tree($USER->id);
		
		if (empty($CFG->enableajax)) {
			$html = $this->get_content_noajax();
		}
		
		$this->page->requires->js('/blocks/sharing_cart/module.js');
		$this->page->requires->yui_module('block_sharing_cart', 'M.block_sharing_cart.init', array(), null, true);
		$this->page->requires->strings_for_js(
			array('yes', 'no', 'ok', 'cancel', 'error', 'edit', 'move', 'delete', 'movehere'),
			'moodle'
			);
		$this->page->requires->strings_for_js(
			array('copyhere', 'notarget', 'backup', 'restore', 'movedir', 'clipboard',
			      'confirm_backup', 'confirm_userdata', 'confirm_delete'),
			__CLASS__
			);
		
		$footer = '<div style="display:none;">'
		        . '<div class="header-commands">' . $this->get_header() . '</div>'
		        . '</div>';
		return $this->content = (object)array('text' => $html, 'footer' => $footer);
	}

	/**
	 *  Get the block header
	 *  
	 *  @global core_renderer $OUTPUT
	 *  @return string
	 */
	private function get_header()
	{
		global $OUTPUT;
		
		// link to bulkdelete
		$alt = get_string('bulkdelete', __CLASS__);
		$src = new moodle_url('/blocks/sharing_cart/pix/bulkdelete.gif');
		$url = new moodle_url('/blocks/sharing_cart/bulkdelete.php', array('course' => $this->page->course->id));
		$bulkdelete = '<a class="icon editing_bulkdelete" title="' . $alt . '" href="' . $url . '">'
		            . '<img src="' . $src . '" alt="' . $alt . '" />'
		            . '</a>';
		
		// help for Sharing Cart
		$helpicon = $OUTPUT->help_icon('sharing_cart', __CLASS__);
		
		return $bulkdelete . $helpicon;
	}

	/**
	 *  Get the block content for no-AJAX
	 *  
	 *  @global core_renderer $OUTPUT
	 *  @return string
	 */
	private function get_content_noajax()
	{
		global $OUTPUT;
		
		$html = '<div class="error">' . get_string('err:requireajax', __CLASS__) . '</div>';
		if (has_capability('moodle/site:config', context_system::instance())) {
			$url = new moodle_url('/admin/settings.php?section=ajax');
			$link = '<a href="' . $url . '">' . get_string('ajaxuse') . '</a>';
			$html .= '<div>' . $OUTPUT->rarrow() . ' ' . $link . '</div>';
		}
		return $html;
	}
}