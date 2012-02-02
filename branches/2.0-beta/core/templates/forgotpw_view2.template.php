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

if(!empty(api()->context['request_revoked']))
{
	echo '
			<h1>', l('Reset Request Revoked'), '</h1>
			<div class="message-box">
				<p>', l('The password reset request for your account was successfully revoked.'), '</p>
			</div>';
}
else
{
	echo '
			<h1>', l('Reset Your Password'), '</h1>
			<p>', l('You may now reset your account&#039;s password.'), '</p>';

	api()->run_hooks('display_reset_password_form', array(&$handled));

	// Did no one display the log in form?
	if(empty($handled))
	{
		// Any errors?
		if(count(api()->context['form']->errors('reset_password_form')) > 0 || count(api()->apply_filters('reset_password_form_messages', array())) > 0)
		{
			echo '
				<div class="', count(api()->context['form']->errors('reset_password_form')) > 0 ? 'error-message' : 'message-box', '">';

			$messages = count(api()->context['form']->errors('reset_password_form')) > 0 ? api()->context['form']->errors('reset_password_form') : api()->apply_filters('reset_password_form_messages', array());
			foreach($messages as $message)
			{
				echo '
					<p>', $message, '</p>';
		}

			echo '
				</div>';
		}

		// Nope, so it is up to us to do it then.
		echo '
			', api()->context['form']->open('reset_password_form'), '
				<p class="label"><label for="member_name">', api()->context['form']->input('new_password')->label(), '</label></p>
				<p class="input">', api()->context['form']->input('new_password')->generate(), '</p>
				<p class="label"><label for="member_pass">', api()->context['form']->input('verify_password')->label(), '</label></p>
				<p class="input">', api()->context['form']->input('verify_password')->generate(), '</p>';

		// Maybe you want to put something here?
		api()->run_hooks('reset_password_form_between');

		echo '
				<p class="buttons"><input type="submit" name="reset_password_form" id="reset_password_form_submit" value="', l('Reset password'), '" /></p>
				', api()->context['form']->generate('id'), '
				', api()->context['form']->generate('code'), '
			', api()->context['form']->close('reset_password_form');
	}
}
?>