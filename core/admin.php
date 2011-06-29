<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//                  Released under the GNU GPL v3 License                 //
//                    www.gnu.org/licenses/gpl-3.0.txt                    //
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

if(!defined('IN_SNOW'))
{
  die('Nice try...');
}

// Title: Admin switch

if(!function_exists('admin_prepend'))
{
  /*
    Function: admin_prepend

    With events there is no switch function which all administrative
    sections branch off from, and we would rather not require each
    plugin to have to check and see if the user needs to authenticate
    themselves again. So it is done here, via a hook :).

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_prepend()
  {
    global $icons;

    // Whether or not you can view the Admin Control Panel, load the theme!
    require_once(coredir. '/admin/admin_theme.class.php');

    $GLOBALS['theme'] = api()->load_class(api()->apply_filters('admin_theme_class', 'Admin_Theme'), array(l('Control Panel'). ' - '. settings()->get('site_name', 'string'), api()->apply_filters('admin_theme_image_url', themeurl. '/default/style/images/admincp')));

    if(member()->can('access_admin_cp'))
    {
      // Generate all the icons, done here as it is used in other places
      // than just the control panel home.
      $icons = array(
        l('SnowCMS') => array(
                          array(
                            'id' => 'system_settings',
                            'href' => baseurl. '/index.php?action=admin&amp;sa=settings',
                            'title' => l('System settings'),
                            'src' => theme()->url(). '/settings.png',
                            'label' => l('Settings'),
                            'show' => member()->can('manage_system_settings'),
                          ),
                          array(
                            'id' => 'manage_themes',
                            'href' => baseurl. '/index.php?action=admin&amp;sa=themes',
                            'title' => l('Manage themes'),
                            'src' => theme()->url(). '/manage_themes.png',
                            'label' => l('Themes'),
                            'show' => member()->can('manage_themes'),
                          ),
                          array(
                            'id' => 'system_update',
                            'href' => baseurl. '/index.php?action=admin&amp;sa=update',
                            'title' => l('Check for updates'),
                            'src' => theme()->url(). '/update.png',
                            'label' => l('Update'),
                            'show' => member()->can('update_system'),
                          ),
                          array(
                            'id' => 'system_about',
                            'href' => baseurl. '/index.php?action=admin&amp;sa=about',
                            'title' => l('About SnowCMS and system information'),
                            'src' => theme()->url(). '/about.png',
                            'label' => l('About'),
                            'show' => true,
                          ),
                          array(
                            'id' => 'system_error_log',
                            'href' => baseurl. '/index.php?action=admin&amp;sa=error_log',
                            'title' => l('View the error log'),
                            'src' => theme()->url(). '/error_log.png',
                            'label' => l('Errors'),
                            'show' => member()->can('view_error_log') && settings()->get('errors_log', 'bool'),
                          ),
                        ),
        l('Members') => array(
                          array(
                            'id' => 'members_add',
                            'href' => baseurl. '/index.php?action=admin&amp;sa=members_add',
                            'title' => l('Add a new member'),
                            'src' => theme()->url(). '/members_add.png',
                            'label' => l('Add'),
                            'show' => member()->can('add_new_member'),
                          ),
                          array(
                            'id' => 'members_manage',
                            'href' => baseurl. '/index.php?action=admin&amp;sa=members_manage',
                            'title' => l('Manage existing members'),
                            'src' => theme()->url(). '/members_manage.png',
                            'label' => l('Manage'),
                            'show' => member()->can('manage_members'),
                          ),
                          array(
                            'id' => 'members_settings',
                            'href' => baseurl. '/index.php?action=admin&amp;sa=members_settings',
                            'title' => l('Member settings'),
                            'src' => theme()->url(). '/members_settings.png',
                            'label' => l('Settings'),
                            'show' => member()->can('manage_member_settings'),
                          ),
                          array(
                            'id' => 'members_permissions',
                            'href' => baseurl. '/index.php?action=admin&amp;sa=members_permissions',
                            'title' => l('Set member group permissions'),
                            'src' => theme()->url(). '/members_permissions.png',
                            'label' => l('Permissions'),
                            'show' => member()->can('manage_permissions'),
                          ),
                        ),
        l('Plugins') => array(
                          array(
                            'id' => 'plugins_add',
                            'href' => baseurl. '/index.php?action=admin&amp;sa=plugins_add',
                            'title' => l('Add a new plugin'),
                            'src' => theme()->url(). '/plugins_add.png',
                            'label' => l('Add'),
                            'show' => member()->can('add_plugins'),
                          ),
                          array(
                            'id' => 'plugins_manage',
                            'href' => baseurl. '/index.php?action=admin&amp;sa=plugins_manage',
                            'title' => l('Manage plugins'),
                            'src' => theme()->url(). '/plugins_manage.png',
                            'label' => l('Manage'),
                            'show' => member()->can('manage_plugins'),
                          ),
                          array(
                            'id' => 'plugins_settings',
                            'href' => baseurl. '/index.php?action=admin&amp;sa=plugins_settings',
                            'title' => l('Manage plugin settings'),
                            'src' => theme()->url(). '/plugins_settings.png',
                            'label' => l('Settings'),
                            'show' => member()->can('manage_plugin_settings'),
                          ),
                        ),
      );

      // You can make changes via this filter:
      $icons = api()->apply_filters('admin_icons', $icons);

      // Remove any that don't need showing, though.
      $tmp = array();
      foreach($icons as $header => $icon)
      {
        foreach($icon as $key => $info)
        {
          if(empty($info['show']))
          {
            unset($icon[$key]);
          }
        }

        if(count($icon) > 0)
        {
          $tmp[$header] = $icon;
        }
      }

      // Put it back :P
      $icons = $tmp;
    }

    // You could be making an ajax request (Oh yeah, did I mention any ajax
    // requests dealing with control panel stuff should be prepended by
    // action=admin&sa=ajax{rest of your stuff}? It should be!!!)
    if(member()->can('access_admin_cp') && substr($_SERVER['QUERY_STRING'], 0, 20) == 'action=admin&sa=ajax')
    {
      // So it's an ajax request!
      // Is an administrative prompt required?
      if(admin_prompt_required())
      {
        // You sending us a password? Cool!
        if(isset($_POST['admin_password']))
        {
          // Did it work?
          if(!admin_prompt_password($_POST['admin_password']))
          {
            // Nope :(
            echo json_encode(array('error' => l('Incorrect password'), 'admin_prompt_required' => true));
            exit;
          }
        }
        else
        {
          // Yes, it is.
          echo json_encode(array('error' => l('Your session has timed out'), 'admin_prompt_required' => true));
          exit;
        }
      }
    }
    else
    {
      // Not allowed to access the Admin Control Panel?
      if(!member()->can('access_admin_cp'))
      {
        // There's a function for that. :P
        admin_access_denied();
      }

      // We may require you to enter a password, for security reasons!
      admin_prompt_password();
    }

    // You can make changes to the theme and what not now :)
    api()->run_hooks('admin_prepend_authenticated', array('ajax' => substr($_SERVER['QUERY_STRING'], 0, 20) == 'action=admin&sa=ajax'));
  }
}

if(!function_exists('admin_prompt_required'))
{
  /*
    Function: admin_prompt_required

    Checks to see if the user needs to verify their session with
    their account password. Useful for AJAX kind of things, ;).

    Parameters:
      none

    Returns:
      bool - Returns true if the user needs to supply their password
             in order to continue, false if not.

    Note:
      This function is overloadable.
  */
  function admin_prompt_required()
  {
    // Check to see if your last check has now timed out, quite simple
    // really! But if you for some strange reason have it disabled,
    // nevermind!
    if(!settings()->get('disable_admin_security', 'bool', false) && (empty($_SESSION['admin_password_prompted']) || ((int)$_SESSION['admin_password_prompted'] + (settings()->get('admin_login_timeout', 'int', 15) * 60)) < time_utc()))
    {
      return true;
    }

    // Your good, for now!
    return false;
  }
}

