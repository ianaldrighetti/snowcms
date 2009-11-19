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
# This is where SnowText is parsed and SnowText templates are defined.
# What? Wikitext? I don't know what you're talking about.
#
# string snowtext(string $message[, string $title = null[, int $views =
#                 null[, array $creator = null[, int $created_time =
#                 null[, int $modified_time = $created_time]]]]]);
#   - Formats SnowText as HTML. HTML already in $message is preserved.
#   string $message - The message that is formatted.
#   string $title - The title of the message, used in the {{title}}
#                   template, if omitted or set to null, uses of the
#                   {{title}} template will be ignored.
#   int $views - The amount of views of the message, used in the
#                {{views}} template, if omitted or set to null, uses
#                 of the {{views}} template will be ignored.
#   array $creator - An array containing the creator of the message's
#                    ID and display name for use in the {{creator}}
#                    template, in the form of:
#                      
#                      array(
#                        'id' => /* Creator's ID */,
#                        'name' => /* Creator's display name */,
#                      )
#                      
#                      If omitted or set to null, uses of the
#                      {{creator}} template will be ignored.
#   int $created_time - The time the message was created, used in the
#                       {{created}} template, if omitted or set to
#                       null, uses of the {{created}} template will be
#                       ignored.
#   int $modified_time - The time the message was last modified, used
#                        in the {{modified}} template, if omitted or
#                        set to null, it defaults to the page's
#                        created time.
#
# Templates:
#
# string snowtext_core(mixed $core, array $inline);
#   - Returns $core.
#
# string snowtext_member(array $core, array $inline);
#   - Returns member's name and profile link.
#   array $core - Member's ID and name, bust be of the format:
#                      
#                      array(
#                        'id' => /* Creator's ID */,
#                        'name' => /* Creator's display name */,
#                      )
#                      
#                  If ID is not given, no profile link is forced.
#   array $inline - If first argument is false or 'false', no profile
#                   link is attached. Defaults to true.
#
# string snowtext_time(int $core, array $inline);
#   - Returns a time, formatted.
#   int $core - The time returned in a Unix timestamp.
#   array $inline - If first argument is not 'default' or 'auto', time
#                   is formatted by it, otherwise time is formatted by
#                   the usual time format. Defaults to 'default'.
#

