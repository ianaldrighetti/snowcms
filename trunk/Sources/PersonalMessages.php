<?php
//                 SnowCMS
//           By aldo and soren121
//  Founded by soren121 & co-founded by aldo
//    http://snowcms.northsalemcrew.net
//
// SnowCMS is released under the GPL v3 License
// Which means you are free to edit it and then
//       redistribute it as your wish!
// 
//              Admin.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");

function PM() {
global $l, $user, $db_prefix;
  
  // Does it need redirecting?
  if (@$_REQUEST['redirect']) {
    if (@$_REQUEST['sa'])
      redirect('forum.php?action=pm;sa='.clean_header($_REQUEST['sa']));
    else
      redirect('forum.php?action=pm');
  }
  
  // Do they want to delete a PM?
  if (@$_REQUEST['did']) {
   // Clean anything that's used in an SQL query
    $did = (int)$_REQUEST['did'];
    // Are they allowed to delete PMs?
    if (can('pm_delete')) {
      // Is their session valid?
      if (ValidateSession(@$_REQUEST['sc'])) {
        $pm = mysql_fetch_array(sql_query("SELECT * FROM {$db_prefix}pms WHERE `pm_id` = '$did'"));
        // Does the PM exist?
        if ($pm) {
          // Was the PM sent to them?
          if ($pm['uid_to'] == $user['id']) {
            // Has the member who sent it also deleted it
            if ($pm['deleted_from'])
              // They both have, so permanently delete it
              sql_query("DELETE FROM {$db_prefix}pms WHERE `pm_id` = '$did'");
            else
              // Only this member has, so mark it as such
              sql_query("UPDATE {$db_prefix}pms SET `deleted_to` = '1' WHERE `pm_id` = '$did'");
          }
          // Did they send the PM?
          elseif ($pm['uid_from'] == $user['id']) {
            // Has the member who it was sent to also deleted it
            if ($pm['deleted_to'])
              // They both have, so permanently delete it
              sql_query("DELETE FROM {$db_prefix}pms WHERE `pm_id` = '$did'");
            else
              // Only this member has, so mark it as such
              sql_query("UPDATE {$db_prefix}pms SET `deleted_from` = '1' WHERE `pm_id` = '$did'");
          }
          // This PM doesn't PM doesn't belong to them
          else
            $_SESSION['error'] = $l['pm_error_delete_doesntexist'];
        }
        // That PM doesn't exist
        else
          $_SESSION['error'] = $l['pm_error_delete_doesntexist'];
      }
      // Their session verification failed
      else
        $_SESSION['error'] = $l['pm_error_invalid_session'];
    }
    // They are not allowed to delete pages
    else
      $_SESSION['error'] = $l['pm_error_delete_notallowed'];
    // Redirect them so that if they refresh it won't do it again
    if (@$_REQUEST['sa'])
      redirect('forum.php?action=pm;sa='.clean_header($_REQUEST['sa']));
    else
      redirect('forum.php?action=pm');
  }
  
  // Are they allowed to view PMs?
  if (can('pm_view')) {
    // Are they viewing a PM?
    if (@$_REQUEST['msg'])
      PMMessage();
    // Then which action is taking place?
    else
      switch (@$_REQUEST['sa']) {
        case 'outbox': PMOutbox(); break;
        case 'compile': PMCompile(); break; //////
        default: PMInbox();
      }
  }
  // They aren't allowed to view PMs
  else {
    $settings['page']['title'] = $l['pm_notallowed_title'];
    loadForum('PersonalMessages','NotAllowed');
  }
}

function PMInbox() {
global $l, $settings, $user, $db_prefix;
  
  // Get the PMs
  $settings['page']['messages'] = array();
  $result = sql_query("
    SELECT
      *, t.display_name AS to_name, f.display_name AS from_name, f.email AS from_email
    FROM {$db_prefix}pms
      LEFT JOIN {$db_prefix}members AS t ON `uid_to` = t.id
      LEFT JOIN {$db_prefix}members AS f ON `uid_from` = f.id
    WHERE `uid_to` = '{$user['id']}'
      AND `deleted_to` = '0'");
  while ($row = mysql_fetch_assoc($result)) {
    $settings['page']['messages'][] = array(
      'id' => $row['pm_id'],
      'to' => $row['to_name'],
      'to_id' => $row['uid_to'],
      'from' => $row['from_name'] ? $row['from_name'] : $row['name_from'],
      'from_id' => $row['uid_from'],
      'from_email' => $row['from_email'] ? $row['from_email'] : $row['email_from'],
      'from_ip' => $row['ip'],
      'time' => formattime($row['sent_time'],2),
      'subject' => $row['subject'],
      'body' => $row['body']
    );
  }
  
  $settings['page']['user'] = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}members WHERE `id` = '{$user['id']}'"));
  $settings['page']['user'] = $settings['page']['user']['display_name'];
  
  // They are viewing their own inbox
  if ($settings['page']['messages']) {
    // It is not empty
    $settings['page']['title'] = $l['pm_inbox_title'];
    loadForum('PersonalMessages','Inbox');
  }
  else {
    // It is empty
    $settings['page']['title'] = $l['pm_inbox_empty_title'];
    loadForum('PersonalMessages','InboxEmpty');
  }
}

