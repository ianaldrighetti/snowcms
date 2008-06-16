<?php
// default/Online.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $cmsurl, $l, $settings, $user;
echo '
  <h1>'.$l['online_header'].'</h1>
  <p>'.$l['online_desc'].'</p>
  <table>
    <tr>
      <td>'.$l['online_user'].'</td>'; if(can('view_online_special')) { echo '<td>'.$l['online_ip'].'</td>'; } echo '<td>'.$l['online_currently_viewing'].'</td>
    </tr>';
  foreach($settings['page']['online'] as $online) {
  
  }
  echo '
  </table>';
}