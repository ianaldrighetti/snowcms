<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//           Topic.template.php

if(!defined('Snow')) 
  die('Hacking Attempt...');
  
function Main() {
global $cmsurl, $theme_url, $l, $settings, $user;
  echo '
  <h1>'.$settings['page']['topic-name'].'</h1>

  <table class="topic_panel">
    <tr>
      <td style="text-align: left;">', pagination($settings['page']['page'],$settings['page']['page_last'],'forum.php?topic='.$settings['topic']), '</td>
      <td style="text-align: right;">';
  if (canforum('move_any', $settings['bid']))
    echo '<a href="'.$cmsurl.'forum.php?action=move;topic='.$settings['topic'].'">'.$l['topic_move'].'</a>';
  if (canforum('move_any', $settings['bid']) && (canforum('sticky_topic', $settings['bid']) || canforum('lock_topic', $settings['bid']) || canforum('post_new', $settings['bid']) || canforum('post_reply', $settings['bid'])))
    echo ' - ';
  if (canforum('sticky_topic', $settings['bid'])) {
    if ($settings['sticky'])
      echo '<a href="'.$cmsurl.'forum.php?action=sticky;topic='.$settings['topic'].';sc='.$user['sc'].'">'.$l['topic_unsticky'].'</a>';
    else
      echo '<a href="'.$cmsurl.'forum.php?action=sticky;topic='.$settings['topic'].';sc='.$user['sc'].'">'.$l['topic_sticky'].'</a>';
  }
  if (canforum('sticky_topic', $settings['bid']) && (canforum('lock_topic', $settings['bid']) || canforum('post_new', $settings['bid']) || canforum('post_reply', $settings['bid'])))
    echo ' - ';
  if (canforum('lock_topic', $settings['bid'])) {
    if ($settings['locked'])
      echo '<a href="'.$cmsurl.'forum.php?action=lock;topic='.$settings['topic'].';sc='.$user['sc'].'">'.$l['topic_unlock'].'</a>';
    else
      echo '<a href="'.$cmsurl.'forum.php?action=lock;topic='.$settings['topic'].';sc='.$user['sc'].'">'.$l['topic_lock'].'</a>';
  }
  if (canforum('lock_topic', $settings['bid']) && (canforum('post_new', $settings['bid']) || canforum('post_reply', $settings['bid']))) echo ' - ';
  if (canforum('post_new', $settings['bid'])) echo '<a href="'.$cmsurl.'forum.php?action=post;board='.$settings['bid'].'">'.$l['topic_newtopic'].'</a>';
  if (canforum('post_new', $settings['bid']) && canforum('post_reply', $settings['bid'])) echo ' - ';
  if (canforum('post_reply', $settings['bid'])) echo '<a href="'.$cmsurl.'forum.php?action=post;topic='.$settings['topic'].'">'.$l['topic_reply'].'</a>';
  echo '</td>
    </tr>
  </table>
  ';
  foreach($settings['posts'] as $post) {
  echo '
    <a name="mid'.$post['mid'].'"></a>
    <div id="post-container">
    <div id="post-left">
      <p><a href="'.$cmsurl.'index.php?action=profile;u='.$post['uid'].'">'.$post['username'].'</a></p>
      <p>'.$post['membergroup'].'</p>';
  if ($post['avatar'])
    echo '<p><img src="'.$post['avatar'].'" alt="'.str_replace('%user%',$post['username'],$l['topic_avatar']).'" /></p>';
  echo '<br />
      <p>
        ', $l['topic_posts'], ' ',$post['numposts'], '
      </p>
      <br />
      <table style="display: inline">
        <tr><td>
      '.($post['status']
          ? '<img src="'.$theme_url.'/'.$settings['theme'].'/images/status_online.png"
              alt="'.$l['topic_online'].'" width="16" height="16" /></td>
            <td>'.$l['topic_online']
          : '<img src="'.$theme_url.'/'.$settings['theme'].'/images/status_offline.png"
              alt="'.$l['topic_offline'].'" width="16" height="16" /></td>
            <td>'.$l['topic_offline'])
      .'
        </td></tr>
      </table>
    </div>
    <div id="post-info">
      <div class="image">
        ', $user['is_logged'] ? '<a href="'. $cmsurl. 'forum.php?action=post;topic='. $post['tid']. ';quote='. $post['mid']. '" title="'. $l['topic_quote']. '"><img src="'. $theme_url.'/'.$settings['theme'].'/images/quote.png" alt="'. $l['topic_quote']. '"/></a>' : '', ' ', $post['can']['edit'] ? '<a href="'. $cmsurl. 'forum.php?action=post;topic='.$post['tid'].';edit='. $post['mid']. '" title="'. $l['topic_editpost']. '"><img src="'. $theme_url.'/'.$settings['theme'].'/images/edit_post.png" alt="'. $l['topic_editpost'] .'"/></a>' : '', ' ', $post['can']['del'] ? '<a href="'. $cmsurl. 'forum.php?action=delete;topic='.$post['tid'].';msg='. $post['mid']. ';sc='.$user['sc'].'" onClick="return confirm(\''. $l['topic_delconfirm']. '\')" title="'. $l['topic_deletemsg']. '"><img src="'. $theme_url. '/'. $settings['theme']. '/images/delete.png" alt="'. $l['topic_deletemsg']. '"/></a>' : '', ' ', $post['can']['split'] ? '<a href="'. $cmsurl. 'forum.php?action=split;msg='. $post['mid']. ';sc='. $user['sc']. '" title="'. $l['topic_split']. '"><img src="'. $theme_url. '/'.$settings['theme'].'/images/split.png" alt="'. $l['topic_split']. '"/></a>' : '', '
      </div>
      <div class="text">
        '.str_replace('%subject%','<a href="'.$cmsurl.'forum.php?topic='.$post['tid'].';msg='.$post['mid'].'">'.$post['subject'].'</a>',str_replace('%time%',$post['post_time'],$l['topic_header'])).'</p>',
      '</div>
    </div>
    <div id="post-right">
      <div class="post-content" id="pcmid'.$post['mid'].'">
        <div><p>
          '.$post['body'].'
        </p></div>
      ';
  if ($post['can']['edit'])
    echo '<p style="font-size: x-small; padding-top: 0px; text-align: right; margin-bottom: 0"><a href="#" onClick="quickEdit('.$post['tid'].','.$post['mid'].');return false;" title="'. $l['topic_editpost']. '"><img src="'. $theme_url.'/'.$settings['theme'].'/images/edit_post.png" alt="'. $l['topic_editpost'] .'"/></a></p>';
    
  if ($post['uid_editor'])
    echo '  <p style="font-size: x-small; padding-top: 5px; margin-bottom: 0">
          <i>'.str_replace('%user%','<a href="'.$cmsurl.'index.php?action=profile;u='.$post['uid_editor'].'">'.$post['editor_name'].'</a>',
              str_replace('%time%',formattime($post['edit_time'],2),$l['topic_edited'])).'</i>
        </p>';
       
  echo '</div>';
      if (!is_null($post['signature']))
      echo '
      <div id="user-sig">
        <hr />
        <p>'.$post['signature'].'</p>
      </div>';
  echo '
    </div>
    <div class="break">
    </div>
  <br />
  </div>';
  }
  echo '
  <div class="topic-page-end">
  </div>
  <table class="topic_panel">
    <tr>
      <td style="text-align: left;">', pagination($settings['page']['page'],$settings['page']['page_last'],'forum.php?topic='.$settings['topic']), '</td>
      <td style="text-align: right;">';
  if (canforum('move_any', $settings['bid']))
    echo '<a href="'.$cmsurl.'forum.php?action=move;topic='.$settings['topic'].'">'.$l['topic_move'].'</a> - ';
  if (canforum('sticky_topic', $settings['bid'])) {
    if ($settings['sticky'])
      echo '<a href="'.$cmsurl.'forum.php?action=sticky;topic='.$settings['topic'].';sc='.$user['sc'].'">'.$l['topic_unsticky'].'</a>';
    else
      echo '<a href="'.$cmsurl.'forum.php?action=sticky;topic='.$settings['topic'].';sc='.$user['sc'].'">'.$l['topic_sticky'].'</a>';
  }
  if (canforum('sticky_topic', $settings['bid']) && (canforum('lock_topic', $settings['bid']) || canforum('post_new', $settings['bid']) || canforum('post_reply', $settings['bid']))) echo ' - ';
  if (canforum('lock_topic', $settings['bid'])) {
    if ($settings['locked'])
      echo '<a href="'.$cmsurl.'forum.php?action=lock;topic='.$settings['topic'].';sc='.$user['sc'].'">'.$l['topic_unlock'].'</a>';
    else
      echo '<a href="'.$cmsurl.'forum.php?action=lock;topic='.$settings['topic'].';sc='.$user['sc'].'">'.$l['topic_lock'].'</a>';
  }
  if (canforum('lock_topic', $settings['bid']) && (canforum('post_new', $settings['bid']) || canforum('post_reply', $settings['bid']))) echo ' - ';
  if (canforum('post_new', $settings['bid'])) echo '<a href="'.$cmsurl.'forum.php?action=post;board='.$settings['bid'].'">'.$l['topic_newtopic'].'</a>';
  if (canforum('post_new', $settings['bid']) && canforum('post_reply', $settings['bid'])) echo ' - ';
  if (canforum('post_reply', $settings['bid'])) echo '<a href="'.$cmsurl.'forum.php?action=post;topic='.$settings['topic'].'">'.$l['topic_reply'].'</a>';
  echo '</td>
    </tr>
  </table>
  <br />
  ';
  if (canforum('post_reply', $settings['bid'])) echo '
  <p><b>Quick Reply:</b></p>
  <form action="', $cmsurl, 'forum.php?action=post2;topic=', $settings['topic'], '" method="post" class="write">
  <p><textarea class="quickreply" name="body"></textarea></p>
  <p style="text-align: center"><input type="submit" value="'.$l['topic_reply'].'" /></p>
  </form>
  ';
}

