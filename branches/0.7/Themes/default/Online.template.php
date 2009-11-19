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
  <table width="100%" style="text-align: center">
    <tr>
      <th>'.$l['online_user'].'</th>'; if(can('view_online_special')) { echo '<th>'.$l['online_ip'].'</th>'; } echo '<th>'.$l['online_time'].'</th><th>'.$l['online_currently_viewing'].'</th>
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