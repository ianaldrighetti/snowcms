<?php
//                      SnowCMS
//     Founded by soren121 & co-founded by aldo
// Developed by Myles, aldo, antimatter15 & soren121
//              http://www.snowcms.com/
//
//   SnowCMS is released under the GPL v3 License
//       which means you are free to edit and
//          redistribute it as your wish!
//
//                  Admin.php file


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
  
  // Make member's unread PMs none, because they have noticed there are new ones
  sql_query("UPDATE {$db_prefix}members SET `unread_pms` = '0' WHERE `id` = '{$user['id']}'");
  $user['unread_pms'] = 0;
  
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
  if(can('manage_members')) {
    // So they can, yippe for you! :P
    // Are they just viewing the list, or managing a member, or something else perhaps?
    if (!@$_REQUEST['u'] && !@$_REQUEST['ssa']) {
      // K, just load the list of members
      loadPMlist();
    }
    elseif (@$_REQUEST['u'] && !@$_REQUEST['ssa']) {
      // :o They are moderating/viewing someone's profile
      loadProf();
    }
    elseif (@$_REQUEST['u'] && @$_REQUEST['ssa']) {
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
  
  // Redirect post data into get data
  if (@$_POST['f'] == 'all' && @$_POST['s'])
    redirect('index.php?action=admin;sa=pms;s='.$_POST['s']);
  elseif (@$_POST['f'] && @$_POST['s'])
    redirect('index.php?action=admin;sa=pms;f='.$_POST['f'].';s='.$_POST['s']);
  elseif (@$_POST['f'] == 'all')
    redirect('index.php?action=admin;sa=pms');
  elseif (@$_POST['f'])
    redirect('index.php?action=admin;sa=pms;f='.$_POST['f']);
  elseif (@$_POST['s'])
    redirect('index.php?action=admin;sa=pms;s='.$_POST['s']);
  
  // Set some variables' defaults encase they don't get set in the following switch statement
  $settings['page']['id_desc'] = '';
  $settings['page']['username_desc'] = '';
  $settings['page']['group_desc'] = '';
  $settings['page']['joindate_desc'] = '';
  
  // Get the sort SQL ready for use in following SQL query
  switch (@$_REQUEST['s']) {
    // Sort by ID number
    case 'id':
      $sort = 'ORDER BY `id`, `reg_date`';
      $settings['page']['id_desc'] = '_desc';
      break;
    // Sort by ID number, descending
    case 'id_desc':
      $sort = 'ORDER BY `id` DESC, `reg_date`';
      break;
    // Sort by display name
    case 'username':
      $sort = 'ORDER BY `display_name`, `reg_date`';
      $settings['page']['username_desc'] = '_desc';
      break;
    // Sort by display name, descending
    case 'username_desc':
      $sort = 'ORDER BY `display_name` DESC, `reg_date`';
      break;
    // Sort by group name
    case 'group':
      $sort = 'ORDER BY `groupname`, `reg_date`';
      $settings['page']['group_desc'] = '_desc';
      break;
    // Sort by group name descending
    case 'group_desc':
      $sort = 'ORDER BY `groupname` DESC, `reg_date`';
      break;
    // Sort by date joined
    case 'joindate':
      $sort = 'ORDER BY `reg_date`';
      $settings['page']['joindate_desc'] = '_desc';
      break;
    // Sort by date joined, descending
    case 'joindate_desc':
      $sort = 'ORDER BY `reg_date` DESC';
      break;
    // No sort specified, so sort by date joined
    default:
      $sort = 'ORDER BY `reg_date`';
  }
  
  // Get the filter SQL ready for use in a following SQL query
  switch (@$_REQUEST['f']) {
    // Member is activated and not suspended or banned
    case 'active':   $filter = "WHERE `activated` = '1' AND `suspension` < '".time()."' AND `banned` = '0'"; break;
    // Member is activated
    case 'activated':   $filter = "WHERE `activated` = '1'"; break;
    // Member is unactivated
    case 'unactivated': $filter = "WHERE `activated` = '0'"; break;
    // Member is suspended
    case 'suspended':   $filter = "WHERE `suspension` > '".time()."'"; break;
    // Member is unsuspended
    case 'banned':      $filter = "WHERE `banned` = '1'"; break;
    // All members
    case '':      $filter = ""; break;
    // Members of a particular group
    default: $filter = "WHERE `group_id` = '".clean($_REQUEST['f'])."'";
  }
  
  // Get the member records of all members out of the database
  $all_members = sql_query("SELECT * FROM {$db_prefix}members LEFT JOIN {$db_prefix}membergroups ON `group` = `group_id` $filter $sort");
  // The total amount of members
  $settings['page']['total_members'] = @mysql_num_rows($all_members);
  
  // The current page number
  $page = @$_REQUEST['pg'];
  // If the page number is lower then zero then make it zero
  if ($page < 0)
    $page = 0;
  // If page number is higher then maximum, lower it until it isn't
  while ($settings['num_members'] * $page >= $settings['page']['total_members'] && $page > 0)
    $page -= 1;
  
  // The first member number of this page
  $settings['page']['first_member'] = 1 + $start = $page * $settings['num_members'];
  // The last member number of this page
  $settings['page']['last_member'] = $settings['page']['total_members'] < $start + $settings['num_members']
                                   ? $settings['page']['total_members']
                                   : $start + $settings['num_members'];
  // Get the member records of this page out of the database
  $members = sql_query("
               SELECT
                 `uid_to` AS `to_id`, `uid_from` AS `from_id`, `to_member`.`display_name` AS `to`,
                 `from_member`.`display_name` AS `from`, `subject`, `pm_id` AS `id`, `sent_time` AS `date_sent`
               FROM {$db_prefix}pms
               LEFT JOIN {$db_prefix}members AS `to_member` ON `uid_to` = `to_member`.`id`
               LEFT JOIN {$db_prefix}members AS `from_member` ON `uid_from` = `from_member`.`id`
               LIMIT $start, ".$settings['num_members']);
  // Convert the members from an SQL result resource into a multi-demensional array
  while ($row = mysql_fetch_assoc($members)) {
    $settings['page']['members'][] = $row;
  }
  
  // The current page number
  $settings['page']['page'] = $page;
  // The last page number
  $settings['page']['page_last'] = ceil($settings['page']['total_members'] / $settings['num_members']);
  
  // Get the page number from the query string
  if (@$_REQUEST['pg'])
    $settings['page']['page_get'] = @$_REQUEST['pg'];
  else
    $settings['page']['page_get'] = '';
   // Get the filter from the query string
  if (@$_REQUEST['f'])
    $settings['page']['filter_get'] = @$_REQUEST['f'];
  else
    $settings['page']['filter_get'] = '';
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
  if (mysql_num_rows($members)) {
    $settings['page']['title'] = $l['moderatepms_title'];
    loadTheme('ModeratePMs');
  }
  else
    loadTheme('ModeratePMs','NoMembers');
}

function loadProf() {
global $l, $settings, $db_prefix;
  
  // Load member data
  $result = sql_query("SELECT * FROM {$db_prefix}members LEFT JOIN {$db_prefix}membergroups ON `group` = `group_id` WHERE id = ".@$_REQUEST['u']);
  $row = mysql_fetch_assoc($result);
  $settings['page']['title'] = str_replace("%name%",$row['display_name'] ? $row['display_name'] : $row['username'],$l['moderatepms_moderate_title']);
  $settings['page']['member'] = $row;
  
  $settings['page']['member']['birthdate_day'] = ($row['birthdate']) ? date('j',$row['birthdate']) : '';
  $settings['page']['member']['birthdate_month'] = ($row['birthdate']) ? date('n',$row['birthdate']) : '';
  $settings['page']['member']['birthdate_year'] = ($row['birthdate']) ? date('Y',$row['birthdate']) : '';
  
  // Load groups
  $result = sql_query("SELECT * FROM {$db_prefix}membergroups");
  while ($row = mysql_fetch_assoc($result)) {
    if ($row['group_id'] != -1)
      $settings['page']['groups'][] = $row;
  }
  
  loadTheme('ModeratePMs','Moderate');
}

function processModeration() {
global $l, $db_prefix, $user, $settings, $cookie_prefix;
  
  // Note: Error handling needs work
  if (!ValidateSession(@$_REQUEST['sc']) || !@$_REQUEST['u'])
    die("Hacking Attempt...");
  
  // Note: If own group is edited glitches could occur
  
  // Check if someone else is using that username or display name
  $result = sql_query("SELECT * FROM {$db_prefix}members") or die(mysql_error());
  if (mysql_num_rows($result))
    while ($row = mysql_fetch_assoc($result)) {
      if ($_REQUEST['u'] != $row['id'] && ($_REQUEST['user_name'] == $row['username'] || $_REQUEST['user_name'] == $row['display_name']))
        $settings['error'] = $l['moderatepms_error_username_already_used'];
      if ($_REQUEST['u'] != $row['id'] && $_REQUEST['display_name'] != '' && ($_REQUEST['display_name'] == $row['username'] || $_REQUEST['display_name'] == $row['display_name']))
        $settings['error'] = $l['moderatepms_error_display_name_already_used'];
    }
  
  // Clean the user ID
  $u = clean($_REQUEST['u']);
  // Clean the username
  $username = clean($_REQUEST['user_name']);
  // Clean the display name
  $display_name = clean($_REQUEST['display_name']) ? clean($_REQUEST['display_name']) : $username;
  // Clean the email address
  $email = clean($_REQUEST['email']);
  // Clean the member group
  $group = clean($_REQUEST['group']);
  // Clean the birthdate
  $day = (int)@$_REQUEST['day'];
  $month = (int)@$_REQUEST['month'];
  $year = (int)@$_REQUEST['year'];
  if ($day && $year)
    $birthdate = strtotime($year.'-'.$month.'-'.$day);
  else
    $birthdate = 0;
  // Clean the avatar
  $avatar = clean(@$_REQUEST['avatar']);
  if (substr($avatar,0,7) != 'http://' && substr($avatar,0,8) != 'https://' && substr($avatar,0,6) != 'ftp://' && substr($avatar,0,7) != 'ftps://' && $avatar != '')
    $avatar = 'http://'.$avatar;
  // Clean the signature
  $signature = clean($_REQUEST['signature']);
  // Clean the profile text
  $profile = clean($_REQUEST['profile']);
  // Clean the password
  $password_new = clean($_REQUEST['password-new']);
  $password_verify = clean($_REQUEST['password-verify']);
  
  // Check for errors in data
  if (!$username)
    $_SESSION['error'] = $l['moderatepms_error_username_none'];
  if (!$email)
    $_SESSION['error'] = $l['moderatepms_error_email_none'];
  if(!preg_match("/^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$/i", @$_REQUEST['email']))
    $_SESSION['error'] = $l['moderatepms_error_email_invalid'];
  if (strlen($password_new) < 5 && $password_new)
    $_SESSION['error'] = $l['moderatepms_error_password_too_short'];
  if ($password_new != $password_verify && $password_new)
    $_SESSION['error'] = $l['moderatepms_error_password_failed_verification'];
  if (!@$_REQUEST['group'])
    $_SESSION['error'] = $l['moderatepms_error_group_invalid'];
  
  $password_new = md5($password_new);
  
  // Get current member information, so that it can replace any data this member isn't allowed to modify
  $result = sql_query("SELECT * FROM {$db_prefix}members WHERE `id` = '$u'");
  if (!mysql_num_rows($result))
    die('Internal error');
  $row = mysql_fetch_assoc($result);
  
  // Are they trying to change their username and are they allowed to?
  if (!can('moderate_usrname') && $username != $row['username'])
    $_SESSION['error'] = $l['moderatepms_error_notallowed_username'];
  // Are they trying to change their display name and are they allowed to?
  if (!can('moderate_display_name') && $display_name != $row['display_name'])
    $_SESSION['error'] = $l['moderatepms_error_notallowed_displayname'];
  // Are they trying to change their email address and are they allowed to?
  elseif (!can('moderate_email') && $email != $row['email'])
    $_SESSION['error'] = $l['moderatepms_error_notallowed_email'];
  // Are they trying to change their username and are they allowed to?
  if (!can('moderate_group') && $group != $row['group'])
    $_SESSION['error'] = $l['moderatepms_error_notallowed_group'];
  // Are they trying to change their birthdate and are they allowed to?
  elseif (!can('moderate_birthdate') && $birthdate != $row['birthdate'])
    $_SESSION['error'] = $l['moderatepms_error_notallowed_birthdate'];
  // Are they trying to change their avatar and are they allowed to?
  elseif (!can('moderate_avatar') && $avatar != $row['avatar'])
    $_SESSION['error'] = $l['moderatepms_error_notallowed_avatar'];
  // Are they trying to change their signature and are they allowed to?
  elseif (!can('moderate_signature') && $signature != $row['signature'])
    $_SESSION['error'] = $l['moderatepms_error_notallowed_signature'];
  // Are they trying to change their profile text and are they allowed to?
  elseif (!can('moderate_profile') && $profile != $row['profile'])
    $_SESSION['error'] = $l['moderatepms_error_notallowed_profile'];
  // Are they trying to change their password and are they allowed to?
  elseif (!can('moderate_password') && $password_new != $row['password'] && @$_REQUEST['password-new'] != '')
    $_SESSION['error'] = $l['moderatepms_error_notallowed_password'];
  
  if (!@$_SESSION['error']) {
  // Update member's data
  if ($_REQUEST['password-new']) // And change password
    sql_query("UPDATE {$db_prefix}members SET `username` = '$username', `display_name` = '$display_name', `email` = '$email', `birthdate` = '$birthdate', `avatar` = '$avatar', `password` = '$password_new', `group` = '$group', `signature` = '$signature', `profile` = '$profile' WHERE `id` = '{$_REQUEST['u']}'") or die('Internal error');
  else // And don't change password
    sql_query("UPDATE {$db_prefix}members SET `username` = '$username', `display_name` = '$display_name', `email` = '$email', `birthdate` = '$birthdate', `avatar` = '$avatar', `group` = '$group', `signature` = '$signature', `profile` = '$profile' WHERE `id` = '$u'") or die('Internal error');
  
    // If they changed their own username change settings to keep 'em logged in
    if ($_REQUEST['u'] == $user['id']) {
      setcookie($cookie_prefix.'username',$_REQUEST['user_name']);
      // More settings if they changed their p[assword
      if ($_REQUEST['password-new']) {
        setcookie($cookie_prefix."password", $password_new);
        $_SESSION['pass'] = $password_new;
      }
    }
  }
  
  redirect('index.php?action=admin;sa=pms;u='.clean_header($_REQUEST['u']));
}

?>