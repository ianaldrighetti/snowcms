<?php
// MessageIndex.template.php by the SnowCMS Dev's

if(!defined('Snow')) 
  die('Hacking Attempt...');
  
function Main() {
global $cmsurl, $theme_url, $l, $settings, $user;
echo '
<table id="topic_panel">
  <tr>
    <td style="text-align: left;">Pages: ', $settings['pagination'], '</td>
    <td style="text-align: right;">';
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
  <div id="post-right">
    <div id="post-info">
      <p style="float: left;">'.str_replace('%subject%','<a href="'.$cmsurl.'forum.php?topic='.$post['tid'].';msg='.$post['mid'].'">'.$post['subject'].'</a>',str_replace('%time%',$post['post_time'],$l['topic_header'])).'</p><p style="float: right;">', $user['is_logged'] ? '<a href="'. $cmsurl. 'forum.php?action=post;topic='. $post['tid']. ';quote='. $post['mid']. '" title="'. $l['topic_quote']. '"><img src="'. $theme_url.'/'.$settings['theme'].'/images/quote.png" alt="'. $l['topic_quote']. '"/></a>' : '', ' ', $post['can']['edit'] ? '<a href="'. $cmsurl. 'forum.php?action=post;topic='.$post['tid'].';edit='. $post['mid']. '" title="'. $l['topic_editpost']. '"><img src="'. $theme_url.'/'.$settings['theme'].'/images/edit_post.png" alt="'. $l['topic_editpost'] .'"/></a>' : '', ' ', $post['can']['del'] ? '<a href="'. $cmsurl. 'forum.php?action=delete;topic='.$post['tid'].';msg='. $post['mid']. ';sc='.$user['sc'].'" onClick="return confirm(\''. $l['topic_delconfirm']. '\')" title="'. $l['topic_deletemsg']. '"><img src="'. $theme_url. '/'. $settings['theme']. '/images/delete.png" alt="'. $l['topic_deletemsg']. '"/></a>' : '', ' ', $post['can']['split'] ? '<a href="'. $cmsurl. 'forum.php?action=split;msg='. $post['mid']. ';sc='. $user['sc']. '" title="'. $l['topic_split']. '"><img src="'. $theme_url. '/'.$settings['theme'].'/images/split.png" alt="'. $l['topic_split']. '"/></a>' : '', '</p>
      <div class="break">
      </div>
    </div>
    <div id="post-content">
      <p>'.$post['body'].'</p>
    </div>';
    if(!is_null($post['signature']))
    echo '
    <div id="user-sig">
      <p>'.$post['signature'].'</p>
    </div>';
echo '
  </div>
  <div class="break">
  </div>
</div>';
}
echo '
<table id="topic_panel">
  <tr>
    <td style="text-align: left;">Pages: ', $settings['pagination'], '</td>
    <td style="text-align: right;">';
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
echo '
<div id="post-container">
<div id="post-left">
'.$l['forum_quickreply'].'
</div>
<div id="post-right">
<form action="', $cmsurl, 'forum.php?action=post2;topic=', $settings['topic'], '" method="post" class="write">
<textarea class="quickreply" name="body"></textarea>
<input type="submit" value="'. $l['topic_post_button'].'">
</form>
</div>
  <div class="break">
  </div>
</div>
';
}
?>