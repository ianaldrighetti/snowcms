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
# void ip_lookup();
#

function ip_lookup()
{
  global $base_url, $db, $l, $page, $settings, $source_dir, $user;
  
  # Screen permissions
  error_screen('view_ips');
  
  # Load the language
  language_load('ip');
  
  # Get the IP in question
  $page['ip'] = isset($_GET['ip']) ? $_GET['ip'] : '';
  
  # Get the members associated with the IP address
  $result = $db->query("
    SELECT
      ipl.ip, ipl.first_time, ipl.last_time, mem.member_id, mem.displayName AS username
    FROM {$db->prefix}ip_logs AS ipl
    LEFT JOIN {$db->prefix}members AS mem ON mem.member_id = ipl.member_id
    WHERE ip = %ip",
    array(
      'ip' => array('string', $page['ip']),
    ));
  
  $page['members'] = array();
  while($row = $db->fetch_assoc($result))
    $page['members'][] = $row;
  
  # Get the posts created with the IP address
  $result = $db->query("
    SELECT
      msg.msg_id, msg.subject, msg.poster_ip, msg.poster_time, mem.displayName AS username
    FROM {$db->prefix}messages AS msg
    LEFT JOIN {$db->prefix}members AS mem ON mem.member_id = msg.member_id
    WHERE poster_ip = %poster_ip",
    array(
      'poster_ip' => array('string', $page['ip']),
    ));
  
  $page['posts'] = array();
  while($row = $db->fetch_assoc($result))
    $page['posts'][] = $row;
  
  # Get the pages created with the IP address
  $result = $db->query("
    SELECT
      page_id, page_title, creator_ip
    FROM {$db->prefix}pages
    WHERE creator_ip = %creator_ip",
    array(
      'creator_ip' => array('string', $page['ip']),
    ));
  
  $page['pages'] = array();
  while($row = $db->fetch_assoc($result))
    $page['pages'][] = $row;
  
  # Get the errors associated with the IP address
  $result = $db->query("
    SELECT
      error_id, ip
    FROM {$db->prefix}error_log
    WHERE ip = %ip",
    array(
      'ip' => array('string', $page['ip']),
    ));
  
  $page['errors'] = array();
  while($row = $db->fetch_assoc($result))
    $page['errors'][] = $row;
  
  # Set the title
  $page['title'] = sprintf($l['ip_lookup_title'], $page['ip']);
  
  # Load the theme
  theme_load('ip', 'ip_lookup_show');
}
?>