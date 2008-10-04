<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//       ManageMembers.template.php

if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $l, $db_prefix, $settings, $cmsurl, $theme_url;
  
  // Load the filter listbox element
  loadFilter();
  
  echo '
    <h1>'.$l['managemembers_header'].'</h1>
    ';
  
  if (@$_SESSION['error'])
    echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  
  $first_member = $settings['page']['first_member'];
  $last_member = $settings['page']['last_member'];
  $members = $settings['page']['members'];
  $total_members = $settings['page']['total_members'];
  if ($page_get = $settings['page']['page_get'])
    $page_get = ';pg='.$page_get;
  if ($page_get = $settings['page']['page_get'])
    $page_get = ';pg='.$page_get;
  if ($filter_get = $settings['page']['filter_get'])
    $filter_get = ';f='.$filter_get;
  if ($sort_get = $settings['page']['sort_get'])
    $sort_get = ';s='.$sort_get;
  
  if ($first_member != $last_member)
    echo '<p>'.str_replace("%from%",$first_member,str_replace("%to%",$last_member,$l['managemembers_showing'])).'</p>';
  else
    echo '<p>'.str_replace("%number%",$first_member,$l['managemembers_showing_one']).'</p>';
  
  if ($total_members) {
    // Show filter
    echo '<form action="'.$cmsurl.'index.php?action=admin;sa=members" method="post" style="text-align: right; margin-bottom: 0"><p style="display: inline">
       '.$settings['managemembers']['filter'].'
        </p></form>
        ';
    
    pagination($settings['page']['page'],$settings['page']['page_last'],'index.php?action=admin;sa=members'.$filter_get.$sort_get);
    
    // Show member list header
    echo '<table style="width: 100%; text-align: center">
          <tr>
            <th style="border-style: solid; border-width: 1px; width: 11%"><a href="'.$cmsurl.'index.php?action=admin;sa=members'.$page_get.$filter_get.';s=id'.$settings['manage_members']['id_desc'].'">'.$l['managemembers_id'].'</a></th>
            <th style="border-style: solid; border-width: 1px; width: 29%"><a href="'.$cmsurl.'index.php?action=admin;sa=members'.$page_get.$filter_get.';s=username'.$settings['manage_members']['username_desc'].'">'.$l['managemembers_username'].'</a></th>
            <th style="border-style: solid; border-width: 1px; width: 28%"><a href="'.$cmsurl.'index.php?action=admin;sa=members'.$page_get.$filter_get.';s=group'.$settings['manage_members']['group_desc'].'">'.$l['managemembers_group'].'</a></th>
            <th style="border-style: solid; border-width: 1px; width: 29%"><a href="'.$cmsurl.'index.php?action=admin;sa=members'.$page_get.$filter_get.';s=joindate'.$settings['manage_members']['joindate_desc'].'">'.$l['managemembers_join_date'].'</a></th>
            <th width="6%"></th>
          </tr>';
    
    // Show members on this page
    foreach ($members as $member) {
      echo '<tr>
        <td>'.$member['id'].'</td>
        <td><a href="'.$cmsurl.'index.php?action=profile;u='.$member['id'].'">'.$member['display_name'].'</a></td>
        <td>'.$member['groupname'].'</td>
        <td>'.formattime($member['reg_date']).'</td>
        <td><a href="'.$cmsurl.'index.php?action=admin;sa=members;u='.$member['id'].'">
            <img src="'.$theme_url.'/'.$settings['theme'].'/images/modify.png" alt="'.$l['managemembers_moderate_button'].'" width="15" height="15" />
            </a></td>
      </tr>';
    }
    
    // Show member list footer
    echo '</table>';
    
    pagination($settings['page']['page'],$settings['page']['page_last'],'index.php?action=admin;sa=members'.$filter_get.$sort_get);
    
    // Show filter
    echo '<form action="'.$cmsurl.'index.php" method="post" style="text-align: right; margin-bottom: 0"><p style="display: inline">
       '.$settings['managemembers']['filter'].'
       </p></form>';
  }
}

function NoMembers() {
global $l, $settings, $cmsurl;
  
  // Load the filter listbox element
  loadFilter();
  
  echo '
  <h1>'.$l['managemembers_header'].'</h1>
  
  <p><br /></p>
  
  <form action="'.$cmsurl.'index.php" method="post" style="text-align: right; margin-bottom: 0">
    <p style="display: inline">
      '.$settings['managemembers']['filter'].'
    </p>
  </form>
  
  <p><b>[1]</b></p>
  
  <p style="text-align: center">'.$l['managemembers_showing_none'].'</p>
  
  <p><b>[1]</b></p>
  
  <form action="'.$cmsurl.'index.php" method="post" style="text-align: right; margin-bottom: 0">
    <p style="display: inline">
      '.$settings['managemembers']['filter'].'
    </p>
  </form>';
}

