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

// Title: Email Address Verification

if(!function_exists('verify_email'))
{
	/*
		Function: verify_email

		Completes the process of an email address change for a user. This will
		verify the activation code with the specified member account, and if
		they match their new email address will be set.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.
	*/
	function verify_email()
	{
		// We need a member ID and an activation code.
		$member_id = isset($_GET['id']) && (int)$_GET['id'] > 0 ? (int)$_GET['id'] : 0;
		$email_acode = isset($_GET['code']) ? $_GET['code'] : '';

		// We should only go ahead and load up the stuff required to start the
		// process of completing the change if the information supplied has the
		// remote possibility of being valid!
		api()->context['message'] = '';
		api()->context['is_error'] = true;
		if($member_id > 0 && strlen($email_acode) == 40)
		{
			// The Members class has what we need.
			$members = api()->load_class('Members');

			// We will go ahead and attempt to load the specified members
			// information.
			$members->load($member_id);

			// Make sure the account exists, that the account is activated, and
			// there is a current request for an email address change.

			if(($member_info = $members->get($member_id)) !== false && !empty($member_info['is_activated']) && isset($member_info['data']['member_email'], $member_info['data']['email_acode']))
			{
				// Everything seems to be good so far, but let's make sure that the
				// email address is still allowed. You never know, someone could
				// have claimed it between the time the email was sent and when the
				// verification link was clicked.
				if($members->email_allowed($member_info['data']['member_email'], $member_info['id']))
				{
					// Alright, just one last thing, is the activation code legit?
					if($member_info['data']['email_acode'] == $email_acode)
					{
						// Update their account.
						$members->update($member_info['id'], array(
																									 'member_email' => $member_info['data']['member_email'],
																									 'data' => array(
																															 'member_email' => false,
																															 'email_acode' => false,
																														 ),
																								 ));

						// Set our success message!
						api()->context['message'] = l('Your new email address has been successfully verified, the changes have been applied to your account.');
						api()->context['is_error'] = false;
					}
					else
					{
						api()->context['message'] = l('Sorry, but the verification code supplied was not valid.');
					}
				}
				else
				{
					// Since the email address is no longer allowed, we will simply
					// remove the request entirely.
					$members->update($member_info['id'], array(
																								 'data' => array(
																														 'member_email' => false,
																														 'email_acode' => false,
																													 ),
																							 ));

					api()->context['message'] = l('Sorry, but that email address is already in use.');
				}
			}
			else
			{
				api()->context['message'] = l('Sorry, but that account does not exist, is not activated, or has not made an email address change request.');
			}
		}
		else
		{
			api()->context['message'] = l('Sorry, but no email address verification information was supplied.');
		}

		theme()->set_title(l('Verify Your Email Address'));

		theme()->render('verify_email_view');
	}
}