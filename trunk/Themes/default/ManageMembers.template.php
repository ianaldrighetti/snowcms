<?php

function Main() {
global $l, $db_prefix, $settings, $cmsurl;
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
      echo '<p style="float: right"><a href="'.$cmsurl.'index.php?action=admin&sa=members&pg='.$next_page.'">'.$l['managemembers_next_page'].'</a></p>';
    else
      echo '<p style="float: right">&nbsp;</p>';
    if ($prev_page > 0)
      echo '<p><a href="'.$cmsurl.'index.php?action=admin&sa=members&pg='.$prev_page.'">'.$l['managemembers_previous_page'].'</a></p>';
    elseif ($prev_page == 0)
      echo '<p><a href="'.$cmsurl.'index.php?action=admin&sa=members">'.$l['managemembers_previous_page'].'</a></p>';
    
    // Show members
    echo '<table style="width: 100%; text-align: center">
          <tr><th style="border-style: solid; border-width: 1px; width: 11%">'.$l['managemembers_id'].'</th><th style="border-style: solid; border-width: 1px; width: 30%">'.$l['managemembers_screen_name'].'</th><th style="border-style: solid; border-width: 1px; width: 30%">'.$l['managemembers_group'].'</th><th style="border-style: solid; border-width: 1px; width: 30%">'.$l['managemembers_join_date'].'</th></tr>';
    $i = 0;
    while (($row = mysql_fetch_assoc($member_rows)) && $i < $page_end - ($page_start - 1)) {
      echo '<tr><td>'.$row['id'].'</td><td><a href="'.$cmsurl.'index.php?action=profile&u='.$row['id'].'">'.$row['username'].'</a></td><td>'.$row['groupname'].'</td><td>'.date($settings['timeformat'],$row['reg_date']).'</td></tr>';
      $i += 1;
    }
    echo '</table>';
    
    // Show next and previous page links
    if ($total_members > $page_end)
      echo '<p style="float: right"><a href="'.$cmsurl.'index.php?action=admin&sa=members&pg='.$next_page.'">'.$l['managemembers_next_page'].'</a></p>';
    else
      echo '<p style="float: right">&nbsp;</p>';
    if ($prev_page > 0)
      echo '<p><a href="'.$cmsurl.'index.php?action=admin&sa=members&pg='.$prev_page.'">'.$l['managemembers_previous_page'].'</a></p>';
    elseif ($prev_page == 0)
      echo '<p><a href="'.$cmsurl.'index.php?action=admin&sa=members">'.$l['managemembers_previous_page'].'</a></p>';
  }
}

function NoMembers() {
global $l;
  echo '
        <h1>'.$l['managemembers_title'].'</h1>
        <p>'.$l['managemembers_showing_none'].'</p>';
}

?>