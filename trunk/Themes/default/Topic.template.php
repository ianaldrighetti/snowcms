<?php
// MessageIndex.template.php by the SnowCMS Dev's

if(!defined('Snow')) 
  die('Hacking Attempt...');
  
function Main() {
global $cmsurl, $l, $settings, $user;
echo '
<table id="topic_panel">
  <tr>
    <td style="text-align: left;">Pages: [1]</td>
    <td style="text-align: right;">'; if(canforum('post_new', $settings['bid'])) { echo '<a href="'.$cmsurl.'forum.php?action=post;board='.$settings['bid'].'">'.$l['topic_newtopic'].'</a>'; } if(canforum('post_reply', $settings['bid'])) { echo '<a href="'.$cmsurl.'forum.php?action=post;topic='.$_REQUEST['topic'].'">'.$l['topic_reply'].'</a>'; } echo '</td>
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
    <br /><br />
    <p>Posts: '.$post['numposts'].'</p>
  </div>
  <div id="post-right">
    <div id="post-info">
      <p><a href="'.$cmsurl.'forum.php?topic='.$post['tid'].'">'.$post['subject'].'</a> '.$l['topic_on'].' '.$post['post_time'].'</p>
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