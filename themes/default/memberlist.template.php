<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#     Settings Layout template, April 11, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function memberlist_display_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  $sort_asc = ' <img src="'. $theme_url. '/'. $settings['theme']. '/images/sort_asc.png" alt="'. $l['asc']. '" />';
  $sort_desc = ' <img src="'. $theme_url. '/'. $settings['theme']. '/images/sort_desc.png" alt="'. $l['desc']. '" />';
  
  $sort_id = $page['sort'] == 'id' ? $sort_asc : ($page['sort'] == 'id;desc' ? $sort_desc : '');
  $sort_username = $page['sort'] == 'username' ? $sort_asc : ($page['sort'] == 'username;desc' ? $sort_desc : '');
  $sort_registered = $page['sort'] == 'registered' ? $sort_asc : ($page['sort'] == 'registered;desc' ? $sort_desc : '');
  $sort_online = $page['sort'] == 'online' ? $sort_asc : ($page['sort'] == 'online;desc' ? $sort_desc : '');
  $sort_posts = $page['sort'] == 'posts' ? $sort_asc : ($page['sort'] == 'posts;desc' ? $sort_desc : '');
  
  # Echo the title, table header, etc.
  echo '
      <h1>', $l['memberlist_header'], '</h1>
      <p>', $l['memberlist_desc'], '</p>
      
      <br />
      
      <p>'. $page['pagination']. '</p>
      
      <br />
      
      <table class="htable">
        <tr>
          <th><a href="'. $base_url. '/index.php?action=memberlist;sort=id'. ($page['sort'] == 'id' ? ';desc' : ''). '">', $l['memberlist_id'], '</a>'. $sort_id. '</th>
          <th><a href="'. $base_url. '/index.php?action=memberlist;sort=username'. ($page['sort'] == 'username' ? ';desc' : ''). '">', $l['memberlist_username'], '</a>'. $sort_username. '</th>
          <th><a href="'. $base_url. '/index.php?action=memberlist;sort=registered'. ($page['sort'] == 'registered' ? ';desc' : ''). '">', $l['memberlist_registered'], '</a>'. $sort_registered. '</th>
          <th><a href="'. $base_url. '/index.php?action=memberlist;sort=online'. ($page['sort'] == 'online' ? ';desc' : ''). '">', $l['memberlist_online'], '</a>'. $sort_online. '</th>
          <th><a href="'. $base_url. '/index.php?action=memberlist;sort=posts'. ($page['sort'] == 'posts' ? ';desc' : ''). '">', $l['memberlist_posts'], '</a>'. $sort_posts. '</th>
        </tr>';
  
  # Echo the members
  foreach($page['members'] as $member)
    echo '
        <tr>
          <td>'. numberformat($member['member_id']). '</td>
          <td><a href="'. $base_url. '/index.php?action=profile;u='. $member['member_id']. '">'. $member['displayName']. '</a></td>
          <td>'. timeformat($member['reg_time']). '</td>
          <td>'. timeformat($member['last_online']). '</td>
          <td>'. numberformat($member['num_posts']). '</td>
        </tr>';
  
  # Echo the footer stuff
  echo '
      </table>
      
      <br />
      
      <p>'. $page['pagination']. '</p>';
}
?>