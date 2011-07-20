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

// Title: Account activation

if(!function_exists('activate_view'))
{
	/*
		Function: activate_view

		Handles the activation of members who registered an account but were
		required to activate their account via email.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function activate_view()
	{
		global $api, $member, $settings, $theme;

		api()->run_hooks('activate_view');

		// Are you logged in? Then why would you need to activate another account?
		if(member()->is_logged())
		{
			redirect(baseurl. '/index.php');
		}
		// What is the registration type? Is it actually email?
		elseif(settings()->get('registration_type', 'int', 0) != 3)
		{
			theme()->set_title(l('An Error Occurred'));

			api()->context['error_title'] = l('Registration Error');
			api()->context['error_message'] = l('It appears that either registration is disabled or the administrator must manually activate your account.');

			theme()->render('error');
			exit;
		}

		// It should use a form in reality, but since this can be done through
		// a URL that wouldn't be the best solution. So we will hand make it,
		// in this case!
		if((!empty($_REQUEST['id']) || !empty($_REQUEST['name'])) && !empty($_REQUEST['code']) && $_REQUEST['code'] != 'admin_approval')
		{
			// We will be needing this. That's for sure :P
			$members = api()->load_class('Members');

			// Did you give is a name? We need to convert it to an ID.
			if(empty($_REQUEST['id']) && !empty($_REQUEST['name']))
			{
				$_REQUEST['id'] = (int)$members->name_to_id($_REQUEST['name']);
			}

			// Load up that member :)
			$members->load($_REQUEST['id']);
			$member_info = $members->get($_REQUEST['id']);

			if(!empty($member_info))
			{
				// Just because you got the right ID doesn't mean nothin' :P
				// Has this account already been activated?
				if($member_info['is_activated'] == 1)
				{
					api()->add_filter('activation_message', create_function('$value', '
																								 return l(\'It appears that the specified member is already activated. If this is your account, you can <a href="%s">login</a> now.\', baseurl. \'/index.php?action=login\');'));
					api()->run_hooks('activation_member_already_activated', array($member_info));

					$_REQUEST['name'] = $member_info['username'];
				}
				// Do the codes not match?
				elseif($member_info['acode'] != $_REQUEST['code'] || strlen($member_info['acode']) == 0 || $member_info['acode'] == 'admin_approval')
				{
					api()->add_filter('activation_message', create_function('$value', '
																									 return l(\'The supplied activation code is invalid.\');'));
					api()->run_hooks('activation_member_invalid_acode', array($member_info));

					$_REQUEST['name'] = $member_info['username'];
				}
				else
				{
					// Sweet! It's right ;D
					$members->update($_REQUEST['id'], array(
																							'member_acode' => '',
																							'member_activated' => 1,
																						));

					api()->add_filter('activation_message_id', create_function('$value', '
																											return \'activation_success\';'));
					api()->add_filter('activation_message', create_function('$value', '
																										 return l(\'Your account has been successfully activated. You may now proceed to <a href="%s">login</a>.\', baseurl. \'/index.php?action=login\');'));
					api()->run_hooks('activation_member_success', array($member_info));

					$_REQUEST['name'] = '';
					$_REQUEST['code'] = '';
				}
			}
			else
			{
				// It appears that member does not exist... Interesting.
				api()->add_filter('activation_message', create_function('$value', '
																								 return l(\'It appears that the specified member does not exist. Please try again.\');'));
				api()->run_hooks('activation_member_nonexist');
			}
		}

		theme()->set_title('Activate your account');

		// No indexing if you have anything extra set ;)
		if(isset($_GET['id']) || isset($_GET['code']))
		{
			theme()->add_meta(array('name' => 'robots', 'content' => 'noindex'));
		}

		theme()->render('activate_view');
	}
}
?>