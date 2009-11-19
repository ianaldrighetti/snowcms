<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#     Permissions Layout template, April 11, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function permissions_membergroups_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
      <h1>', $l['permissions_membergroups_header'], '</h1>
      
      <p>', $l['permissions_membergroups_desc'], '</p>
      
      <br />
      
      <table class="htable">
        <tr>
          <th>', $l['permissions_membergroups_name'], '</th>
          <th>', $l['permissions_membergroups_members'], '</th>
          <th>', $l['permissions_membergroups_allowed_pm_size'], '</th>
        </tr>';
  
  foreach($page['groups'] as $group)
  {
    echo '
        <tr>
          <td><a href="', $base_url, '/index.php?action=admin;sa=permissions;area=groups;group=', $group['group_id'], '">', $group['group_name_plural'], '</a></td>
          <td>', $group['members'] != -1 ? numberformat($group['members']) : '', '</td>
          <td>', $group['allowed_pm_size'] ? $group['allowed_pm_size']. $l['kb'] : $l['permissions_membergroups_unlimited'], '</td>
        </tr>';
  }
  
  echo '
      </table>';
}

function permissions_edit_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
      <h1>', sprintf($l['permissions_edit_header'], $page['group']['group_name']), '</h1>
      
      <p>', sprintf($l['permissions_edit_desc'], $page['group']['group_name']), '</p>
      
      <br />
      
      <form action="" method="post">
        <input type="hidden" name="process" value="permissions" />
        
        <table>
          <tr>
            <td><label for="allowed_pm_size">', $l['permissions_edit_allowed_pm_size'], '</label></td>
            <td><input type="text" name="allowed_pm_size" id="allowed_pm_size" value="', $page['group']['allowed_pm_size'], '" size="5" /> ', $l['kb'], '</td>
          </tr>
          <tr>
            <td style="font-size: x-small;">', $l['permissions_edit_allowed_pm_size_desc'], '</td>
          </tr>
        </table>
        
        <br />
        
        <ul class="permissions">';
  
  foreach($page['perms'] as $category => $perms)
  {
    echo '
          <li class="category"><a href="javascript:void(0);" onclick="toggle_visibility(\'', $category, '\');"><tt id="', $category, '_change">+</tt> ', $l['permissions_edit_category_'. $category], '</a>', ' [<span id="', $category, '_selected">', $page['perms_selected'][$category], '</span>/', $page['perms_totals'][$category], ']
            <ul id="', $category, '_perms" class="perms">
              <script type="text/javascript">_.G(\'', $category, '_perms\').style.display = \'none\';</script>';
    
    foreach($perms as $perm => $checked)
    {
      echo '
              <li class="perm"><input type="checkbox" name="', $perm, '" id="', $perm, '" ', $checked ? ' checked="checked"' : '', ' onclick="toggle_checked(this, \'', $category, '\');" /> <label for="', $perm, '">'. $l['permissions_edit_perm_'. $perm]. '</label></li>';
    }
    
    echo '
            </ul>
         </li>';
  }
  
  echo '
        </ul>
        
        <br />
        
        <p><input type="submit" value="', $l['permissions_edit_submit'], '" /></p>
        
      </form>';
}

function permissions_edit_show_invalid()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
      <h1>', $l['permissions_edit_invalid_header'], '</h1>
      
      <p>', $l['permissions_edit_invalid_desc'], '</p>';
}
?>