function snowtext($message, $title = null, $views = null, $creator = null, $created_time = null, $modified_time = null)
{
  global $base_url, $user;
  
  # Modified time defaults to created time
  if($modified_time === null)
    $modified_time = $created_time;
  
  # Encode ampersands
  $message = str_replace('&', '&amp;', $message);
  
  # Simple 'tags', the key is both the start and end of the tag. The
  # value is what it is replaced with, %s% is the text in-between the
  # start and end
  $simple = array(
    '\'\'\'\'\'' => '<b><i>%s%</i></b>',
    '\'\'\'\'' => '<b>\'%s%\'</b>',
    '\'\'\'' => '<b>%s%</b>',
    '\'\'' => '<i>%s%</i>',
    '======' => '<h6>%s%</h6>',
    '=====' => '<h5>%s%</h5>',
    '====' => '<h4>%s%</h4>',
    '===' => '<h3>%s%</h3>',
    '==' => '<h2>%s%</h2>',
  );
  
  # Template definitions. The key is the name of the template, e.g.
  # 'visitor' is the {{visitor}} template
  # The value is an array with the form:
  #   
  #   array(
  #     /* Function handler */,
  #     /* First argument of function handler */,
  #   )
  #   
  $templates = array(
    # {{visitor}} displays the visitor, whether they be a member or a guest
    'visitor' => array('snowtext_member', array(
                                          'id' => $user['id'],
                                          'name' => $user['name'],
                                        )),
    # {{now}} displays the current time
    'now' => array('snowtext_time', time_utc() + $user['timezone'] * 3600),
  );
  
  # {{title}} displays the title of the message (From second argument of snowtext())
  if(!is_null($title))
    $templates['title'] = array('snowtext_core', $title);
  
  # {{views}} displays the amount of views of the message (From third argument of snowtext())
  if(!is_null($views))
    $templates['views'] = array('snowtext_core', $views);
  
  # {{creator}} displays the creator of the message (From fourth argument)
  if(is_array($creator))
    $templates['creator'] = array('snowtext_member', $creator);
  
  # {{created}} and {{modified}} display the time created and modified (From fifth and sixth arguments, respectively)
  if(!is_null($created_time))
  {
    $templates['created'] = array('snowtext_time', $created_time);
    $templates['modified'] = array('snowtext_time', $modified_time);
  }
  
  # Time to process the simple 'tags'
  foreach($simple as $snowtext => $html)
  {
    # Separate before the contained text and after
    $html = explode('%s%', $html);
    
    # Keep going until the SnowText in question is no longer found
    while(mb_substr_count($message, $snowtext))
    {
      # Get where the SnowText starts
      $start = mb_strpos($message, $snowtext);
      
      # Get the amount of characters until the SnowText ends
      $length = mb_strpos(mb_substr($message, $start + mb_strlen($snowtext)), $snowtext);
      
      # Increase that number to the end of the message, if it wasn't found
      $length = is_int($length) ? $length : mb_strlen($message);
      
      # Recreate the message with the five parts:
      #   
      #   - Before the SnowText
      #   - Starting HTML
      #   - In-between the SnowText
      #   - Ending HTML
      #   - After the SnowText
      #   
      # The SnowText 'tags' are not included (Removed)
      $message = mb_substr($message, 0, $start). $html[0]. mb_substr($message, $start + mb_strlen($snowtext), $length). $html[1]. mb_substr($message, $start + $length + mb_strlen($snowtext) * 2);
    }
  }
  
  # Time to parse the templates, continue until there are no more
  while(mb_substr_count($message, '{{'))
  {
    # Find the start of the template
    $start = mb_strpos($message, '{{');
    
    # Find the characters until the template ends
    $length = mb_strpos(mb_substr($message, $start + mb_strlen($snowtext)), '}}');
    
    # Increase the length to the end if there is no end of the template
    $length = is_int($length) ? $length : mb_strlen($message);
    
    # Get the template text (Name and arguments, not {{ or }})
    $template = mb_substr($message, $start + 2, $length);
    
    # If there are arguments
    if(mb_substr_count($template, '|'))
    {
      # Split the arguments from the template's name
      $template_args = explode('|', mb_substr($template, mb_strpos($template, '|') + 1));
      $template = mb_substr($template, 0, mb_strpos($template, '|'));
    }
    
    # If it's a valid template
    if(isset($templates[mb_strtolower($template)]))
    {
      # Recreate the message with these three parts:
      #   
      #   - Before the template
      #   - The template's return value
      #   - After the template
      #   
      # The template's return value is decided by the function chosen
      # in the template definition above. The first argument of the
      # function is decided in the template's definition, the second
      # argument is an array of inline arguments from the message
      $message = mb_substr($message, 0, $start). $templates[mb_strtolower($template)][0]($templates[mb_strtolower($template)][1], $template_args). mb_substr($message, $start + $length + 4);
    }
    else
    {
      # Recreate the message with the template call removed
      $message = mb_substr($message, 0, $start). mb_substr($message, $start + $length + 4);
    }
  }
  
  # Time to parse the links. Continue until no link start tag ([) is found
  while(mb_substr_count($message, '['))
  {
    # Find the start of the link tag ([)
    $start = mb_strpos($message, '[');
    
    # Count the characters ahead until a space is reached
    $space = mb_strpos(mb_substr($message, $start + mb_strlen($snowtext)), ' ');
    
    # Make that the end, if there isn't one
    $space = is_int($space) ? $space : mb_strlen($message);
    
    # Count the characters ahead (From the start tag) until the end tag (])
    $length = mb_strpos(mb_substr($message, $start + mb_strlen($snowtext)), ']');
    
    # Increase it to the end if there isn't one
    $length = is_int($length) ? $length : mb_strlen($message);
    
    # If there was a space before the end tag
    if($space < $length)
    {
      # Recreate the message with:
      #   
      #   - Before the link tag
      #   - <a href="
      #   - The link's URL (Before the first space)
      #   - ">
      #   - The link's label (After the first space)
      #   - </a>
      #   - After the link tag
      #   
      # In the process encode any square brackets found in the link,
      #  so that we won't end up with links inside links
      $message = mb_substr($message, 0, $start). '<a href="'. str_replace('[', '&#91;', str_replace(']', '&#93;', mb_substr($message, $start + 1, $space + 1))). '">'. str_replace('[', '&#91;', str_replace(']', '&#93;', mb_substr($message, $start + $space + 2, $length - $space))). '</a>'. mb_substr($message, $start + $length + 3);
    }
    else
    {
      # Same as abbove, only this time the link's URL and label are
      # both the same, the full text of the link, since there was no
      # space
      $message = mb_substr($message, 0, $start). '<a href="'. str_replace('[', '&#91;', str_replace(']', '&#93;', mb_substr($message, $start + 1, $length + 1))). '">'. str_replace('[', '&#91;', str_replace(']', '&#93;', mb_substr($message, $start + 1, $length + 1))). '</a>'. mb_substr($message, $start + $length + 3);
    }
  }
  
  # Decode ampersands and square brackets from their entities, also adds <br />s to newlines
  return str_replace("\n", '<br />'. "\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace('&amp;', '&', str_replace('&#91;', '[', str_replace('&#93;', ']', $message))))));
}

function snowtext_core($core, $inline)
{
  # Return the core text, ignore inline arguments
  return $core;
}

function snowtext_member($core, $inline)
{
  # Display the member set with the data from the core argument, in an
  # array with the form:
  #   
  #   array(
  #     'id' => /* Member's ID */
  #     'name' => /* Member's display name */
  #   )
  #   
  # If the ID variable is missing or 0, profile link won't be used.
  
  # If the first inline argument was false or if the user is a guest, we don't provide a link to the member's profile
  if($core['id'] || (isset($inline[0]) && (!$inline[0] || $inline[0] == 'false')))
    return $core['name'];
  else
    return '<a href="'. $base_url. '/index.php?action=profile;u='. $core['id']. '">'. $core['name']. '</a>';
}

function snowtext_time($core, $inline)
{
  # Show the time from the core argument, which should be a Unix timestamp
  
  # If there are no inline arguments or the first one is set to default or auto, then use the built-in time format
  if(empty($inline[0]) || $inline[0] == 'default' || $inline[0] == 'auto')
    return timeformat($core);
  else
    return date($inline[0], $core);
}