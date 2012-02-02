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
			<h1>', l('Activate Your Account'), '</h1>
			<p>', l('If you haven&#039;t received your activation email you can <a href="%s">request a new one</a>.', baseurl. '/index.php?action=resend'), '</p>';

api()->run_hooks('display_resend_form', array(&$handled));

if(empty($handled))
{
	// Any errors?
	if(count(api()->apply_filters('activate_form_errors', array())) > 0 || count(api()->apply_filters('activate_form_messages', array())) > 0)
	{
		echo '
				<div class="', count(api()->apply_filters('activate_form_errors', array())) > 0 ? 'error-message' : 'message-box', '">';

		$messages = count(api()->apply_filters('activate_form_errors', array())) > 0 ? api()->apply_filters('activate_form_errors', array()) : api()->apply_filters('activate_form_messages', array());
		foreach($messages as $message)
		{
			echo '
					<p>', $message, '</p>';
		}

		echo '
				</div>';
	}


	echo '
			<form action="', baseurl, '/index.php?action=activate" method="post" id="activate_form" class="form">
				<p class="label"><label for="member_name">', l('Username or email address:'), '</label></p>
				<p class="input"><input type="text" name="member_name" id="member_name" value="', htmlchars(!empty($_REQUEST['member_name']) ? $_REQUEST['member_name'] : ''), '" /></p>
				<p class="label"><label for="member_acode">', l('Activation code:'), '</label></p>
				<p class="input"><input type="text" name="code" id="member_acode" value="', htmlchars(!empty($_REQUEST['code']) ? $_REQUEST['code'] : ''), '" /></p>
				<p class="buttons"><input type="submit" name="activate_form" value="', l('Activate account'), '" /></p>
			</form>';
}
?>