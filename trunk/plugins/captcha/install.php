<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//                  Released under the GNU GPL v3 License                 //
//                    www.gnu.org/licenses/gpl-3.0.txt                    //
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

// Title: Install

/*
	Function: plugin_install

	A function which is called when the CAPTCHA plugin is installed.

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function.
*/
function plugin_install()
{
	global $settings;

	# All we need to do is set a couple settings.
	# That is, unless they already exist. In which case, we won't bother them!
	if($settings->get('captcha_enable', 'int', -1) == -1)
	{
		$settings->set('captcha_enable', 1);
	}

	if($settings->get('captcha_width', 'int', 0) == 0)
	{
		$settings->set('captcha_width', 200);
	}

	if($settings->get('captcha_height', 'int', 0) == 0)
	{
		$settings->set('captcha_height', 50);
	}

	if($settings->get('captcha_num_chars', 'int', 0) == 0)
	{
		$settings->set('captcha_num_chars', 6);
	}
}
?>