<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#      IP lookup template, January 16, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function ip_lookup_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
       <h1>', sprintf($l['ip_lookup_header'], $page['ip']), '</h1>
       <p>', sprintf($l['ip_lookup_desc'], $page['ip']), '</p>
       <br />
       <table class="htable">
       <tr><th>', $l['ip_lookup_ip'], '</th><th>', $l['ip_lookup_username'], '</th><th>', $l['ip_lookup_first_time'], '</th><th>', $l['ip_lookup_last_time'], '</th></tr>';
  
  foreach($page['members'] as $member)
    echo '
       <tr><td>', $member['ip'], '</td><td><a href="', $base_url, '/index.php?action=profile;u=', $member['member_id'], '">', $member['username'], '</a></td><td>', timeformat($member['first_time']), '</td><td>', timeformat($member['last_time']), '</td></tr>';
  
  echo '
       </table>
       <br />
       <table class="htable">
       <tr><th>', $l['ip_lookup_ip'], '</th><th>', $l['ip_lookup_subject'], '</th><th>', $l['ip_lookup_poster'], '</th><th>', $l['ip_lookup_time_posted'], '</th></tr>';
  
  foreach($page['posts'] as $post)
    echo '
       <tr><td>', $post['poster_ip'], '</td><td><a href="', $base_url, '/forum.php?msg=', $post['msg_id'], '">', $post['subject'], '</td><td><a href="', $base_url, '/index.php?action=profile;u=', $post['member_id'], '">', $post['username'], '</a></td><td>', timeformat($post['poster_time']), '</td></tr>';
  
  echo '
       </table>
       <br />
       <table class="htable">
       <tr><th>', $l['ip_lookup_ip'], '</th><th>', $l['ip_lookup_page_title'], '</th><th>', $l['ip_lookup_creator'], '</th><th>', $l['ip_lookup_time_created'], '</th></tr>';
  
  foreach($page['pages'] as $page)
    echo '
       <tr><td>', $page['creator_ip'], '</td><td><a href="', $base_url, '/index.php?page=', $page['page_id'], '">', $page['page_title'], '</a></td><td><a href="', $base_url, '/index.php?action=profile;u=', $member['member_id'], '">', $page['username'], '</a></td><td>', timeformat($page['time_created']), '</td></tr>';
  
  echo '
       </table>
       <br />
       <table class="htable">
       <tr><th>', $l['ip_lookup'], '</th><th>', $l['ip_lookup'], '</th></tr>';
  
  foreach($page['errors'] as $error)
    echo '
       <tr><td>', $error['ip'], '</td><td>', $member['error_id'], '</a></td></tr>';
  
  echo '
       </table>';
}
?>