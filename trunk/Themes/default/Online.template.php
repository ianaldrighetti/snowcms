<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//          Online.template.php

if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $cmsurl, $l, $settings, $user;
echo '
  <h1>'.$l['online_header'].'</h1>
  <p>'.$l['online_desc'].'</p>
  <table width="100%">
    <tr>
      <td>'.$l['online_user'].'</td>'; if(can('view_online_special')) { echo '<td>'.$l['online_ip'].'</td>'; } echo '<td>'.$l['online_time'].'</td><td>'.$l['online_currently_viewing'].'</td>
    </tr>';
  foreach($settings['page']['online'] as $member) {
    echo '
    <tr>
      <td>', $member['id'] ? '<a href="'. $cmsurl. 'index.php?action=profile;u='. $member['id']. '">'. $member['name']. '</a>' : $member['name'], '</td>'; if(can('view_online_special')) { echo '<td>', $member['ip'], '</td>'; } echo '<td>', $member['last_active'], '</td><td>', $member['viewing'], '</td>
    </tr>';
  }
  echo '
  </table>';
}
?>