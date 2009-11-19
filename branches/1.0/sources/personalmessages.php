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
# Handles personal message system.
#
# void personalmessages_switch();
#
# void personalmessages_flag();
#
# void personalmessages_archive();
#
# void personalmessages_delete();
#
# void personalmessages_recycle();
#
# void personalmessages_receipt();
#
# void personalmessages_unread();
#
# void personalmessages_compose();
#
# void personalmessages_reply();
#
# void personalmessages_forward();
#
# void personalmessages_view();
#
# void personalmessages_list();
#
# void personalmessages_send();
#

function personalmessages_switch()
{
  global $base_url, $db, $user, $page, $settings, $l;
  
  # Work out PM space used in KB
  $page['space'] = array(
    'used' => numberformat($user['pm_size'] / 1024, 1). $l['kb'],
    'total' => $user['allowed_pm_size'] ? numberformat($user['allowed_pm_size'], 1). $l['kb'] : false,
    'percent' => $user['allowed_pm_size'] ? min(round(($user['pm_size'] / 1024) / $user['allowed_pm_size'] * 100), 100) : 0,
  );
  
  # Are they allowed to use the PM system?
  if(!can('view_pms'))
  {
    # Not allowed
    personalmessages_disallowed();
  }
  # What are we suppose to be doing?
  elseif(isset($_GET['flag']))
  {
    # Flagging or unflagging a message
    personalmessages_flag();
  }
  elseif(isset($_GET['archive']))
  {
    # Archiving or unarchiving a message
    personalmessages_archive();
  }
  elseif(isset($_GET['delete']))
  {
    # Deleting a message to either the recycle bin or permanently
    personalmessages_delete();
  }
  elseif(isset($_GET['undelete']))
  {
    # Removing a message from the recycle bin
    personalmessages_recycle();
  }
  elseif(isset($_GET['receipt']))
  {
    # Sending/denying read receipt
    personalmessages_receipt();
  }
  elseif(isset($_GET['unread']))
  {
    # Marking a message as unread
    personalmessages_unread();
  }
  elseif(isset($_GET['sa']) && $_GET['sa'] == 'compose')
  {
    # Composing a message
    personalmessages_compose();
  }
  elseif(isset($_GET['sa']) && ($_GET['sa'] == 'reply' || $_GET['sa'] == 'reply-all') && !empty($_GET['pm']))
  {
    # Replying to a message
    personalmessages_reply();
  }
  elseif(isset($_GET['sa']) && $_GET['sa'] == 'forward')
  {
    # Forwarding to a message
    personalmessages_forward();
  }
  elseif(isset($_GET['pm']))
  {
    # Viewing a PM
    personalmessages_view();
  }
  else
  {
    # Displaying PM list
    personalmessages_list();
  }
}

function personalmessages_disallowed()
{
  global $base_url, $theme_url, $db, $user, $page, $settings, $l;
  
  # Load the language
  language_load('personalmessages');
  
  # Set the title
  $page['title'] = $l['pm_not_allowed_title'];
  
  # Load the theme
  theme_load('personalmessages', 'personalmessages_disallowed_show');
}

function personalmessages_flag()
{
  global $base_url, $db, $user, $page, $settings, $l;
  
  # Check who owns the PM in question
  $result = $db->query("
    SELECT
      member_id, flagged
    FROM {$db->prefix}personal_messages
    WHERE pm_id = %pm_id",
    array(
      'pm_id' => array('int', $_GET['flag']),
    ));
  @list($member_id, $flagged) = $db->fetch_row($result);
  
  # Only allow them to flag/unflag the PM if they own it
  if($user['id'] == $member_id)
  {
    # Update the changed flag in the database
    $db->query("
      UPDATE {$db->prefix}personal_messages
      SET flagged = %flagged
      WHERE pm_id = %pm_id",
      array(
        'pm_id' => array('int', $_GET['flag']),
        'flagged' => array('int', (int)!$flagged),
      ));
  }
  
  # Redirect
  if(isset($_GET['sa']))
    redirect('index.php?action=pm;sa='. $_GET['sa']);
  else
    redirect('index.php?action=pm');
}

