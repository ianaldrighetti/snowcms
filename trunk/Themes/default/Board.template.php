<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//           Board.template.php

if (!defined('Snow')) 
  die('Hacking Attempt...');
  
function Main() {
global $bperms, $cmsurl, $l, $settings, $user, $theme_url;
  
  echo '
  <h1>'.$settings['page']['board-name'].'</h1>

  <table class="messageindex_panel" width="100%">
    <tr>
      <td style="text-align: left;">', pagination($settings['page']['page'],$settings['page']['page_last'],'forum.php?board='.$_REQUEST['board']), '</td>
      <td style="text-align: right;">'; if (canforum('post_new', $_REQUEST['board'])) { echo '<a href="'. $cmsurl. 'forum.php?action=post;board='. $_REQUEST['board']. '">'.$l['board_newtopic'].'</a>';} echo '</td>
    </tr>
  </table>
  <table width="100%">
    <tr class="title">
      <th width="4%" class="no-border"></th>
      <th width="50%">'.$l['board_subject'].'</th>
      <th width="13%">'.$l['board_creator'].'</th>
      <th width="6%" style="font-size: 70%">'.$l['board_replies'].'</th>
      <th width="5%" style="font-size: 70%">'.$l['board_views'].'</th>
      <th width="22%">'.$l['board_lastpost'].'</th>
    </tr>';
if (count($settings['topics']) > 0) {
  foreach ($settings['topics'] as $topic) {
     echo '
   <tr class="indexcontent">
     <td style="text-align: center; padding: 5px;"><img src="'.$theme_url.'/'.$settings['theme'].'/images/';
     
     if ($topic['is_new'] && $topic['is_own'])
       echo 'topic_own_new.png" alt="'.$l['board_topic_own_new'].'"';
     else if ($topic['is_new'])
       echo 'topic_new.png" alt="'.$l['board_topic_new'].'"';
     else if ($topic['is_own'])
       echo 'topic_own_old.png" alt="'.$l['board_topic_own_old'].'"';
     else
       echo 'topic_old.png" alt="'.$l['board_topic_old'].'"';
     
     echo '/></td>
     <td style="padding: 5px;">
       ';
     if ($topic['locked'])
       echo '<img src="'.$theme_url.'/'.$settings['theme'].'/images/topic_locked.png" style="float: right" />';
     if ($topic['sticky'])
       echo $l['board_sticky'].': <b><a href="'.$cmsurl.'forum.php?topic='.$topic['tid'].'">'.$topic['subject'].'</a></b>';
     else
       echo '<a href="'.$cmsurl.'forum.php?topic='.$topic['tid'].'">'.$topic['subject'].'</a>';
     echo '
     </td>
     <td style="text-align: center; padding: 5px;"><a href="'. $cmsurl. 'index.php?action=profile;u='. $topic['starter_id']. '">'.$topic['username']. '</a></td>
     <td style="text-align: center; padding: 5px;">'. $topic['numReplies']. '</td>
     <td style="text-align: center; padding: 5px;">'. $topic['numViews']. '</td>
     <td style="text-align: center">'.str_replace(
     '%time%','<a href="'.$cmsurl.'forum.php?topic='.$topic['tid'].';msg='.$topic['last_post']['mid'].'">'.
     (strtotime(date('j/t/y')) < $topic['last_post']['time']
     ? formattime($topic['last_post']['time'],3)
     : formattime($topic['last_post']['time'],1)
     ).'</a>',
     str_replace('%user%','<a href="'.$cmsurl.'index.php?action=profile;u='.$topic['last_post']['uid'].'">'.$topic['last_post']['username'].'</a>',
     $l['board_lastpost_data'])).'</td>
   </tr>';
    }
  }
  else
    echo '
    <tr>
      <td colspan="6" style="text-align: center; padding: 10px;">'.$l['board_noposts'].'</td>
    </tr>';
  echo '
  </table>
  <table class="messageindex_panel" width="100%">
    <tr>
      <td style="text-align: left;">', pagination($settings['page']['page'],$settings['page']['page_last'],'forum.php?board='.$_REQUEST['board']), '</td>
      <td style="text-align: right;">'; if(canforum('post_new', $_REQUEST['board'])) { echo '<a href="'. $cmsurl. 'forum.php?action=post;board='. $_REQUEST['board']. '">'.$l['board_newtopic'].'</a>'; } echo '</td>
    </tr>
  </table>';
}

function UnknownBoard() {
global $cmsurl, $l, $settings, $user;
  
  echo '
  <h1>'.$l['board_unknown_header'].'</h1>
  
  <p>'.$l['board_unknown_desc'].'</p>';
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