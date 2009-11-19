<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#      Stats Layout template, January 16, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function personalmessages_compose_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
       <h1>', $l['pm_compose_header'], '</h1>
       <p>', $l['pm_compose_desc'], '</p>
       
      <br />
      
      <div class="pm_space_outer">
        <div class="pm_space">
          <div class="pm_space_total"><div class="pm_space_used" style="width: ', $page['space']['percent'], '%;"></div></div>
          <div class="pm_space_text"><p>', $page['space']['used'], $page['space']['total'] ? '/' : '', $page['space']['total'], '</p></div>
        </div>
        <p>', $l['pm_space_used'], ':</p>
      </div>
      
      <table class="pm_links">
        <tr>
          <td><img src="', $settings['images_url'], '/compose.png" alt="', $l['pm_compose'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=compose">', $l['pm_compose'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_inbox.png" alt="', $l['pm_folder_inbox'], '" /> <a href="', $base_url, '/index.php?action=pm">', $l['pm_folder_inbox'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_outbox.png" alt="', $l['pm_folder_outbox'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=outbox">', $l['pm_folder_outbox'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_archive.png" alt="', $l['pm_folder_archive'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=archive">', $l['pm_folder_archive'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_deleted.png" alt="', $l['pm_folder_deleted'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=deleted">', $l['pm_folder_deleted'], '</a></td>
        </tr>
      </table>
      
      <div style="clear: both;"></div>
      ';
  
  if($page['sent'])
    echo '
       <div class="generic_success">
         <p>', $l['pm_compose_sent'], '</p>
       </div>
       ';
  # Show any errors
  elseif($page['errors'])
  {
    echo '
      <div class="generic_error">';
    
    foreach($page['errors'] as $error)
      echo '
        <p>', $error, '</p>';
    
    echo '
      </div>';
  }
  
  echo '
       <fieldset>
        <form action="', $base_url, '/index.php?action=pm;sa=compose;send" method="post">
          <table border="0" cellspacing="0" cellpadding="4" width="100%" class="admin_settings">
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label for="recipients">', $l['pm_compose_recipients'], '</label><br /><span class="small subtext">', $l['pm_compose_sub_recipients'], '</span></td>
              <td style="width: 50%"><input type="text" id="recipients" name="recipients" autocomplete="off" style="width:98%" value="', $page['to'], '" />
              <ul id="autofillbox" class="autofillbox">
              </td>
            </tr>
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label for="subject">', $l['pm_compose_subject'], '</label></td>
              <td style="width: 50%"><input type="text" id="subject" name="subject" style="width:98%" value="', $page['subject'], '" /></td>
            </tr>
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label for="body">', $l['pm_compose_message'], '</label></td>
              <td style="width: 50%"><textarea id="body" name="body" cols="48" rows="10">', $page['body'], '</textarea></td>
            </tr>
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label for="outbox">', $l['pm_compose_outbox'], '</label><br /><span class="small subtext">', $l['pm_compose_sub_outbox'], '</span></td>
              <td style="width: 50%"><input type="checkbox" id="outbox" name="outbox" value="1" checked="checked" /></td>
            </tr>
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label for="read_receipt">', $l['pm_compose_read_receipt'], '</label><br /><span class="small subtext">', $l['pm_compose_sub_read_receipt'], '</span></td>
              <td style="width: 50%"><input type="checkbox" id="read_receipt" name="read_receipt" value="1" /></td>
            </tr>
            <tr>
              <td colspan="3" align="center" valign="middle"><input type="submit" name="send" value="', $l['pm_compose_send'], '" /></td>
            </tr>
          </table>
        </form>
      </fieldset>
      <script type="text/javascript" src="', $theme_url, '/default/js/autofill.js"></script>
      <script type="text/javascript">
        _.ready(function() {
          new_autofill(_.id("recipients"), _.id("autofillbox"),base_url + "/index.php?action=interface;sa=user_suggest","search={string}","multi")
        })
      </script>';
}

function personalmessages_reply_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
       <h1>', $l['pm_reply_header'], '</h1>
       <p>', $l['pm_reply_desc'], '</p>
       
      <br />
      
      <div class="pm_space_outer">
        <div class="pm_space">
          <div class="pm_space_total"><div class="pm_space_used" style="width: ', $page['space']['percent'], '%;"></div></div>
          <div class="pm_space_text"><p>', $page['space']['used'], $page['space']['total'] ? '/' : '', $page['space']['total'], '</p></div>
        </div>
        <p>', $l['pm_space_used'], ':</p>
      </div>
      
      <table class="pm_links">
        <tr>
          <td><img src="', $settings['images_url'], '/compose.png" alt="', $l['pm_compose'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=compose">', $l['pm_compose'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_inbox.png" alt="', $l['pm_folder_inbox'], '" /> <a href="', $base_url, '/index.php?action=pm">', $l['pm_folder_inbox'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_outbox.png" alt="', $l['pm_folder_outbox'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=outbox">', $l['pm_folder_outbox'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_archive.png" alt="', $l['pm_folder_archive'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=archive">', $l['pm_folder_archive'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_deleted.png" alt="', $l['pm_folder_deleted'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=deleted">', $l['pm_folder_deleted'], '</a></td>
        </tr>
      </table>
      
      <div style="clear: both;"></div>
      ';
  
  if($page['sent'])
    echo '
       <div class="generic_success">
         <p>', $l['pm_compose_sent'], '</p>
       </div>
       ';
  # Show any errors
  elseif($page['errors'])
  {
    echo '
      <div class="generic_error">
      ';
    
    foreach($page['errors'] as $error)
      echo '
        <p>', $error, '</p>';
    
    echo '
      </div>';
  }
  
  echo '
       <fieldset>
        <form action="', $base_url, '/index.php?action=pm;sa=reply;pm=', $page['pm']['id'], ';send" method="post">
          <table border="0" cellspacing="0" cellpadding="4" width="100%" class="admin_settings">
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label>', $l['pm_reply_recipients'], '</label></td>
              <td style="width: 50%">
                <a href="', $base_url, '/index.php?action=profile;u='. $page['pm']['recipient']['id']. '">'. $page['pm']['recipient']['name']. '</a>';
  
  # Include all the other recipients if reply all
  if($page['reply_all'])
    foreach($page['pm']['extra_recipients'] as $recipient)
      echo ', <a href="', $base_url, '/index.php?action=profile;u='. $recipient['id']. '">'. $recipient['name']. '</a>';
  
  echo '
                <input type="hidden" name="recipients" value="'. $page['pm']['recipient']['name'];
  
  # Include all the other recipients if reply all
  if($page['reply_all'])
    foreach($page['pm']['extra_recipients'] as $recipient)
      echo ','. $recipient['name'];
  
  echo '" /></td>
            </tr>
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label for="subject">', $l['pm_reply_subject'], '</label></td>
              <td style="width: 50%"><input type="text" id="subject" name="subject" value="', $page['pm']['subject'], '" /></td>
            </tr>
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label for="body">', $l['pm_reply_message'], '</label></td>
              <td style="width: 50%"><textarea id="body" name="body" cols="48" rows="10">', $page['pm']['body'], '</textarea></td>
            </tr>
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label for="outbox">', $l['pm_reply_outbox'], '</label><br /><span class="small subtext">', $l['pm_compose_sub_outbox'], '</span></td>
              <td style="width: 50%"><input type="checkbox" id="outbox" name="outbox" value="1" checked="checked" /></td>
            </tr>
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label for="read_receipt">', $l['pm_reply_read_receipt'], '</label><br /><span class="small subtext">', $l['pm_reply_sub_read_receipt'], '</span></td>
              <td style="width: 50%"><input type="checkbox" id="read_receipt" name="read_receipt" value="1" /></td>
            </tr>
            <tr>
              <td colspan="3" align="center" valign="middle"><input type="submit" name="send" value="', $l['pm_reply_send'], '" /></td>
            </tr>
          </table>
        </form>
      </fieldset>';
}

function personalmessages_reply_show_self()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
      <h1>', $l['pm_reply_self_header'], '</h1>
      <p>', $l['pm_reply_self_desc'], '</p>
      
      <br />
      
      <div class="pm_space_outer">
        <div class="pm_space">
          <div class="pm_space_total"><div class="pm_space_used" style="width: ', $page['space']['percent'], '%;"></div></div>
          <div class="pm_space_text"><p>', $page['space']['used'], $page['space']['total'] ? '/' : '', $page['space']['total'], '</p></div>
        </div>
        <p>', $l['pm_space_used'], ':</p>
      </div>
      
      <table class="pm_links">
        <tr>
          <td><img src="', $settings['images_url'], '/compose.png" alt="', $l['pm_compose'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=compose">', $l['pm_compose'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_inbox.png" alt="', $l['pm_folder_inbox'], '" /> <a href="', $base_url, '/index.php?action=pm">', $l['pm_folder_inbox'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_outbox.png" alt="', $l['pm_folder_outbox'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=outbox">', $l['pm_folder_outbox'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_archive.png" alt="', $l['pm_folder_archive'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=archive">', $l['pm_folder_archive'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_deleted.png" alt="', $l['pm_folder_deleted'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=deleted">', $l['pm_folder_deleted'], '</a></td>
        </tr>
      </table>
      
      <div style="clear: both;"></div>
      
      <br />
      
      <div class="generic_error">
        <p>', $l['pm_reply_self_error'], '</p>
      </div>';
}

function personalmessages_forward_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
       <h1>', $l['pm_forward_header'], '</h1>
       <p>', $l['pm_forward_desc'], '</p>
       
      <br />
      
      <div class="pm_space_outer">
        <div class="pm_space">
          <div class="pm_space_total"><div class="pm_space_used" style="width: ', $page['space']['percent'], '%;"></div></div>
          <div class="pm_space_text"><p>', $page['space']['used'], $page['space']['total'] ? '/' : '', $page['space']['total'], '</p></div>
        </div>
        <p>', $l['pm_space_used'], ':</p>
      </div>
      
      <table class="pm_links">
        <tr>
          <td><img src="', $settings['images_url'], '/compose.png" alt="', $l['pm_compose'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=compose">', $l['pm_compose'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_inbox.png" alt="', $l['pm_folder_inbox'], '" /> <a href="', $base_url, '/index.php?action=pm">', $l['pm_folder_inbox'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_outbox.png" alt="', $l['pm_folder_outbox'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=outbox">', $l['pm_folder_outbox'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_archive.png" alt="', $l['pm_folder_archive'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=archive">', $l['pm_folder_archive'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_deleted.png" alt="', $l['pm_folder_deleted'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=deleted">', $l['pm_folder_deleted'], '</a></td>
        </tr>
      </table>
      
      <div style="clear: both;"></div>
      ';
  
  if($page['sent'])
    echo '
       <div class="generic_success">
         <p>', $l['pm_compose_sent'], '</p>
       </div>
       ';
  # Show any errors
  elseif($page['errors'])
  {
    echo '
      <div class="generic_error">
      ';
    
    foreach($page['errors'] as $error)
      echo '
        <p>', $error, '</p>';
    
    echo '
      </div>';
  }
  
  echo '
       <fieldset>
        <form action="', $base_url, '/index.php?action=pm;sa=reply;pm=', $page['pm']['id'], ';send" method="post">
          <table border="0" cellspacing="0" cellpadding="4" width="100%" class="admin_settings">
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label>', $l['pm_forward_recipients'], '</label></td>
              <td style="width: 50%"><input type="text" id="recipients" name="recipients" value="" /></td>
            </tr>
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label for="subject">', $l['pm_forward_subject'], '</label></td>
              <td style="width: 50%"><input type="text" id="subject" name="subject" value="', $page['pm']['subject'], '" /></td>
            </tr>
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label for="body">', $l['pm_forward_message'], '</label></td>
              <td style="width: 50%"><textarea id="body" name="body" cols="48" rows="10">', $page['pm']['body'], '</textarea></td>
            </tr>
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label for="outbox">', $l['pm_forward_outbox'], '</label><br /><span class="small subtext">', $l['pm_compose_sub_outbox'], '</span></td>
              <td style="width: 50%"><input type="checkbox" id="outbox" name="outbox" value="1" checked="checked" /></td>
            </tr>
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label for="read_receipt">', $l['pm_forward_read_receipt'], '</label><br /><span class="small subtext">', $l['pm_reply_sub_read_receipt'], '</span></td>
              <td style="width: 50%"><input type="checkbox" id="read_receipt" name="read_receipt" value="1" /></td>
            </tr>
            <tr>
              <td colspan="3" align="center" valign="middle"><input type="submit" name="send" value="', $l['pm_forward_send'], '" /></td>
            </tr>
          </table>
        </form>
      </fieldset>';
}

function personalmessages_view_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
      <h1>', $l['pm_view_header'], '</h1>
      <p>', sprintf($l['pm_view_desc'], '<a href="'. $base_url. '/index.php?action=pm;folder='. $page['pm']['folder_name']. '">'. $l['pm_folder_'. $page['pm']['folder_name']]. '</a>'), '</p>
      
      <br />
      
      <div class="pm_space_outer">
        <div class="pm_space">
          <div class="pm_space_total"><div class="pm_space_used" style="width: ', $page['space']['percent'], '%;"></div></div>
          <div class="pm_space_text"><p>', $page['space']['used'], $page['space']['total'] ? '/' : '', $page['space']['total'], '</p></div>
        </div>
        <p>', $l['pm_space_used'], ':</p>
      </div>
      
      <table class="pm_links">
        <tr>
          <td><img src="', $settings['images_url'], '/compose.png" alt="', $l['pm_compose'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=compose">', $l['pm_compose'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_inbox.png" alt="', $l['pm_folder_inbox'], '" /> <a href="', $base_url, '/index.php?action=pm">', $l['pm_folder_inbox'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_outbox.png" alt="', $l['pm_folder_outbox'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=outbox">', $l['pm_folder_outbox'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_archive.png" alt="', $l['pm_folder_archive'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=archive">', $l['pm_folder_archive'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_deleted.png" alt="', $l['pm_folder_deleted'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=deleted">', $l['pm_folder_deleted'], '</a></td>
        </tr>
      </table>
      
      <div style="clear: both;"></div>
      
      <br />
      ';
  
  if($page['pm']['read_receipt'])
   echo '
      <div class="generic_error">
        <p>', sprintf($l['pm_view_read_receipt'], '<a href="'. $base_url. '/index.php?action=profile;u='. $page['pm']['frm_id']. '">'. $page['pm']['frm_name']. '</a>'), '</p>
        <br />
        <p><a href="', $base_url, '/index.php?action=pm;pm=', $page['pm']['id'], ';receipt=send">', $l['pm_view_read_receipt_send'], '</a> - <a href="', $base_url, '/index.php?action=pm;pm=', $page['pm']['id'], ';receipt=deny">', $l['pm_view_read_receipt_deny'], '</a></p>
      </div>
      <br />
      ';
  
  echo '
      <table class="vtable">
        <tr><th style="width: 20%;">', $l['pm_view_subject'], '</th><td>', $page['pm']['subject'], '</td></tr>
        <tr><th>', $l['pm_view_recipients'], '</th><td>
          <a href="', $base_url, '/index.php?action=profile;u=', $page['pm']['recipient']['id'], '">', $page['pm']['recipient']['name'], '</a>';
  
  foreach($page['pm']['extra_recipients'] as $recipient)
    echo ', <a href="', $base_url, '/index.php?action=profile;u=', $recipient['id'], '">', $recipient['name'], '</a>';
  
  echo '
        </td></tr>
        <tr><th>', $l['pm_view_sender'], '</th><td>', ($page['pm']['frm_id'] ? '<a href="'. $base_url. '/index.php?action=profile;u='. $page['pm']['frm_id']. '">'. $page['pm']['frm_name']. '</a>' : $page['pm']['frm_name']), '</td></tr>
      </table>
      
      <br />
      
      <div>
      ', $page['pm']['body'], '
      </div>
      
      <br />
      
      <table width="100%" style="text-align: center;">
        <tr>
          <td style="width: 20%"><img src="', $settings['images_url'], '/pm_reply.png" alt="', $l['pm_view_reply'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=reply;pm=', $page['pm']['id'], '">', $l['pm_view_reply'], '</a></td>
          <td style="width: 20%"><img src="', $settings['images_url'], '/pm_reply_all.png" alt="', $l['pm_view_reply_all'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=reply-all;pm=', $page['pm']['id'], '">', $l['pm_view_reply_all'], '</a></td>
          <td style="width: 20%"><img src="', $settings['images_url'], '/pm_forward.png" alt="', $l['pm_view_forward'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=forward;pm=', $page['pm']['id'], '">', $l['pm_view_forward'], '</a></td>
          <td style="width: 20%"><img src="', $settings['images_url'], '/delete.png" alt="', $l['pm_view_delete'], '" /> <a href="', $base_url, '/index.php?action=pm;delete=', $page['pm']['id'], '">', $l['pm_view_delete'], '</a></td>
          <td style="width: 20%"><img src="', $settings['images_url'], '/pm_read.png" alt="', $l['pm_view_unread'], '" /> <a href="', $base_url, '/index.php?action=pm;unread=', $page['pm']['id'], '">', $l['pm_view_unread'], '</a></td>
        </tr>
      </table>
      
      <br />';
}

function personalmessages_folder_show_list()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  $sort_asc = ' <img src="'. $theme_url. '/'. $settings['theme']. '/images/sort_asc.png" alt="'. $l['asc']. '" />';
  $sort_desc = ' <img src="'. $theme_url. '/'. $settings['theme']. '/images/sort_desc.png" alt="'. $l['desc']. '" />';
  
  $sort_subject = $page['sort'] == 'subject' ? $sort_asc : ($page['sort'] == 'subject;desc' ? $sort_desc : '');
  $sort_recipients = $page['sort'] == 'recipients' ? $sort_asc : ($page['sort'] == 'recipients;desc' ? $sort_desc : '');
  $sort_sender = $page['sort'] == 'sender' ? $sort_asc : ($page['sort'] == 'sender;desc' ? $sort_desc : '');
  $sort_sent = $page['sort'] == 'sent' ? $sort_asc : ($page['sort'] == 'sent;desc' ? $sort_desc : '');
  
  echo '
      <h1>', $l['pm_header'], '</h1>
      <p>', sprintf($l['pm_desc'], '<a href="'. $base_url. '/index.php?action=pm;folder='. $page['folder_name']. '">'. $l['pm_folder_'. $page['folder_name']]. '</a>'), '</p>
      
      <br />
      
      <div class="pm_space_outer">
        <div class="pm_space">
          <div class="pm_space_total"><div class="pm_space_used" style="width: ', $page['space']['percent'], '%;"></div></div>
          <div class="pm_space_text"><p>', $page['space']['used'], $page['space']['total'] ? '/' : '', $page['space']['total'], '</p></div>
        </div>
        <p>', $l['pm_space_used'], ':</p>
      </div>
      
      <table class="pm_links">
        <tr>
          <td><img src="', $settings['images_url'], '/compose.png" alt="', $l['pm_compose'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=compose">', $l['pm_compose'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_inbox.png" alt="', $l['pm_folder_inbox'], '" /> <a href="', $base_url, '/index.php?action=pm">', $l['pm_folder_inbox'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_outbox.png" alt="', $l['pm_folder_outbox'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=outbox">', $l['pm_folder_outbox'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_archive.png" alt="', $l['pm_folder_archive'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=archive">', $l['pm_folder_archive'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_deleted.png" alt="', $l['pm_folder_deleted'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=deleted">', $l['pm_folder_deleted'], '</a></td>
        </tr>
      </table>
      
      <div style="clear: both;"></div>
      
      <p>'. $page['pagination']. '</p>
      
      <br />
      
      <table class="htable pm_folder pm_folder_', $page['folder_name'], '">
        <tr>
          <td class="status"></td>
          <th class="subject"><a href="'. $base_url. '/index.php?action=pm;', $page['sa'], $page['page'], 'sort=subject'. ($page['sort'] == 'subject' ? ';desc' : ''). '">', $l['pm_subject'], '</a>'. $sort_subject. '</th>
          <th class="recipients"><a href="'. $base_url. '/index.php?action=pm;', $page['sa'], $page['page'], 'sort=recipients'. ($page['sort'] == 'recipients' ? ';desc' : ''). '">', $l['pm_recipients'], '</a>'. $sort_recipients. '</th>
          <th class="sender"><a href="'. $base_url. '/index.php?action=pm;', $page['sa'], $page['page'], 'sort=sender'. ($page['sort'] == 'sender' ? ';desc' : ''). '">', $l['pm_sender'], '</a>'. $sort_sender. '</th>
          <th class="time_sent"><a href="'. $base_url. '/index.php?action=pm;', $page['sa'], $page['page'], 'sort=sent'. ($page['sort'] == 'sent' ? ';desc' : ''). '">', $l['pm_sent'], '</a>'. $sort_sent. '</th>
          <td class="options"></td>
        </tr>';
  
  # Echo PMs
  foreach($page['pms'] as $pm)
  {
    echo '
        <tr>
          <td><img src="', $settings['images_url'], '/pm_', ($pm['status'] == 2 ? 'replied' : ($pm['status'] ? 'read' : 'unread')), '.png" alt="" /></td>
          <td><a href="', $base_url, '/index.php?action=pm;pm=', $pm['id'], '">', $pm['subject'], '</a></td>
          <td>
            <a href="', $base_url, '/index.php?action=profile;u=', $pm['recipient']['id'], '">', $pm['recipient']['name'], '</a>';
    
    # Echo the extra recipients
    foreach($pm['extra_recipients'] as $recipient)
    echo ',
            <a href="', $base_url, '/index.php?action=profile;u=', $recipient['id'], '">', $recipient['name'], '</a>';
    
    echo '
          </td>
          <td>', ($pm['frm_name'] ? '<a href="'. $base_url. '/index.php?action=profile;u='. $pm['frm_id']. '">'. $pm['frm_name']. '</a>' : $settings['site_name']), '</td>
          <td>'. timeformat($pm['time_sent']). '</td>
          <td>
            <a href="', $base_url, '/index.php?action=pm;sa=', $page['folder_name'], ';flag=', $pm['id']. '">'. ($pm['flagged'] ? '<img src="'. $settings['images_url']. '/flagged.png" alt="'. $l['pm_flagged']. '" title="'. $l['pm_flagged']. '" />' : '<img src="'. $settings['images_url']. '/unflagged.png" alt="'. $l['pm_unflagged']. '" title="'. $l['pm_unflagged']. '" />'). '</a>';
     
     if($page['folder_name'] != 'deleted')
       echo '
            <a href="', $base_url, '/index.php?action=pm;sa=', $page['folder_name'], ';archive=', $pm['id'], '"><img src="', $settings['images_url'], '/', ($page['folder_name'] == 'archive' ? 'un' : ''), 'archive.png" alt="', $l['pm_'. ($page['folder_name'] == 'archive' ? 'un' : ''). 'archive'], '" title="', $l['pm_'. ($page['folder_name'] == 'archive' ? 'un' : ''). 'archive'], '" /></a>';
     else
       echo '
            <a href="', $base_url, '/index.php?action=pm;sa=', $page['folder_name'], ';undelete=', $pm['id'], '"><img src="', $settings['images_url'], '/undelete.png" alt="', $l['pm_undelete'], '" title="', $l['pm_undelete'], '" /></a>';
     
     echo '
            <a href="', $base_url, '/index.php?action=pm;sa=', $page['folder_name'], ';delete=', $pm['id'], '"><img src="', $settings['images_url'], '/delete.png" alt="', $l['pm_delete'. ($page['folder_name'] == 'deleted' ? '_permanently' : '_recycle')], '" title="', $l['pm_delete'. ($page['folder_name'] == 'deleted' ? '_permanently' : '_recycle')], '" /></a>
          </td>
        </tr>';
  }
  
  echo '
      </table>
      
      <br />
      
      <p>'. $page['pagination']. '</p>';
}

function personalmessages_folder_show_threaded()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
      <h1>', $l['pm_header'], '</h1>
      <p>', sprintf($l['pm_desc'], '<a href="'. $base_url. '/index.php?action=pm;folder='. $page['folder_name']. '">'. $l['pm_folder_'. $page['folder_name']]. '</a>'), '</p>
      <br />
      <div class="pm_space_outer">
        <div class="pm_space">
          <div class="pm_space_total"><div class="pm_space_used" style="width: ', $page['space']['percent'], '%;"></div></div>
          <div class="pm_space_text"><p>', $page['space']['used'], $page['space']['total'] ? '/' : '', $page['space']['total'], '</p></div>
        </div>
        <p>', $l['pm_space_used'], ':</p>
      </div>
      <table class="pm_links">
        <tr>
          <td><img src="', $settings['images_url'], '/compose.png" alt="', $l['pm_compose'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=compose">', $l['pm_compose'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_inbox.png" alt="', $l['pm_folder_inbox'], '" /> <a href="', $base_url, '/index.php?action=pm">', $l['pm_folder_inbox'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_outbox.png" alt="', $l['pm_folder_outbox'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=outbox">', $l['pm_folder_outbox'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_archive.png" alt="', $l['pm_folder_archive'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=archive">', $l['pm_folder_archive'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_deleted.png" alt="', $l['pm_folder_deleted'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=deleted">', $l['pm_folder_deleted'], '</a></td>
        </tr>
      </table>
      <div class="break"></div>
      <table width="100%" class="topic_options" cellpadding="6px" cellspacing="0px">
        <tr>
          <td align="left">'. $page['pagination']. '</td>
        </tr>
      </table>
      <table class="post_listing pm_folder pm_folder_', $page['folder_name'], '">';
  
  # Echo PMs
  foreach($page['pms'] as $pm)
  {
    echo '
        <tr>
          <th width="22%"></th>
          <th align="left" valign="middle" width="70%"><a href="', $base_url, '/index.php?action=pm;pm=', $pm['id'], '" id="subject_', $pm['id'], '">', $pm['subject'], '</a> - <span class="normal small">', timeformat($pm['time_sent']), '</span></th>
          <th align="right" valign="middle" width="10%">
            <a href="', $base_url, '/index.php?action=pm;sa=', $page['folder_name'], ';flag=', $pm['id']. '">'. ($pm['flagged'] ? '<img src="'. $settings['images_url']. '/flagged.png" alt="'. $l['pm_flagged']. '" title="'. $l['pm_flagged']. '" />' : '<img src="'. $settings['images_url']. '/unflagged.png" alt="'. $l['pm_unflagged']. '" title="'. $l['pm_unflagged']. '" />'). '</a>';
     
     if($page['folder_name'] != 'deleted')
       echo '
            <a href="', $base_url, '/index.php?action=pm;sa=', $page['folder_name'], ';archive=', $pm['id'], '"><img src="', $settings['images_url'], '/', ($page['folder_name'] == 'archive' ? 'un' : ''), 'archive.png" alt="', $l['pm_'. ($page['folder_name'] == 'archive' ? 'un' : ''). 'archive'], '" title="', $l['pm_'. ($page['folder_name'] == 'archive' ? 'un' : ''). 'archive'], '" /></a>';
     else
       echo '
            <a href="', $base_url, '/index.php?action=pm;sa=', $page['folder_name'], ';undelete=', $pm['id'], '"><img src="', $settings['images_url'], '/undelete.png" alt="', $l['pm_undelete'], '" title="', $l['pm_undelete'], '" /></a>';
     
     echo '
            <a href="', $base_url, '/index.php?action=pm;sa=', $page['folder_name'], ';delete=', $pm['id'], '"><img src="', $settings['images_url'], '/delete.png" alt="', $l['pm_delete'. ($page['folder_name'] == 'deleted' ? '_permanently' : '_recycle')], '" title="', $l['pm_delete'. ($page['folder_name'] == 'deleted' ? '_permanently' : '_recycle')], '" /></a>
          </th>
          <th></th>
        </tr>
        <tr>
          <td align="center" valign="top">
            <a href="'. $base_url. '/index.php?action=profile;u='. $pm['frm_id']. '" class="bold">', $pm['frm_name'], '</a><br />';
      
      if($pm['frm_custom_title'])
        echo '
            <span>', $pm['frm_custom_title'], '</span><br />';
      
      if($pm['frm_group_name'])
        echo '
            <span>', $pm['frm_group_name'], '</span><br />';
      
      if($pm['frm_stars']['amount'])
        echo '
             ';
      for($i = 0; $i < $pm['frm_stars']['amount']; $i += 1)
        echo '<img src="', $theme_url, '/', $settings['theme'], '/images/', $pm['frm_stars']['image'], '" alt="', $pm['frm_group_name'], '" />';
      if($pm['frm_stars']['amount'])
        echo '
             <br />';
      
      if($pm['frm_avatar'])
        echo '
              <img src="', $pm['frm_avatar'], '" alt="', sprintf($l['personalmessages_folder_threaded_avatar'], $pm['frm_name']), '" /><br />';
      
      echo '
            <br />
            <table class="post_member">
              <tr>
                <th>Posts:</th><td>', numberformat($pm['frm_num_posts']), '</td>
              </tr>
              <tr>
                <th>Location:</th><td>', $pm['frm_location'], '</td>
              </tr>
            </table>
            <br />
          </td>
          <td valign="top" colspan="2">
            <table width="100%" cellpadding="4px" cellspacing="0px">
              <tr>
                <td width="100%" style="padding-bottom: 20px;" colspan="3" id="post_', $pm['id'], '">
                  ', $pm['body'], '
                </td>
              </tr>
              <tr>
                <td class="italic small" align="left" width="80%">', !empty($pm['modified']['is']) ? '&laquo; <strong>'. $l['last_edited_by']. '</strong> '. $pm['modified']['link']. (!empty($pm['modified']['reason']) ? ' '. $l['on']. ' '. $pm['modified']['time_sent']. ' <strong>'. $l['reason']. '</strong> '. $pm['modified']['reason'] : ''). ' &raquo;' : '', '</td>
                <td align="right" width="20%">', !empty($pm['can']['view_ip']) ? 'IP: '. $pm['poster']['ip'] : '', '</td>
              </tr>';

    # Any signature?
    if(!empty($pm['poster']['signature']) && $user['visible']['signatures'])
      echo '
              <tr>
                <td width="100%" class="signature" colspan="2">
                  ', $pm['poster']['signature'], '
                </td>
              </tr>';

    echo '
            </table>
          </td>
        </tr>';
  }
  
  echo '
      </table>
      <table width="100%" class="topic_options" cellpadding="6px" cellspacing="0px">
        <tr>
          <td align="left">'. $page['pagination']. '</td>
        </tr>
      </table>';
}

function personalmessages_folder_show_empty()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
      <h1>', $l['pm_header'], '</h1>
      <p>', sprintf($l['pm_desc'], '<a href="'. $base_url. '/index.php?action=pm;folder='. $page['folder_name']. '">'. $l['pm_folder_'. $page['folder_name']]. '</a>'), '</p>
      
      <br />
      
      <div class="pm_space_outer">
        <div class="pm_space">
          <div class="pm_space_total"><div class="pm_space_used" style="width: ', $page['space']['percent'], '%;"></div></div>
          <div class="pm_space_text"><p>', $page['space']['used'], $page['space']['total'] ? '/' : '', $page['space']['total'], '</p></div>
        </div>
        <p>', $l['pm_space_used'], ':</p>
      </div>
      
      <table class="pm_links">
        <tr>
          <td><img src="', $settings['images_url'], '/compose.png" alt="', $l['pm_compose'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=compose">', $l['pm_compose'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_inbox.png" alt="', $l['pm_folder_inbox'], '" /> <a href="', $base_url, '/index.php?action=pm">', $l['pm_folder_inbox'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_outbox.png" alt="', $l['pm_folder_outbox'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=outbox">', $l['pm_folder_outbox'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_archive.png" alt="', $l['pm_folder_archive'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=archive">', $l['pm_folder_archive'], '</a></td>
          <td><img src="', $settings['images_url'], '/folder_deleted.png" alt="', $l['pm_folder_deleted'], '" /> <a href="', $base_url, '/index.php?action=pm;sa=deleted">', $l['pm_folder_deleted'], '</a></td>
        </tr>
      </table>
      
      <div style="clear: both;"></div>
      
      <p>'. $page['pagination']. '</p>
      
      <br />
      
      <div class="empty_pm_folder">
        <p>', $l['pm_folder_empty_'. $page['folder_name']], '</p>
      </div>
      
      <br />
      
      <p>'. $page['pagination']. '</p>';
}

function personalmessages_disallowed_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
      <h1>', $l['pm_not_allowed_header'], '</h1>
      
      <br />
      
      <div class="generic_error">
        <p>', $l['pm_not_allowed_desc'], '</p>
      </div>';
}
?>
