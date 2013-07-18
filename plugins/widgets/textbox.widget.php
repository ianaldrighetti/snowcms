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

class Textbox_Widget extends Widget
{
	public function __construct()
	{
		parent::__construct('Text Box', 'A simple text box widget for entering static content.');
	}

	public function form($widget_id, $options)
	{
		return '<p><input type="text" name="title" value="'. htmlchars($options['title']). '" /></p><p><textarea name="text" style="width: 95%; height: 50px;">'. (!empty($options['text']) ? htmlchars($options['text']) : ''). '</textarea></p>';
	}

	public function save($widget_id, $options, &$errors = array())
	{

	}

	public function render($widget_id, $display_options, $options)
	{

	}
}
?>