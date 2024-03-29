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

// Now it is time to show the form...
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

			// Redirect back to hidden field? Perhaps.
			if(api()->context['form']->input_exists('redir_to'))
			{
				echo '
				', api()->context['form']->generate('redir_to');
			}

			echo '
			', api()->context['form']->close(), '
			<script type="text/javascript">
				s.onload(function()
					{
						if(s.id(\'member_name\').value.length > 0)
						{
							s.id(\'member_pass\').focus();
						}
						else
						{
							s.id(\'member_name\').focus();
						}
					});
			</script>';
?>