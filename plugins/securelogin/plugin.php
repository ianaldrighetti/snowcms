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

if(!defined('INSNOW'))
{
  die('Nice try...');
}

// Title: Secure login plugin

// Only generate the login hash if you are not logged in ;)
api()->add_hook('post_init_member', create_function('', '
                                     // As stated, only if the current user isn\'t logged in.
                                     if(member()->is_guest())
                                     {
                                       // Adding a hook inside a hook? Just weird, weird I say!
                                       api()->add_hook(\'post_init_theme\', \'secure_login_guest_login_prep\');
                                     }'));

/*
  Function: member_guest_login_prep

  If the current person browsing the site is a guest, then a random hash
  needs to be generated which is used for salting their password before
  they login, that is, if they do login.

  Parameters:
    none

  Returns:
    void - Nothing is returned by this function.
*/
function secure_login_guest_login_prep()
{
  // The Members class has a random string generator :)
  $members = api()->load_class('Members');

  // Do we need to store the last random string?
  if(!empty($_SESSION['guest_rand_str']))
  {
    $_SESSION['last_guest_rand_str'] = $_SESSION['guest_rand_str'];
  }

  $_SESSION['guest_rand_str'] = $members->rand_str(mt_rand(20, 40));

  theme()->add_js_var('login_salt', $_SESSION['guest_rand_str']);

  // Add the JavaScript file, we need it ;)
  theme()->add_js_file(array('src' => pluginurl. '/securelogin/secure_form.js'));
}

# Add the hook which checks the validity of the secured password :)
api()->add_hook('login_process_check_custom', create_function('&$login_success, $login, $row, &$errors', '
                                               global $func;

                                               if(!empty($_POST[\'secured_password\']) && !empty($_SESSION[\'last_guest_rand_str\']) && $func[\'strlen\']($_POST[\'secured_password\']) == 40 && $_POST[\'secured_password\'] == sha1($row[\'member_pass\']. $_SESSION[\'last_guest_rand_str\']))
                                               {
                                                 $login_success = true;
                                               }'));


?>