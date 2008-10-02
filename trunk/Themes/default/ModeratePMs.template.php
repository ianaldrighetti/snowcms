<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//        ModeratePMs.template.php

if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $l, $db_prefix, $settings, $cmsurl, $theme_url;
  
  // Load the filter listbox element
  loadFilter();
  
  echo '
    <h1>'.$l['moderatepms_header'].'</h1>
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
    echo '<p>'.str_replace("%from%",$first_member,str_replace("%to%",$last_member,$l['moderatepms_showing'])).'</p>';
  else
    echo '<p>'.str_replace("%number%",$first_member,$l['moderatepms_showing_one']).'</p>';
  
  if ($total_members) {
    // Show filter
    echo '<form action="'.$cmsurl.'index.php?action=admin;sa=pms" method="post" style="text-align: right; margin-bottom: 0"><p style="display: inline">
       '.$settings['page']['filter'].'
        </p></form>
        ';
    
    pagination($settings['page']['page'],$settings['page']['page_last'],'index.php?action=admin;sa=pms'.$filter_get.$sort_get);
    
    // Show member list header
    echo '<table style="width: 100%; text-align: center">
          <tr>
            <th style="border-style: solid; border-width: 1px; width: 20%"><a href="'.$cmsurl.'index.php?action=admin;sa=pms'.$page_get.$filter_get.';s=id'.$settings['page']['id_desc'].'">'.$l['moderatepms_to'].'</a></th>
            <th style="border-style: solid; border-width: 1px; width: 20%"><a href="'.$cmsurl.'index.php?action=admin;sa=pms'.$page_get.$filter_get.';s=username'.$settings['page']['username_desc'].'">'.$l['moderatepms_from'].'</a></th>
            <th style="border-style: solid; border-width: 1px; width: 33%"><a href="'.$cmsurl.'index.php?action=admin;sa=pms'.$page_get.$filter_get.';s=group'.$settings['page']['group_desc'].'">'.$l['moderatepms_subject'].'</a></th>
            <th style="border-style: solid; border-width: 1px; width: 27%"><a href="'.$cmsurl.'index.php?action=admin;sa=pms'.$page_get.$filter_get.';s=joindate'.$settings['page']['joindate_desc'].'">'.$l['moderatepms_date_sent'].'</a></th>
          </tr>';
    
    // Show members on this page
    foreach ($members as $member) {
      echo '<tr>
        <td><a href="'.$cmsurl.'index.php?action=profile;u='.$member['to_id'].'">'.$member['to'].'</a></td>
        <td><a href="'.$cmsurl.'index.php?action=profile;u='.$member['from_id'].'">'.$member['from'].'</a></td>
        <td><a href="'.$cmsurl.'index.php?action=admin;sa=pms;pm='.$member['id'].'">'.$member['subject'].'</a></td>
        <td>'.formattime($member['date_sent']).'</td>
      </tr>';
    }
    
    // Show member list footer
    echo '</table>';
    
    pagination($settings['page']['page'],$settings['page']['page_last'],'index.php?action=admin;sa=pms'.$filter_get.$sort_get);
    
    // Show filter
    echo '<form action="'.$cmsurl.'index.php?action=admin;sa=pms" method="post" style="text-align: right; margin-bottom: 0"><p style="display: inline">
       '.$settings['page']['filter'].'
       </p></form>';
  }
}

function NoMembers() {
global $l, $settings, $cmsurl;
  
  // Load the filter listbox element
  loadFilter();
  
  echo '
  <h1>'.$l['moderatepms_header'].'</h1>
  
  <p><br /></p>
  
  <form action="'.$cmsurl.'index.php" method="post" style="text-align: right; margin-bottom: 0">
    <p style="display: inline">
      '.$settings['page']['filter'].'
    </p>
  </form>
  
  <p><b>[1]</b></p>
  
  <p style="text-align: center">'.$l['moderatepms_showing_none'].'</p>
  
  <p><b>[1]</b></p>
  
  <form action="'.$cmsurl.'index.php" method="post" style="text-align: right; margin-bottom: 0">
    <p style="display: inline">
      '.$settings['page']['filter'].'
    </p>
  </form>';
}

function Moderate() {
global $l, $settings, $user, $cmsurl;
  
  $member = $settings['page']['member'];
  $last_ip = $member['last_ip'] ? $member['last_ip'] : $member['reg_ip'];
  $last_login = $member['last_login'] ? date($settings['timeformat'].', '.$settings['dateformat'],$member['last_login']) : $l['moderatepms_moderate_never'];
  
  echo '
      <h1>'.str_replace('%subject%',$member['subject'],$l['moderatepms_message_header']).'</h1>
      
      <p>'.
      str_replace('%from%','<a href="'.$cmsurl.'index.php?action=profile;u='.$member['from_id'].'">'.$member['from'].'</a>',
      str_replace('%to%','<a href="'.$cmsurl.'index.php?action=profile;u='.$member['to_id'].'">'.$member['to'].'</a>',
      str_replace('%time%','<b>'.formattime($member['date_sent'],2).'</b>',
      $l['moderatepms_message_heading'])))
      .'</p>
      
      <p>'.$member['body'].'</p>
      
      <form action="" method="post" style="display: inline">
        <p style="display: inline">
          <input type="hidden" name="delete" value="true" />
          <input type="submit" value="'.$l['moderatepms_message_delete'].'"
           onclick="return confirm(\''.$l['moderatepms_message_delete_areyousure'].'\')" />
        </p>
      </form>
      <form action="'.$cmsurl.'index.php?action=admin;sa=pms" method="post" style="display: inline">
        <p style="display: inline">
          <input type="hidden" name="redirect" value="true" />
          <input type="submit" value="'.$l['moderatepms_message_cancel'].'" .>
        </p>
      </form>
      ';
}

