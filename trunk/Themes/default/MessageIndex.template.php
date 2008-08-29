<?php
// MessageIndex.template.php by the SnowCMS Dev's

if(!defined('Snow')) 
  die('Hacking Attempt...');
  
function Main() {
global $bperms, $cmsurl, $l, $settings, $user, $theme_url;
echo '
<table id="messageindex_panel">
  <tr>
    <td style="text-align: left;">Pages: [1]</td>
    <td style="text-align: right;">'; if(canforum('post_new', $_REQUEST['board'])) { echo '<a href="'. $cmsurl. 'forum.php?action=post;board='. $_REQUEST['board']. '">New Topic</a>'; } echo '</td>
  </tr>
</table>
<div id="messageindex">
  <table width="800px">
    <tr id="title">
      <td width="5%" style="padding: 5px;">&nbsp;</td>
      <td width="50%" style="padding: 5px;">Subject</td>
      <td width="15%" style="padding: 5px;">Started By</td>
      <td width="5%" style="padding: 5px;">Replies</td>
      <td width="5%" style="padding: 5px;">Views</td>
      <td width="20%" style="padding: 5px;">Last Post</td>
    </tr>';
  if(count($settings['topics'])>0) {
    foreach($settings['topics'] as $topic) {
       echo '
       <tr class="indexcontent">
         <td style="text-align: center; padding: 5px;"><img src="'.$theme_url.'/'.$settings['theme'].'/images/';
         
         if ($topic['is_new'] && $topic['is_own'])
           echo 'topic_own_new.png" alt="'.$l['forum_topic_own_new'].'"';
         else if ($topic['is_new'])
           echo 'topic_new.png" alt="'.$l['forum_topic_new'].'"';
         else if ($topic['is_own'])
           echo 'topic_own_old.png" alt="'.$l['forum_topic_own_old'].'"';
         else
           echo 'topic_old.png" alt="'.$l['forum_topic_old'].'"';
         
  echo '/></td>
         <td style="padding: 5px;"><a href="'. $cmsurl. 'forum.php?topic='. $topic['tid']. '">'. $topic['subject']. '</a></td>
         <td style="text-align: center; padding: 5px;"><a href="'. $cmsurl. 'index.php?action=profile;u='. $topic['starter_id']. '">'.$topic['username']. '</a></td>
         <td style="text-align: center; padding: 5px;">'. $topic['numReplies']. '</td>
         <td style="text-align: center; padding: 5px;">'. $topic['numViews']. '</td>
         <td style="padding: 5px;">Last Post info :o</td>
       </tr>';
    }
  }
  else {
      echo '
      <tr>
        <td colspan="6" style="text-align: center; padding: 10px;">No Posts</td>
      </tr>';
  }
echo '
  </table>
</div>';
}
?>