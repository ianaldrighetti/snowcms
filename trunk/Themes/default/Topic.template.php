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
if(canforum('post_new', $settings['bid'])) { echo '<a href="'.$cmsurl.'forum.php?action=post;board='.$settings['bid'].'">'.$l['topic_newtopic'].'</a>'; }
if (canforum('post_new', $settings['bid']) || canforum('post_reply', $settings['bid'])) echo ' - ';
if(canforum('post_reply', $settings['bid'])) { echo '<a href="'.$cmsurl.'forum.php?action=post;topic='.$_REQUEST['topic'].'">'.$l['topic_reply'].'</a>'; } echo '</td>
  </tr>
</table>
';
foreach($settings['posts'] as $post) {
echo '  
  <a name="mid'.$post['mid'].'"></a>
  <div id="post-container">
  <div id="post-left">
    <p><a href="'.$cmsurl.'index.php?action=profile;u='.$post['uid'].'">'.$post['username'].'</a></p>
    <p>'.$post['membergroup'].'</p>
    <br />
    <p>
    ', $l['topic_status'], ' ', $post['status'] ? '<img src="'. $theme_url.'/'.$settings['theme'].'/images/status_online.png" alt=""/>' : '', '<br />
    ', $l['topic_posts'], ' ',$post['numposts'], '
    </p>
  </div>
  <div id="post-right">
    <div id="post-info">
      <p style="float: left;"><a href="'.$cmsurl.'forum.php?topic='.$post['tid'].';msg='.$post['mid'].'">'.$post['subject'].'</a> '.$l['topic_on'].' '.$post['post_time'].'</p><p style="float: right;">', $user['is_logged'] ? '<a href="'. $cmsurl. 'forum.php?action=post;topic='. $post['tid']. ';quote='. $post['mid']. '" title="'. $l['topic_quote']. '"><img src="'. $theme_url.'/'.$settings['theme'].'/images/quote.png" alt="'. $l['topic_quote']. '"/></a>' : '', ' ', $post['can']['edit'] ? '<a href="'. $cmsurl. 'forum.php?action=post;topic='.$post['tid'].';edit='. $post['mid']. '" title="'. $l['topic_editpost']. '"><img src="'. $theme_url.'/'.$settings['theme'].'/images/edit_post.png" alt="'. $l['topic_editpost'] .'"/></a>' : '', ' ', $post['can']['del'] ? '<a href="'. $cmsurl. 'forum.php?action=delete;topic='.$post['tid'].';msg='. $post['mid']. ';sc='.$user['sc'].'" onClick="return confirm(\''. $l['topic_delconfirm']. '\')" title="'. $l['topic_deletemsg']. '"><img src="'. $theme_url. '/'. $settings['theme']. '/images/delete.png" alt="'. $l['topic_deletemsg']. '"/></a>' : '', ' ', $post['can']['split'] ? '<a href="'. $cmsurl. 'forum.php?action=split;msg='. $post['mid']. ';sc='. $user['sc']. '" title="'. $l['topic_split']. '"><img src="'. $theme_url. '/'.$settings['theme'].'/images/split.png" alt="'. $l['topic_split']. '"/></a>' : '', '</p>
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
}
?>