function personalmessages_archive()
{
  global $base_url, $db, $user, $page, $settings, $l;
  
  # Check who owns the PM in question and which folder it's in
  $result = $db->query("
    SELECT
      member_id, sender_id, folder
    FROM {$db->prefix}personal_messages
    WHERE pm_id = %pm_id",
    array(
      'pm_id' => array('int', $_GET['archive']),
    ));
  @list($member_id, $sender_id, $folder) = $db->fetch_row($result);
  
  # Only allow them to archive/unarchive the PM if they own it
  if($user['id'] == $member_id)
  {
    # Is the PM not in the archive?
    if($folder != 3)
    {
      # Move it to the archive
      $db->query("
        UPDATE {$db->prefix}personal_messages
        SET folder = 3
        WHERE pm_id = %pm_id",
        array(
          'pm_id' => array('int', $_GET['archive']),
        ));
    }
    # Okay, so it's in the archive
    else
    {
      # Move it out of the archive
      $db->query("
        UPDATE {$db->prefix}personal_messages
        SET folder = %folder
        WHERE pm_id = %pm_id",
        array(
          'pm_id' => array('int', $_GET['archive']),
          # If it was sent from the current member, we'll move
          # it to the outbox, otherwise the inbox
          'folder' => array('int', $sender_id == $user['id'] ? 2 : 1),
        ));
    }
  }
  
  # Redirect
  if(isset($_GET['sa']))
    redirect('index.php?action=pm;sa='. $_GET['sa']);
  else
    redirect('index.php?action=pm');
}

function personalmessages_delete()
{
  global $base_url, $db, $user, $page, $settings, $l;
  
  # Check who owns the PM in question and which folder it's in
  $result = $db->query("
    SELECT
      member_id, folder
    FROM {$db->prefix}personal_messages
    WHERE pm_id = %pm_id",
    array(
      'pm_id' => array('int', $_GET['delete']),
    ));
  @list($member_id, $folder) = $db->fetch_row($result);
  
  # Only allow them to delete the PM if they own it
  if($user['id'] == $member_id)
  {
    # If it's not in the recycle bin, move it there, otherwise delete it
    if($folder != 4)
    {
      # Move it to the recycle bin
      $db->query("
        UPDATE {$db->prefix}personal_messages
        SET
          folder = 4
        WHERE pm_id = %pm_id",
        array(
          'pm_id' => array('int', $_GET['delete']),
        ));
    }
    else
    {
      # Permanently deleting? We'll need the file size and read status then
      $result = $db->query("
        SELECT
          subject, body, status
        FROM {$db->prefix}personal_messages
        WHERE pm_id = %pm_id",
        array(
          'pm_id' => array('int', $_GET['delete']),
        ));
      @list($subject, $body, $status) = $db->fetch_row($result);
      
      # Get the PM's size in bytes, strlen() instead of mb_strlen() is deliberate
      $pm_size = strlen($subject) + strlen($body);
      
      # Decrease sender's PM total, total PM size and if applicable unread PM count
      $db->query("
        UPDATE {$db->prefix}members
        SET
          total_pms = total_pms - 1, pm_size = pm_size - $pm_size,
          unread_pms = unread_pms - %unread_pms
        WHERE member_id = %member_id",
        array(
          'member_id' => array('int', $user['id']),
          'unread_pms' => array('int', !$status),
        ));
      
      # Delete it
      $db->query("
        DELETE FROM {$db->prefix}personal_messages
        WHERE pm_id = %pm_id",
        array(
          'pm_id' => array('int', $_GET['delete']),
        ));
    }
  }
  
  # Redirect
  if(isset($_GET['sa']))
    redirect('index.php?action=pm;sa='. $_GET['sa']);
  else
    redirect('index.php?action=pm');
}

function personalmessages_recycle()
{
  global $base_url, $db, $user, $page, $settings, $l;
  
  # Check who owns the PM in question and which folder it's in
  $result = $db->query("
    SELECT
      member_id, sender_id, folder
    FROM {$db->prefix}personal_messages
    WHERE pm_id = %pm_id",
    array(
      'pm_id' => array('int', $_GET['undelete']),
    ));
  @list($member_id, $sender_id, $folder) = $db->fetch_row($result);
  
  # Only allow them to undelete the PM if they own it
  if($user['id'] == $member_id && $folder == 4)
  {
    $db->query("
      UPDATE {$db->prefix}personal_messages
      SET folder = %folder
      WHERE pm_id = %pm_id",
      array(
        'pm_id' => array('int', $_GET['undelete']),
        # If it was sent from the current member, we'll move
        # it to the outbox, otherwise the inbox
        'folder' => array('int', $sender_id == $user['id'] ? 2 : 1),
      ));
  }
  
  # Redirect
  if(isset($_GET['sa']))
    redirect('index.php?action=pm;sa='. $_GET['sa']);
  else
    redirect('index.php?action=pm');
}

