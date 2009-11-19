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
# Member listing stuff is done here.
#
# void memberlist_display();
#   - Display the list of members.
#

function memberlist_display()
{
  global $base_url, $db, $l, $page;
  
  # Get the language
  language_load('memberlist');
  
  # Define possible things to sort by
  $sorts = array(
    'id' => 'member_id',
    'username' => 'displayName',
    'registered' => 'reg_time',
    'online' => 'last_online',
    'posts' => 'num_posts',
  );
  
  # Get the sort URL stuff
  $page['sort'] = isset($_GET['sort']) && in_array($_GET['sort'],array_keys($sorts)) ? $_GET['sort'] : 'posts';
  $page['sort'] .= isset($_GET['desc']) ? ';desc' : '';
  
  # Get the sort SQL stuff
  $sort_sql = isset($_GET['sort']) && in_array($_GET['sort'],array_keys($sorts)) ? $sorts[$_GET['sort']] : 'num_posts';
  $sort_sql .= isset($_GET['desc']) ? ' DESC' : '';
  
  # Get the total members
  $num_members = $db->query("
    SELECT
      COUNT(*)
    FROM {$db->prefix}members",
    array());
  @list($num_members) = $db->fetch_row($num_members);
  
  # Deal with the pagination stuff
  $page_num = isset($_GET['page']) ? (int)$_GET['page'] : 0;
  $page['pagination'] = pagination_create($base_url. '/index.php?action=memberlist;sort='. $page['sort'], $page_num, $num_members);
  
  # Get the members from the database
  $result = $db->query("
    SELECT
      member_id, displayName, reg_time, last_online, num_posts
    FROM {$db->prefix}members
    ORDER BY $sort_sql
    LIMIT $page_num, 10",
    array());
  
  # Format the members in a 2D array
  while($row = $db->fetch_assoc($result))
    $page['members'][] = $row;
  
  # Set the title
  $page['title'] = $l['memberlist_title'];
  
  # Load the theme
  theme_load('memberlist', 'memberlist_display_show');
}
?>