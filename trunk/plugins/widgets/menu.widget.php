<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//            Released under the Microsoft Reciprocal License             //
//                 www.opensource.org/licenses/ms-rl.html                 //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//       SnowCMS originally pawned by soren121 started in early 2008      //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//                  SnowCMS v2.0 began in November 2009                   //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                       File version: SnowCMS 2.0                        //
////////////////////////////////////////////////////////////////////////////

if(!defined('INSNOW'))
{
	die('Nice try...');
}

class Menu_Widget extends Widget
{
	public function __construct()
	{
		parent::__construct('Menu', 'A simple menu widget for a list of static links.');
	}

	public function form($widget_id, $options)
	{
		$menu_categories = array();
		foreach(api()->return_menu_categories() as $category_id => $category_name)
		{
			$menu_categories[] = '<option value="'. htmlchars($category_id). '">'. htmlchars($category_name). '</option>';
		}

		$menu_categories = implode('', $menu_categories);

		return '<p><input type="text" name="title" value="'. htmlchars($options['title']). '" /></p><p><select name="menu_id" style="width: 100%;">'. $menu_categories. '</select></p>';
	}

	public function save($widget_id, $options, &$errors = array())
	{
		// We need to make sure the menu category is valid.
		$menu_categories = api()->return_menu_categories();

		if(!isset($menu_categories[$options['menu_id']]))
		{
			// It appears it doesn't exist. Woops!
			$errors[] = var_export($options, true);

			return false;
		}

		return array(
							'title' => $options['title'],
							'menu_id' => $options['menu_id'],
						);
	}

	public function render($widget_id, $display_options, $options)
	{
		// The menu_links will contain the HTML of the links and menu_items is
		// an array of all menu items in the menu category.
		$menu_links = array();
		$menu_items = api()->return_menu_items($options['menu_id']);

		// Make sure there is anything there.
		if($menu_items !== false)
		{
			foreach($menu_items as $item)
			{
				// We won't give it the content or extra stuff, but we will *need* the
				// content stuff ourselves.
				$content = $item['content'];
				unset($item['content'], $item['extra']);

				$menu_links[] = '
		'. api()->apply_filters('theme_menu_before', '<li>'). theme()->generate_tag('a', $item, false). $content. '</a>'. api()->apply_filters('theme_menu_after', '</li>');
			}

		}

		return $display_options['before']. (!empty($options['title']) ? $display_options['before_title']. $options['title']. $display_options['after_title'] : ''). $display_options['before_content']. '<ul id="navigation">'. implode('', $menu_links). '</ul>'. $display_options['after_content']. $display_options['after'];
	}
}
?>