function personalmessages_unread()
{
  global $base_url, $db, $user, $page, $settings, $l;
  
  # Check who owns the PM in question
  $result = $db->query("
    SELECT
      member_id
    FROM {$db->prefix}personal_messages
    WHERE pm_id = %pm_id",
    array(
      'pm_id' => array('int', $_GET['unread']),
    ));
  @list($member_id) = $db->fetch_row($result);
  
  # Only allow them to mark the PM as unread if they own it
  if($user['id'] == $member_id)
  {
    # Update the changed flag in the database
    $db->query("
      UPDATE {$db->prefix}personal_messages
      SET status = 0
      WHERE pm_id = %pm_id",
      array(
        'pm_id' => array('int', $_GET['unread']),
      ));
    
    # Increase member's unread PM total
    $db->query("
      UPDATE {$db->prefix}members
      SET
        unread_pms = unread_pms + 1
      WHERE member_id = %member_id",
      array(
        'member_id' => array('int', $member_id),
      ));
  }
  
  # Redirect
  if(isset($_GET['sa']))
    redirect('index.php?action=pm;sa='. $_GET['sa']);
  else
    redirect('index.php?action=pm');
}

function personalmessages_compose()
{
  global $base_url, $db, $user, $page, $settings, $source_dir, $l;
  
  # Load language
  language_load('personalmessages');
  
  # No errors yet and not sent yet
  $page['errors'] = array();
  
  # Sending?
  if(isset($_GET['send']))
  {
    personalmessages_send();
  }
  
  # Get the to default (GET) or entered (POST) fields
  $page['subject'] = isset($_REQUEST['subject']) ? $_REQUEST['subject'] : '';
  $page['body'] = isset($_REQUEST['body']) ? $_REQUEST['body'] : '';
  
  # Get the default (GET) or entered (POST) to field
  # The default is a member's ID whereas an entered value is one or more usernames
  if(isset($_POST['to']))
  {
    $page['to'] = $_POST['to'];
  }
  # Default (GET) value
  elseif(isset($_GET['to']))
  {
    # Get the member from the database
    $result = $db->query("
      SELECT
        displayName
      FROM {$db->prefix}members
      WHERE member_id = %member_id",
      array(
        'member_id' => array('int', $_GET['to']),
      ));
    
    # If a member was found, get the display name
    $row = $db->fetch_assoc($result);
    if($row)
      $page['to'] = $row['displayName'];
    else
      $page['to'] = '';
  }
  # No default or entered value for to field
  else
    $page['to'] = '';
  
  # Has the PM just been sent?
  $page['sent'] = isset($_GET['sent']);
  
  # Title
  $page['title'] = $l['pm_compose_title'];
  
  # And theme
  theme_load('personalmessages', 'personalmessages_compose_show');
}

function personalmessages_reply()
{
  global $base_url, $db, $user, $page, $settings, $source_dir, $l;
  
  # Load language
  language_load('personalmessages');
  
  # No errors yet and not sent yet
  $page['errors'] = array();
  
  # Get the PM that this one is replying to's information
  $result = $db->query("
    SELECT
      pm.pm_id AS id, pm.subject, pm.body, pm.time_sent, pm.status, pm.flagged, pm.read_receipt,
      pm.recipients, frm.member_id AS frm_id, frm.displayName AS frm_name, pm.time_sent
    FROM {$db->prefix}personal_messages AS pm
    LEFT JOIN {$db->prefix}members AS frm ON pm.sender_id = frm.member_id
    WHERE pm.pm_id = %pm_id",
    array(
      'pm_id' => array('int', $_GET['pm']),
    ));
  $page['pm'] = $db->fetch_assoc($result);
  
  # Reply all?
  $page['reply_all'] = $_GET['sa'] == 'reply-all';
  
  # Get the first recipient (Usually previous sender, but not if this is from the outbox)
  if($page['pm']['frm_id'] != $user['id'])
  {
    $page['pm']['recipient'] = array(
      'id' => $page['pm']['frm_id'],
      'name' => $page['pm']['frm_name'],
    );
  }
  
  # Make sure that at least one of the people they are replying to is not themself
  if($page['pm']['frm_id'] != $user['id'] || ($page['reply_all'] && $page['pm']['recipients'] != $user['id']))
  {
    # Sending?
    if(isset($_GET['send']))
    {
      personalmessages_send();
    }
    
    # Has the PM just been sent?
    $page['sent'] = isset($_GET['sent']);
    
    # Add Re: to the subject
    if(mb_substr($page['pm']['subject'], 0, mb_strlen($l['pm_reply_re'])) != $l['pm_reply_re'])
      $page['pm']['subject'] = $l['pm_reply_re']. $page['pm']['subject'];
    
    # Surround the body of PM with [quote] BBCode
    $page['pm']['body'] = '[quote from='. $page['pm']['frm_name']. ' time='. $page['pm']['time_sent']. "]\r\n". $page['pm']['body']. "\r\n[/quote]\r\n\r\n";
    
    # Add extra information for reply all
    if($page['reply_all'])
    {
      # Get the recipients
      $result = $db->query("
        SELECT
          member_id AS id, displayName AS name
        FROM {$db->prefix}members
        WHERE FIND_IN_SET(member_id, %recipients) AND member_id != %me",
        array(
          'recipients' => array('string', $page['pm']['recipients']),
          'me' => array('int', $user['id']),
        ));
      
      # If the previous sender is the replier, we'll not make them in the recipients
      if($page['pm']['frm_id'] == $user['id'])
      {
        $row = $db->fetch_assoc($result);
        $page['pm']['recipient'] = $row;
      }
      
      # Get the rest of the recipients
      $page['pm']['extra_recipients'] = array();
      while($row = $db->fetch_assoc($result))
        $page['pm']['extra_recipients'][] = $row;
    }
    
    # Title
    $page['title'] = $l['pm_reply_title'];
    
    # And theme
    theme_load('personalmessages', 'personalmessages_reply_show');
  }
  # They're replying to themself...
  else
  {
    # Title
    $page['title'] = $l['pm_reply_self_title'];
    
    # And theme
    theme_load('personalmessages', 'personalmessages_reply_show_self');
  }
}

