<?php
// default/PersonalMessages.template.php by SnowCMS Dev's
if(!defined('Snow'))
  die("Hacking Attempt...");

function Inbox() {
global $l, $settings, $cmsurl;
  
  echo '
  <table class="pm"><tr><td>
  
  <h1>'.$l['pm_inbox_header'].'</h1>
  
  '.PMBar().'
  
  <p>'.$l['pm_inbox_desc'].'</p>
  ';
  
  if (count($settings['page']['messages'])) {
    echo '<table width="100%" style="text-align: center">
      <tr>
        <th style="border-style: solid; border-width: 1px; width: 45%">'.$l['pm_inbox_subject'].'</th>
        <th style="border-style: solid; border-width: 1px; width: 15%">'.$l['pm_inbox_from'].'</th>
        <th style="border-style: solid; border-width: 1px; width: 40%">'.$l['pm_inbox_received'].'</th>
      </tr>';
    foreach ($settings['page']['messages'] as $message) {
      echo '
      <tr>
        <td><a href="'.$cmsurl.'forum.php?action=pm;msg='.$message['id'].'">'.$message['subject'].'</a></td>
        <td><a href="'.$cmsurl.'index.php?action=profile;u='.$message['from_id'].'">'.$message['from'].'</a></td>
        <td>'.$message['time'].'</td>
      </tr>
      ';
    }
    echo '</table>';
  }
  
  echo '
  </td></tr></table>';
}

function InboxAdmin() {
global $l, $settings, $cmsurl;
  
  echo '
  <table class="pm"><tr><td>
  
  <h1>'.str_replace('%user%',$settings['page']['user'],$l['pm_inbox_admin_header']).'</h1>
  
  <p>'.str_replace('%user%',$settings['page']['user'],$l['pm_inbox_admin_desc']).'</p>
  ';
  
  if (count($settings['page']['messages'])) {
    echo '<table width="100%" style="text-align: center">
      <tr>
        <th style="border-style: solid; border-width: 1px; width: 45%">'.$l['pm_inbox_admin_subject'].'</th>
        <th style="border-style: solid; border-width: 1px; width: 15%">'.$l['pm_inbox_admin_from'].'</th>
        <th style="border-style: solid; border-width: 1px; width: 40%">'.$l['pm_inbox_admin_received'].'</th>
      </tr>';
    foreach ($settings['page']['messages'] as $message) {
      echo '
      <tr>
        <td><a href="'.$cmsurl.'forum.php?action=pm;msg='.$message['id'].'">'.$message['subject'].'</a></td>
        <td><a href="'.$cmsurl.'index.php?action=profile;u='.$message['from_id'].'">'.$message['from'].'</a></td>
        <td>'.$message['time'].'</td>
      </tr>
      ';
    }
    echo '</table>';
  }
  
  echo '
  </td></tr></table>';
}

function Message() {
global $l, $settings, $cmsurl;
  
  $message = $settings['page']['message'];
  
  echo '
  <table class="pm"><tr><td>
  
  <h1>'.$l['pm_message_header'].'</h1>
  
  '.PMBar().'
  
  <p>'.$l['pm_message_desc'].'</p>
  
  <table width="100%">
    <tr>
      <td style="border-style: solid; border-width: 1px">
      '.
      str_replace('%subject%','<b>'.$message['subject'].'</b>',
      str_replace('%from%','<a href="'.$cmsurl.'index.php?action=profile;u='.$message['from_id'].'">'.$message['from'].'</a>',
      str_replace('%time%','<b>'.$message['time'].'</b>',
      $l['pm_message_heading'])))
      .'
      </td>
    </tr>
    <tr>
      <td>
      '.$message['body'].'
      </td>
    </tr>
  </table>
  
  </td></tr></table>';
  
}

function MessageAdmin() {
global $l, $settings, $cmsurl;
  
  echo '
  <table class="pm"><tr><td>
  
  <h1>'.str_replace('%user%',$settings['page']['user'],$l['pm_message_admin_header']).'</h1>
  
  <p>'.str_replace('%user%',$settings['page']['user'],$l['pm_message_admin_desc']).'</p>
  ';
  
  if (count($settings['page']['messages'])) {
    echo '<table width="100%" style="text-align: center">
      <tr>
        <th style="border-style: solid; border-width: 1px; width: 45%">'.$l['pm_message_admin_subject'].'</th>
        <th style="border-style: solid; border-width: 1px; width: 15%">'.$l['pm_message_admin_from'].'</th>
        <th style="border-style: solid; border-width: 1px; width: 40%">'.$l['pm_message_admin_received'].'</th>
      </tr>';
    foreach ($settings['page']['messages'] as $message) {
      echo '
      <tr>
        <td><a href="'.$cmsurl.'forum.php?action=pm;msg='.$message['id'].'">'.$message['subject'].'</a></td>
        <td><a href="'.$cmsurl.'index.php?action=profile;u='.$message['from_id'].'">'.$message['from'].'</a></td>
        <td>'.$message['time'].'</td>
      </tr>
      ';
    }
    echo '</table>';
  }
  
  echo '
  </td></tr></table>';
}

function Outbox() {
global $l, $cmsurl;
  
  echo '
  <table class="pm"><tr><td>
  
  <h1>'.$l['pm_outbox_header'].'</h1>
  
  '.PMBar().'
  
  <p>'.$l['pm_outbox_desc'].'</p>
  
  </td></tr></table>';
}

function Compile() {
global $l, $cmsurl;
  
  echo '
  <table class="pm"><tr><td>
  
  <h1>'.$l['pm_compile_header'].'</h1>
  
  '.PMBar().'
  
  <p>'.$l['pm_compile_desc'].'</p>
  
  </td></tr></table>';
}

function NotAllowed() {
global $l, $cmsurl;
  
  echo '
  <table class="pm"><tr><td>
  
  <h1>'.$l['pm_message_notallowed_header'].'</h1>
  
  <p>'.$l['pm_message_notallowed_desc'].'</p>
  
  </td></tr></table>';
}

function PMBar() {
global $l, $cmsurl;
  
  return '
  <div style="text-align: center">
    <form action="'.$cmsurl.'forum.php?action=pm" method="post" style="display: inline">
      <p style="display: inline">
        <input type="hidden" name="redirect" value="true" />
        <input type="submit" value="'.$l['pm_button_inbox'].'" />
      </p>
    </form>
    
    <form action="'.$cmsurl.'forum.php?action=pm;sa=compile" method="post" style="display: inline">
      <p style="display: inline">
        <input type="hidden" name="redirect" value="true" />
        <input type="submit" value="'.$l['pm_button_compile'].'" />
        </p>
    </form>
    
    <form action="'.$cmsurl.'forum.php?action=pm;sa=outbox" method="post" style="display: inline">
      <p style="display: inline">
        <input type="hidden" name="redirect" value="true" />
        <input type="submit" value="'.$l['pm_button_outbox'].'" />
        </p>
    </form>
  </div>
  ';
}
?>