function Moderate() {
global $l, $settings, $user, $cmsurl;
  
  $member = $settings['page']['member'];
  $last_ip = $member['last_ip'] ? $member['last_ip'] : $member['reg_ip'];
  $last_login = $member['last_login'] ? date($settings['timeformat'].', '.$settings['dateformat'],$member['last_login']) : $l['managemembers_moderate_never'];
  
  echo '
      <h1>'.str_replace('%name%',$member['display_name'],$l['managemembers_moderate_header']).'</h1>
      ';
  
  if (@$_SESSION['error'])
    echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  
  echo '<form action="'.$cmsurl.'index.php?action=admin;sa=members;u='.$_REQUEST['u'].'" method="post" style="display: inline">
        
        <p>
        <input type="hidden" name="sc" value="'.$user['sc'].'" />
        <input type="hidden" name="ssa" value="process-moderate" />
        </p>
        
        <table style="width: 100%" class="padding">
        <tr><th style="text-align: left; width: 30%">'.$l['managemembers_moderate_id'].':</th><td>'.$member['id'].'</td></tr>
        ';
   
  if (can('moderate_username'))
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_username'].':</th><td><input name="username" value="'.$member['username'].'" /></td></tr>
        ';
  else
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_username'].':</th><td>
          '.$member['username'].'
          <input type="hidden" name="username" value="'.$member['username'].'" />
        </td></tr>';
  
  if (can('moderate_display_name'))
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_display_name'].':</th><td><input name="display_name" value="'.$member['display_name'].'" /></td></tr>';
  else
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_display_name'].':</th><td>
          '.$member['display_name'].'
          <input type="hidden" name="display_name" value="'.$member['display_name'].'" />
        </td></tr>';
  
  if (can('moderate_email'))
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_email'].':</th><td><input name="email" value="'.$member['email'].'" /></td></tr>';
  else
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_email'].':</th><td>
          <a href="mailto:'.$member['email'].'">'.$member['email'].'</a>
          <input type="hidden" name="email" value="'.$member['email'].'" />
        </td></tr>';
  
  if (can('moderate_group')) {
      echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_group'].':</th><td>
          <select name="membergroup">
          ';
  
    foreach ($settings['page']['groups'] as $row) {
      if ($member['group'] == $row['group_id'])
        echo '<option value="'.$row['group_id'].'" selected="selected">'.$row['groupname'].'</option>'."\n";
      else
        echo '<option value="'.$row['group_id'].'">'.$row['groupname'].'</option>'."\n";
    }
    echo '</select>
    ';
  }
  else
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_group'].':</th><td>
          '.$member['groupname'].'
          <input type="hidden" name="membergroup" value="'.$member['group'].'" />
        </td></tr>';
  
  if (can('moderate_birthdate')) {
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_birthdate'].'</th><td>
          <input name="day" value="'.$member['birthdate_day'].'" style="width: 30px" />
          -
          <select name="month" style="width: 55px">
            ';
    
    $i = 1;
    while ($i <= 12) {
      if ($member['birthdate_month'] == $i)
        echo '<option value="'.$i.'" selected="selected">'.$l['main_month_'.$i.'_short'].'</option>
          ';
      else
        echo '<option value="'.$i.'">'.$l['main_month_'.$i.'_short'].'</option>
          ';
      $i += 1;
    }
    
    echo '</select>
          -
          <input name="year" value="'.$member['birthdate_year'].'" size="1" />
        </td></tr>
        ';
  }
  else
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_birthdate'].':</th><td>
          '.$member['birthdate'].'
          <input type="hidden" name="day" value="'.$member['birthdate_day'].'" />
          <input type="hidden" name="month" value="'.$member['birthdate_month'].'" />
          <input type="hidden" name="year" value="'.$member['birthdate_year'].'" />
        </td></tr>';
  
  if (can('moderate_avatar'))
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_avatar'].':</th><td><input name="avatar" value="'.$member['avatar'].'" /></td></tr>';
  else
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_avatar'].':</th><td>
          '.$member['avatar'].'
          <input type="hidden" name="group" value="'.$member['avatar'].'" />
        </td></tr>';
  
  if (can('moderate_password'))
    echo '<tr><td colspan="2"><br /></td></tr>
        <tr><th style="text-align: left">'.$l['managemembers_moderate_password_new'].':</th><td><input type="password" name="password-new" /></td></tr>
        <tr><th style="text-align: left">'.$l['managemembers_moderate_password_verify'].':</th><td><input type="password" name="password-verify" /></td></tr>';
  
  echo '</td></tr>
      <tr><td colspan="2"><br /></td></tr>
      <tr><th style="text-align: left">'.$l['managemembers_moderate_registration_date'].':</th><td>'.date($settings['timeformat'].', '.$settings['dateformat'],$member['reg_date']).'</td></tr>
      <tr><th style="text-align: left">'.$l['managemembers_moderate_last_login'].':</th><td>'.$last_login.'</td></tr>
      ';
  
  if ($member['suspension'] > time())
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_suspended_until'].':</th><td>'.date($settings['timeformat'].', '.$settings['dateformat'],$member['suspension']).'</td></tr>';
  
  echo '
      <tr><td colspan="2"><br /></td></tr>
      ';
  
  if (can('manage_ips_ban') || can('manage_ips_unban'))
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_registration_ip'].':</th><td>'.$member['reg_ip'].'</td></tr>
      <tr><th style="text-align: left">'.$l['managemembers_moderate_last_ip'].':</th><td>'.$last_ip.'</td></tr>
      <tr><td colspan="2"><a href="index.php?action=admin;sa=members;ssa=ips;u='.$member['id'].'">'.$l['managemembers_moderate_ips'].'</a></td></tr>
      ';
  
  if (can('moderate_signature'))
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_signature'].':</th><td><textarea name="signature" cols="45" rows="4">'.$member['signature'].'</textarea></td></tr>';
  else
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_signature'].':</th><td>
          '.$member['signature'].'
          <input type="hidden" name="signature" value="'.$member['signature'].'" />
        </td></tr>';
  
  if (can('moderate_profile'))
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_profile_text'].':</th><td><textarea name="profile" cols="45" rows="4">'.$member['profile'].'</textarea></td></tr>';
  else
    echo '<tr><th style="text-align: left">'.$l['managemembers_moderate_profile'].':</th><td>
          '.$member['profile'].'
          <input type="hidden" name="group" value="'.$member['profile'].'" />
        </td></tr>';
  
  echo '</table>
        
        <br />
        ';
  if (can('moderate_username') || can('moderate_display_name') || can('moderate_email') || can('moderate_password') || can('moderate_group') ||
      can('moderate_signature') || can('moderate_profile'))
  echo '<p style="display: inline"><input type="submit" value="'.$l['managemembers_moderate_change'].'" /></p>';
  echo '</form>
        
        <form action="'.$cmsurl.'index.php?action=profile;u='.$member['id'].'" method="post" style="display: inline">
        <p style="display: inline">
        <input type="hidden" name="action" value="profile" />
        <input type="submit" value="'.$l['managemembers_moderate_profile'].'" />
        </p>
        </form>
       <br />
       <br />
       ';
  if (!$member['activated']) {
    if (can('moderate_activate'))
      echo '<form action="'.$cmsurl.'index.php" method="get" style="display: inline">
        <p style="display: inline">
        <input type="hidden" name="action" value="admin" />
        <input type="hidden" name="sa" value="members" />
        <input type="hidden" name="ssa" value="activate" />
        <input type="hidden" name="sc" value="'.$user['sc'].'" />
        <input type="hidden" name="u" value="'.$member['id'].'" />
        <input type="submit" value="'.$l['managemembers_moderate_activate'].'" />
        </p>
        </form>
        ';
  }
  else {
    if ($member['suspension'] <= time()) {
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
    if (!@$member['banned']) {
      if (can('moderate_ban'))
        echo '<form action="'.$cmsurl.'index.php?action=admin;sa=members;u='.$_REQUEST['u'].'" method="post" style="display: inline">
        <p style="display: inline">
        <input type="hidden" name="action" value="admin" />
        <input type="hidden" name="sa" value="members" />
        <input type="hidden" name="ssa" value="ban" />
        <input type="hidden" name="sc" value="'.$user['sc'].'" />
        <input type="hidden" name="u" value="'.$member['id'].'" />
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
        <input type="hidden" name="u" value="'.$member['id'].'" />
        <input type="submit" value="'.$l['managemembers_moderate_unban'].'" />
        </p>
       </form>
       ';
  }
}

function loadFilter() {
global $l, $settings;
  
  // Get filter HTML
  $filter = '
          <input type="submit" value="'.$l['managemembers_filter_button'].'" />
          <input type="hidden" name="action" value="admin" />
          <input type="hidden" name="sa" value="members" />
          ';
  $filter .= '<select name="f">';
  switch (@$_REQUEST['f']) {
    case '':
        $filter .= '<option value="all" selected="selected">'.$l['managemembers_filter_everyone'].'</option>
          <option value="active">'.$l['managemembers_filter_active'].'</option>
          <option value="activated">'.$l['managemembers_filter_activated'].'</option>
          <option value="unactivated">'.$l['managemembers_filter_unactivated'].'</option>
          <option value="suspended">'.$l['managemembers_filter_suspended'].'</option>
          <option value="banned">'.$l['managemembers_filter_banned'].'</option>
          '; break;
    case 'active':
      $filter .= '<option value="all" selected="selected">'.$l['managemembers_filter_everyone'].'</option>
          <option value="active" selected="selected">'.$l['managemembers_filter_active'].'</option>
          <option value="activated">'.$l['managemembers_filter_activated'].'</option>
          <option value="unactivated">'.$l['managemembers_filter_unactivated'].'</option>
          <option value="suspended">'.$l['managemembers_filter_suspended'].'</option>
          <option value="banned">'.$l['managemembers_filter_banned'].'</option>
          '; break;
    case 'activated':
      $filter .= '<option value="all">'.$l['managemembers_filter_everyone'].'</option>
          <option value="active">'.$l['managemembers_filter_active'].'</option>
          <option value="activated" selected="selected">'.$l['managemembers_filter_activated'].'</option>
          <option value="unactivated">'.$l['managemembers_filter_unactivated'].'</option>
          <option value="suspended">'.$l['managemembers_filter_suspended'].'</option>
          <option value="banned">'.$l['managemembers_filter_banned'].'</option>
          '; break;
    case 'unactivated':
      $filter .= '<option value="all">'.$l['managemembers_filter_everyone'].'</option>
          <option value="active">'.$l['managemembers_filter_active'].'</option>
          <option value="activated">'.$l['managemembers_filter_activated'].'</option>
          <option value="unactivated" selected="selected">'.$l['managemembers_filter_unactivated'].'</option>
          <option value="suspended">'.$l['managemembers_filter_suspended'].'</option>
          <option value="banned">'.$l['managemembers_filter_banned'].'</option>
          '; break;
    case 'suspended':
      $filter .= '<option value="all">'.$l['managemembers_filter_everyone'].'</option>
          <option value="active">'.$l['managemembers_filter_active'].'</option>
          <option value="activated">'.$l['managemembers_filter_activated'].'</option>
          <option value="unactivated">'.$l['managemembers_filter_unactivated'].'</option>
          <option value="suspended" selected="selected">'.$l['managemembers_filter_suspended'].'</option>
          <option value="banned">'.$l['managemembers_filter_banned'].'</option>
          '; break;
    case 'banned':
      $filter .= '<option value="all">'.$l['managemembers_filter_everyone'].'</option>
          <option value="active">'.$l['managemembers_filter_active'].'</option>
          <option value="activated">'.$l['managemembers_filter_activated'].'</option>
          <option value="unactivated">'.$l['managemembers_filter_unactivated'].'</option>
          <option value="suspended">'.$l['managemembers_filter_suspended'].'</option>
          <option value="banned" selected="selected">Banned</option>
          '; break;
    default:
      $filter .= '<option value="all">'.$l['managemembers_filter_everyone'].'</option>
          <option value="active">'.$l['managemembers_filter_active'].'</option>
          <option value="activated">'.$l['managemembers_filter_activated'].'</option>
          <option value="unactivated">'.$l['managemembers_filter_unactivated'].'</option>
          <option value="suspended">'.$l['managemembers_filter_suspended'].'</option>
          <option value="banned">'.$l['managemembers_filter_banned'].'</option>
          ';
  }
  $filter .= '<option value="all">-----------------</option>
          ';
  foreach ($settings['page']['groups'] as $group) {
    if ($settings['page']['filter_get'] == $group['group_id'])
      $filter .= '<option value="'.$group['group_id'].'" selected="selected">'.$group['groupname'].'</option>'."\n";
    else
      $filter .= '<option value="'.$group['group_id'].'">'.$group['groupname'].'</option>'."\n";
  }
  $filter .= '</select>';
  if (@$_REQUEST['s'])
    $filter .= '<input type="hidden" name="s" value="'.$_REQUEST['s'].'" />
          ';
  $settings['managemembers']['filter'] = $filter;
}

function pagination($page, $last, $url) {
global $l, $cmsurl;
  
  echo '<p>';
  $i = $page < 2 ? 0 : $page - 2;
  if ($i > 1)
    echo '<a href="'.$cmsurl.$url.'">1</a> ... ';
  elseif ($i == 1)
    echo '<a href="'.$cmsurl.$url.'">1</a> ';
  while ($i < ($page + 3 < $last ? $page + 3 : $last)) {
    if ($i == $page)
      echo '<b>['.($i+1).']</b> ';
    elseif ($i)
      echo '<a href="'.$cmsurl.$url.';pg='.$i.'">'.($i+1).'</a> ';
    else
      echo '<a href="'.$cmsurl.$url.'">'.($i+1).'</a> ';
    $i += 1;
  }
  if ($i < $last - 1)
    echo '... <a href="'.$cmsurl.$url.';pg='.($last-1).'">'.$last.'</a>';
  elseif ($i == $last - 1)
    echo '<a href="'.$cmsurl.$url.';pg='.($last-1).'">'.$last.'</a>';
  echo '</p>';
}
?>