function personalmessages_forward()
{
  global $base_url, $db, $user, $page, $settings, $source_dir, $l;
  
  # Load language
  language_load('personalmessages');
  
  # No errors yet and not sent yet
  $page['errors'] = array();
  
  # Sending?
  if(isset($_GET['send']))
  {
    personalmessages_send();
  }
  
  # Has the PM just been sent?
  $page['sent'] = isset($_GET['sent']);
  
  # Get the PM that this one is replying to's information
  $result = $db->query("
    SELECT
      pm.pm_id AS id, pm.subject, pm.body, pm.time_sent, pm.status, pm.flagged, pm.read_receipt,
      pm.recipients, frm.member_id AS frm_id, frm.displayName AS frm_name, pm.time_sent
    FROM {$db->prefix}personal_messages AS pm
    LEFT JOIN {$db->prefix}members AS frm ON frm.member_id = pm.sender_id
    WHERE pm.pm_id = %pm_id",
    array(
      'pm_id' => array('int', $_GET['pm']),
    ));
  $page['pm'] = $db->fetch_assoc($result);
  
  # Add Fwd: to the subject
  if(mb_substr($page['pm']['subject'], 0, mb_strlen($l['pm_forward_fwd'])) != $l['pm_reply_fwd'])
    $page['pm']['subject'] = $l['pm_forward_fwd']. $page['pm']['subject'];
  
  # Surround the body of PM with [quote] BBCode
  $page['pm']['body'] = '

[quote from='. ($page['pm']['frm_id'] ? $page['pm']['frm_name'] : $settings['site_name']). ' time='. $page['pm']['time_sent']. ']
'. $page['pm']['body']. '
[/quote]';
  
  # Title
  $page['title'] = $l['pm_reply_title'];
  
  # And theme
  theme_load('personalmessages', 'personalmessages_forward_show');
}

