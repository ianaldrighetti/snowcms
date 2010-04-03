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

  $api->run_hooks('post_init_func', array(&$func));
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

if(!function_exists('create_pagination'))
{
  /*
    Function: create_pagination

    Creates a pagination... You know, those things that allow you to go to
    the next page and what not ;-).

    Parameters:
      string $tpl_url - The URL which will have &page={NUM} appended to.
      int &$start - The starting page, this is to be obtained from $_GET['page'].
                    This is a reference parameter, after the pagination is generated
                    this is meant to be put into a query in the LIMIT $start,PER_PAGE
                    clause.
      int $num_items - The total number of items.
      int $per_page - The number of items to display per page.

    Returns:
      string - Returns the string containing the generated pagination.

    Note:
      This function is overloadable.
  */
  function pagination_create($tpl_url, &$start, $num_items, $per_page = 10)
  {
    # So how many pages total..?
    $total_pages = ceil((int)($num_items == 0 ? 1 : $num_items) / (int)$per_page);

    # Make sure start is an integer... At least make it one.
    $start = (int)$start;

    # We can't have a page less then one,
    # or greater then total_pages ;)
    if($start < 1)
      $start = 1;
    elseif($start > $total_pages)
      $start = $total_pages;

    # So start... Make an array holding all our stuffs.
    $index = array();

    # So the << First :) Though we may not link it
    # if we are on the first page.
    $index[] = '<span class="pagination_first">'. ($start != 1 ? '<a href="'. $tpl_url. '">' : ''). l('&laquo;&laquo; First'). ($start != 1 ? '</a>' : ''). '</span>';

    # Now the < which is the previous one... Don't link
    # it if thats where we are :P
    $index[] = '<span class="pagination_prev">'. ($start != 1 ? '<a href="'. (($start - 1) > 1 ? $tpl_url. '&page='. ($start - 1) : $tpl_url). '">' : ''). l('&laquo; Previous'). ($start != 1 ? '</a>' : ''). '</span>';

    # So now the page numbers...
    if($total_pages < 6)
    {
      # Hmm... Less then 5 :P
      $page_start = 1;
      $page_end = $total_pages;
    }
    elseif($start - 2 < 1)
    {
      # We are gonna go from 1 to 5 ;)
      $page_start = 1;
      $page_end = 5;
    }
    elseif($start + 2 <= $total_pages)
    {
      # Somewhere in between...
      $page_start = $start - 2;
      $page_end = $start + 2;
    }
    else
    {
      # The end of the line...
      # Some weird buggy that needs fixing...
      $page_start = ($start == ($total_pages - 1) ? $start - 3 : $start - 4);
      $page_end = $total_pages;
    }

    # So now that we have our numbers, for loop :D
    for($page = $page_start; $page < ($page_end + 1); $page++)
    {
      # So add the page number... Also, don't link the page number
      # if thats where we are at ;) oh, ya and, don't add &page=
      # to the end of our template url if its page one :)
      $index[] = '<span class="pagination_page'. ($page == $start ? ' pagination_current' : ''). '">'. ($page != $start ? '<a href="'. ($page != 1 ? $tpl_url. '&page='. $page : $tpl_url). '">' : ''). $page. ($page != $start ? '</a>' : ''). '</span>';
    }

    # Almost done :D!
    # So add the > which is the next one ;)
    # Don't link it if thats our current page...
    $index[] = '<span class="pagination_next">'. ($start < $total_pages ? '<a href="'. $tpl_url. '&page='. ($start + 1). '">' : ''). l('Next &raquo;'). ($start < $total_pages ? '</a>' : ''). '</span>';

    # Now the Last >> Of course, don't link it if thats where we are.
    $index[] = '<span class="pagination_last">'. ($start < $total_pages ? '<a href="'. $tpl_url. '&page='. $total_pages. '">' : ''). l('Last &raquo;&raquo;'). ($start < $total_pages ? '</a>' : ''). '</span>';

    # And we are done with the hard stuffs, yay.
    # So before we implode the stuff, take away 1 from
    # start, then multiply it by per_page. What for? For LIMIT clauses :D
    $start = ($start - 1) * $per_page;

    # Return it imploded...
    return implode(' ', $index);
  }
}
?>