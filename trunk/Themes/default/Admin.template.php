<?php
// default/Admin.template.php by SnowCMS Dev's
if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $cmsurl, $settings, $l, $user;
  echo '
  <div style="overflow: auto; width: 503px; height: 100px;">
    '.$settings['page']['news'].'
  </div>
  <table>
    <tr>
      <td>'.$l['admin_current_version'].'</td>
      <td>'.$settings['version'].'</td>
    </tr>
    <tr>
      <td>'.$l['admin_snowcms_current_version'].'</td>
      <td>v0.2</td>
    </tr>
  </table>';
}

function Error() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['admin_error_header'].'</h1>
  <p>'.$l['admin_error_reason'].'</p>';
}