function personalmessages_receipt()
{
  global $base_url, $db, $user, $page, $settings, $l;
  
  # Load language
  language_load('personalmessages');
  
  # Get the PM
  $pm = isset($_GET['pm']) ? $_GET['pm'] : 0;
  
  # Check who owns the PM in question and which folder it's in
  $result = $db->query("
    SELECT
      pm.member_id, mem.displayName, pm.sender_id, pm.subject, pm.read_receipt
    FROM {$db->prefix}personal_messages AS pm
    LEFT JOIN {$db->prefix}members AS mem ON mem.member_id = pm.member_id
    WHERE pm.pm_id = %pm_id",
    array(
      'pm_id' => array('int', $pm),
    ));
  @list($member_id, $member_name, $sender_id, $subject, $read_receipt) = $db->fetch_row($result);
  
  # Only allow them to archive/unarchive the PM if they own it
  if($user['id'] == $member_id && $read_receipt)
  {
    # Are they sending the receipt?
    if($_GET['receipt'] == 'send')
    {
      # The subject of the read receipt
      $subject = sprintf($l['pm_read_receipt_subject'], $subject);
      
      # The body of the read receipt
      $body = sprintf($l['pm_read_receipt_body'], '[url='. $base_url. '/index.php?action=profile;u='. $member_id. ']'. $member_name. '[/url]');
      
      # Get the PM's size in bytes, strlen() instead of mb_strlen() is deliberate
      $pm_size = strlen($subject) + strlen($body);
      
      # Send the receipt
      $db->insert('insert', $db->prefix. 'personal_messages',
            array(
              'member_id' => 'int', 'folder' => 'int', 'recipients' => 'text',
              'sender_id' => 'int', 'sender_ip' => 'text', 'subject' => 'text',
              'body' => 'text', 'time_sent' => 'int',
              ),
            array(
              $sender_id, '1', $sender_id,
              0, '', $subject,
              $body, time_utc(),
              ),
            array());
      
      # Increase receipt requester's unread PMs, PM total and total PM size
      $db->query("
        UPDATE {$db->prefix}members
        SET
          unread_pms = unread_pms + 1, total_pms = total_pms + 1,
          pm_size = pm_size + $pm_size
        WHERE member_id = %member_id",
        array(
          'member_id' => array('int', $sender_id),
        ));
      
      # Remove the receipt requested tag
      $db->query("
        UPDATE {$db->prefix}personal_messages
        SET read_receipt = 0
        WHERE pm_id = %pm_id",
        array(
          'pm_id' => array('int', $pm),
        ));
    }
    # Are they denying the receipt?
    elseif($_GET['receipt'] == 'deny')
    {
      # Remove the receipt requested tag
      $db->query("
        UPDATE {$db->prefix}personal_messages
        SET read_receipt = 0
        WHERE pm_id = %pm_id",
        array(
          'pm_id' => array('int', $pm),
        ));
    }
  }
  
  # Redirect
  redirect('index.php?action=pm;pm='. $pm);
}

function personalmessages_view()
{
  global $base_url, $theme_url, $db, $user, $page, $settings, $l;
  
  # Load language
  language_load('personalmessages');
  
  # Get the PM they're viewing
  $result = $db->query("
    SELECT
      pm.pm_id AS id, pm.subject, pm.body, pm.time_sent, pm.status, pm.flagged, pm.read_receipt,
      pm.recipients, frm.member_id AS frm_id, frm.displayName AS frm_name, pm.folder
    FROM {$db->prefix}personal_messages AS pm
    LEFT JOIN {$db->prefix}members AS frm ON frm.member_id = pm.sender_id
    WHERE pm.pm_id = %pm_id",
    array(
      'pm_id' => array('int', $_GET['pm']),
    ));
  $page['pm'] = $db->fetch_assoc($result);
  
  # If the sender is 'no one', mark their name as the site name, since that's who it must have come from
  if(!$page['pm']['frm_id'])
    $page['pm']['frm_name'] = $settings['site_name'];
  
  # Get the folder name
  switch($page['pm']['folder'])
  {
    case 2: $page['pm']['folder_name'] = 'outbox'; break;
    case 3: $page['pm']['folder_name'] = 'archive'; break;
    case 4: $page['pm']['folder_name'] = 'delete'; break;
    default: $page['pm']['folder_name'] = 'inbox';
  }
  
  # First let's see if it's marked as unread
  if(!$page['pm']['status'])
  {
    # Okay, so it's unread, let's mark it as read then
    $db->query("
      UPDATE {$db->prefix}personal_messages
      SET
        status = 1
      WHERE pm_id = %pm_id",
      array(
        'pm_id' => array('int', $_GET['pm']),
      ));
    $page['pm']['status'] = 1;
    
    # Decrease member's total unread PM count
    $db->query("
      UPDATE {$db->prefix}members
      SET
        unread_pms = unread_pms - 1
      WHERE member_id = %member_id",
      array(
        'member_id' => array('int', $user['id']),
      ));
    
    # Recount member's unread PMs for this page load
    $user['unread_pms'] -= 1;
  }
  
  # Parse the BBCode in the message
  $page['pm']['body'] = bbc($page['pm']['body'], true, 'pm-'. $_GET['pm']);
  
  # Get the recipients
  $result = $db->query("
    SELECT
      member_id AS id, displayName AS name
    FROM {$db->prefix}members
    WHERE FIND_IN_SET(member_id, %recipients)",
    array(
      'recipients' => array('string', $page['pm']['recipients']),
    ));
  
  # Sort out the recipients
  $page['pm']['recipient'] = array(
    'id' => '',
    'name' => '',
  );
  $page['pm']['extra_recipients'] = array();
  
  # Only if there is at least one recipient
  if($db->num_rows($result))
  {
    # Get the first recipient
    $row = $db->fetch_assoc($result);
    $page['pm']['recipient'] = $row;
    
    # Get all the other recipients
    while($row = $db->fetch_assoc($result))
      $page['pm']['extra_recipients'][] = $row;
  }
  
  # Title
  $page['title'] = $l['pm_view_title'];
  
  # And theme
  theme_load('personalmessages', 'personalmessages_view_show');
}

