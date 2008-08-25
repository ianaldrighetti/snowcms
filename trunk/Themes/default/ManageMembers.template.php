<?php

function Main() {
global $l, $db_prefix, $settings, $cmsurl, $theme_url;
  
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
    
    // Show filter
    echo '<form action="'.$cmsurl.'index.php" method="post" style="float: right; margin-bottom: 0"><p style="display: inline">
       '.$settings['managemembers']['filter'].'
        </p></form>
        ';
    
    // Show next and previous page links
    if ($prev_page > 0)
      echo '<table width="100%">
        <tr><td><a href="'.$cmsurl.'index.php?action=admin;sa=members;pg='.$prev_page.$settings['manage_members']['filter_get'].$settings['manage_members']['sort_get'].'">'.$l['managemembers_previous_page'].'</a></td>
        ';
    elseif ($prev_page == 0)
      echo '<table width="100%">
        <tr><td><a href="'.$cmsurl.'index.php?action=admin;sa=members'.$settings['manage_members']['filter_get'].$settings['manage_members']['sort_get'].'">'.$l['managemembers_previous_page'].'</a></td>
        ';
    else
      echo '<table width="100%">
        <tr><td></td>
        ';
    if ($total_members > $page_end)
      echo '<td style="text-align: right"><a href="'.$cmsurl.'index.php?action=admin;sa=members;pg='.$next_page.$settings['manage_members']['filter_get'].$settings['manage_members']['sort_get'].'">'.$l['managemembers_next_page'].'</a></td></tr>
        </table>
        ';
    else
      echo '<td style="text-align: right"></td></tr>
        </table>
        ';
    
    // Show members
    echo '<table style="width: 100%; text-align: center">
          <tr><th style="border-style: solid; border-width: 1px; width: 11%"><a href="'.$cmsurl.'index.php?action=admin;sa=members'.$settings['manage_members']['page_get'].$settings['manage_members']['filter_get'].';s=id'.$settings['manage_members']['id_desc'].'">'.$l['managemembers_id'].'</a></th><th style="border-style: solid; border-width: 1px; width: 29%"><a href="'.$cmsurl.'index.php?action=admin;sa=members'.$settings['manage_members']['page_get'].$settings['manage_members']['filter_get'].';s=username'.$settings['manage_members']['username_desc'].'">'.$l['managemembers_username'].'</a></th><th style="border-style: solid; border-width: 1px; width: 28%"><a href="'.$cmsurl.'index.php?action=admin;sa=members'.$settings['manage_members']['page_get'].$settings['manage_members']['filter_get'].';s=group'.$settings['manage_members']['group_desc'].'">'.$l['managemembers_group'].'</a></th><th style="border-style: solid; border-width: 1px; width: 29%"><a href="'.$cmsurl.'index.php?action=admin;sa=members'.$settings['manage_members']['page_get'].$settings['manage_members']['filter_get'].';s=joindate'.$settings['manage_members']['joindate_desc'].'">'.$l['managemembers_join_date'].'</a></th><th width="6%"></th></tr>';
    $i = 0;
    while (($row = mysql_fetch_assoc($member_rows)) && $i < $page_end - ($page_start - 1)) {
      echo '<tr>
      <td>'.$row['id'].'</td>
      <td><a href="'.$cmsurl.'index.php?action=profile;u='.$row['id'].'">'.($row['display_name'] ? $row['display_name'] : $row['username']).'</a></td>
      <td>'.$row['groupname'].'</td><td>'.date($settings['dateformat'],$row['reg_date']).'</td>
      <td><a href="'.$cmsurl.'index.php?action=admin;sa=members;u='.$row['id'].'">
          <img src="'.$theme_url.'/'.$settings['theme'].'/images/modify.png" alt="'.$l['managemembers_moderate_button'].'" width="15" height="15" />
          </a></td>
      </tr>';
      $i += 1;
    }
    echo '</table>';
    
    // Show next and previous page links
    if ($prev_page > 0)
      echo '<table width="100%">
        <tr><td><a href="'.$cmsurl.'index.php?action=admin;sa=members;pg='.$prev_page.$settings['manage_members']['filter_get'].$settings['manage_members']['sort_get'].'">'.$l['managemembers_previous_page'].'</a></td>
        ';
    elseif ($prev_page == 0)
      echo '<table width="100%">
        <tr><td><a href="'.$cmsurl.'index.php?action=admin;sa=members'.$settings['manage_members']['filter_get'].$settings['manage_members']['sort_get'].'">'.$l['managemembers_previous_page'].'</a></td>
        ';
    else
      echo '<table width="100%">
        <tr><td></td>
        ';
    if ($total_members > $page_end)
      echo '<td style="text-align: right"><a href="'.$cmsurl.'index.php?action=admin;sa=members;pg='.$next_page.$settings['manage_members']['filter_get'].$settings['manage_members']['sort_get'].'">'.$l['managemembers_next_page'].'</a></td></tr>
        </table>
        ';
    else
      echo '<td style="text-align: right"></td></tr>
        </table>
        ';
    
    // Show filter
    echo '<form action="'.$cmsurl.'index.php" method="get" style="float: right; margin-bottom: 0"><p style="display: inline">
       '.$settings['managemembers']['filter'].'
       </p></form>';
  }
}