if(!function_exists('admin_prompt_password'))
{
  /*
    Function: admin_prompt_password

    Unlike <admin_prompt_required>, this function actually prompts
    for the password itself. A form is shown where the user can enter
    their password, or, a parameter can be passed containing their
    password (plain text, SHA-1'd or secured) for use by AJAX means.
    Hint hint ;)

    Parameters:
      string $password - The users plain text or SHA-1'd,
                         if left blank, the form is displayed.

    Returns:
      mixed - This function returns nothing if password is null,
              otherwise it returns a bool, true if the password
              was correct, false if not.

    Note:
      This function is overloadable.
  */
  function admin_prompt_password($password = null)
  {
    // Is it time for you to re-enter your password?
    if(admin_prompt_required())
    {
      // Did you supply a password?
      if($password !== null)
      {
        $errors = array();
        if(admin_prompt_handle(array('admin_verification_password' => $password), $errors))
        {
          return true;
        }
        else
        {
          return false;
        }
      }

      // Generate the login form.
      admin_prompt_generate_form();

      $form = api()->load_class('Form');

      // Has the form been submitted? Process it!
      if(isset($_POST['admin_prompt_form']))
      {
        $success = $form->process('admin_prompt_form');

        // Did they pass?
        if(!empty($success))
        {
          // Yup, no need to continue!
          return;
        }
      }

      theme()->set_title(l('Log in'));

			api()->context['form'] = $form;

      theme()->render('admin_prompt_password');

      // Don't execute anything else.
      exit;
    }
  }
}

