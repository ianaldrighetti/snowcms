<?php

function Main() {
global $l, $db_prefix, $settings, $cmsurl, $theme_url;
  echo '<h1>'.$l['managemembers_title'].'</h1>';
  
  $page = $settings['manage_members']['page'];
  $member_rows = $settings['manage_members']['member_rows'];
  $total_members = $settings['manage_members']['total_members'];
  $page_start = $settings['manage_members']['page_start'];
  $page_end = $settings['manage_members']['page_end'];
  $next_page = $settings['manage_members']['next_page'];
  $prev_page = $settings['manage_members']['prev_page'];
  
  if ($page_start != $page_end)
    echo '<p>'.str_replace("%from%",$page_start,str_replace("%to%",$page_end,$l['managemembers_showing'])).'</p>';
  else
    echo '<p>'.str_replace("%number%",$page_start,$l['managemembers_showing_one']).'</p>';
  
  if ($total_members) {
    // Make setting the first member of the page work
    $i = 0;
    while ($i < $page_start - 1) {
      $row = mysql_fetch_assoc($member_rows);
      $i += 1;
    }
    
    // Show next and previous page links
    if ($total_members > $page_end)
      echo '<p style="float: right"><a href="'.$cmsurl.'index.php?action=admin&sa=members&pg='.$next_page.$settings['manage_members']['sort_get'].'">'.$l['managemembers_next_page'].'</a></p>';
    else
      echo '<p style="float: right">&nbsp;</p>';
    if ($prev_page > 0)
      echo '<p><a href="'.$cmsurl.'index.php?action=admin&sa=members&pg='.$prev_page.$settings['manage_members']['sort_get'].'">'.$l['managemembers_previous_page'].'</a></p>';
    elseif ($prev_page == 0)
      echo '<p><a href="'.$cmsurl.'index.php?action=admin&sa=members'.$settings['manage_members']['sort_get'].'">'.$l['managemembers_previous_page'].'</a></p>';
    
    // Show members
    echo '<table style="width: 100%; text-align: center">
          <tr><th style="border-style: solid; border-width: 1px; width: 11%"><a href="'.$cmsurl.'index.php?action=admin&sa=members'.$settings['manage_members']['page_get'].'&sort=id'.$settings['manage_members']['id_desc'].'">'.$l['managemembers_id'].'</a></th><th style="border-style: solid; border-width: 1px; width: 29%"><a href="'.$cmsurl.'index.php?action=admin&sa=members'.$settings['manage_members']['page_get'].'&sort=username'.$settings['manage_members']['username_desc'].'">'.$l['managemembers_username'].'</a></th><th style="border-style: solid; border-width: 1px; width: 28%"><a href="'.$cmsurl.'index.php?action=admin&sa=members'.$settings['manage_members']['page_get'].'&sort=group'.$settings['manage_members']['group_desc'].'">'.$l['managemembers_group'].'</a></th><th style="border-style: solid; border-width: 1px; width: 29%"><a href="'.$cmsurl.'index.php?action=admin&sa=members'.$settings['manage_members']['page_get'].'&sort=joindate'.$settings['manage_members']['joindate_desc'].'">'.$l['managemembers_join_date'].'</a></th><th width="6%"></th></tr>';
    $i = 0;
    while (($row = mysql_fetch_assoc($member_rows)) && $i < $page_end - ($page_start - 1)) {
      echo '<tr><td>'.$row['id'].'</td><td><a href="'.$cmsurl.'index.php?action=profile&u='.$row['id'].'">'.$row['username'].'</a></td><td>'.$row['groupname'].'</td><td>'.date($settings['timeformat'],$row['reg_date']).'</td><td><a href="'.$cmsurl.'index.php?action=admin&sa=members&u='.$row['id'].'"><img src="'.$theme_url.'/'.$settings['theme'].'/moderate.png" alt="'.$l['managemembers_moderate_button'].'" width="12" height="12" style="border: 0" /></a></td></tr>';
      $i += 1;
    }
    echo '</table>';
    
    // Show next and previous page links
    if ($total_members > $page_end)
      echo '<p style="float: right"><a href="'.$cmsurl.'index.php?action=admin&sa=members&pg='.$next_page.$settings['manage_members']['sort_get'].'">'.$l['managemembers_next_page'].'</a></p>';
    else
      echo '<p style="float: right">&nbsp;</p>';
    if ($prev_page > 0)
      echo '<p><a href="'.$cmsurl.'index.php?action=admin&sa=members&pg='.$prev_page.$settings['manage_members']['sort_get'].'">'.$l['managemembers_previous_page'].'</a></p>';
    elseif ($prev_page == 0)
      echo '<p><a href="'.$cmsurl.'index.php?action=admin&sa=members'.$settings['manage_members']['sort_get'].'">'.$l['managemembers_previous_page'].'</a></p>';
  }
}

