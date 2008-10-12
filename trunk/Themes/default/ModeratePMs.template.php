<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//        ModeratePMs.template.php

if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $l, $settings, $user, $cmsurl, $theme_url;
  
  echo '
    <h1>'.$l['moderatepms_header'].'</h1>
    ';
  
  if (@$_SESSION['error'])
    echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  
  $first_pm = $settings['page']['first_pm'];
  $last_pm = $settings['page']['last_pm'];
  $pms = $settings['page']['pms'];
  $total_pms = $settings['page']['total_pms'];
  if ($page_get = $settings['page']['page_get'])
    $page_get = ';pg='.$page_get;
  if ($page_get = $settings['page']['page_get'])
    $page_get = ';pg='.$page_get;
  if ($sort_get = $settings['page']['sort_get'])
    $sort_get = ';s='.$sort_get;
  
  if ($first_pm != $last_pm)
    echo '<p>'.str_replace("%from%",$first_pm,str_replace("%to%",$last_pm,$l['moderatepms_showing'])).'</p>';
  else
    echo '<p>'.str_replace("%number%",$first_pm,$l['moderatepms_showing_one']).'</p>';
  
  if ($total_pms) {
    pagination($settings['page']['page'],$settings['page']['page_last'],'index.php?action=admin;sa=pms'.$sort_get);
    
    // Show member list header
    echo '<table style="width: 100%; text-align: center">
          <tr>
            <th style="width: 20%"><a href="'.$cmsurl.'index.php?action=admin;sa=pms'.$page_get.';s=to'.$settings['page']['to_desc'].'">'.$l['moderatepms_to'].'</a></th>
            <th style="width: 20%"><a href="'.$cmsurl.'index.php?action=admin;sa=pms'.$page_get.';s=from'.$settings['page']['from_desc'].'">'.$l['moderatepms_from'].'</a></th>
            <th style="width: 28%"><a href="'.$cmsurl.'index.php?action=admin;sa=pms'.$page_get.';s=subject'.$settings['page']['subject_desc'].'">'.$l['moderatepms_subject'].'</a></th>
            <th style="width: 25%"><a href="'.$cmsurl.'index.php?action=admin;sa=pms'.$page_get.';s=sentdate'.$settings['page']['sentdate_desc'].'">'.$l['moderatepms_date_sent'].'</a></th>
            <th class="no-border" style="width: 7%"></th>
          </tr>';
    
    // Show members on this page
    foreach ($pms as $pm) {
      echo '<tr>
        <td><a href="'.$cmsurl.'index.php?action=profile;u='.$pm['to_id'].'">'.$pm['to'].'</a></td>
        <td><a href="'.$cmsurl.'index.php?action=profile;u='.$pm['from_id'].'">'.$pm['from'].'</a></td>
        <td><a href="'.$cmsurl.'index.php?action=admin;sa=pms;pm='.$pm['id'].'">'.$pm['subject'].'</a></td>
        <td>'.formattime($pm['date_sent']).'</td>
        <td>
          <a href="'.$cmsurl.'index.php?action=admin;sa=pms;pm='.$pm['id'].';clear=true;sc='.$user['sc'].'" onclick="return confirm(\''.$l['moderatepms_clear_areyousure'].'\')"><img src="'.$theme_url.'/'.$settings['theme'].'/images/okay.png" alt="'.$l['moderatepms_clear'].'" /></a>
          <a href="'.$cmsurl.'index.php?action=admin;sa=pms;pm='.$pm['id'].';delete=true;sc='.$user['sc'].'" onclick="return confirm(\''.$l['moderatepms_delete_areyousure'].'\')"><img src="'.$theme_url.'/'.$settings['theme'].'/images/delete.png" alt="'.$l['moderatepms_delete'].'" /></a>
        </td>
      </tr>';
    }
    
    // Show member list footer
    echo '</table>';
    
    pagination($settings['page']['page'],$settings['page']['page_last'],'index.php?action=admin;sa=pms'.$sort_get);
  }
}

function NoPMs() {
global $l, $settings, $cmsurl;
  
  echo '
  <h1>'.$l['moderatepms_header'].'</h1>
  
  <p><br /></p>
  
  <p><b>[1]</b></p>
  
  <p style="text-align: center">'.$l['moderatepms_showing_none'].'</p>
  
  <p><b>[1]</b></p>';
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
          <input type="hidden" name="clear" value="true" />
          <input type="submit" value="'.$l['moderatepms_message_clear'].'"
           onclick="return confirm(\''.$l['moderatepms_message_clear_areyousure'].'\')" />
        </p>
      </form>
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