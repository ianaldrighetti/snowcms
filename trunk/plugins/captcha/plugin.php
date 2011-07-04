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

// Title: CAPTCHA plugin

# Register the CAPTCHA image action.
$api->add_event('action=captcha', 'captcha_display', dirname(__FILE__). '/captcha.php');

# Hooks into the registration form which shows the CAPTCHA image!
$api->add_hook('registration_form', 'captcha_add_field');

# Adds settings to the miscellaneous plugin settings page.
$api->add_hook('admin_plugins_settings_form', 'captcha_add_settings');

/*
  Function: add_captcha_field

  This hook adds a CAPTCHA field to the registration form.

  Parameters:
    none

  Returns:
    void - Nothing is returned by this function.
*/
function captcha_add_field()
{
  global $api, $func, $settings;

  # Is CAPTCHA not enabled?
  if(!$settings->get('captcha_enable', 'bool', 1))
  {
    # Nope, it is not, so don't add the CAPTCHA image.
    return;
  }

  $form = $api->load_class('Form');

  # Add our field which displays the CAPTCHA image.
  $form->add_field('registration_form', 'captcha_text', array(
                                                          'type' => 'custom-function',
                                                          'label' => l('Image verification:'),
                                                          'subtext' => l('In order to prevent spam, please enter the text you see inside the image. There are no zeros, and it is case-insensitive.'),
                                                          'function' => create_function('$value, $form_name, &$error', '
                                                            global $func;

                                                            # Did you enter it right?
                                                            if(isset($_SESSION[\'captcha_text\'][\'registration_form\']) && strtolower($_SESSION[\'captcha_text\'][\'registration_form\']) == $func[\'strtolower\']($value))
                                                              return true;
                                                            else
                                                            {
                                                              $error = l(\'Image verification failed.\');
                                                              return false;
                                                            }'),
                                                          'value' => create_function('', '
                                                            global $base_url;

                                                            return \'<p><img src="\'. $base_url. \'/index.php?action=captcha&amp;id=registration_form" alt="" title="\'. l(\'Image verification\'). \'" /></p><p><input type="text" name="captcha_text" value="" /></p>\';'),
                                                          'save'=> false,
                                                        ));
}

/*
  Function: captcha_add_settings

  Adds CAPTCHA settings to the plugins settings form.

  Parameters:
    none

  Returns:
    void - Nothing is returned by this function.
*/
function captcha_add_settings()
{
  global $api, $settings;

  # Load the Form class, so we can add some fields.
  $form = $api->load_class('Form');

  $form->add_field('admin_plugins_settings_form', 'captcha_enable', array(
                                                                      'type' => 'checkbox',
                                                                      'label' => l('Enable CAPTCHA:'),
                                                                      'subtext' => l('Whether or not to enable CAPTCHA on such pages as registration.'),
                                                                      'value' => $settings->get('captcha_enable', 'int', 1),
                                                                    ));

  $form->add_field('admin_plugins_settings_form', 'captcha_width', array(
                                                                     'type' => 'int',
                                                                     'label' => l('CAPTCHA width:'),
                                                                     'subtext' => l('The width of the CAPTCHA image (in pixels).'),
                                                                     'length' => array(
                                                                                   'min' => 100,
                                                                                 ),
                                                                     'value' => $settings->get('captcha_width', 'int', 200),
                                                                   ));

  $form->add_field('admin_plugins_settings_form', 'captcha_height', array(
                                                                      'type' => 'int',
                                                                      'label' => l('CAPTCHA height:'),
                                                                      'subtext' => l('The height of the CAPTCHA image (in pixels).'),
                                                                      'length' => array(
                                                                                    'min' => 50,
                                                                                  ),
                                                                      'value' => $settings->get('captcha_height', 'int', 50),
                                                                    ));

  $form->add_field('admin_plugins_settings_form', 'captcha_num_chars', array(
                                                                         'type' => 'int',
                                                                         'label' => l('Characters in CAPTCHA:'),
                                                                         'subtext' => l('How many characters should be in the image? Be sure not to have too many, they might not all fit!'),
                                                                         'length' => array(
                                                                                       'min' => 1,
                                                                                     ),
                                                                         'value' => $settings->get('captcha_num_chars', 'int', 6),
                                                                       ));
}
?>