if(!function_exists('admin_prompt_generate_form'))
{
  /*
    Function: admin_prompt_generate_form

    Generates the form which displays the administrative security prompt.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_prompt_generate_form()
  {
    // Create the form so you can enter your password.
    $form = api()->load_class('Form');

    $form->add('admin_prompt_form', array(
                                      'action' => '',
                                      'callback' => 'admin_prompt_handle',
                                      'submit' => l('Login'),
                                    ));

    $form->add_field('admin_prompt_form', 'admin_verification_password', array(
                                                                           'type' => 'password',
                                                                           'label' => l('Password:'),
                                                                         ));

    // Let's add all the post data you were entering before ;)
    foreach($_POST as $key => $value)
    {
      $form->add_field('admin_prompt_form', $key, array(
                                                    'type' => 'hidden',
                                                    'value' => $value,
                                                  ));
    }
  }
}

if(!function_exists('admin_prompt_handle'))
{
  /*
    Function: admin_prompt_handle

    Handles the verification of the supplied administrator password.

    Parameters:
      array $data
      array &$errors

    Returns:
      bool - Returns true if the supplied password was correct, false if
             not.

    Note:
      This function is overloadable.
  */
  function admin_prompt_handle($data, &$errors = array())
  {
    global $func;

    // No password? Pfft.
    if(empty($data['admin_verification_password']) || $func['strlen']($data['admin_verification_password']) == 0)
    {
      $errors[] = l('Please enter your password.');
      return false;
    }

    // The Members class has a useful method called authenticate :)
    $members = api()->load_class('Members');

    // Pretty simple to do. There are a couple hooks in that method, fyi.
    if($members->authenticate(member()->name(), $data['admin_verification_password']))
    {
      // Set the last time you verified in your session information ;)
      $_SESSION['admin_password_prompted'] = time_utc();

      return true;
    }
    else
    {
      $errors[] = l('Incorrect password supplied.');
      return false;
    }
  }
}

if(!function_exists('admin_access_denied'))
{
  /*
    Function: admin_access_denied

    Shows an error screen denying the member access to the page they
    requested.

    Parameters:
      string $title - The title of the page, defaults to Access denied
      string $message - The error message to display, defaults to "Sorry,
                        but you are not allowed to access the page you
                        have requested."

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_access_denied($title = null, $message = null)
  {
    // No title? Just use a generic one, then.
    if(empty($title))
    {
      $title = l('Access denied');
    }

    // No special message? We will take it that they just don't have the
    // right to access whatever it is you are wanting to block them from :P
    if(empty($message))
    {
      $message = l('Sorry, but you are not allowed to access the page you have requested.');
    }

    theme()->set_title($title);

		api()->context['error_title'] = $title;
		api()->context['error_message'] = $message;

    theme()->render('error');

    // Exit!
    exit;
  }
}
?>