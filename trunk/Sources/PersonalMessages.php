<?php
//                      SnowCMS
//     Founded by soren121 & co-founded by aldo
// Developed by Myles, aldo, antimatter15 & soren121
//              http://www.snowcms.com/
//
//   SnowCMS is released under the GPL v3 License
//       which means you are free to edit and
//           redistribute it as you wish!
//
//                  Admin.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));

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
        $_SESSION['error'] = $l['pm_error_delete_invalidsession'];
    }
    // They are not allowed to delete PMs
    else
      $_SESSION['error'] = $l['pm_error_delete_notallowed'];
    // Redirect them so that if they refresh it won't do it again
    if (@$_REQUEST['sa'])
      redirect('forum.php?action=pm;sa='.clean($_REQUEST['sa']));
    else
      redirect('forum.php?action=pm');
  }
  
  // Do they want to report a PM?
  if (@$_REQUEST['report']) {
   // Clean anything that's used in an SQL query
    $report = (int)$_REQUEST['report'];
    // Are they allowed to report PMs?
    if (can('pm_report')) {
      // Is their session valid?
      if (ValidateSession(@$_REQUEST['sc'])) {
        $pm = mysql_fetch_array(sql_query("SELECT * FROM {$db_prefix}pms WHERE `pm_id` = '$report'"));
        // Does the PM exist?
        if ($pm) {
          // Was the PM sent to them?
          if ($pm['uid_to'] == $user['id'])
              sql_query("UPDATE {$db_prefix}pms SET `reported` = '1' WHERE `pm_id` = '$report'");
          // That PM wasn't sent to them
          else
            $_SESSION['error'] = $l['pm_error_report_doesntexist'];
        }
        // That PM doesn't exist
        else
          $_SESSION['error'] = $l['pm_error_report_doesntexist'];
      }
      // Their session verification failed
      else
        $_SESSION['error'] = $l['pm_error_report_invalidsession'];
    }
    // They are not allowed to report PMs
    else
      $_SESSION['error'] = $l['pm_error_report_notallowed'];
    // Redirect them so that if they refresh it won't do it again
    if (@$_REQUEST['msg'])
      redirect('forum.php?action=pm;msg='.clean($_REQUEST['msg']));
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
        case 'compile': PMCompile(); break;
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
  
  // Make member's unread PMs none, because they have noticed there are new ones
  sql_query("UPDATE {$db_prefix}members SET `unread_pms` = '0', `pms_lastread` = '".time()."' WHERE `id` = '{$user['id']}'");
  $user['unread_pms'] = 0;
  // Reload menus to reflect the change
  loadMenus();
  
  // The current page number
  $settings['page']['page'] = $page = $page_before = (int)@$_REQUEST['pg'];
  // If the page number is lower then zero then make it zero
  if ($page < 0)
    $page = 0;
  // The total amount of PMs
  $settings['page']['total_pages'] = mysql_num_rows(sql_query("SELECT * FROM {$db_prefix}pms WHERE `uid_to` = '{$user['id']}'"));
  // If page number is higher then maximum, lower it until it isn't
  while ($settings['num_pms'] * $page >= $settings['page']['total_pages'] && $page > 0)
    $page -= 1;
  // If the page changed, redirect
  if ($page != $page_before) {
    $page = clean_header($page ? ';pg='.$page : '');
    redirect('forum.php?action=pm'.$page);
  }
  // Total pages
  $settings['page']['page_last'] = @($settings['page']['total_pages'] / $settings['num_pms']);
  $offset = $page * $settings['num_pms'];
  
  // Get the PMs
  $settings['page']['messages'] = array();
  $result = sql_query("
    SELECT
      *, t.display_name AS to_name, f.display_name AS from_name, f.email AS from_email
    FROM {$db_prefix}pms
      LEFT JOIN {$db_prefix}members AS t ON `uid_to` = t.id
      LEFT JOIN {$db_prefix}members AS f ON `uid_from` = f.id
    WHERE `uid_to` = '{$user['id']}'
      AND `deleted_to` = '0'
    LIMIT $offset, {$settings['num_pms']}");
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
  
  // The current page number
  $settings['page']['page'] = $page = $page_before = (int)@$_REQUEST['pg'];
  // If the page number is lower then zero then make it zero
  if ($page < 0)
    $page = 0;
  // The total amount of PMs
  $settings['page']['total_pages'] = mysql_num_rows(sql_query("SELECT * FROM {$db_prefix}pms WHERE `uid_from` = '{$user['id']}'"));
  // If page number is higher then maximum, lower it until it isn't
  while ($settings['num_pms'] * $page >= $settings['page']['total_pages'] && $page > 0)
    $page -= 1;
  // If the page changed, redirect
  if ($page != $page_before) {
    $page = clean_header($page ? ';pg='.$page : '');
    redirect('forum.php?action=pm;sa=outbox'.$page);
  }
  // Total pages
  $settings['page']['page_last'] = @($settings['page']['total_pages'] / $settings['num_pms']);
  $offset = $page * $settings['num_pms'];
  
  // Get the PMs
  $settings['page']['messages'] = array();
  $result = sql_query("
    SELECT
      *, t.display_name AS to_name, f.display_name AS from_name, f.email AS from_email
    FROM {$db_prefix}pms
      LEFT JOIN {$db_prefix}members AS t ON `uid_to` = t.id
      LEFT JOIN {$db_prefix}members AS f ON `uid_from` = f.id
    WHERE `uid_from` = '{$user['id']}'
      AND `deleted_from` = '0'
    LIMIT $offset, {$settings['num_pms']}");
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
        // Update recipient's total unread messages
        sql_query("UPDATE {$db_prefix}members SET `unread_pms` = `unread_pms` + 1 WHERE `id` = '{$to['id']}'");
        // Redirect them
        redirect('forum.php?action=pm');
      }
      else
       // There was an error, redirect and inform them
       redirect('forum.php?action=pm;sa=compile');
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

function ModeratePMs() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  // Add link to the link tree
  AddTree('PMs','index.php?action=admin;sa=pms');
  
  // Are they alloed to moderate PMs?
  if (can('moderate_pms')) {
    // Are they just viewing the list, a message or something else?
    if (!@$_REQUEST['pm'] && !@$_REQUEST['ssa']) {
      // K, just load the list of messages
      loadPMlist();
    }
    elseif (@$_REQUEST['pm'] && !@$_REQUEST['ssa']) {
      // They are moderating/viewing a message
      loadMessage();
    }
    elseif (@$_REQUEST['pm'] && @$_REQUEST['ssa']) {
      // A Super Sub Action :D!
      switch ($_REQUEST['ssa']) {
        case 'process-moderate': processModeration(); break; // An admin/mod wants to change someone's member data
        case 'activate': activate(); break; // An admin/mod wants to activate a member's account
        case 'suspend': suspend(); break; // An admin/mod wants to suspend a member
        case 'unsuspend': unsuspend(); break; // An admin/mod wants to unsuspend a member
        case 'ban': ban(); break; // An admin/mod wants to ban a member
        case 'unban': unban(); break; // An admin/mod wants to unban a member
      }
    }
  }
  else {
    // You can't Manage Members silly!
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('ModeratePMs','Error');
  }
}

function loadPMlist() {
global $l, $settings, $db_prefix, $cmsurl;
  
  // Redirect to remove post data
  if (@$_REQUEST['redirect'])
    redirect('index.php?action=admin;sa=pms');
  
  // Redirect post data into get data
  if (@$_POST['s'])
    redirect('index.php?action=admin;sa=pms;s='.clean($_POST['s']));
  
  // Set some variables' defaults encase they don't get set in the following switch statement
  $settings['page']['to_desc'] = '';
  $settings['page']['from_desc'] = '';
  $settings['page']['subject_desc'] = '';
  $settings['page']['sentdate_desc'] = '';
  
  // Get the sort SQL ready for use in following SQL query
  switch (@$_REQUEST['s']) {
    // Sort by ID number
    case 'to':
      $sort = 'ORDER BY `to_member`.`display_name`, `sent_time`';
      $settings['page']['to_desc'] = '_desc';
      break;
    // Sort by ID number, descending
    case 'to_desc':
      $sort = 'ORDER BY `to_member`.`display_name` DESC, `sent_time`';
      break;
    // Sort by display name
    case 'from':
      $sort = 'ORDER BY `from_member`.`display_name`, `sent_time`';
      $settings['page']['from_desc'] = '_desc';
      break;
    // Sort by display name, descending
    case 'from_desc':
      $sort = 'ORDER BY `from_member`.`display_name` DESC, `sent_time`';
      break;
    // Sort by group name
    case 'subject':
      $sort = 'ORDER BY `subject`, `sent_time`';
      $settings['page']['subject_desc'] = '_desc';
      break;
    // Sort by group name descending
    case 'subject_desc':
      $sort = 'ORDER BY `subject` DESC, `sent_time`';
      break;
    // Sort by date joined
    case 'sentdate':
      $sort = 'ORDER BY `sent_time`';
      $settings['page']['sentdate_desc'] = '_desc';
      break;
    // Sort by date joined, descending
    case 'sentdate_desc':
      $sort = 'ORDER BY `sent_time` DESC';
      break;
    // No sort specified, so sort by date joined
    default:
      $sort = 'ORDER BY `sent_time`';
  }
  
  // Get the member records of all members out of the database
  $all_pms = sql_query("SELECT * FROM {$db_prefix}pms WHERE `reported` = '1'");
  // The total amount of members
  $settings['page']['total_pms'] = @mysql_num_rows($all_pms);
  
  // The current page number
  $page = @$_REQUEST['pg'];
  // If the page number is lower then zero then make it zero
  if ($page < 0)
    $page = 0;
  // If page number is higher then maximum, lower it until it isn't
  while ($settings['num_pms'] * $page >= $settings['page']['total_pms'] && $page > 0)
    $page -= 1;
  
  // The first member number of this page
  $settings['page']['first_pm'] = 1 + $start = $page * $settings['num_pms'];
  // The last member number of this page
  $settings['page']['last_pm'] = $settings['page']['total_pms'] < $start + $settings['num_pms']
                                   ? $settings['page']['total_pms']
                                   : $start + $settings['num_pms'];
  // Get the member records of this page out of the database
  $pms = sql_query("
               SELECT
                 *, `uid_to` AS `to_id`, `uid_from` AS `from_id`, `to_member`.`display_name` AS `to`,
                 `from_member`.`display_name` AS `from`, `subject`, `pm_id` AS `id`, `sent_time` AS `date_sent`
               FROM {$db_prefix}pms
               LEFT JOIN {$db_prefix}members AS `to_member` ON `uid_to` = `to_member`.`id`
               LEFT JOIN {$db_prefix}members AS `from_member` ON `uid_from` = `from_member`.`id`
               WHERE `reported` = '1'
               $sort
               LIMIT $start, {$settings['num_pms']}");
  // Convert the members from an SQL result resource into a multi-demensional array
  while ($row = mysql_fetch_assoc($pms)) {
    $settings['page']['pms'][] = $row;
  }
  
  // The current page number
  $settings['page']['page'] = $page;
  // The last page number
  $settings['page']['page_last'] = ceil($settings['page']['total_pms'] / $settings['num_pms']);
  
  // Get the page number from the query string
  if (@$_REQUEST['pg'])
    $settings['page']['page_get'] = @$_REQUEST['pg'];
  else
    $settings['page']['page_get'] = '';
  // Get the sort from the query string
  if (@$_REQUEST['s'])
    $settings['page']['sort_get'] = @$_REQUEST['s'];
  else
    $settings['page']['sort_get'] = '';
  
  // Load groups
  $result = sql_query("SELECT * FROM {$db_prefix}membergroups");
  while ($row = mysql_fetch_assoc($result)) {
    if ($row['group_id'] != -1)
      $settings['page']['groups'][] = $row;
  }
  
  // Load theme to show member lsit
  if (mysql_num_rows($pms)) {
    $settings['page']['title'] = $l['moderatepms_title'];
    loadTheme('ModeratePMs');
  }
  else
    loadTheme('ModeratePMs','NoPMs');
}

function loadMessage() {
global $l, $settings, $db_prefix;
  
  // Check if a message is being deleted
  if (@$_REQUEST['delete']) {
    $pm = (int)@$_REQUEST['pm'];
    // Check if still considered unread, if so, stop considering it as such :P
    $row = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}pms LEFT JOIN {$db_prefix}members ON `uid_to` = `id` WHERE `pm_id` = '$pm'"));
    if ((int)$row['unread_pms'] && $row['sent_time'] > $row['pms_lastread'])
      sql_query("UPDATE {$db_prefix}members SET `unread_pms` = `unread_pms` - 1 WHERE `id` = '{$row['id']}'");
    // Delete message
    sql_query("DELETE FROM {$db_prefix}pms WHERE `pm_id` = '$pm'");
    // Redirect back to PM list
    redirect('index.php?action=admin;sa=pms');
  }
  
  // Check if a message is being cleared as okay
  if (@$_REQUEST['clear']) {
    $pm = (int)@$_REQUEST['pm'];
    sql_query("UPDATE {$db_prefix}pms SET `reported` = '0' WHERE `pm_id` = '$pm'");
    // Redirect back to PM list
    redirect('index.php?action=admin;sa=pms');
  }
  
  // Load member data
  $result = sql_query("
               SELECT
                 *, `uid_to` AS `to_id`, `uid_from` AS `from_id`, `to_member`.`display_name` AS `to`,
                 `from_member`.`display_name` AS `from`, `subject`, `pm_id` AS `id`, `sent_time` AS `date_sent`
               FROM {$db_prefix}pms
               LEFT JOIN {$db_prefix}members AS `to_member` ON `uid_to` = `to_member`.`id`
               LEFT JOIN {$db_prefix}members AS `from_member` ON `uid_from` = `from_member`.`id`
               WHERE
                 `pm_id` = '".(int)$_REQUEST['pm']."'");
  $row = mysql_fetch_assoc($result);
  $settings['page']['title'] = str_replace("%subject%",$row['subject'],$l['moderatepms_message_title']);
  $settings['page']['member'] = $row;
  $settings['page']['member']['body'] = bbc($row['body']);
  $settings['page']['member']['body_bbc'] = $row['body'];
  
  // Load groups
  $result = sql_query("SELECT * FROM {$db_prefix}membergroups");
  while ($row = mysql_fetch_assoc($result)) {
    if ($row['group_id'] != -1)
      $settings['page']['groups'][] = $row;
  }
  
  loadTheme('ModeratePMs','Moderate');
}

?>