function loadFilter() {
global $l, $settings;
  
  // Get filter HTML
  $filter = '
          <input type="submit" value="'.$l['moderatepms_filter_button'].'" />
          <input type="hidden" name="action" value="admin" />
          <input type="hidden" name="sa" value="pms" />
          ';
  $filter .= '<select name="f">';
  switch (@$_REQUEST['f']) {
    case '':
        $filter .= '<option value="all" selected="selected">'.$l['moderatepms_filter_everyone'].'</option>
          <option value="active">'.$l['moderatepms_filter_active'].'</option>
          <option value="activated">'.$l['moderatepms_filter_activated'].'</option>
          <option value="unactivated">'.$l['moderatepms_filter_unactivated'].'</option>
          <option value="suspended">'.$l['moderatepms_filter_suspended'].'</option>
          <option value="banned">'.$l['moderatepms_filter_banned'].'</option>
          '; break;
    case 'active':
      $filter .= '<option value="all" selected="selected">'.$l['moderatepms_filter_everyone'].'</option>
          <option value="active" selected="selected">'.$l['moderatepms_filter_active'].'</option>
          <option value="activated">'.$l['moderatepms_filter_activated'].'</option>
          <option value="unactivated">'.$l['moderatepms_filter_unactivated'].'</option>
          <option value="suspended">'.$l['moderatepms_filter_suspended'].'</option>
          <option value="banned">'.$l['moderatepms_filter_banned'].'</option>
          '; break;
    case 'activated':
      $filter .= '<option value="all">'.$l['moderatepms_filter_everyone'].'</option>
          <option value="active">'.$l['moderatepms_filter_active'].'</option>
          <option value="activated" selected="selected">'.$l['moderatepms_filter_activated'].'</option>
          <option value="suspended">'.$l['moderatepms_filter_suspended'].'</option>
          <option value="unactivated">'.$l['moderatepms_filter_unactivated'].'</option>
          <option value="banned">'.$l['moderatepms_filter_banned'].'</option>
          '; break;
    case 'suspended':
      $filter .= '<option value="all">'.$l['moderatepms_filter_everyone'].'</option>
          <option value="active">'.$l['moderatepms_filter_active'].'</option>
          <option value="activated">'.$l['moderatepms_filter_activated'].'</option>
          <option value="suspended" selected="selected">'.$l['moderatepms_filter_suspended'].'</option>
          <option value="unactivated">'.$l['moderatepms_filter_unactivated'].'</option>
          <option value="banned">'.$l['moderatepms_filter_banned'].'</option>
          '; break;
    case 'unactivated':
      $filter .= '<option value="all">'.$l['moderatepms_filter_everyone'].'</option>
          <option value="active">'.$l['moderatepms_filter_active'].'</option>
          <option value="activated">'.$l['moderatepms_filter_activated'].'</option>
          <option value="suspended">'.$l['moderatepms_filter_suspended'].'</option>
          <option value="unactivated" selected="selected">'.$l['moderatepms_filter_unactivated'].'</option>
          <option value="banned">'.$l['moderatepms_filter_banned'].'</option>
          '; break;
    case 'banned':
      $filter .= '<option value="all">'.$l['moderatepms_filter_everyone'].'</option>
          <option value="active">'.$l['moderatepms_filter_active'].'</option>
          <option value="activated">'.$l['moderatepms_filter_activated'].'</option>
          <option value="suspended">'.$l['moderatepms_filter_suspended'].'</option>
          <option value="unactivated">'.$l['moderatepms_filter_unactivated'].'</option>
          <option value="banned" selected="selected">'.$l['moderatepms_filter_banned'].'</option>
          '; break;
    default:
      $filter .= '<option value="all">'.$l['moderatepms_filter_everyone'].'</option>
          <option value="active">'.$l['moderatepms_filter_active'].'</option>
          <option value="activated">'.$l['moderatepms_filter_activated'].'</option>
          <option value="unactivated">'.$l['moderatepms_filter_unactivated'].'</option>
          <option value="suspended">'.$l['moderatepms_filter_suspended'].'</option>
          <option value="banned">'.$l['moderatepms_filter_banned'].'</option>
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
  $settings['page']['filter'] = $filter;
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