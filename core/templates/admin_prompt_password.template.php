<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}
// !!! TODO: Allow log in if they are not logged in yet.

			echo '
			<h1>Log In</h1>

			<div class="alert-message">
				<p>', l('Please enter your password for security purposes.'), '</p>
			</div>

			<form action="" method="post" id="login-form">
				<p class="label"><label for="member_name">', l('Username:'), '</label></p>
				<p class="input"><input type="text" name="member_name" id="member_name" value="', member()->name(), '" disabled="disabled" /></p>
				<p class="label"><label for="member_pass">', l('Password:'), '</label></p>
				<p class="input"><input type="password" name="member_pass" id="member_pass" value="" /></p>
				<div class="float-left">
					<p class="no-margin">Session length: <select name="session_length">
																								 <option>Forever</option>
																							 </select></p>
				</div>
				<div class="float-right">
					<p class="no-margin"><input type="submit" name="proc_login" value="', l('Log in'), '" /></p>
				</div>
				<div class="break">
				</div>';

			foreach($_POST as $index => $value)
			{
				echo '
				<input type="hidden" name="', htmlchars($index), '" value="', htmlchars($value), '" />';
			}

			echo '
			</form>
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