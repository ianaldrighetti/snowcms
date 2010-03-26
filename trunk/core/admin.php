<?php
#########################################################################
#                             SnowCMS v2.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#                  Released under the GNU GPL v3 License                #
#                     www.gnu.org/licenses/gpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#                SnowCMS v2.0 began in November 2009                    #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 2.0                         #
#########################################################################

if(!defined('IN_SNOW'))
  die;

# Title: Admin switch

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
    global $api, $core_dir, $member, $settings, $theme;

    # First things first, are you even allowed to view the Admin CP?
    # Plugins can add more groups through the admin_allowed_groups filter.
    $allowed_groups = $api->apply_filters('admin_allowed_groups', array('administrator'));

    if(count($allowed_groups) > 0)
      foreach($allowed_groups as $group)
        if($member->is_a($group))
        {
          $allowed = true;
          break;
        }

    # You could be making an ajax request (Oh yeah, did I mention any ajax
    # requests dealing with control panel stuff should be prepended by
    # action=admin&sa=ajax{rest of your stuff}? It should be!!!)
    if(substr($_SERVER['QUERY_STRING'], 0, 20) == 'action=admin&sa=ajax')
    {
      # So it's an ajax request!
      # Is an administrative prompt required?
      if(admin_prompt_required())
      {
        # You sending us a password? Cool!
        if(isset($_POST['admin_password']))
        {
          # Did it work?
          if(!admin_prompt_password($_POST['admin_password']))
          {
            # Nope :(
            echo json_encode(array('error' => l('Incorrect password'), 'admin_prompt_required' => true));
            exit;
          }
        }
        else
        {
          # Yes, it is.
          echo json_encode(array('error' => l('Your session has timed out'), 'admin_prompt_required' => true));
          exit;
        }
      }

      # Are you not allowed? Sorry!
      if(empty($allowed))
      {
        echo json_encode(array('error' => l('Access denied')));
        exit;
      }
    }
    else
    {
      if(empty($allowed))
      {
        $theme->set_title(l('Access denied'));
        $theme->add_meta(array('name' => 'robots', 'content' => 'noindex'));

        $theme->header();

        echo '
        <h1>', l('Access denied'), '</h1>
        <p>', l('Sorry, but you are not allowed to access the page you have requested.'), '</p>';

        $theme->footer();
        exit;
      }

      # We may require you to enter a password, for security reasons!
      admin_prompt_password();

      # You seem to be authenticated, and now we can switch over to the admin theme :)
      require_once($core_dir. '/admin/admin_theme.class.php');

      $theme = $api->load_class($api->apply_filters('admin_theme_class', 'Admin_Theme'), l('Control Panel'). ' - '. $settings->get('site_name', 'string'));
    }

    # You can make changes to the theme and what not now :)
    $api->run_hooks('admin_prepend_authenticated', array('ajax' => substr($_SERVER['QUERY_STRING'], 0, 20) == 'action=admin&sa=ajax'));
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
    global $api, $member, $settings;

    # Check to see if your last check has now timed out, quite simple really! But
    # if you for some strange reason have it disabled, nevermind!
    if(!$settings->get('disable_admin_security', 'bool', false) && (empty($_SESSION['admin_password_prompted']) || ((int)$_SESSION['admin_password_prompted'] + ($settings->get('admin_login_timeout', 'int', 15) * 60)) < time_utc()))
      return true;

    # Your good, for now!
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
  */
  function admin_prompt_password($password = null)
  {
    global $api, $member, $settings, $theme;

    # Is it time for you to re-enter your password?
    if(admin_prompt_required())
    {
      # Did you supply a password?
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

      # Generate the login form.
      admin_prompt_generate_form();

      $form = $api->load_class('Form');

      # Has the form been submitted? Process it!
      if(isset($_POST['admin_prompt_form']))
      {
        $success = $form->process('admin_prompt_form');

        # Did they pass?
        if(!empty($success))
          # Yup, no need to continue!
          return;
      }

      $theme->set_title(l('Login'));

      $theme->header();

      echo '
      <h1>', l('Login'), '</h1>
      <p>', l('For security purposes, please enter your account password below. This is done to help make sure that you are who you say you are.'), '</p>';

      $form->show('admin_prompt_form');

      $theme->footer();

      # Don't execute anything else.
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
    global $api;

    # Create the form so you can enter your password.
    $form = $api->load_class('Form');

    $form->add('admin_prompt_form', array(
                                      'action' => '',
                                      'callback' => 'admin_prompt_handle',
                                      'submit' => l('Login'),
                                    ));

    $form->add_field('admin_prompt_form', 'admin_verification_password', array(
                                                                           'type' => 'password',
                                                                           'label' => l('Password:'),
                                                                         ));

    # Let's add all the post data you were entering before ;)
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
      bool - Returns true if the supplied password was correct, false if not.

    Note:
      This function is overloadable.
  */
  function admin_prompt_handle($data, &$errors = array())
  {
    global $api, $func, $member;

    # No password? Pfft.
    if(empty($data['admin_verification_password']) || $func['strlen']($data['admin_verification_password']) == 0)
    {
      $errors[] = l('Please enter your password.');
      return false;
    }

    # The Members class has a useful method called authenticate :)
    $members = $api->load_class('Members');

    # Pretty simple to do. There are a couple hooks in that method, fyi.
    if($members->authenticate($member->name(), $data['admin_verification_password']))
    {
      # Set the last time you verified in your session information ;)
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
?>