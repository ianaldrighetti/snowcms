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
# Loads up the theme, the theme header and footer... It also calls
# on the appropriate sub function in the specific template file you
# wanted
#
# void theme_load(string $theme_file[, string $function = 'show']);
#   string $theme_file - The file name of the theme template you want
#                        to be loaded. So if the template name is admin.template.php
#                        you would simply do admin
#   string $function - The function to call on from the loaded theme file
#                      if you enter nothing, it will default to show
#   - theme_load() handles not only loading the theme file you asked for
#     and calling on the function you want but it also loads the
#     actual layout of the site from main.template.php which is the overall
#     layout, which contains the header, the menu(s), and also the footer.
#   - theme_load() is setup to have template fall back. That means if the theme
#     you are using does not have Admin.template.php and you call on the template
#     Admin, it will load the Admin.template.php file from the default theme.
#     But of course, if you call on a template that doesn't exist in either
#     then well, that won't be to good :/
#
# void theme_data();
#   - Loads theme data from the theme's INI file, this will be used
#     for a lot of different things to help theme creators have all
#     the more control. This is performed long before loading the
#     actual theme.
#
# array theme_list();
#

function theme_load($theme_file, $function = 'show')
{
  global $l, $page, $settings, $started_time, $theme_dir, $user;
  
  # Update total page views
  update_settings(array('total_page_views' => $settings['total_page_views'] + 1));
  
  # Sanitize JavaScript variables for W3C validation
  if(isset($page['js_vars']) && count($page['js_vars']))
    foreach($page['js_vars'] as $key => $val)
    {
      # Integer or double? It doesn't have these! :P
      if(!is_int($val) && !is_double($val))
        $page['js_vars'][$key] = '"'. str_replace('&', '&amp;', $val). '"';
    }
  
  # Make theme file name case-insensitive, by converting to lowercase
  $theme_file = mb_strtolower($theme_file);
  
  # Lets see... We need to load the main.template.php file
  # which has the header_template(); and footer_template();
  # functions we need for the overall layout :)
  # Do they exist..?
  # NOTE: main.template.php does NOT have template fall back
  # because, well... You need main.template.php no matter what
  if(file_exists($theme_dir. '/'. $user['theme']. '/main.template.php'))
  {
    # It exists so we can get it.
    require_once($theme_dir. '/'. $user['theme']. '/main.template.php');

    # Does the theme file they want exist?
    if(file_exists($theme_dir. '/'. $user['theme']. '/'. $theme_file. '.template.php'))
      require_once($theme_dir. '/'. $user['theme']. '/'. $theme_file. '.template.php');
    # Backup? :S
    elseif(file_exists($theme_dir. '/default/'. $theme_file. '.template.php'))
      require_once($theme_dir. '/default/'. $theme_file. '.template.php');
    else
      # OH NOES! It just plain doesn't exist :S
      $file_exists = false;

    if(!isset($page['js_vars']))
      $page['js_vars'] = array();

    $page['js_vars']['online_timeout'] = $settings['online_timeout'];

    if(!isset($page['scripts']))
      $page['scripts'] = array();

    $page['scripts'][] = $settings['default_theme_url']. '/js/keepalive.js';

    # Okay... Before we load the theme we can now do the time it took to
    # create the page...
    if(isset($started_time) && !empty($started_time))
      $page['created_in'] = round(array_sum(explode(' ', microtime())) - array_sum(explode(' ', $started_time)), 3);

    # So call on the template_header();
    header_template();

    # If it or the function doesn't exist, say so now or forever hold your piece.
    if(isset($file_exists) && !$file_exists)
    {
      echo '
    <div class="theme_error">
      <p>', sprintf($l['theme_file_failed'], $theme_file), '</p>
    </div>';
    }
    elseif(!function_exists($function))
    {
      echo '
    <div class="theme_error">
      <p>', sprintf($l['theme_function_failed'], $function, $theme_file), '</p>
    </div>';
    }
    else
      # Just call it...
      $function();

    # Now the template footer :)
    footer_template();
  }
  else
  {
    # Oh noes! It doesn't exist :(
    die($l['no_theme_main_error']);
  }
}

function theme_data()
{
  global $user, $theme, $theme_dir;

  # Get the theme information... if any.
  if(file_exists($theme_dir. '/'. $user['theme']. '/theme_info.ini'))
    $theme = parse_ini_file($theme_dir. '/'. $user['theme']. '/theme_info.ini');
  # No INI? Just load an empty array then
  else
    $theme = array();
}

function theme_list()
{
  global $theme_dir;
  
  # Go through each theme directory
  $themes = array();
  foreach(scandir($theme_dir) as $dir)
  {
    # Check if the theme has a theme_info.ini file
    if(is_readable($theme_dir. '/'. $dir. '/theme_info.ini'))
    {
      # Add the INI file's information into an array
      $themes[$dir] = parse_ini_file($theme_dir. '/'. $dir. '/theme_info.ini');
    }
  }
  
  # Return the themes
  return $themes;
}
?>