function personalmessages_list()
{
  global $base_url, $db, $user, $page, $settings, $l;
  
  # Load language
  language_load('personalmessages');
  
  # Get the folder they're viewing
  switch(isset($_GET['sa']) ? $_GET['sa'] : '')
  {
    case '':
      $page['folder_id'] = 1;
      $page['folder_name'] = 'inbox';
      break;
    case 'outbox':
      $page['folder_id'] = 2;
      $page['folder_name'] = 'outbox';
      break;
    case 'archive':
      $page['folder_id'] = 3;
      $page['folder_name'] = 'archive';
      break;
    case 'deleted':
      $page['folder_id'] = 4;
      $page['folder_name'] = 'deleted';
      break;
    default:
      redirect('index.php?action=pm');
  }
  
  # Get the parts of the URL
  $page['sa'] = $page['folder_name'] != 'inbox' ? 'sa='. $page['folder_name']. ';' : '';
  $page['page'] = !empty($_GET['page']) && $_GET['page'] != 1 ? 'page='. $_GET['page']. ';' : '';
  
  # Define possible things to sort by
  $sorts = array(
      'subject' => 'pm.subject',
      'recipients' => 'pm.recipients',
      'sender' => 'pm.sender_id',
      'sent' => 'pm.time_sent',
    );
  
  # Get the sort URL stuff
  $page['sort'] = isset($_GET['sort']) && in_array($_GET['sort'],array_keys($sorts)) ? $_GET['sort'] : 'sent';
  $page['sort'] .= isset($_GET['desc']) ? ';desc' : '';
  
  # Get the sort SQL stuff
  $sort_sql = isset($_GET['sort']) && in_array($_GET['sort'],array_keys($sorts)) ? $sorts[$_GET['sort']] : 'pm.time_sent';
  # Time sent is reversed
  if($sort_sql == 'pm.time_sent')
    $sort_sql .= isset($_GET['desc']) ? '' : ' DESC';
  else
    $sort_sql .= isset($_GET['desc']) ? ' DESC' : '';
  
  # Get the total PMs
  $num_pms = $db->query("
    SELECT
      COUNT(*)
    FROM {$db->prefix}personal_messages
    WHERE member_id = %member_id AND folder = %folder",
    array(
      'member_id' => array('int', $user['id']),
      'folder' => array('int', $page['folder_id']),
    ));
  @list($num_pms) = $db->fetch_row($num_pms);
  
  # Deal with the pagination stuff
  $page_num = isset($_GET['page']) ? (int)$_GET['page'] : 0;
  if(isset($_GET['sa']))
    $page['pagination'] = pagination_create($base_url. '/index.php?action=pm;sa='. $_GET['sa']. ';sort='. $page['sort'], $page_num, $num_pms);
  else
    $page['pagination'] = pagination_create($base_url. '/index.php?action=pm;sort='. $page['sort'], $page_num, $num_pms);
  
  # Get this member's personal messages
  $result = $db->query("
    SELECT
      pm.pm_id AS id, pm.subject, pm.time_sent, pm.status, pm.flagged, pm.recipients,
      pm.body, frm.member_id AS frm_id, frm.displayName AS frm_name, frm.avatar AS frm_avatar, frm.num_posts AS frm_num_posts, frm.location AS frm_location,
      frm.custom_title AS frm_custom_title, grp.group_name AS frm_group_name, grp.group_color AS frm_group_color,
      grp.stars AS frm_stars
    FROM {$db->prefix}personal_messages AS pm
    LEFT JOIN {$db->prefix}members AS frm ON frm.member_id = pm.sender_id
    LEFT JOIN {$db->prefix}membergroups AS grp ON grp.group_id = frm.group_id
    LEFT JOIN {$db->prefix}membergroups AS grp2 ON grp2.group_id = frm.post_group_id
    WHERE pm.member_id = %member_id AND pm.folder = %folder
    ORDER BY $sort_sql
    LIMIT $page_num, 10",
    array(
      'member_id' => array('int', $user['id']),
      'folder' => array('int', $page['folder_id']),
    ));
  
  # Process the data from the database into some arrays
  $page['pms'] = array();
  $recipients = '';
  while($row = $db->fetch_assoc($result))
  {
    # Sort out the member group stars.
    list($stars['amount'], $stars['image']) = explode('|', $row['frm_stars']);
    $row['frm_stars'] = $stars;
    
    # Sort out the avatar.
    $row['frm_avatar'] = return_avatar($row['frm_avatar']);
    
    # Add PM's data.
    $page['pms'][] = $row;
    
    # Add on all the recipients for this PM
    $recipients .= ','. $row['recipients'];
  }
  
  # Remove duplicate recipients
  $recipients = implode(',', array_unique(explode(',', mb_substr($recipients, 1))));
  
  # Get all the recipients for all the PMs on this page
  $result = $db->query("
    SELECT
      member_id AS id, displayName AS name
    FROM {$db->prefix}members
    WHERE FIND_IN_SET(member_id, %recipients)",
    array(
      'recipients' => array('string', $recipients),
    ));
  $recipients = array();
  while($row = $db->fetch_assoc($result))
    $recipients[$row['id']] = $row;
  
  # Add recipients to PMs
  foreach($page['pms'] as $key => $pm)
  {
    # No recipients added yet
    $page['pms'][$key]['recipient'] = array();
    $page['pms'][$key]['extra_recipients'] = array();
    
    # We need this as an array, not a string
    $pm['recipients'] = explode(',', $pm['recipients']);
    
    # Add recipient's data
    foreach($pm['recipients'] as $recipient)
    {
      # Make sure we get the first one in a different array
      if(!$page['pms'][$key]['recipient'])
        $page['pms'][$key]['recipient'] = $recipients[$recipient];
      else
        $page['pms'][$key]['extra_recipients'][] = $recipients[$recipient];
    }
    
    # We don't need this anymore, since we just processed it
    unset($page['pms'][$key]['recipients']);
  }
  
  # If there was at least one PM
  if($page['pms'])
  {
    # Title
    $page['title'] = $l['pm_title'];
    
    # And theme
    theme_load('personalmessages', 'personalmessages_folder_show_'. ($user['preference']['pm_display'] ? 'threaded' : 'list'));
  }
  else
  {
    # No PMs, let's let them know
    $page['title'] = $l['pm_folder_empty_title'];
    theme_load('personalmessages', 'personalmessages_folder_show_empty');
  }
}

