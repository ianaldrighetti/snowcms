<?php
#########################################################################
#                             SnowCMS v1.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#               Released under the GNU Lesser GPL v3 License            #
#                    www.gnu.org/licenses/lgpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#  SnowCMS v1.0 began in November 2008 by Myles, aldo and antimatter15  #
#                       aka the SnowCMS Dev Team                        #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 1.0                         #
#########################################################################

# No Direct access please ^^
if(!defined('InSnow'))
  die;

#
# void email_compose();
#

function email_compose()
{
  global $base_url, $source_dir, $db, $user, $page, $settings, $source_dir, $l;
  
  # Load language
  language_load('email');
  
  # No errors yet and not sent yet
  $page['errors'] = array();
  
  # Get the member's ID
  $member_id = isset($_GET['u']) ? $_GET['u'] : 0;
  
  # Get the member's information
  $result = $db->query("
    SELECT
      member_id AS id, displayName AS username, email, receive_email, show_email
    FROM {$db->prefix}members
    WHERE member_id = %member_id",
    array(
      'member_id' => array('int', $member_id),
    ));
  $page['member'] = $db->fetch_assoc($result);
  
  # Check if that's a real member ID and if they allow people to send them emails
  if(!$page['member'] || (!$page['member']['receive_email'] && !$page['member']['show_email'] && !can('override_show_email') && $user['id'] != $page['member']['id']))
    error_screen();
  
  # Get the to entered values
  $page['subject'] = isset($_POST['subject']) ? $_POST['subject'] : '';
  $page['body'] = isset($_POST['body']) ? $_POST['body'] : '';
  
  # Sending?
  if(isset($_GET['send']))
  {
    # Check if there is a subject
    if(!$page['subject'])
      $page['errors'][] = $l['email_compose_error_subject_none'];
    # Check if there is a message
    if(!$page['body'])
      $page['errors'][] = $l['email_compose_error_body_none'];
    
    # Chck if there we no errors
    if(!$page['errors'])
    {
      # mail.php contains mail_send(), which we need to send the email
      require_once($source_dir. '/mail.php');
      
      # Send the email and/or add it to the queue
      mail_send($member['email'], $page['subject'], $page['body']);
      
      # Redirect
      redirect('index.php?action=email;u='. $member['id']. ';sent');
    }
  }
  
  # Has the PM just been sent?
  $page['sent'] = isset($_GET['sent']);
  
  # Title
  $page['title'] = sprintf($l['email_compose_title'], $page['member']['username']);
  
  # And theme
  theme_load('email', 'email_compose_show');
}
?>