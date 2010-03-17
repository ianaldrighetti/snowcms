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
  Title: Variable functions

  Function: init_func

  init_func initializes the the $func array which contains variable
  functions for handling strings to allow better language support.

  Parameters:
    none

  Returns:
    void - Nothing is returned by this function.
*/
function init_func()
{
  global $api, $func, $settings;

  $func = array(
    # Add a couple aliases.
    'htmlchars' => 'htmlchars',
    'htmlspecialchars' => 'htmlchars',
    'htmlchars_decode' => 'htmlchars_decode',
    'htmlspecialchars_decode' => 'htmlchars_decode',
  );

  # Enable multibyte strings (which we set to use UTF-8).
  if($settings->get('enable_utf8', 'bool') && function_exists('mb_internal_encoding'))
  {
    # Set the internal encoding to UTF-8.
    mb_internal_encoding('UTF-8');
    mb_http_output(mb_internal_encoding());

    # Handle the output buffer correctly.
    $api->add_filter('output_callback', create_function('$value', '
                                          return \'mb_output_handler\';'), 1);

    # Setup the variable functions for use!
    $func += array(
      'parse_str' => 'mb_parse_str',
      'mail' => 'mb_send_mail',
      'stripos' => create_function('$haystack, $needle, $offset = 0', '
                     # This function doesn\'t exist until PHP 5.2.0 >=
                     if(function_exists(\'mb_stripos\'))
                       return mb_stripos($haystack, $needle, $offset);
                     else
                       # Simple to emulate, really.
                       return mb_strpos(mb_strtolower($haystack), mb_strtolower($needle), $offset);'),
      'stristr' => create_function('$haystack, $needle, $part = false', '
                     # Same as mb_stripos, this doesn\'t exist until 5.2.0 as well.
                     if(function_exists(\'mb_stristr\'))
                       return mb_stristr($haystack, $needle, $part);
                     else
                       # Pretty easy to emulate too.
                       return mb_strstr(mb_strtolower($haystack), mb_strtolower($needle), $part);'),
      'strlen' => 'mb_strlen',
      'strpos' => 'mb_strpos',
      'strrchr' => 'mb_strrchr',
      'strrichr' => create_function('$haystack, $needle, $part = false', '
                      if(function_exists(\'mb_strrichr\'))
                        return mb_strrichr($haystack, $needle, $part);
                      else
                        return mb_strrchr(mb_strtolower($haystack), mb_strtolower($needle), $part);'),
      'strripos' => create_function('$haystack, $needle, $offset = 0', '
                      if(function_exists(\'mb_strripos\'))
                        return mb_strripos($haystack, $needle, $offset);
                      else
                        return mb_strrpos(mb_strtolower($haystack), mb_strtolower($needle), $offset);'),
      'strrpos' => 'mb_strrpos',
      'strstr' => 'mb_strstr',
      'strtolower' => 'mb_strtolower',
      'strtoupper' => 'mb_strtoupper',
      'ucwords' => create_function('$str', '
                     # It may not have its own dedicated function, but this is good enough :P
                     return mb_convert_case($str, MB_CASE_TITLE);'),
      'substr_count' => 'mb_substr_count',
      'substr' => 'mb_substr',
    );
  }
  else
  {
    # Define all the same variable functions, just without mb_ in front, really.
    $func += array(
      'parse_str' => 'parse_str',
      'mail' => 'mail',
      'stripos' => 'stripos',
      'stristr' => 'stristr',
      'strlen' => 'strlen',
      'strpos' => 'strpos',
      'strrchr' => 'strrchr',
      'strrichr' => create_function('$haystack, $needle, $part = false', '
                      return strrchar(strtolower($haystack), strtolower($needle));'),
      'strripos' => 'strripos',
      'strrpos' => 'strrpos',
      'strstr' => 'strstr',
      'strtolower' => 'strtolower',
      'strtoupper' => 'strtoupper',
      'ucwords' => 'ucwords',
      'substr_count' => 'substr_count',
      'substr' => 'substr',
    );
  }

  $api->run_hook('post_init_func', array(&$func));
}

/*
  Function: htmlchars

  Encodes the supplied string with htmlspecialchars with ENT_QUOTES and UTF-8
  as parameters. This function is here to simplify coding so you don't have to
  repeatedly to ENT_QUOTES, 'UTF-8' over and over again! ;)

  Parameters:
    string $str - The string to encode.

  Returns:
    string - Returns the encoded string.
*/
function htmlchars($str)
{
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/*
  Function: htmlchars_decode

  Decodes the supplied string with htmlspecialchars_decode with ENT_QUOTES
  as parameters.

  Parameters:
    string $str - The string to decode.

  Returns:
    string - Returns the decoded string.
*/
function htmlchars_decode($str)
{
  return htmlspecialchars_decode($str, ENT_QUOTES);
}
?>