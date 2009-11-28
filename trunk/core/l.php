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

#
# This function is simply passed the text string you want translated (in English!), and
# the translated string is returned. If no translation is available, the original string
# is returned. (A plugin is required for translations, it is available at the SnowCMS plugin site)
#
# NOTE: You can use this function just as you would with sprintf, pass all the parameters you
#       want to be replaced in the string. Check out www.php.net/sprintf for more information.
#
function l($str)
{
  global $api;

  # CAN HAZ TRANSLATION?
  $api->run_hook('translate', array(&$str));

  # Hmm, any warrant for calling sprintf?
  if(func_num_args() > 1)
  {
    $args = func_get_args();

    $str = call_user_func_array('sprintf', $args);
  }

  return $str;
}
?>