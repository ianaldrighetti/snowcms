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
# A Simple file, it creates the Meta Description and Keywords from a given
# piece of content, the reason this is not in CoreCMS.php is because this
# is something you can turn off and on, plus its not really something
# SnowCMS requires to have in order to run, because it doesn't! :D
#
# string metadata_description(string $content);
#   string $content - Supply this function with a string of content, and
#                     it will return a description for that content
#   returns string - A string with the description will be returned...
#
#   NOTE: This function will strip tags, do NOT give it BBCode because its
#         not setup to see if it is BBCode, and then convert it to HTML then
#         strip, so if the content is BBCode, use bbc(); first ;)
#
# mixed metadata_keywords(string $content[, bool $return_array = false[, string $cache_key = null]]);
#   string $content - Just like createDescription(), supply this with content
#                     and it will return a string or an array depending upon
#                     what you set $return_array to.
#   bool $return_array - You can choose whether or not you want this function
#                        to return the keywords as an array, otherwise the keywords
#                        will be returned as a string separated by commas, which
#                        is the default.
#   string $cache_key - If you want these to be cached, to save resources, enter a unique key :)
#   returns mixed - Depending upon whether $return_array is true or false, it can return a string
#                   (If set to false) or an array (If set to true).
#
#   NOTE: As with metadata_description(), this function expects the content to be HTML
#         and not BBCode!
#

function metadata_description($content)
{
  global $settings;

  # This is quite simple :)
  # We need to do a couple things...
  # before we strip_tags, like simply turn <br />'s into
  # a space ;)
  $content = str_ireplace(array('<br />', '<br>'), ' ', $content);

  # Now strip all tags.
  $content = strip_tags($content);

  # Do we need to shorten the content?
  if(mb_strlen($content) > 255)
    $content = mb_substr($content, 0, 255);

  # Now return it :)
  return $content;
}

function metadata_keywords($content, $return_array = false, $cache_key = null)
{
  global $settings;

  # Caching perhaps..?
  if(!empty($cache_key) && ($cache = cache_get($cache_key)) != null)
    # Its cached!
    return $cache;

  # Ok... So we need to do some stuff...
  # Gotta replace a couple things with space, just incase :)
  $content = str_ireplace(array('<br />', '<br>', '.', ',', '?', '!', ';'), ' ', $content);

  # Now remove tags
  $content = strip_tags($content);

  # Explosion! Yay!
  $keywords = explode(' ', $content);

  # We need a unique array...
  $unique = array_unique($keywords);
  $unique = array_values($unique);

  # We also need to count how many times the word has appeared.
  $count = array();
  foreach($unique as $word)
  {
    $word = trim($word);
    if(!empty($word))
    {
      if(!isset($count[$word]))
        $count[$word] = 1;
      else
        $count[$word]++;
    }
  }

  # We only need to do this if keyword_appears is
  # bigger then 0 :P
  if($settings['keyword_appears'] > 0)
  {
    $unique = array();
    # Gotta loop through them :P
    foreach($count as $keyword => $appears)
      if($appears >= $settings['keyword_appears'])
        $unique[] = $keyword;
  }

  # Lol... Lots of arrays and lots of loops huh?
  # We got just 1 more...
  $return = array();
  $num_unique = count($unique);
  for($i = 0; $i < ($settings['keyword_appears'] <= $num_unique ? $settings['keyword_appears'] : $num_unique); $i++)
    $return[] = $unique[$i];

  # Now did they want an array or string?
  if(!$return_array)
    $return = implode(', ', $return);

  # Do we want to cache it?
  if(!empty($cache_key))
    cache_save($cache_key, $return, 120);

  return $return;
}
?>