function personalmessages_send()
{
  global $base_url, $db, $user, $page, $settings, $source_dir, $l;
  
  # Get recipients
  $recipients_string = !empty($_POST['recipients']) ? array_unique(array_filter(preg_split('/\s*,\s*/s',$_POST['recipients']))) : array();
  
  # Get subject and body
  $subject = isset($_POST['subject']) ? $_POST['subject'] : '';
  $body = isset($_POST['body']) ? $_POST['body'] : '';
  $read_receipt = isset($_POST['read_receipt']) ? $_POST['read_receipt'] : 0;
  $save_outbox = isset($_POST['outbox']) ? $_POST['outbox'] : false;
  
  # Get the time sent (Now)
  $time_sent = time_utc();
  
  # Get the PM's size in bytes, strlen() instead of mb_strlen() is deliberate
  $pm_size = strlen($subject) + strlen($body);
  
  # Convert recipient display names into member IDs
  $recipients = array();
  $to_sender = false;
  $recipient_errors = array();
  foreach($recipients_string as $recipient)
  {
    # Run the database query
    $result = $db->query("
      SELECT
        mem.member_id AS id, mem.displayName AS name, mem.gender, mem.pm_size AS pm_size,
        grp.allowed_pm_size, grp2.allowed_pm_size AS post_allowed_pm_size
      FROM {$db->prefix}members AS mem
      LEFT JOIN {$db->prefix}membergroups AS grp ON mem.group_id = grp.group_id
      LEFT JOIN {$db->prefix}membergroups AS grp2 ON mem.post_group_id = grp.group_id
      WHERE LOWER(displayName) = LOWER(%displayName)",
      array(
        'displayName' => array('string', $recipient),
      ), __FILE__,__LINE__);
    $row = $db->fetch_assoc($result);
    
    # Check that this recipient isn't the sender
    if($recipient == $user['id'])
      $to_sender = true;
    
    # Check that they have enough space for the PM
    if($row['allowed_pm_size'] && ($row['post_allowed_pm_size'] || $row['post_allowed_pm_size'] === null)
       && $row['pm_size'] / 1000 + $pm_size / 1000 > max($row['allowed_pm_size'],
                                           $row['post_allowed_pm_size']))
    {
      # Failed to send to this recipient
      if($row['gender'] == 2)
        $recipient_errors = sprintf($l['pm_compose_error_recipient_size_male'], $row['name']);
      elseif($row['gender'] == 1)
        $recipient_errors = sprintf($l['pm_compose_error_recipient_size_female'], $row['name']);
      else
        $recipient_errors = sprintf($l['pm_compose_error_recipient_size_unknown'], $row['name']);
      unset($recipients[$recipient]);
    }
    else
    {
      # Add the recipient
      $recipients[$recipient] = $row;
    }
  }
  
  # Check if they failed to send to all their recipients
  if(!$recipients && $recipient_errors)
    $page['errors'] = $recipient_errors;
  # Check that there was at least one recipient
  elseif(!$recipients)
    $page['errors'][] = $l['pm_compose_error_recipients_none'];
  # Check if any members couldn't be found
  elseif(count($recipients) != count(array_filter($recipients)))
    $page['errors'][] = $l['pm_compose_error_recipients_invalid'];
  # Check that they didn't try to send it to themselves
  elseif($to_sender)
    $page['errors'][] = $l['pm_compose_error_recipients_self'];
  
  # Check if the subject is too short
  if(mb_strlen($subject) < 3)
    $page['errors'][] = $l['pm_compose_error_subject'];
  
  # Check if the message body is too short
  if(mb_strlen($body) < 3)
    $page['errors'][] = $l['pm_compose_error_body'];
  
  # Check that there were no errors
  if(!$page['errors'])
  {
    # No fatal errors? Continue then
    # Add recipient errors (Which aren't fatal)
    if($recipient_errors)
      $page['errors'][] = $recipient_errors;
    
    # Remove all data from recipients except member IDs
    foreach($recipients as $key => $recipient)
      $recipients[$key] = $recipient['id'];
    
    # Create the PM in the recipients' inboxes
    foreach($recipients as $recipient)
    {
      # Insert the PM in the database
      $db->insert('insert', $db->prefix. 'personal_messages',
        array(
          'member_id' => 'int', 'folder' => 'int', 'recipients' => 'text',
          'sender_id' => 'int', 'sender_ip' => 'text', 'subject' => 'text',
          'body' => 'text', 'time_sent' => 'int', 'read_receipt' => 'int',
          ),
        array(
          $recipient, 1, implode(',', $recipients),
          $user['id'], $user['ip'], $subject,
          $body, $time_sent, $read_receipt,
          ),
        array());
      
      # Increase recipient's unread PMs, PM total and total PM size
      $db->query("
        UPDATE {$db->prefix}members
        SET
          unread_pms = unread_pms + 1, total_pms = total_pms + 1,
          pm_size = pm_size + $pm_size
        WHERE member_id = %member_id",
        array(
          'member_id' => array('int', $recipient),
        ));
    }
    
    # Check if they have enough space for the outbox copy
    if($save_outbox && $user['allowed_pm_size'] && $user['pm_size'] / 1000 + $pm_size / 1000 >= $user['allowed_pm_size'])
      $page['errors'][] = $l['pm_compose_error_outbox'];
    # Do they want it in their outbox?
    elseif($save_outbox)
    {
      # Create the PM in the sender's outbox
      $db->insert('insert', $db->prefix. 'personal_messages',
          array(
            'member_id' => 'int', 'folder' => 'int', 'recipients' => 'text',
            'sender_id' => 'int', 'sender_ip' => 'text', 'subject' => 'text',
            'body' => 'text', 'time_sent' => 'int', 'status' => 'int',
            ),
          array(
            $user['id'], '2', implode(',',$recipients),
            $user['id'], $user['ip'], $subject,
            $body, $time_sent,  1,
            ),
          array());
      
      # Increase sender's PM total and total PM size
        $db->query("
          UPDATE {$db->prefix}members
          SET
            total_pms = total_pms + 1, pm_size = pm_size + $pm_size
          WHERE member_id = %member_id",
          array(
            'member_id' => array('int', $user['id']),
          ));
    }
    
    # If there were no errors
    if(!$page['errors'])
    {
      # Redirect...
      redirect('index.php?action=pm;sa=compose;sent');
    }
  }
}
?>