function NoMembers() {
global $l, $settings, $cmsurl;
  
  echo '<h1>'.$l['managemembers_title'].'</h1>
        ';
  
  $page = $settings['manage_members']['page'];
  $member_rows = $settings['manage_members']['member_rows'];
  $total_members = $settings['manage_members']['total_members'];
  $page_start = $settings['manage_members']['page_start'];
  $page_end = $settings['manage_members']['page_end'];
  $next_page = $settings['manage_members']['next_page'];
  $prev_page = $settings['manage_members']['prev_page'];
  
  // Show filter
    echo '<form action="'.$cmsurl.'index.php" method="get" style="float: right; margin-bottom: 0"><p style="display: inline">
       '.$settings['managemembers']['filter'].'
        </p></form>
        <br />
        <br />
        ';
  
  // Show next and previous page links
    if ($prev_page > 0)
      echo '<table width="100%">
        <tr><td><a href="'.$cmsurl.'index.php?action=admin;sa=members;pg='.$prev_page.$settings['manage_members']['filter_get'].$settings['manage_members']['sort_get'].'">'.$l['managemembers_previous_page'].'</a></td>
        ';
    elseif ($prev_page == 0)
      echo '<table width="100%">
        <tr><td><a href="'.$cmsurl.'index.php?action=admin;sa=members'.$settings['manage_members']['filter_get'].$settings['manage_members']['sort_get'].'">'.$l['managemembers_previous_page'].'</a></td>
        ';
    else
      echo '<table width="100%">
        <tr><td></td>
        ';
    if ($total_members > $page_end)
      echo '<td style="text-align: right"><a href="'.$cmsurl.'index.php?action=admin;sa=members;pg='.$next_page.$settings['manage_members']['filter_get'].$settings['manage_members']['sort_get'].'">'.$l['managemembers_next_page'].'</a></td></tr>
        </table>
        ';
    else
      echo '<td style="text-align: right"></td></tr>
        </table>
        ';
  
  echo '<p>'.$l['managemembers_showing_none'].'</p>
        ';
  
  // Show next and previous page links
    if ($prev_page > 0)
      echo '<table width="100%">
        <tr><td><a href="'.$cmsurl.'index.php?action=admin;sa=members;pg='.$prev_page.$settings['manage_members']['filter_get'].$settings['manage_members']['sort_get'].'">'.$l['managemembers_previous_page'].'</a></td>
        ';
    elseif ($prev_page == 0)
      echo '<table width="100%">
        <tr><td><a href="'.$cmsurl.'index.php?action=admin;sa=members'.$settings['manage_members']['filter_get'].$settings['manage_members']['sort_get'].'">'.$l['managemembers_previous_page'].'</a></td>
        ';
    else
      echo '<table width="100%">
        <tr><td></td>
        ';
    if ($total_members > $page_end)
      echo '<td style="text-align: right"><a href="'.$cmsurl.'index.php?action=admin;sa=members;pg='.$next_page.$settings['manage_members']['filter_get'].$settings['manage_members']['sort_get'].'">'.$l['managemembers_next_page'].'</a></td></tr>
        </table>
        ';
    else
      echo '<td style="text-align: right"></td></tr>
        </table>
        ';
  
  // Show filter
    echo '<form action="'.$cmsurl.'index.php" method="get" style="float: right; margin-bottom: 0"><p style="display: inline">
       '.$settings['managemembers']['filter'].'
       </p></form>';
}

