<?php
// BoardIndex.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die('Hacking Attempt...');

// This constructs the Board Index of the forum, which shows categories, boards
// and information about the boards, like the description, number of posts and topics
// and the last post information, if any
function Main() {
global $cmsurl, $theme_url, $l, $settings, $user;
  foreach($settings['forum']['cats'] as $cat) {
    echo '
    <div id="category">
      <p class="title"><a name="'.$cat['id'].'">'.$cat['name'].'</a></p>';
      foreach($cat['boards'] as $board) {
        echo '
        <div id="board">
          <p class="title">&nbsp;</p>
          <table width="100%">
            <tr>
              <td width="10%"><img src="'.$theme_url.'/'.$settings['theme'].'/images/'; if($board['is_new']) { echo 'on.gif" alt="'.$l['forum_board_new'].'"'; } else { echo 'off.gif" alt="'.$l['forum_board_old'].'"'; } echo '/></td>
              <td width="54%"><p><a href="'.$cmsurl.'forum.php?board='.$board['id'].'">'.$board['name'].'</a><br />
                  '.$board['desc'].'</p>
              </td>
              <td width="16%">'.str_replace('%posts%',$board['posts'],str_replace('%topics%',$board['topics'],$l['forum_board_stats'])).'</td>
              <td valign="middle" width="20%">'.str_replace(
         '%msg%','<a href="'.$cmsurl.'forum.php?topic='.$board['last_post']['tid'].';msg='.$board['last_post']['mid'].'">'.$board['last_post']['subject'].'</a>',
         str_replace('%user%','<a href="'.$cmsurl.'index.php?action=profile;u='.$board['last_post']['uid'].'">'.$board['last_post']['username'].'</a>',
         $l['forum_last_post'])).'</td>
            </tr>
          </table>
        </div>';
      }
    echo '
    <div class="break">
    </div>
    </div>';
  }
}
?>