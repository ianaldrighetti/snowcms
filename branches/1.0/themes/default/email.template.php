<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#      Stats Layout template, January 16, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function email_compose_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
       <h1>', sprintf($l['email_compose_header'], $page['member']['username']), '</h1>
       <p>', sprintf($l['email_compose_desc'], '<a href="">'. $page['member']['username']. '</a>'), '</p>';
  
  if($page['sent'])
    echo '
       <div class="generic_success">
         <p>', $l['email_compose_sent'], '</p>
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
        <form action="', $base_url, '/index.php?action=email;u=', $page['member']['id'], ';send" method="post">
          <table border="0" cellspacing="0" cellpadding="4" width="100%" class="admin_settings">
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label for="subject">', $l['email_compose_subject'], '</label></td>
              <td style="width: 50%"><input type="text" id="subject" name="subject" value="', $page['subject'], '" /></td>
            </tr>
            <tr class="setting_container">
              <td style="width: 16"></td>
              <td valign="top"><label for="body">', $l['email_compose_message'], '</label></td>
              <td style="width: 50%"><textarea id="body" name="body" cols="48" rows="10">', $page['body'], '</textarea></td>
            </tr>
            <tr>
              <td colspan="3" align="center" valign="middle"><input type="submit" name="send" value="', $l['email_compose_submit'], '" /></td>
            </tr>
          </table>
        </form>
      </fieldset>';
}
?>