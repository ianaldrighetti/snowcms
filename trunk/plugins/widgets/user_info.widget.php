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

class User_Info_Widget extends Widget
{
	public function __construct()
	{
		parent::__construct('User Info', 'This widget will display useful links to the current user depending upon whether they are logged in or a guest.');
	}

	public function form($widget_id, $options)
	{

	}

	public function save($widget_id, $options, &$errors = array())
	{

	}

	public function render($widget_id, $display_options, $options)
	{

	}
}
?>