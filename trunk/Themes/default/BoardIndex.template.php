<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//         BoardIndex.template.php

if(!defined('Snow'))
  die('Hacking Attempt...');

// This constructs the board index of the forum, which shows categories, boards
// and information about the boards, like the description, number of posts and topics
// and the last post information, if any
function Main() {
global $cmsurl, $theme_url, $l, $settings, $user;
  
  echo '
  <h1>'.$l['forum_header'].'</h1>
  ';
  
  foreach($settings['forum']['cats'] as $cat) {
    echo '
    <h2><a name="'.$cat['id'].'"></a>'.$cat['name'].'</h2>';
    
    foreach($cat['boards'] as $board) {
      echo '
      <div id="board">
        <table width="100%">
          <tr>
            <td width="5%" style="text-align: center">
              <img src="'.$theme_url.'/'.$settings['theme'].'/images/';
              if ($board['is_new'])
                echo 'board_new.png" alt="'.$l['forum_board_new'].'"';
              else
                echo 'board_old.png" alt="'.$l['forum_board_old'].'"';
              echo ' width="30" height="30" />
            </td>
            <td width="55%"><p><a href="'.$cmsurl.'forum.php?board='.$board['id'].'">'.$board['name'].'</a><br />
              '.$board['desc'].'</p>
            </td>
            <td width="10%">
              '.$board['posts'].'&nbsp;'.$l['forum_board_posts'].'
              <br />
              '.$board['topics'].'&nbsp;'.$l['forum_board_topics'].'
            </td>
            <td width="30%" style="text-align: center">'.
            ($board['last_post']['tid'] ? str_replace(
             '%msg%','<a href="'.$cmsurl.'forum.php?topic='.$board['last_post']['tid'].';msg='.$board['last_post']['mid'].'">'.
            (strlen($board['last_post']['subject']) > 30
            ? substr($board['last_post']['subject'],0,27).'...'
            : $board['last_post']['subject']
            ).'</a>',
             str_replace('%user%','<a href="'.$cmsurl.'index.php?action=profile;u='.$board['last_post']['uid'].'">'.$board['last_post']['username'].'</a>',$l['forum_board_lastpost'])) : '')
         .'</td>
          </tr>
        </table>
      </div>';
    }
    
    echo '
    <div class="break">
    </div>';
  }
}

function NotAllowed() {
global $l;
  
  echo '
  <h1>'.$l['forum_notallowed_header'].'</h1>
  
  <p>'.$l['forum_notallowed_desc'].'</p>
  ';
}
?>