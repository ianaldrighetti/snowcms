<?php
#########################################################################
#                             SnowCMS v1.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#               Released under the GNU Lesser GPL v3 License            #
#                    www.gnu.org/licenses/lgpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#  SnowCMS v1.0 began in November 2008 by Myles, aldo and antimatter15  #
#                       aka the SnowCMS Dev Team                        #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 1.0                         #
#########################################################################

# No Direct access please ^^
if(!defined('InSnow'))
  die;

#
# Language.php handles the language of course
#
# void language_load(string $lng_file);
#   string $lng_file - The language file in which to load from
#                      the default themes and the current themes
#                      language directory.
#   - You access the language variables through the variable $l
#

function language_load($lng_file)
{
  global $l, $settings, $theme_dir, $user;

  # Lower it :P
  $lng_file = mb_strtolower($lng_file);

  # Check if the language file exists in the
  # default themes language directory...
  if(file_exists($theme_dir. '/default/language/'. mb_strtolower($settings['default_language']). '/'. $lng_file. '.language.php'))
    # Ok :P Require it :)
    require_once($theme_dir. '/default/language/'. mb_strtolower($settings['default_language']). '/'. $lng_file. '.language.php');

  # Maybe a custom one from a different (non-default) theme?
  if(file_exists($theme_dir. '/'. $settings['theme']. '/language/'. mb_strtolower($settings['default_language']). '/'. $lng_file. '.language.php') && $settings['theme'] != 'default')
    # Ok, get it too!
    require_once($theme_dir. '/'. $settings['theme']. '/language/'. mb_strtolower($settings['default_language']). '/'. $lng_file. '.language.php');

  # Now we may need to get a specific language file... Only if the current
  # users is not the same as the sites default, since it is already loaded
  if(mb_strtolower($user['language']) != mb_strtolower($settings['default_language']))
  {
    # So yeah... Lets see...
    # default themes language directory...
    if(file_exists($theme_dir. '/default/language/'. mb_strtolower($user['language']). '/'. $lng_file. '.language.php'))
      # Ok :P Require it :)
      require_once($theme_dir. '/default/language/'. mb_strtolower($user['language']). '/'. $lng_file. '.language.php');

    # Maybe a custom one from a different (non-default) theme?
    if(file_exists($theme_dir. '/'. $settings['theme']. '/language/'. mb_strtolower($user['language']). '/'. $lng_file. '.language.php') && $settings['theme'] != 'default')
      # Ok, get it too!
      require_once($theme_dir. '/'. $settings['theme']. '/language/'. mb_strtolower($user['language']). '/'. $lng_file. '.language.php');
  }
}
?>