function NoMembers() {
global $l;
  
  echo '
        <h1>'.$l['managemembers_title'].'</h1>
        
        <p>'.$l['managemembers_showing_none'].'</p>
        ';
}

function Profile() {
global $l, $settings, $cmsurl;
  
  $last_ip = $settings['managemembers']['member']['last_ip'] ? $settings['managemembers']['member']['last_ip'] : $settings['managemembers']['member']['reg_ip'];
  $last_login = $settings['managemembers']['member']['last_login'] ? date($settings['timeformat'],$settings['managemembers']['member']['last_login']) : 'Never';
  
  echo '
        <h1>'.$settings['page']['title'].'</h1>
        
        <form action="'.$cmsurl.'index.php" method="post" style="display: inline">
        
        <table style="width: 100%" class="padding">
        <tr><th style="text-align: left; width: 30%">'.$l['managemembers_moderate_username'].':</th><td><input name="username" value="'.$settings['managemembers']['member']['username'].'" /></td></tr>
        <tr><th style="text-align: left">'.$l['managemembers_moderate_display_name'].':</th><td><input name="display_name" value="'.$settings['managemembers']['member']['display_name'].'" /></td></tr>
        <tr><th style="text-align: left">'.$l['managemembers_moderate_email'].':</th><td><input name="email" value="'.$settings['managemembers']['member']['email'].'" /></td></tr>
        <tr><th style="text-align: left">'.$l['managemembers_moderate_group'].':</th><td>
        <select name="group">
        ';
  
  while ($row = mysql_fetch_assoc($settings['managemembers']['groups'])) {
    if ($settings['managemembers']['member']['groupname'] == $row["groupname"])
      echo '<option value="'.$row['group_id'].'" selected="selected">'.$row['groupname'].'</option>'."\n";
    else
      echo '<option value="'.$row['group_id'].'">'.$row['groupname'].'</option>'."\n";
  }
  
  echo '</select>
        </td></tr>
        <tr><th style="text-align: left">'.$l['managemembers_moderate_posts'].':</th><td>'.$settings['managemembers']['member']['numposts'].'</td></tr>
        <tr><th style="text-align: left">'.$l['managemembers_moderate_registration_date'].':</th><td>'.date($settings['timeformat'],$settings['managemembers']['member']['reg_date']).'</td></tr>
        <tr><th style="text-align: left">'.$l['managemembers_moderate_last_login'].':</th><td>'.$last_login.'</td></tr>
        ';
  
  if (@$settings['managemembers']['member']['suspension'])
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_suspended_until'].':</th><td>'.$settings['managemembers']['member']['suspension'].'</td></tr>';
  
  echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_registration_ip'].':</th><td>'.$settings['managemembers']['member']['reg_ip'].'</td></tr>
        <tr><th style="text-align: left">'.$l['managemembers_moderate_last_ip'].':</th><td>'.$last_ip.'</td></tr>
        <tr><th style="text-align: left">'.$l['managemembers_moderate_signature'].':</th><td><textarea name="signature" cols="30" rows="4">'.$settings['managemembers']['member']['signature'].'</textarea></td></tr>
        </table>
        
        <br />
        
        <p style="display: inline"><input type="submit" value="'.$l['managemembers_moderate_change'].'" /></p>
        </form>
        
        <form action="'.$cmsurl.'index.php" method="get" style="display: inline">
        <p style="display: inline">
        <input type="hidden" name="action" value="profile" />
        <input type="hidden" name="u" value="'.$settings['managemembers']['member']['id'].'" />
        <input type="submit" value="'.$l['managemembers_moderate_profile'].'" />
        </p>
        </form>
       <br />
       <br />
       ';
  echo '<form action="'.$cmsurl.'index.php" method="get" style="display: inline"><p style="display: inline">
        '.str_replace('%button%','<input type="submit" value="'.$l['managemembers_moderate_suspend_button'].'" />',str_replace('%input%','<input name="suspend" value="3" style="text-align: center; width: 30px" maxlength="4" />',$l['managemembers_moderate_suspend'])).
        '</p></form>
        <br />
        <br />
        
        <form action="'.$cmsurl.'index.php" method="get" style="display: inline">
        <p style="display: inline">
        <input type="hidden" name="action" value="admin" />
        <input type="hidden" name="sa" value="members" />
        ';
  if (!@$settings['managemembers']['member']['banned'])
    echo '<input type="hidden" name="ssa" value="ban" />
        <input type="hidden" name="u" value="'.$settings['managemembers']['member']['id'].'" />
        <input type="submit" value="'.$l['managemembers_moderate_ban'].'" />
        ';
  else
    echo '<input type="hidden" name="ssa" value="unban" />
        <input type="hidden" name="u" value="'.$settings['managemembers']['member']['id'].'" />
        <input type="submit" value="'.$l['managemembers_moderate_unban'].'" />
        ';
  echo '</p>
       </form>
       ';
}

?>