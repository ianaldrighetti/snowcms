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

# Title: CAPTCHA plugin

# Register the CAPTCHA image action.
$api->add_event('action=captcha', 'captcha_display', dirname(__FILE__). '/captcha.php');

# Hooks into the registration form which shows the CAPTCHA image!
$api->add_hook('registration_form', 'add_captcha_field');

/*
  Function: add_captcha_field

  This hook adds a CAPTCHA field to the registration form.

  Parameters:
    none

  Returns:
    void - Nothing is returned by this function.
*/
function add_captcha_field()
{
  global $api, $func;

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
?>