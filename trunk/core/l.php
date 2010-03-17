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

/*
  Title: Language

  Function: l
  This function is simply passed the text string you want translated (in English!), and
  the translated string is returned. If no translation is available, the original string
  is returned. (A plugin is required for translations, it is available at the SnowCMS plugin site)

  Parameters:
    string $str - The string to translate, this string must be English (en_US)
    mixed ... - You can pass on parameters which replace content inside the string ($str)
                in printf format. For more information, see www.php.net/sprintf

  Returns:
    string - Returns a translated string, but of course, if the current language is en_US
             the original string is simply translated, with all the formatting replaced, though.

  Note:
    You can use this function just as you would with sprintf, pass all the parameters you
    want to be replaced in the string. Check out http://www.php.net/sprintf for more information.
*/
function l($str)
{
  global $api;

  # Any extra parameters?
  if(func_num_args() > 1)
    $args = func_get_args();

  # CAN HAZ TRANSLATION?
  $api->run_hooks('translate', array(&$str, &$args));

  # Hmm, any warrant for calling sprintf?
  if(func_num_args() > 1)
    $str = call_user_func_array('sprintf', $args);

  return $str;
}
?>
