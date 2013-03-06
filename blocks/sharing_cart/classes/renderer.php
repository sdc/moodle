<?php
/**
 *  Sharing Cart
 *  
 *  @author  VERSION2, Inc.
 *  @version $Id: renderer.php 799 2012-09-13 07:53:58Z malu $
 */
namespace sharing_cart;

/**
 *  Sharing Cart item tree renderer
 */
class renderer
{
	/**
	 *  Render an item tree
	 *  
	 *  @param array & $tree
	 */
	public static function render_tree(array & $tree)
	{
		return '<ul class="tree list" style="font-size:90%;">'
		     . self::render_node($tree, '/')
		     . '</ul>';
	}
	
	/**
	 *  Render a node of item tree
	 *  
	 *  @param array & $node
	 *  @param string  $path
	 */
	private static function render_node(array & $node, $path)
	{
		$html = '';
		foreach ($node as $name => & $leaf) {
			if ($name !== '') {
				$next = rtrim($path, '/') . '/' . $name;
				$html .= self::render_dir_open($next);
				$html .= self::render_node($leaf, $next);
				$html .= self::render_dir_close();
			} else {
				foreach ($leaf as $item)
					$html .= self::render_item($path, $item);
			}
		}
		return $html;
	}
	/**
	 *  Render a directory open
	 *  
	 *  @param string $path
	 */
	private static function render_dir_open($path)
	{
		$components = explode('/', trim($path, '/'));
		$depth = count($components) - 1;
		return '
		<li class="directory">
			<div class="mod-indent-' . $depth . '" title="' . htmlspecialchars($path) . '">
				<img class="activityicon" src="' . $GLOBALS['OUTPUT']->pix_url('i/closed') . '" alt="" />
				<span class="instancename">' . htmlspecialchars(end($components)) . '</span>
			</div>
			<ul class="list" style="display:none;">';
	}
	/**
	 *  Render an item
	 *  
	 *  @param string $path
	 *  @param record $item
	 */
	private static function render_item($path, $item)
	{
		$components = array_filter(explode('/', trim($path, '/')), 'strlen');
		$depth = count($components);
		$class = $item->modname . ' ' . "modtype_{$item->modname}";
		return '
				<li class="activity ' . $class . '" id="block_sharing_cart-item-' . $item->id . '">
					<div class="mod-indent-' . $depth . '">
						' . self::render_modicon($item) . '
						<span class="instancename">' . $item->modtext . '</span>
						<span class="commands"></span>
					</div>
				</li>';
	}
	/**
	 *  Render a directory close
	 */
	private static function render_dir_close()
	{
		return '
			</ul>
		</li>';
	}
	
	
	/**
	 *  Render a module icon
	 *  
	 *  @param object $item
	 *  @return string
	 */
	public static function render_modicon($item)
	{
		if ($item->modname === 'label')
			return '';
		$src = $GLOBALS['OUTPUT']->pix_url('icon', $item->modname);
		if (!empty($item->modicon)) {
			// @see /lib/modinfolib.php#get_icon_url()
			if (strncmp($item->modicon, 'mod/', 4) == 0) {
				list ($modname, $iconname) = explode('/', substr($item->modicon, 4), 2);
				$src = $GLOBALS['OUTPUT']->pix_url($iconname, $modname);
			} else {
				$src = $GLOBALS['OUTPUT']->pix_url($item->modicon);
			}
		}
		return '<img class="activityicon" src="' . $src . '" alt="" />';
	}
}
