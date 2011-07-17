<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}
// !!! TODO: Allow log in if they are not logged in yet.

			echo '
			<h1>Log In</h1>';

// Should we show the message as to why they are here, or errors?
if(count(api()->context['form']->errors()) > 0)
{
	echo '
			<div class="error-message">';

	foreach(api()->context['form']->errors() as $error_message)
	{
		echo '
				<p>', $error_message, '</p>';
	}

	echo '
			</div>';
}
else
{
	echo '
			<div class="alert-message">
				<p>', l('Please enter your password for security purposes.'), '</p>
			</div>';
}

			echo '
			', api()->context['form']->open(), '
				<p class="label"><label for="member_name">', l('Username:'), '</label></p>
				<p class="input">', api()->context['form']->generate('member_name'), '</p>
				<p class="label"><label for="member_pass">', l('Password:'), '</label></p>
				<p class="input">', api()->context['form']->generate('member_pass'), '</p>
				<div class="float-left">
					<p class="no-margin">', l('Session length:'), ' ', api()->context['form']->generate('session_length'), '</p>
				</div>
				<div class="float-right">
					<p class="no-margin"><input type="submit" name="proc_login" value="', l('Log in'), '" /></p>
				</div>
				<div class="break">
				</div>';

			foreach($_POST as $index => $value)
			{
				// Don't put the hidden field in if it is for the log in form.
				if(in_array($index, array('member_name', 'member_pass', 'session_length', 'proc_login')))
				{
					continue;
				}

				echo '
				<input type="hidden" name="', htmlchars($index), '" value="', htmlchars($value), '" />';
			}

			echo '
			', api()->context['form']->close(), '
			<script type="text/javascript">
				s.onload(function() { document.getElementById(\'member_pass\').focus(); });
			</script>
';
			/*<h1>', l('Password Required'), '</h1>
			<p>', l('For security purposes, please enter your account password below. This is done to help make sure that you are who you say you are.'), '</p>
			<script type="text/javascript">
				s.onload(function() { document.getElementById(\'admin_prompt_form_admin_verification_password_input\').focus(); });
			</script>';

			api()->context['form']->show('admin_prompt_form');*/
?>