function UnknownTopic() {
global $cmsurl, $l, $settings, $user;
  
  echo '
  <h1>'.$l['topic_unknown_header'].'</h1>
  
  <p>'.$l['topic_unknown_desc'].'</p>
  ';
}

function MoveTopic() {
global $l, $settings, $cmsurl;
  
  echo '
  <h1>'.$l['topic_move_header'].'</h1>
  
  ';
  
  if (empty($_SESSION['error']))
    echo '<p>'.str_replace('%topic%','<b>'.$settings['page']['topic']['subject'].'</b>',$l['topic_move_desc']).'</p>';
  else
    echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  
  echo '
  <form action="" method="post" style="display: inline">
    <p>
      <input type="hidden" name="move-topic" value="true" />
    </p>
    <table>
      <tr>
        <td>'.$l['topic_move_board'].':</td>
        <td>
          <select name="board">
          ';
  
  foreach ($settings['page']['boards'] as $board)
    echo '  <option value="'.$board['bid'].'">'.$board['name'].'</option>
       ';
  
  echo '</select>
        </td>
      </tr>
      <tr>
        <td>'.$l['topic_move_subject'].':</td>
        <td><input name="subject" value="'.$l['topic_move_moved'].': '.$settings['page']['topic']['subject'].'" /></td>
      </tr>
    </table>
    <p>
      <textarea name="body" cols="93" rows="12" style="width: 100%">'.
      str_replace('%url%',$cmsurl.'forum.php?topic='.$settings['page']['topic']['tid'],
      str_replace('%subject%',$settings['page']['topic']['subject'],
      $l['topic_move_message'])).'</textarea>
    </p>
    <p style="display: inline">
      <input type="submit" value="'.$l['topic_move_submit'].'" />
    </p>
  </form>
  <form action="'.$cmsurl.'forum.php?topic='.$settings['page']['topic']['tid'].'" method="post" style="display: inline">
    <p style="display: inline">
      <input type="submit" value="'.$l['main_cancel'].'" />
    </p>
  </form>
  <br />
  <br />
  ';
}

function MoveNoBoards() {
global $cmsurl, $l, $settings, $user;
  
  echo '
  <h1>'.$l['topic_move_noboards_header'].'</h1>
  
  <p>'.$l['topic_move_noboards_desc'].'</p>
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