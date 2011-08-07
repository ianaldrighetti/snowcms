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
			<h1>', l('Log In'), '</h1>';

// Before we say they can register an account, let's make sure
// registration is open!
if(api()->apply_filters('display_registration_message', settings()->get('registration_type', 'int', 1) != 0))
{
	echo '
			<p>', l('If you don&#039;t have an account feel free to <a href="%s">create one</a>, as you may gain access to more features and content on this website with an account.', baseurl. '/index.php?action=register'), '</p>';
}

api()->run_hooks('display_login_form', array(&$handled));

// Did no one display the log in form?
if(empty($handled))
{
	// Any errors?
	if(count(api()->context['form']->errors('login_form')) > 0 || count(api()->apply_filters('login_form_messages', array())) > 0)
	{
		echo '
				<div class="', count(api()->context['form']->errors('login_form')) > 0 ? 'error-message' : 'message-box', '">';

		$messages = count(api()->context['form']->errors('login_form')) > 0 ? api()->context['form']->errors('login_form') : api()->apply_filters('login_form_messages', array());
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
			', api()->context['form']->open('login_form'), '
				<p class="label"><label for="member_name">', api()->context['form']->input('member_name')->label(), '</label></p>
				<p class="input">', api()->context['form']->input('member_name')->generate(), '</p>
				<p class="label"><label for="member_pass">', api()->context['form']->input('member_pass')->label(), '</label></p>
				<p class="input">', api()->context['form']->input('member_pass')->generate(), '</p>';

	// Maybe you want to put something here?
	api()->run_hooks('login_form_between');

	echo '
				<div id="login_form_below">
					<div class="float-left">
						<p><label>', api()->context['form']->input('session_length')->label(), ' ', api()->context['form']->input('session_length')->generate(), '</label></p>
					</div>
					<div class="float-right">
						<p class="buttons"><input type="submit" name="login_form" id="login_form_submit" value="', l('Log In'), '" /></p>
					</div>
					<div class="break">
					</div>
					<p class="right smaller"><a href="', baseurl, '/index.php?action=forgotpw" title="', l('If you forgot your password you can request a new one'), '">', l('Forgot your password?'), '</a></p>
				</div>
				', api()->context['form']->input('redir_to')->generate(), '
			', api()->context['form']->close('login_form');
}
?>