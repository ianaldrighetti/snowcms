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
    <table class="memberlist"><tr><td>
    
    <h1>'.$l['memberlist_header'].'</h1>
    ';
  
  if (@$_SESSION['error'])
    echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  
  $first_member = $settings['page']['first_member'];
  $last_member = $settings['page']['last_member'];
  $members = $settings['page']['members'];
  $prev_page = $settings['page']['previous_page'];
  $page = $settings['page']['current_page'];
  $next_page = $settings['page']['next_page'];
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
    echo '<p>'.str_replace("%from%",$first_member,str_replace("%to%",$last_member,$l['memberlist_showing'])).'</p>';
  else
    echo '<p>'.str_replace("%number%",$first_member,$l['memberlist_showing_one']).'</p>';
  
  if ($total_members) {
    // Show filter
    echo '<form action="'.$cmsurl.'forum.php?action=members" method="post" style="float: right; margin-bottom: 0"><p style="display: inline">
       '.$settings['memberlist']['filter'].'
        </p></form>
        ';
    
    // Show the pervious page link if it is at least page two
    if ($prev_page > 0)
      echo '<table width="100%">
        <tr><td><a href="'.$cmsurl.'forum.php?action=members;pg='.$prev_page.$filter_get.$sort_get.'">'.$l['memberlist_previous_page'].'</a></td>
        ';
    // Show the previous page link if it is page one
    elseif ($prev_page == 0)
      echo '<table width="100%">
        <tr><td><a href="'.$cmsurl.'forum.php?action=members'.$filter_get.$sort_get.'">'.$l['memberlist_previous_page'].'</a></td>
        ';
    // Don't show the previous page link, because it is the first page
    else
      echo '<table width="100%">
        <tr><td></td>
        ';
    // Show the next page link
    if (@($total_members / $settings['manage_members_per_page']) > $next_page)
      echo '<td style="text-align: right"><a href="'.$cmsurl.'forum.php?action=members;pg='.$next_page.$filter_get.$sort_get.'">'.$l['memberlist_next_page'].'</a></td></tr>
        </table>
        ';
    // Don't show the next page link, because it is the last page
    else
      echo '<td style="text-align: right"></td></tr>
        </table>
        ';
    
    // Show member list header
    echo '<table style="width: 100%; text-align: center">
          <tr>
            <th style="border-style: solid; border-width: 1px; width: 11%"><a href="'.$cmsurl.'forum.php?action=members'.$page_get.$filter_get.';s=id'.$settings['manage_members']['id_desc'].'">'.$l['memberlist_id'].'</a></th>
            <th style="border-style: solid; border-width: 1px; width: 29%"><a href="'.$cmsurl.'forum.php?action=members'.$page_get.$filter_get.';s=username'.$settings['manage_members']['username_desc'].'">'.$l['memberlist_username'].'</a></th>
            <th style="border-style: solid; border-width: 1px; width: 28%"><a href="'.$cmsurl.'forum.php?action=members'.$page_get.$filter_get.';s=group'.$settings['manage_members']['group_desc'].'">'.$l['memberlist_group'].'</a></th>
            <th style="border-style: solid; border-width: 1px; width: 29%"><a href="'.$cmsurl.'forum.php?action=members'.$page_get.$filter_get.';s=joindate'.$settings['manage_members']['joindate_desc'].'">'.$l['memberlist_join_date'].'</a></th>
          </tr>';
    
    // Show members on this page
    foreach ($members as $member) {
      echo '<tr>
        <td>'.$member['id'].'</td>
        <td><a href="'.$cmsurl.'forum.php?action=profile;u='.$member['id'].'">'.($member['display_name'] ? $member['display_name'] : $member['username']).'</a></td>
        <td>'.$member['groupname'].'</td><td>'.date($settings['dateformat'],$member['reg_date']).'</td>
      </tr>';
    }
    
    // Show member list footer
    echo '</table>';
    
    // Show the pervious page link if it is at least page two
    if ($prev_page > 0)
      echo '<table width="100%">
        <tr><td><a href="'.$cmsurl.'forum.php?action=members;pg='.$prev_page.$filter_get.$sort_get.'">'.$l['memberlist_previous_page'].'</a></td>
        ';
    // Show the previous page link if it is page one
    elseif ($prev_page == 0)
      echo '<table width="100%">
        <tr><td><a href="'.$cmsurl.'forum.php?action=members'.$filter_get.$sort_get.'">'.$l['memberlist_previous_page'].'</a></td>
        ';
    // Don't show the previous page link, because it is the first page
    else
      echo '<table width="100%">
        <tr><td></td>
        ';
    // Show the next page link
    if (@($total_members / $settings['manage_members_per_page']) > $next_page)
      echo '<td style="text-align: right"><a href="'.$cmsurl.'forum.php?action=members;pg='.$next_page.$filter_get.$sort_get.'">'.$l['memberlist_next_page'].'</a></td></tr>
        </table>
        ';
    // Don't show the next page link, because it is the last page
    else
      echo '<td style="text-align: right"></td></tr>
        </table>
        ';
    
    // Show filter
    echo '<form action="'.$cmsurl.'forum.php" method="post" style="float: right; margin-bottom: 0"><p style="display: inline">
       '.$settings['memberlist']['filter'].'
       </p></form>';
  }
  
  echo '
  </td></tr></table>';
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

?>