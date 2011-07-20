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

		echo '
			<h1>', l('Log in to your account'), '</h1>
			<p>', l('Here you can log in to your account, if you do not have an account, you can <a href="%s">register one</a>. Did you forget your password? Request a new one <a href="%s">here</a>.', baseurl. '/index.php?action=register', baseurl. '/index.php?action=reminder'), '</p>';

		// Display that lovely login form.
		api()->context['form']->render('login_form');
?>