function PMOutbox() {
global $l, $settings, $db_prefix, $user;
  
  // Get the PMs
  $settings['page']['messages'] = array();
  $result = sql_query("
    SELECT
      *, t.display_name AS to_name, f.display_name AS from_name, f.email AS from_email
    FROM {$db_prefix}pms
      LEFT JOIN {$db_prefix}members AS t ON `uid_to` = t.id
      LEFT JOIN {$db_prefix}members AS f ON `uid_from` = f.id
    WHERE `uid_from` = '{$user['id']}'
      AND `deleted_from` = '0'");
  while ($row = mysql_fetch_assoc($result)) {
    $settings['page']['messages'][] = array(
      'id' => $row['pm_id'],
      'to' => $row['to_name'],
      'to_id' => $row['uid_to'],
      'from' => $row['from_name'] ? $row['from_name'] : $row['name_from'],
      'from_id' => $row['uid_from'],
      'from_email' => $row['from_email'] ? $row['from_email'] : $row['email_from'],
      'from_ip' => $row['ip'],
      'time' => formattime($row['sent_time'],2),
      'subject' => $row['subject'],
      'body' => $row['body']
    );
  }
  
  $settings['page']['user'] = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}members WHERE `id` = '{$user['id']}'"));
  $settings['page']['user'] = $settings['page']['user']['display_name'];
  
  // They are viewing their own outbox
  if ($settings['page']['messages']) {
    // It is not empty
    $settings['page']['title'] = $l['pm_outbox_title'];
    loadForum('PersonalMessages','Outbox');
  }
  else {
    // It is empty
    $settings['page']['title'] = $l['pm_outbox_empty_title'];
    loadForum('PersonalMessages','OutboxEmpty');
  }
}

function PMMessage() {
global $l, $settings, $user, $db_prefix;
  
  // Admin are allowed to view other members' PMs
  $uid = clean(@$_REQUEST['u'] ? $_REQUEST['u'] : $user['id']);
  $msg = clean($_REQUEST['msg']);
  if ($uid == $user['id'] || can('view_pms_any')) {
    // Get the PMs
    $settings['page']['messages'] = array();
    $result = sql_query("
      SELECT *, t.display_name AS to_name, f.display_name AS from_name, f.email AS from_email
      FROM {$db_prefix}pms
      LEFT JOIN {$db_prefix}members AS t ON `uid_to` = t.id
      LEFT JOIN {$db_prefix}members AS f ON `uid_from` = f.id
      WHERE `pm_id` = '$msg'");
    $row = mysql_fetch_assoc($result);
    $settings['page']['message'] = array(
      'id' => $row['pm_id'],
      'to' => $row['to_name'],
      'to_id' => $row['uid_to'],
      'from' => $row['from_name'] ? $row['from_name'] : $row['name_from'],
      'from_id' => $row['uid_from'],
      'from_email' => $row['from_email'] ? $row['from_email'] : $row['email_from'],
      'from_ip' => $row['ip'],
      'time' => formattime($row['sent_time'],2),
      'subject' => $row['subject'],
      'body' => bbc($row['body'])
    );
    
    $settings['page']['user'] = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}members WHERE `id` = '$uid'"));
    $settings['page']['user'] = $settings['page']['user']['display_name'];
    
    // Load the theme
    if ($uid == $user['id']) {
      // They are viewing one of their own messages
      $settings['page']['title'] = $l['pm_message_title'];
      loadForum('PersonalMessages','Message');
    }
    else {
      // An admin is viewing one of someone else's messages
      $settings['page']['title'] = str_replace('%user%',$settings['page']['user'],$l['pm_message_admin_title']);
      loadForum('PersonalMessages','MessageAdmin');
    }
  }
  else {
    // They are not allowed to view this message
    $settings['page']['title'] = $l['pm_message_notallowed_title'];
    loadForum('PersonalMessages','NotAllowed');
  }
}

function PMCompile() {
global $l, $settings, $db_prefix, $user;
  
  // Are they allowed to send PMs?
  if (can('pm_compile')) {
    // Are they sending a PM?
    if (@$_REQUEST['to'] !== null) {
      // Clean the data
      $to = clean($_REQUEST['to']);
      $subject = clean(@$_REQUEST['subject']);
      $body = clean(@$_REQUEST['body']);
      
      // Convert the recipient username to an ID
      $to = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}members WHERE `display_name` = '$to'"));
      
      // Validate the information
      if (!$to['id'])
        $_SESSION['error'] = $l['pm_error_to_invalid'];
      elseif ($to['id'] == $user['id'])
        $_SESSION['error'] = $l['pm_error_to_self'];
      elseif (strlen(str_replace(' ','',$subject)) <= 3)
        $_SESSION['error'] = $l['pm_error_subject_short'];
      elseif (strlen(str_replace(' ','',$body)) <= 3)
        $_SESSION['error'] = $l['pm_error_body_short'];
      
      if (!@$_SESSION['error']) {
        // Send PM
        sql_query("INSERT INTO {$db_prefix}pms (`uid_to`, `uid_from`, `subject`, `sent_time`, `name_from`, `email_from`, `ip`, `body`)
                   VALUES ('{$to['id']}', '{$user['id']}', '$subject', '".time()."', '{$user['name']}', 'N/A', 'N/A', '$body')");
      }
      // Redirect them
      redirect('forum.php?action=pm');
    }
    
    $settings['page']['to'] = str_replace('+',' ',str_replace('%20',' ',clean(@$_REQUEST['to'])));
    $settings['page']['subject'] = str_replace('+',' ',str_replace('%20',' ',clean(@$_REQUEST['subject'])));
    $settings['page']['title'] = $l['pm_compile_title'];
    loadForum('PersonalMessages','Compile');
  }
  // They are not allowed to send PMs
  else {
    $settings['page']['title'] = $l['pm_compile_notallowed_title'];
    loadForum('PersonalMessages','CompileNotAllowed');
  }
}

function PMDelete() {
  
}
?>