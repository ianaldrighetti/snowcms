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

// Title: Manage Widgets

if(!function_exists('admin_themes_widgets'))
{
	/*
		Function: admin_themes_widgets

		Provides the interface for managing the widgets displayed within the
		current theme.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.
	*/
	function admin_themes_widgets()
	{
		api()->run_hooks('admin_themes_widgets');

		// You can only manage widgets if you're allowed to, of course!
		if(!member()->can('manage_themes'))
		{
			admin_access_denied();
		}

		admin_current_area('widgets_manage_themes');

		theme()->set_title(l('Manage Widgets'));

		theme()->render('admin_themes_widgets');
	}
}
?>