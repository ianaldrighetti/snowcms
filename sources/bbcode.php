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
# bbcode.php is where we process BBCode.
#
# string bbc(string $str[, string $cache_key = null[, int $ttl = 120]]);
#   - Converts the BBCode in the string into HTML and returns it.
#   $cache_key - Unique key used for caching, if not given data will
#                not be cached.
#   $ttl - Amount of time to keep the cached data in seconds, defaults
#          to 120 seconds.
#

function bbc($message, $parse_emoticons = true, $cache_key = null, $disabled = array())
{
  global $base_url, $emoticon_url, $db, $settings, $l;

  # Anything in the cache?
  if(!empty($cache_key) && ($parsed = cache_get('bbc-'. $cache_key)) != null)
  {
    # Yeah :)
    return $parsed;
  }

  # Be sure we are dealing with a bool here :P
  $parse_emoticons = !empty($parse_emoticons);

  # The disabled var MUST be an array.
  if(!is_array($disabled))
    $disabled = array();
  
  # Parse emoticons :) Only if set to though
  if($parse_emoticons)
  {
    # We need the emoticons from the database, but let's try the cache first
    if(!empty($settings['cache_enabled']) && ($cache = cache_get('emoticons')) != null)
    {
      # And we're cached
      $emoticons = $cache;
    }
    else
    {
      # No cache :(
      # Call on the database
      $result = $db->query("
        SELECT
          filename, sequences, name
        FROM {$db->prefix}emoticons
        WHERE pack = %pack",
        array(
          'pack' => array('string', $settings['emoticon_pack']),
        ));
      
      # Convert them to an array
      $emoticons = array();
      while(@list($filename, $sequences, $name) = $db->fetch_row($result))
      {
        # Each emoticon can have multiple sequences
        foreach(explode(' ', $sequences) as $sequence)
        {
          # First escape the sequence so it can be used in regex
          $escape = array(
            '\\' => '\\\\',
            '[' => '\\[',
            '^' => '\\^',
            '$' => '\\$',
            '.' => '\\.',
            '|' => '\\|',
            '?' => '\\?',
            '*' => '\\*',
            '+' => '\\+',
            '(' => '\\(',
            ')' => '\\)',
            '{' => '\\{',
            '}' => '\\}',
          );
          $sequence = str_replace(array_keys($escape), array_values($escape), $sequence);
          $emoticons[$sequence] = array(
            'filename' => $filename,
            'name' => $name,
          );
        }
      }
      
      # And let's see if we can cache them for next time
      if($settings['cache_enabled'])
        cache_save('emoticons', $emoticons);
    }
    
    # Now we've got our emoticons, let's parse! :)
    foreach($emoticons as $sequence => $emoticon)
    {
      $message = mb_substr(preg_replace('/(\s)'. $sequence. '(\s)/is', '$1<img src="'. $emoticon_url. '/'. $settings['emoticon_pack']. '/'. (!empty($emoticon['filename']) ? $emoticon['filename'] : ''). '" alt="'. (!empty($emoticon['name']) ? $emoticon['name'] : ''). '" />$2', ' '. $message. ' '), 1, -1);
      # And again, in case two emoticons used the same whitespace
      $message = mb_substr(preg_replace('/(\s)'. $sequence. '(\s)/is', '$1<img src="'. $emoticon_url. '/'. $settings['emoticon_pack']. '/'. (!empty($emoticon['filename']) ? $emoticon['filename'] : ''). '" alt="'. (!empty($emoticon['name']) ? $emoticon['name'] : ''). '" />$2', ' '. $message. ' '), 1, -1);
    }
  }
  
  # Now lets define all the BBCodes shall we?
  $bbcodes = array(
    # Your standard tags ;)
    array(
      'tag' => 'b',
      'replace' => '<strong>$1</strong>',
      'disabled' => !empty($disabled['b']),
    ),
    array(
      'tag' => 'i',
      'replace' => '<em>$1</em>',
      'disabled' => !empty($disabled['i']),
    ),
    array(
      'tag' => 'u',
      'replace' => '<u>$1</u>',
      'disabled' => !empty($disabled['u']),
    ),
    array(
      'tag' => 's',
      'replace' => '<del>$1</del>',
      'disabled' => !empty($disabled['s']),
    ),
    array(
      'tag' => 'tt',
      'replace' => '<span style="font-family: monospace;">$1</span>',
      'disabled' => !empty($disabled['tt']) || !empty($disabled['icode']),
    ),
    array(
      'tag' => 'icode',
      'replace' => '<span style="font-family: monospace;">$1</span>',
      'disabled' => !empty($disabled['tt']) || !empty($disabled['icode']),
    ),
    array(
      'tag' => 'pre',
      'replace' => '<pre>$1</pre>',
      'disabled' => !empty($disabled['pre']),
    ),
    array(
      'tag' => 'sup',
      'replace' => '<sup>$1</sup>',
      'disabled' => !empty($disabled['sup']),
    ),
    array(
      'tag' => 'sub',
      'replace' => '<sub>$1</sub>',
      'disabled' => !empty($disabled['sub']),
    ),
    # Alignments ;)
    array(
      'tag' => 'left',
      'replace' => '<div style="text-align: left;">$1</div>',
      'disabled' => !empty($disabled['left']) || !empty($disabled['align']),
    ),
    array(
      'tag' => 'center',
      'replace' => '<div style="text-align: center;">$1</div>',
      'disabled' => !empty($disabled['center']) || !empty($disabled['align']),
    ),
    array(
      'tag' => 'right',
      'replace' => '<div style="text-align: right;">$1</div>',
      'disabled' => !empty($disabled['right']) || !empty($disabled['align']),
    ),
    # Color :) Either hex or name XD
    array(
      'regex' => '~\[color=#([a-f0-9]{6})\](.*?)\[\/color\]~is',
      'replace' => '<span style="color: $1;">$2</span>',
      'disabled' => !empty($disabled['color']),
    ),
    array(
      'regex' => '~\[color=#([a-f0-9]{3})\](.*?)\[\/color\]~is',
      'replace' => '<span style="color: $1;">$2</span>',
      'disabled' => !empty($disabled['color']),
    ),
    array(
      'regex' => '~\[color=(blue|green|lime-green|maroon|purple|red|teal|yellow|black|gr(?:e|a)y|white)\](.*?)\[\/color\]~is',
      'replace' => '<span style="color: $1;">$2</span>',
      'disabled' => !empty($disabled['color']),
    ),
    # Font :) Either [font] or [face]
    array(
      'regex' => '~\[(font|face)=([a-z- ,]*?)\](.*?)\[\/\1\]~is',
      'replace' => '<span style="font-family: $2;">$3</span>',
      'disabled' => !empty($disabled['font']) || !empty($disabled['face']),
    ),
    # Size! Yay...
    # By number ;) nothing, pt or px
    array(
      'regex' => '~\[size=([0-9]{1,2}(?:pt|px|))\](.*?)\[\/size\]~is',
      'replace' => '<span style="font-size: $1;">$2</span>',
      'disabled' => !empty($disabled['size']),
    ),
    # size by name :D
    array(
      'regex' => '~\[size=(x{1,2}-small|small|medium|large|x{1,2}-large)\](.*?)\[\/size\]~is',
      'replace' => '<span style="font-size: $1;">$2</span>',
      'disabled' => !empty($disabled['size']),
    ),
    # ME? How nice of you to think of me! :)
    array(
      'regex' => '~\[me=(.*?)\](.*?)\[\/me\]~is',
      'replace' => '<div class="me_bbc">$1 $2</div>',
      'disabled' => !empty($disabled['me']),
    ),
    # Quoting with from.
    array(
      'regex' => '~\[quote\]\r?\n?(.*?)\r?\n?\[\/quote\]\r?\n?~is',
      'replace' => '<div class="quote_header">'. $l['quote']. '</div><blockquote>$1</blockquote>',
      'disabled' => !empty($disabled['quote']),
    ),
    # Quoting with from.
    array(
      'regex' => '~\[quote=(.*?)\]\r?\n?(.*?)\r?\n?\[\/quote\]\r?\n?~is',
      'replace' => '<div class="quote_header">'. sprintf($l['quote_from'], '$1'). '</div><blockquote>$2</blockquote>',
      'disabled' => !empty($disabled['quote']),
    ),
    # Quoting with from and message id link...
    array(
      'regex' => '~\[quote\s*from=(.*?)\s*msg=(\d*)\]\r?\n?(.*?)\r?\n?\[\/quote\]\r?\n?~is',
      'replace' => '<div class="quote_header">'. sprintf($l['quote_from'], '<a href="'. $base_url. '/forum.php?msg=$2">$1</a>'). '</div><blockquote>$3</blockquote>',
      'disabled' => !empty($disabled['quote']),
    ),
    # Emails.
    array(
      'tag' => 'email',
      'replace' => '<a href="mailto:$1" target="_blank">$1</a>',
      'disabled' => !empty($disabled['email']),
    ),
    array(
      'regex' => '~\[email=(.*?)\](.*?)\[\/email\]~is',
      'replace' => '<a href="mailto:$1" target="_blank">$2</a>',
      'disabled' => !empty($disabled['email']),
    ),
    # Links.
    array(
      'regex' => '~\[url\]((?:ht|f)tps?:\/\/[^"\']*?)\[\/url\]~is',
      'replace' => '<a href="$1" target="_blank">$1</a>',
      'disabled' => !empty($disabled['url']),
    ),
    array(
      'regex' => '~\[url=((?:ht|f)tps?:\/\/[^"\']*?)\](.*?)\[\/url\]~is',
      'replace' => '<a href="$1" target="_blank">$2</a>',
      'disabled' => !empty($disabled['url']),
    ),
    # Links without Protocols O.o HTTP :D
    array(
      'regex' => '~\[url\](www\.[^\s"\']+?)\[\/url\]~is',
      'replace' => '<a href="http://$1" target="_blank">$1</a>',
      'disabled' => !empty($disabled['url']),
    ),
    array(
      'regex' => '~\[url=(www\.[^\s"\']+?)\](.*?)\[\/url\]~is',
      'replace' => '<a href="http://$1" target="_blank">$2</a>',
      'disabled' => !empty($disabled['url']),
    ),
    # Images...
    array(
      'regex' => '~\[img\]((?:ht|f)tps?:\/\/[^"\']*?)\[\/img\]~is',
      'replace' => '<img src="$1" alt="" title="" />',
      'disabled' => !empty($disabled['img']),
    ),
    array(
      'regex' => '~\[img=((?:ht|f)tps?:\/\/[^"\']*?)\](.*?)\[\/img\]~is',
      'replace' => '<img src="$1" alt="" title="$2" />',
      'disabled' => !empty($disabled['img']),
    ),
    # No protocol? We assume HTTP :)
    array(
      'regex' => '~\[img\](www\.[^\s"\']+?)\[\/img\]~is',
      'replace' => '<img src="http://$1" alt="" title="" />',
      'disabled' => !empty($disabled['img']),
    ),
    array(
      'regex' => '~\[img=(www\.[^\s"\']+?)\](.*?)\[\/img\]~is',
      'replace' => '<img src="http://$1" alt="" title="$2" />',
      'disabled' => !empty($disabled['img']),
    ),
    # Line break and HR tags... can't forget those!
    array(
      'regex' => '~\[br\]~i',
      'replace' => '<br />',
      'disabled' => !empty($disabled['br']),
    ),
    array(
      'regex' => '~\[hr\]~i',
      'replace' => '<hr />',
      'disabled' => !empty($disabled['hr']),
    ),
  );

  # A couple hard coded tags, like quoting WITH timestamp...
  # Why? I could do ~ise or whatever, but thats EVIL! EVIL I TELL YOU! >:|
  if(empty($disabled['quote']) && empty($disabled['quote_timestamp']))
  {
    $message = preg_replace_callback('~\[quote from=(.*?) time=([0-9]{10})\]\r?\n?(.*?)\r?\n?\[\/quote\]\r?\n?~is',
      create_function('$matches', '
        global $base_url;
        return \'<div class="quote_header">Quote from \'. $matches[1]. \' at \'. timeformat($matches[2]). \':</div><blockquote>\'. $matches[3]. \'</blockquote>\';'),
      $message);
  }

  if(empty($disabled['quote']) && empty($disabled['quote_timestamp']))
  {
    $message = preg_replace_callback('~\[quote from=(.*?) msg=(\d*) time=([0-9]{10})\]\r?\n?(.*?)\r?\n?\[\/quote\]\r?\n?~is',
      create_function('$matches', '
        global $base_url;
        return \'<div class="quote_header">Quote from <a href="\'. $base_url. \'/forum.php?msg=\'. $matches[2]. \'">\'. $matches[1]. \'</a> at \'. timeformat($matches[3]). \':</div><blockquote>\'. $matches[4]. \'</blockquote>\';'),
      $message);
  }

  # [php] tag? :)
  if(empty($disabled['php']))
  {
    # Find the PHP tags, this is going to be a nobbc thing ;)
    # So add [nobbc] around it XD
    $message = preg_replace_callback('~\[php\]\r?\n?(.*?)\r?\n?\[\/php\]\r?\n?~is',
      create_function('$matches', '
        $remove_php = false;
        if(mb_substr(trim($matches[1]), 0, 5) != \'&lt;?\')
        {
          $matches[1] = \'&lt;?php \'. $matches[1];
          $remove_php = true;
        }

        $matches[1] = highlight_string(htmlspecialchars_decode($matches[1], ENT_QUOTES), true);
        
        if($remove_php)
          $matches[1] = mb_substr($matches[1], 0, 65). mb_substr($matches[1], 79);
        
        $matches[1] = mb_substr($matches[1], 0, 35). mb_substr($matches[1], 36);
        $matches[1] = mb_substr($matches[1], 0, mb_strlen($matches[1]) - 8). mb_substr($matches[1], mb_strlen($matches[1]) - 7);
        
        return \'[nobbc]\'. $matches[1]. \'[/nobbc]\';'),
      $message);
  }

  # [code] tag ;)
  if(empty($disabled['code']))
  {
    # The code tag... It just puts <code> around is practically...
    # but if you do [code=php] or [code=FILENAME.php] (file extension
    # being php) then it will do some of the same things [php] does.
    $message = preg_replace_callback('~\[code(?:=(.*?))?\]\r?\n?(.*?)\r?\n?\[\/code\]\r?\n?~is',
      create_function('$matches', '
        $matches[1] = trim($matches[1]);
        $phpCheck = mb_strtolower($matches[1]);

        if($phpCheck == \'php\' || mb_substr($phpCheck, mb_strlen($phpCheck) - 4, mb_strlen($phpCheck)) == \'.php\')
        {
          $remove_php = false;
          if(mb_substr(trim($matches[2]), 0, 5) != \'&lt;?\')
          {
            $matches[2] = \'&lt;?php \'. $matches[2];
            $remove_php = true;
          }
          
          $matches[2] = highlight_string(htmlspecialchars_decode($matches[2], ENT_QUOTES), true);
          
          if($remove_php)
            $matches[2] = mb_substr($matches[2], 0, 65). mb_substr($matches[2], 79);
          
          $matches[2] = mb_substr($matches[2], 0, 35). mb_substr($matches[2], 36);
          $matches[2] = mb_substr($matches[2], 0, mb_strlen($matches[2]) - 8). mb_substr($matches[2], mb_strlen($matches[2]) - 7);
        }

        return \'[nobbc]<div class="code_header">'. $l['code']. '\'. (!empty($matches[1]) && $phpCheck != \'php\' ? \' (\'. $matches[1]. \')\' : \'\'). \':</div><code class="blockcode">\'. $matches[2]. \'</code>[/nobbc]\';'),
      $message);
  }

  # [nobbc] tag..?
  # So what do we do? We simply find [nobbc] and the stuff between
  # the tags, then we simply remove the inside stuffs, replace it
  # and save it to an array, then add it back later :) because you
  # can't parse it if it isn't there, can you? >:D!
  $nobbc = array();
  $cur_pos = -1;

  if(preg_match_all('~\[nobbc\](.*?)\[\/nobbc\]~is', $message, $matches))
  {
    # So we do need to undo this.
    $nobbc_done = true;

    # Now replace!
    foreach($matches[1] as $key => $match)
    {
      $cur_pos++;

      # Save the content...
      $nobbc[$cur_pos] = $match;

      # Now replace it with the position in the message
      $message = str_replace($matches[0][$key], '[nobbc]'. $cur_pos. '[/nobbc]', $message);
    }
  }

  # Now we can do the regular BBC
  foreach($bbcodes as $bbcode)
  {
    # Is it disabled?
    if($bbcode['disabled'])
      continue;

    # Need to make it? :|
    if(!empty($bbcode['tag']))
      $bbcode['regex'] = '~\['. $bbcode['tag']. '\](.*?)\[\/'. $bbcode['tag']. '\]~is';

    # Replace it :D
    $message = preg_replace($bbcode['regex'], $bbcode['replace'], $message);
  }

  # We must do auto linking after all BBCode is parsed ;)
  if(empty($disabled['url']) && empty($disabled['auto-link']))
  {
    # Replace! :D If we find anything :P
    # First for non-protocol www. links
    $message = preg_replace('~(?<!=\")([^/])(www.(?:[\w\+\-\@\=\?\.\%\/\:\&\;\~\|#]+)(?:\.)?)~i', '$1<a href="http://$2" target="_blank">$2</a>', $message);
    # And then for protocl links
    $message = preg_replace('~(?<!=\")((?:https?|ftps?)://(?:[\w\+\-\@\=\?\.\%\/\:\&\;\~\|#]+)(?:\.)?)~i', '<a href="$1" target="_blank">$1</a>', $message);
  }

  # Do we need to undo the nobbc?
  if(!empty($nobbc_done))
  {
    # Find the culprits :P
    if(preg_match_all('~\[nobbc\](.*?)\[\/nobbc\]~is', $message, $matches))
    {
      # Now replace, this time without [nobbc] still surrounding!
      foreach($matches[1] as $key => $match)
      {
        # Get the position in the nobbc array.
        $match = (int)$match;

        # It better be there ._.
        if(!empty($nobbc[$match]))
          $match = $nobbc[$match];

        # Now replace it in the message.
        $message = str_replace($matches[0][$key], $match, $message);
      }
    }
  }

  # Auto line breaks..? I hope so!
  if(empty($disabled['auto-breaks']))
    $message = strtr($message, array("\n" => '<br />'));

  # Hmm... Cache it..?
  if(!empty($cache_key))
  {
    # Cache it... =]
    cache_save('bbc-'. $cache_key, $message, 120);
  }

  return $message;
}

function smileys($message)
{
  # Currently a dummy function.
  return $message;
}
?>