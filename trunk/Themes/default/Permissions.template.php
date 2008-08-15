<?php
// default/Permissions.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");
  
function Main() {
global $cmsurl, $settings, $l, $user;
  echo '
    <h2>'.$l['permissions_header'].'</h2>
    <p>'.$l['permissions_desc'].'</p>
  <table width="100%">
    <tr>
      <td width="60%">'.$l['permissions_membergroup'].'</td>
      <td width="10%">'.$l['permissions_numusers'].'</td>
      <td width="10%">'.$l['permissions_permissions'].'</td>
      <td width="20%">&nbsp;</td>
    </tr>';
  foreach($settings['groups'] as $group) {
    echo '
    <tr>
      <td style="padding: 5px;"><a href="'.$cmsurl.'index.php?action=admin&sa=groups&mid='.$group['id'].'">'.$group['name'].'</a></td>
      <td style="text-align: center; padding: 5px;">'.$group['numusers'].'</td>
      <td style="text-align: center; padding: 5px;">'.$group['numperms'].'</td>
      <td style="text-align: center; padding: 5px;"><a href="'.$cmsurl.'index.php?action=admin&sa=permissions&mid='.$group['id'].'">'.$l['permissions_modify'].'</a></td>
    </tr>';
  }
  echo '
  </table>'; 
}

function NoGroup() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['admin_error_header'].'</h1>
  <p>'.$l['permissions_nogroup_desc'].'</p>';
}

function Edit() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h2>'.$l['permissions_edit_header'].'</h2>
  <p>'.$l['permissions_edit_desc'].'</p>';
  echo '
  <form action="'.$cmsurl.'index.php?action=admin&sa=permissions" method="post">
    <fieldset>
      <table>';
  foreach($settings['permissions']['group'] as $perm => $value) {
    echo '
        <tr>
          <td>'.$l['permissions_perm_'.$perm].'</td><td><input name="'.$perm.'" type="checkbox" value="1" ', @isset($settings['perms'][$perm]) ? 'checked="checked"' : '', '/></td>
        </tr>';
  }
  echo '
        <input name="membergroup" type="hidden" value="'.$_REQUEST['mid'].'"/>
        <tr>
          <td>&nbsp;</td><td><input name="update_perms" type="submit" value="'.$l['permissions_edit_save'].'"/></td>
        </tr>
      </table>
    </fieldset>
  </form>';
}
?>