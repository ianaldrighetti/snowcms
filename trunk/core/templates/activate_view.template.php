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
			<h1>', l('Activate your account'), '</h1>
			<p>', l('If your account has not yet been activated, you can enter your activation information here. If you have yet to receive your activation email, you can <a href="%s">request for it to be resent</a>.', baseurl. '/index.php?action=resend'), '</p>';

		if(strlen(api()->apply_filters('activation_message', '')) > 0)
		{
			echo '
			<div id="', api()->apply_filters('activation_message_id', 'activation_error'), '">
				', api()->apply_filters('activation_message', ''), '
			</div>';
		}

		echo '
			<form action="', baseurl, '/index.php?action=activate" method="post" id="activation_form">
				<fieldset>
					<table>
						<tr id="activation_form_name">
							<td class="td_left">', l('Username:'), '</td><td class="td_right"><input id="activation_form_name_input" type="text" name="name" value="', htmlchars(!empty($_REQUEST['name']) ? $_REQUEST['name'] : ''), '" /></td>
						</tr>
						<tr id="activation_form_code">
							<td class="td_left">', l('Activation code:'), '</td><td class="td_right"><input id="activation_form_code_input" type="text" name="code" value="', htmlchars(!empty($_REQUEST['code']) ? $_REQUEST['code'] : ''), '" /></td>
						</tr>
						<tr id="activation_form_submit">
							<td colspan="2" class="buttons"><input type="submit" value="', l('Activate account'), '" /></td>
						</tr>
					</table>
				</fieldset>
			</form>';
?>