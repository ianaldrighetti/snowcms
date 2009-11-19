<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//        MemberList.template.php

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
    echo '<form action="'.$cmsurl.'forum.php?action=members" method="post" style="text-align: right; margin-bottom: 0"><p style="display: inline">
       '.$settings['memberlist']['filter'].'
        </p></form>
        ';
    
    pagination($settings['page']['page'],$settings['page']['page_last'],'forum.php?action=members'.$filter_get.$sort_get);
    
    // Show member list header
    echo '<table style="width: 100%; text-align: center">
          <tr>
            <th style="border-style: solid; border-width: 1px; width: 11%"><a href="'.$cmsurl.'forum.php?action=members'.$page_get.$filter_get.';s=id'.$settings['manage_members']['id_desc'].'">'.$l['managemembers_id'].'</a></th>
            <th style="border-style: solid; border-width: 1px; width: 29%"><a href="'.$cmsurl.'forum.php?action=members'.$page_get.$filter_get.';s=username'.$settings['manage_members']['username_desc'].'">'.$l['managemembers_username'].'</a></th>
            <th style="border-style: solid; border-width: 1px; width: 28%"><a href="'.$cmsurl.'forum.php?action=members'.$page_get.$filter_get.';s=group'.$settings['manage_members']['group_desc'].'">'.$l['managemembers_group'].'</a></th>
            <th style="border-style: solid; border-width: 1px; width: 29%"><a href="'.$cmsurl.'forum.php?action=members'.$page_get.$filter_get.';s=joindate'.$settings['manage_members']['joindate_desc'].'">'.$l['managemembers_join_date'].'</a></th>
          </tr>';
    
    // Show members on this page
    foreach ($members as $member) {
      echo '<tr>
        <td>'.$member['id'].'</td>
        <td><a href="'.$cmsurl.'index.php?action=profile;u='.$member['id'].'">'.$member['display_name'].'</a></td>
        <td>'.$member['groupname'].'</td>
        <td>'.formattime($member['reg_date']).'</td>
      </tr>';
    }
    
    // Show member list footer
    echo '</table>';
    
    pagination($settings['page']['page'],$settings['page']['page_last'],'forum.php?action=members'.$filter_get.$sort_get);
    
    // Show filter
    echo '<form action="'.$cmsurl.'forum.php?action=members" method="post" style="text-align: right; margin-bottom: 0"><p style="display: inline">
       '.$settings['memberlist']['filter'].'
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
  $settings['memberlist']['filter'] = $filter;
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