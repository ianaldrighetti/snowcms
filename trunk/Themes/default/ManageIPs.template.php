<?php
// default/ManagePages.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $cmsurl, $settings, $l, $user, $cmsurl, $theme_url;
  
  $ips = $settings['page']['ips'];
  
  echo '
    <h1>'.$l['manageips_header'].'</h1>
    
    <p>'.$l['manageips_desc'].'</p>
    
    <form action="'.$cmsurl.'index.php?action=admin;sa=ips" method="post" style="display: inline">
    
    <p><input type="hidden" name="change_ips" value="true" /></p>
    
    <table width="100%" style="text-align: center">
      <tr>
        <th style="border-style: solid; border-width: 1px">'.$l['manageips_ip'].'</th>
        <th style="border-style: solid; border-width: 1px">'.$l['manageips_reason'].'</th>
        <th width="15"></th>
      </tr>';
    foreach($ips as $ip) {
      echo '
      <tr>
        <td><input name="ip_'.$ip['ip'].'" value="'.$ip['ip'].'" /></td>
        <td><input name="ip_'.$ip['ip'].'_reason" value="'.$ip['reason'].'" /></td>
        <td><a href="'.$cmsurl.'index.php?action=admin;sa=ips;uip='.$ip['ip'].'"><img src="'.$theme_url.'/'.$settings['theme'].'/images/delete.png" alt="'.$l['manageips_unban'].'" width="15" height="15" style="border: 0" /></a></td>
      </tr>';
    }
    echo '
      <tr>
        <td><input name="new_ip" value="" /></td>
        <td><input name="new_ip_reason" value="" /></td>
        <td></td>
      </tr>
    </table>
  
  <p><input type="submit" value="'.$l['manageips_save'].'" /></p>
  
  </form>';
}

function NoIPs() {
global $l, $settings, $cmsurl;
  
  echo '
    <h1>'.$l['manageips_header'].'</h1>
    
    <p>'.$l['manageips_desc'].'</p>
    
    <form action="'.$cmsurl.'index.php?action=admin;sa=ips" method="post" style="display: inline">
    
    <p><input type="hidden" name="change_ips" value="true" /></p>
    
    <table width="100%" style="text-align: center">
      <tr>
        <th style="border-style: solid; border-width: 1px">'.$l['manageips_ip'].'</th>
        <th style="border-style: solid; border-width: 1px">'.$l['manageips_reason'].'</th>
        <th width="15"></th>
      </tr>
      <tr>
        <td><input name="new_ip" value="" /></td>
        <td><input name="new_ip_reason" value="" /></td>
        <td></td>
      </tr>
    </table>
  
  <p><input type="submit" value="'.$l['manageips_save'].'" /></p>
  
  </form>
  ';
}
?>