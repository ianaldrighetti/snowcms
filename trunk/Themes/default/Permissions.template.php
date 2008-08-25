<?php
// default/Permissions.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");
  
function Main() {
global $cmsurl, $settings, $l, $user, $theme_url;
  echo '
    <p>'.$l['permissions_desc'].'</p>
  
  <form action="'.$cmsurl.'index.php?action=admin;sa=permissions" method="post" style="display: inline">
  
  <p><input type="hidden" name="change_groups" value="true" /></p>
  
  <table width="100%">
    <tr>
      <th width="4%"></th>
      <th width="40%" style="border-style: solid; border-width: 1px">'.$l['permissions_membergroup'].'</th>
      <th width="15%" style="border-style: solid; border-width: 1px">'.$l['permissions_numusers'].'</th>
      <th width="21%" style="border-style: solid; border-width: 1px">'.$l['permissions_permissions'].'</th>
      <th width="20%" colspan="2">&nbsp;</td>
    </tr>';
  foreach($settings['groups'] as $group) {
    echo '
    <tr>
      ';
    if ($settings['default_group'] == $group['id'])
      echo '<td><input type="radio" name="default_group" value="'.$group['id'].'" checked="checked"></td>
      ';
    else
      echo '<td><input type="radio" name="default_group" value="'.$group['id'].'"></td>
      ';
    echo '<td style="padding: 5px;"><input name="group_'.$group['id'].'" value="'.$group['name'].'" /></td>
      <td style="text-align: center; padding: 5px;">'.$group['numusers'].'</td>
      <td style="text-align: center; padding: 5px;">'.$group['numperms'].'</td>
      <td style="text-align: center; padding: 5px;"><a href="'.$cmsurl.'index.php?action=admin;sa=permissions;mid='.$group['id'].'"><img src="'.$theme_url.'/'.$settings['theme'].'/images/modify.png" alt="'.$l['permissions_modify'].'" width="15" height="15" style="border: 0" /></a></td>
      <td style="text-align: center; padding: 5px;"><a href="'.$cmsurl.'index.php?action=admin;sa=permissions;did='.$group['id'].'"><img src="'.$theme_url.'/'.$settings['theme'].'/images/delete.png" alt="'.$l['permissions_delete'].'" width="15" height="15" style="border: 0" /></a></td>
    </tr>';
  }
  echo '
  </table>
  
  <p><input type="submit" value="'.$l['permissions_change_groups'].'" /></p>
  
  </form>
  
  <br />
  
  <form action="'.$cmsurl.'index.php?action=admin;sa=permissions" method="post">
  <p>
  <input name="new_group" />
  <input type="submit" value="'.$l['permissions_new_group'].'" />
  </p>
  </form>';
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
  <p>'.$l['permissions_edit_desc'].'</p>';
  echo '
  <form action="'.$cmsurl.'index.php?action=admin;sa=permissions" method="post">
    <fieldset>
      <table>';
  foreach($settings['permissions']['group'] as $perm => $value) {
    echo '
        <tr>
          <td>'.$l['permissions_perm_'.$perm].'</td><td><input name="'.$perm.'" type="checkbox" value="1" ', @$settings['perms'][$perm]['can'] ? 'checked="checked"' : '', '/></td>
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

function BoardPerms() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h3>', $l['mf_perms_header'], '</h3>
  <p>', $l['mf_perms_desc'], '</p>
  <table width="100%">';    
    foreach($settings['cats'] as $cat) {
      echo '
      <tr>
        <td colspan="2">', $cat['name'], '</td>
      </tr>';
      if(count($cat['boards'])) {
        foreach($cat['boards'] as $board) {
          echo '
          <tr style="margin-left: 10px;">
            <td width="90%">', $board['name'], '</td><td width="5%"><a href="', $cmsurl, 'index.php?action=admin;sa=forum;fa=permissions;bid=', $board['id'], '">', $l['mf_perms_manage'], '</a></td>
          </tr>';
        }
      }
    }
  echo '
  </table>';
}

function NoCats() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h3>', $l['mf_perms_header'], '</h3>
  <p>', $l['mf_perms_desc'], '</p>
  <br />
  <p style="text-align: center; color: red;">', $l['mf_perms_nocats'], '</p>';
}
?>