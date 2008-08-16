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
          <tr><th style="border-style: solid; border-width: 1px; width: 11%"><a href="'.$cmsurl.'index.php?action=admin&sa=members'.$settings['manage_members']['page_get'].'&sort=id'.$settings['manage_members']['id_desc'].'">'.$l['managemembers_id'].'</a></th><th style="border-style: solid; border-width: 1px; width: 29%"><a href="'.$cmsurl.'index.php?action=admin&sa=members'.$settings['manage_members']['page_get'].'&sort=screenname'.$settings['manage_members']['screenname_desc'].'">'.$l['managemembers_screen_name'].'</a></th><th style="border-style: solid; border-width: 1px; width: 28%"><a href="'.$cmsurl.'index.php?action=admin&sa=members'.$settings['manage_members']['page_get'].'&sort=group'.$settings['manage_members']['group_desc'].'">'.$l['managemembers_group'].'</a></th><th style="border-style: solid; border-width: 1px; width: 29%"><a href="'.$cmsurl.'index.php?action=admin&sa=members'.$settings['manage_members']['page_get'].'&sort=joindate'.$settings['manage_members']['joindate_desc'].'">'.$l['managemembers_join_date'].'</a></th><th width="6%"></th></tr>';
    $i = 0;
    while (($row = mysql_fetch_assoc($member_rows)) && $i < $page_end - ($page_start - 1)) {
      echo '<tr><td>'.$row['id'].'</td><td><a href="'.$cmsurl.'index.php?action=profile&u='.$row['id'].'">'.$row['username'].'</a></td><td>'.$row['groupname'].'</td><td>'.date($settings['timeformat'],$row['reg_date']).'</td><td><a href="'.$cmsurl.'index.php?action=admin&sa=moderate&u='.$row['id'].'"><img src="'.$theme_url.'/'.$settings['theme'].'/moderate.png" alt="'.$l['managemembers_moderate'].'" width="12" height="12" style="border: 0" /></a></td></tr>';
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
        <p>'.$l['managemembers_showing_none'].'</p>';
}

?>