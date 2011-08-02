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
			<h1>', l('Request a Password Reset'), '</h1>
			<p>', l('You can begin the password reset process by entering your username or email address below (in case you are wondering, we cannot retrieve your current password due to the way passwords are stored).'), '</p>';

api()->run_hooks('display_forgotpw_form', array(&$handled));

// Did no one display the log in form?
if(empty($handled))
{
	// Any errors?
	if(count(api()->context['form']->errors('forgotpw_form')) > 0 || count(api()->apply_filters('forgotpw_form_messages', array())) > 0)
	{
		echo '
				<div class="', count(api()->context['form']->errors('forgotpw_form')) > 0 ? 'error-message' : 'message-box', '">';

		$messages = count(api()->context['form']->errors('forgotpw_form')) > 0 ? api()->context['form']->errors('forgotpw_form') : api()->apply_filters('forgotpw_form_messages', array());
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
			', api()->context['form']->open('forgotpw_form'), '
				<p class="label"><label for="member_name">', l('Username or email address'), '</label></p>
				<p class="input">', api()->context['form']->input('member_name')->generate(), '</p>';

	// Maybe you want to put something here?
	api()->run_hooks('forgotpw_form_between');

	echo '
				<p class="buttons"><input type="submit" name="forgotpw_form" id="forgotpw_form_submit" value="', l('Request reset'), '" /></p>

			', api()->context['form']->close('forgotpw_form');
}
?>