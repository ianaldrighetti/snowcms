<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

					echo '
			<h1>', l('Set Your New Password'), '</h1>
			<p>', l('Simply enter your new password below to reset your password.'), '</p>';

			api()->context['form']->show('reset_password_form');
?>