function Moderate() {
global $l, $settings, $user, $cmsurl;
  
  $last_ip = $settings['managemembers']['member']['last_ip'] ? $settings['managemembers']['member']['last_ip'] : $settings['managemembers']['member']['reg_ip'];
  $last_login = $settings['managemembers']['member']['last_login'] ? date($settings['timeformat'].', '.$settings['dateformat'],$settings['managemembers']['member']['last_login']) : $l['managemembers_moderate_never'];
  
  echo '<form action="'.$cmsurl.'index.php?action=admin;sa=members;u='.$_REQUEST['u'].'" method="post" style="display: inline">
        
        <p>
        <input type="hidden" name="sc" value="'.$user['sc'].'" />
        <input type="hidden" name="ssa" value="process-moderate" />
        </p>
        
        <table style="width: 100%" class="padding">
        <tr><th style="text-align: left; width: 30%">'.$l['managemembers_moderate_id'].':</th><td>'.$settings['managemembers']['member']['id'].'</td></tr>';
   
  if (can('moderate_username'))
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_username'].':</th><td><input name="user_name" value="'.$settings['managemembers']['member']['username'].'" /></td></tr>';
  if (can('moderate_display_name'))
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_display_name'].':</th><td><input name="display_name" value="'.$settings['managemembers']['member']['display_name'].'" /></td></tr>';
  if (can('moderate_email'))
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_email'].':</th><td><input name="email" value="'.$settings['managemembers']['member']['email'].'" /></td></tr>';
  
  if (can('moderate_password'))
    echo '<tr><td colspan="2"><br /></td></tr>
        <tr><th style="text-align: left">'.$l['profile_edit_password_new'].':</th><td><input type="password" name="password-new" /></td></tr>
        <tr><th style="text-align: left">'.$l['profile_edit_password_verify'].':</th><td><input type="password" name="password-verify" /></td></tr>
        <tr><td colspan="2"><br /></td></tr>';
  if (can('moderate_group')) {
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_group'].':</th><td>
        <select name="group">
        ';
    
    while ($row = mysql_fetch_assoc($settings['managemembers']['groups'])) {
      if ($settings['managemembers']['member']['group'] == $row['group_id'])
        echo '<option value="'.$row['group_id'].'" selected="selected">'.$row['groupname'].'</option>'."\n";
      else
        echo '<option value="'.$row['group_id'].'">'.$row['groupname'].'</option>'."\n";
    }
    
    echo '</select>
        </td></tr>';
  }
  echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_posts'].':</th><td>'.$settings['managemembers']['member']['numposts'].'</td></tr>
        <tr><th style="text-align: left">'.$l['managemembers_moderate_registration_date'].':</th><td>'.date($settings['timeformat'].', '.$settings['dateformat'],$settings['managemembers']['member']['reg_date']).'</td></tr>
        <tr><th style="text-align: left">'.$l['managemembers_moderate_last_login'].':</th><td>'.$last_login.'</td></tr>
        ';
  
  if ($settings['managemembers']['member']['suspension'] > time())
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_suspended_until'].':</th><td>'.date($settings['timeformat'].', '.$settings['dateformat'],$settings['managemembers']['member']['suspension']).'</td></tr>';
  
  echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_registration_ip'].':</th><td>'.$settings['managemembers']['member']['reg_ip'].'</td></tr>
        <tr><th style="text-align: left">'.$l['managemembers_moderate_last_ip'].':</th><td>'.$last_ip.'</td></tr>';
  
  if (can('moderate_signature'))
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_signature'].':</th><td><textarea name="signature" cols="45" rows="4">'.$settings['managemembers']['member']['signature'].'</textarea></td></tr>';
  if (can('moderate_profile'))
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_profile_text'].':</th><td><textarea name="profile" cols="45" rows="4">'.$settings['managemembers']['member']['profile'].'</textarea></td></tr>';
  
  echo '</table>
        
        <br />
        ';
  if (can('moderate_username') || can('moderate_display_name') || can('moderate_email') || can('moderate_password') || can('moderate_group') ||
      can('moderate_signature') || can('moderate_profile'))
  echo '<p style="display: inline"><input type="submit" value="'.$l['managemembers_moderate_change'].'" /></p>';
  echo '</form>
        
        <form action="'.$cmsurl.'index.php?action=profile;u='.$settings['managemembers']['member']['id'].'" method="post" style="display: inline">
        <p style="display: inline">
        <input type="hidden" name="action" value="profile" />
        <input type="submit" value="'.$l['managemembers_moderate_profile'].'" />
        </p>
        </form>
       <br />
       <br />
       ';
  if (!$settings['managemembers']['member']['activated']) {
    if (can('moderate_activate'))
      echo '<form action="'.$cmsurl.'index.php" method="get" style="display: inline">
        <p style="display: inline">
        <input type="hidden" name="action" value="admin" />
        <input type="hidden" name="sa" value="members" />
        <input type="hidden" name="ssa" value="activate" />
        <input type="hidden" name="sc" value="'.$user['sc'].'" />
        <input type="hidden" name="u" value="'.$settings['managemembers']['member']['id'].'" />
        <input type="submit" value="'.$l['managemembers_moderate_activate'].'" />
        </p>
        </form>
        ';
  }
  else {
    if ($settings['managemembers']['member']['suspension'] <= time()) {
      if (can('moderate_suspend'))
        echo '<form action="'.$cmsurl.'index.php?action=admin;sa=members;u='.$_REQUEST['u'].'" method="post" style="display: inline"><p style="display: inline">
        <input type="hidden" name="action" value="admin" />
        <input type="hidden" name="sa" value="members" />
        <input type="hidden" name="ssa" value="suspend" />
        <input type="hidden" name="sc" value="'.$user['sc'].'" />
        '.str_replace('%button%','<input type="submit" value="'.$l['managemembers_moderate_suspend_button'].'" />',str_replace('%input%','<input name="suspension" value="3" style="text-align: center; width: 30px" maxlength="4" />',$l['managemembers_moderate_suspend'])).
        '</p></form>
        <br />
        <br />';
    }
    else {
      if (can('moderate_suspend') && can('moderate_unsuspend'))
        echo str_replace('%renew%','<form action="'.$cmsurl.'index.php?action=admin;sa=members;u='.$_REQUEST['u'].'" method="post" style="display: inline"><p style="display: inline">
        <input type="hidden" name="action" value="admin" />
        <input type="hidden" name="sa" value="members" />
        <input type="hidden" name="ssa" value="suspend" />
        <input type="hidden" name="sc" value="'.$user['sc'].'" />
        <input type="submit" value="'.$l['managemembers_moderate_renew_suspension_button'].'" />
        ',str_replace('%input%','<input name="suspension" value="3" style="text-align: center; width: 30px" maxlength="4" />
        </p></form>',str_replace('%remove%','<form action="'.$cmsurl.'index.php?action=admin;sa=members;u='.$_REQUEST['u'].'" method="post" style="display: inline"><p style="display: inline">
        <input type="submit" value="'.$l['managemembers_moderate_unsuspend_button'].'" />
        <input type="hidden" name="action" value="admin" />
        <input type="hidden" name="sa" value="members" />
        <input type="hidden" name="ssa" value="unsuspend" />
        <input type="hidden" name="sc" value="'.$user['sc'].'" />
        </p></form>
        ',$l['managemembers_moderate_renew_remove_suspension']))).'<br />
        <br />';
      else if (can('moderate_suspend'))
        echo str_replace('%renew%','<form action="'.$cmsurl.'index.php?action=admin;sa=members;u='.$_REQUEST['u'].'" method="post" style="display: inline"><p style="display: inline">
        <input type="hidden" name="action" value="admin" />
        <input type="hidden" name="sa" value="members" />
        <input type="hidden" name="ssa" value="suspend" />
        <input type="hidden" name="sc" value="'.$user['sc'].'" />
        <input type="submit" value="'.$l['managemembers_moderate_renew_suspension_button'].'" />
        ',str_replace('%input%','<input name="suspension" value="3" style="text-align: center; width: 30px" maxlength="4" />
        </p></form>',$l['managemembers_moderate_renew_suspension'])).'<br />
        <br />';
    }
    if (!@$settings['managemembers']['member']['banned']) {
      if (can('moderate_ban'))
        echo '<form action="'.$cmsurl.'index.php?action=admin;sa=members;u='.$_REQUEST['u'].'" method="post" style="display: inline">
        <p style="display: inline">
        <input type="hidden" name="action" value="admin" />
        <input type="hidden" name="sa" value="members" />
        <input type="hidden" name="ssa" value="ban" />
        <input type="hidden" name="sc" value="'.$user['sc'].'" />
        <input type="hidden" name="u" value="'.$settings['managemembers']['member']['id'].'" />
        <input type="submit" value="'.$l['managemembers_moderate_ban'].'" />
        </p>
       </form>
       ';
    }
    else
      if (can('moderate_unban'))
        echo '<form action="'.$cmsurl.'index.php?action=admin;sa=members;u='.$_REQUEST['u'].'" method="post" style="display: inline">
        <p style="display: inline">
        <input type="hidden" name="action" value="admin" />
        <input type="hidden" name="sa" value="members" />
        <input type="hidden" name="ssa" value="unban" />
        <input type="hidden" name="sc" value="'.$user['sc'].'" />
        <input type="hidden" name="u" value="'.$settings['managemembers']['member']['id'].'" />
        <input type="submit" value="'.$l['managemembers_moderate_unban'].'" />
        </p>
       </form>
       ';
  }
}

?>