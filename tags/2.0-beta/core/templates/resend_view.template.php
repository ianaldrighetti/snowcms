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
			<h1>', l('Request a New Activation Email'), '</h1>
			<p>', l('In case you did not receive your activation email you can request a new one by entering your username or email address below. Please be sure to wait a few minutes before requesting another and also check your spam folder.'), '</p>';

// Want to do this, for some odd reason?
api()->run_hooks('resend_form_display', array(&$handled));

if(empty($handled))
{
	// Any errors?
	if(count(api()->context['form']->errors('resend_form')) > 0 || count(api()->apply_filters('resend_form_messages', array())) > 0)
	{
		echo '
				<div class="', count(api()->context['form']->errors('resend_form')) > 0 ? 'error-message' : 'message-box', '">';

		$messages = count(api()->context['form']->errors('resend_form')) > 0 ? api()->context['form']->errors('resend_form') : api()->apply_filters('resend_form_messages', array());
		foreach($messages as $message)
		{
			echo '
					<p>', $message, '</p>';
		}

		echo '
				</div>';
	}

	echo '
			', api()->context['form']->open('resend_form'), '
				<p class="label"><label for="member_name">', l('Username or email address'), '</label></p>
				<p class="input">', api()->context['form']->input('member_name')->generate(), '</p>
				<p class="buttons right"><input type="submit" name="resend_form" id="resend_form_submit" value="', l('Resend activation'), '" /></p>
			', api()->context['form']->close('resend_form');
}
?>