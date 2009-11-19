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

# !!! Needs major overhauling for Database Support !!!

#
# All Maintenance is done here :)
#
# void maintain_database();
#   - Allows you to manage your database such as backing up your database
#     or optimizing your database.
#
# array maintain_database_optimize();
#   - Call on this function and it will return an array with information
#     about the database optimization results
#   returns array - As said, returns information about the optimization
#                   performed on the database.
#
# void maintain_backup();
#   - Backs up your database and allows you to download the backup. You
#     can download the table structure, data, and choose to have the inserts
#     extended or not and also to have the backup be Gzipped.
#
# void maintain_forum();
#   - Maintain all forum stuff here, such as recounting statistics, reattribute posts
#     and so forth.
#
# void maintain_forum_posts();
#
# void maintain_statistics();
#
# void maintain_errors();
#   - Sure the aesthetics of the function name are unorthodox, but we are in changing
#     times of a deep movement rooted in the development team. For this tiny minority
#     may incite future generations and question the status quo of evil camelCase.
#     There may be a war, but this body can not stand to the detriment of progress 
#     indefinitely. It's cold embrace for innovation must be stopped for this would
#     make this description totally useless and probably make Myles and aldo kick 
#     me (antimatter15) off of the development team. I dont find that a necessarily
#     good thing but writing this is actually pretty fun.
#

function maintain_database()
{
  global $base_url, $db, $l, $page, $settings, $user;

  # Just incase someone got passed something else.
  error_screen('maintain_database');

  # So what are we doing?
  if(isset($_GET['optimize']))
  {
    # Optimizing! :)
    $page['title'] = $l['maintenance_optimize_title'];

    # The results :)
    $page['results'] = maintain_database_optimize();

    # Load the theme, and thats it! :D
    theme_load('admin_maintain', 'maintain_database_show');
  }
  elseif(isset($_GET['backup']))
  {
    # No theme ;) It will prompt you to download :)
    maintain_backup();
  }
  else
  {
    # Just a title and thats about it.
    $page['title'] = $l['admin_maintenance_title'];

    # Load the theme which shows options
    theme_load('admin_maintain', 'maintain_options_show');
  }
}

function maintain_database_optimize()
{
  global $base_url, $db_name, $db, $l, $page, $settings, $user;

  # Only if you can!!!
  if(!can('maintain_database'))
    return false;

  # !!! Needs to be recoded for database independent support
  return true;
}

function maintain_backup()
{
  global $base_url, $db_name, $db, $download_dir, $l, $page, $settings, $source_dir, $user;

  # Hey! Get out of here, unless you can be here :P
  error_screen('maintain_database');
  
  # Load the language
  language_load('admin_maintain');
  
  if(isset($_POST['process']) && $_POST['process'] == 'backup')
  {
    # Go through each table in the database one at a time
    $tables = array();
    foreach($db->list_tables() as $key => $table)
    {
      # Get the table's name
      $tables[$key]['name'] = $table;
      
      # Get the table's structure, if they want it
      if(!empty($_POST['structure']))
      {
        $tables[$key]['structure'] = $db->backup_table_structure($table, isset($_POST['onexists']) && ($_POST['onexists'] == 'drop' || $_POST['onexists'] == 'ignore') ? $_POST['onexists'] == 'drop' : '');
      }
      
      # Get the table's data, if they want it
      if($table['data'] && !empty($_POST['data']))
      {
        $tables[$key]['data'] = $db->backup_table_data($table, isset($_POST['extended_inserts']) ? $_POST['extended_inserts'] : false, isset($_POST['num_extended']) ? $_POST['num_extended'] : 10);
      }
    }
    
    # Format custom comments
    if($comments = isset($_POST['comments']) ? $_POST['comments'] : '')
      $comments = "\r\n". '--'. "\r\n". '-- '. preg_replace('/(\r\n|[\r\n])/', "\r\n". '-- ', $comments);
    
    # Get the backup's header comment
    $backup = '--
-- SnowCMS '. $settings['scmsVersion']. ' Backup
-- Backup of database: '. $db_name. '
-- Database engine: '. $db->sql_name. '
-- Time of backup: '. strftime('%Y-%m-%d %H:%M:%S', time_utc()). ' UTC
-- If backup fails visit www.snowcms.com for support'. $comments. '
--
';
    
    # Add each table's SQL to the backup
    foreach($tables as $table)
    {
      # Add the table structure if they want it
      if(!empty($_POST['structure']))
      {
        $backup .= '
--
-- Table structure for '. $table['name']. '
--
'. $table['structure']. '
';
      }
      
      # Add the data if there is any and if they want it
      if(!empty($table['data']))
      {
        $backup .= '
--
-- Table data for '. $table['name']. '
--
'. $table['data']. '
';
      }
    }
    
    # Remove the last newline
    $backup = mb_substr($backup, 0, -2);
    
    # Check if we're GZipping it
    if(!empty($_POST['gzip']) && function_exists('gzencode'))
    {
      # Set the content type and default file name
      header('Content-Type: application/x-gzip');
      header('Content-Disposition: filename="'. str_replace('"', '\\"', str_replace('\\', '\\\\', $db_name)). '_'. strftime('%Y-%m-%d', time_utc()). '.sql.gz"');
      
      # In case GZip output buffering is already on, we need to get rid of it
      ob_get_flush();
      
      # Compress the backup with GZi, the maximum amount possible, then output it
      echo gzencode($backup, 9);
    }
    else
    {
      # Set the content type and default file name
      header('Content-Type: text/sql; charset=UTF-8');
      header('Content-Disposition: filename="'. str_replace('"', '\\"', str_replace('\\', '\\\\', $db_name)). '_'. strftime('%Y-%m-%d', time_utc()). '.sql"');
      
      # Output the bakup
      echo $backup;
    }
    
    # Exit, since we've just sent out our SQL or GZip file
    exit;
  }
  
  # Get the options allowed
  $page['options'] = array(
    'table_drop' => $db->table_drop,
    'table_ignore' => $db->table_ignore,
    'extended_inserts' => $db->extended_inserts,
    'gzip' => function_exists('gzencode'),
  );
  
  # Load the theme
  $page['title'] = $l['maintain_backup_title'];
  
  theme_load('admin_maintain', 'maintain_backup_show');
}

function maintain_forum()
{
  global $base_url, $db, $l, $page, $settings, $user;

  # Only if you can, or if its enabled...
  if(empty($settings['forum_enabled']) || !can('maintain_forum'))
    error_screen();

  # So what we doing?
  if(!empty($_GET['reattribute']))
  {
    # Reattributing posts?
    # So email or username?
    $email = !empty($_REQUEST['email_from']) ? $_REQUEST['email_from'] : '';
    $username = !empty($_REQUEST['username_from']) ? $_REQUEST['username_from'] : '';

    # Who is it going to?
    $username_to = !empty($_REQUEST['username_to']) ? $_REQUEST['username_to'] : '';

    # Call on the function, which will return some information :)
    $page['reattribute'] = reattributePosts(!empty($email) ? 'email' : 'username', !empty($email) ? $email : $username, $username_to);
  }
  elseif(!empty($_GET['recount']))
  {
    # Recounting forum statistics? :)
    $page['recount'] = recountStatistics();
  }
}

function maintain_forum_posts($type, $email_or_username, $username_to)
{
  global $base_url, $db, $l, $page, $settings, $user;

  # Only if you can :P
  if(empty($settings['forum_enabled']) || !can('maintain_forum'))
    return false;

  # Our results.
  $results = array();

  # So lets see if there are even any posts with the email or username...
  $result = $db->query("
    SELECT
      COUNT(*)
    FROM {$db->prefix}messages
    WHERE %search_type = %email_or_username", 
  array(
    'search_type' => array('raw', $type == 'email' ? 'poster_email' : 'poster_name'),
    'email_or_username' => array('string', $email_or_username)
  ));
  list($results['num_to_reattribute']) = $db->fetch_row($result);

  # We can only change the posts if the member exists to where they are going ;)
  $result = $db->query("
    SELECT
      member_id, loginName, email
    FROM {$db->prefix}members
    WHERE loginName = %username_to
    LIMIT 1",
  array(
    'username_to' => array('string', $username_to)
  ));

  # So does this member exist?
  if($db->num_rows($result))
  {
    # We want to get all the information :)
    list($member_id, $loginName, $email) = $db->fetch_row($result);

    # Now update the posts...
    $db->query("
      UPDATE {$db->prefix}messages
      SET member_id = %member_id, poster_name = %loginName, poster_email = %email
      WHERE %search_type = %email_or_username",
    array(
      'member_id' => array('int', $member_id),
      'loginName' => array('string', $loginName),
      'email' => array('string', $email),
      'search_type' => array('raw', $type == 'email' ? 'poster_email' : 'poster_name'),
      'email_or_username' => array('string', $email_or_username)
    ));

    # How many were reattributed..?
    $results['num_reattributed'] = $db->affected_rows();

    # Just incase, any topics they have started?
    # Only if we have their name...
    if($type == 'username')
      $db->query("
        UPDATE {$db->prefix}topics
        SET starter_member_id = %member_id, starter_member_name = %loginName
        WHERE starter_member_name = %loginName",
      array(
        'member_id' => array('int', $member_id),
        'loginName' => array('string', $loginName)
      ));

    # Now all we need to do is add the number of 
    # posts reattributed to their post count.
    $db->query("
      UPDATE {$db->prefix}members
      SET num_posts = num_posts + %num_reattributed
      WHERE member_id = %member_id
      LIMIT 1",
    array(
      'num_reattributed' => array('int', $results['num_reattributed']),
      'member_id' => array('int', $member_id)
    ));
  }
  else
    # Nope... flag the error...
    # Yes! I know... I will do language stuffs later!
    $results['error'] = sprintf('Sorry, but the member %s was not found, so the posts could not be reattributed', stripslashes($username_to));

  # Return the results...
  return $results;
}

function maintain_statistics()
{
  global $base_url, $db, $l, $page, $settings, $user;

  # Only if you can :P
  if(empty($settings['forum_enabled']) || !can('maintain_forum'))
    return false;

  # Lets get the number of posts and topics for boards...
  $result = $db->query("
    SELECT
      board_id, COUNT(msg_id) AS num_posts
    FROM {$db->prefix}messages
    GROUP BY board_id", array());

  # Loop and set :)
  while($row = $db->fetch_assoc($result))
    $db->query("
      UPDATE {$db->prefix}boards
      SET num_posts = %num_posts
      WHERE board_id = %board_id
      LIMIT 1",
    array(
      'num_posts' => array('int', $row['num_posts']),
      'board_id' => array('int', $row['board_id'])
    ));

  # We did the posts, now the topics.
  $result = $db->query("
    SELECT
      board_id, COUNT(topic_id) AS num_topics
    FROM {$db->prefix}topics
    GROUP BY board_id", array());
  # Loop and update ;)
  while($row = $db->fetch_assoc($result))
    $db->query("
      UPDATE {$db->prefix}boards
      SET num_topics = %num_topics
      WHERE board_id = %board_id
      LIMIT 1",
    array(
      'num_topics' => array('int', $row['num_topics']),
      'board_id' => array('int', $row['board_id'])
    ));

  # Recounting statistics also includes getting the correct
  # last post of a board, go figure :P
  $result = $db->query("
    SELECT
      board_id
    FROM {$db->prefix}boards", array());
  while($row = $db->fetch_assoc($result))
  {
    # So get the last message ;)
    $request = $db->query("
      SELECT
        msg_id, member_id, poster_name
      FROM {$db->prefix}messages
      WHERE board_id = %board_id
      ORDER BY poster_time DESC
      LIMIT 1",
      array(
        'board_id' => array('int', $row['board_id'])
      ));

    # So fetch and yeah...
    $message = $db->fetch_assoc($request);

    # Update the board...
    $db->query("
      UPDATE {$db->prefix}boards
      SET last_msg_id = %last_msg_id, last_member_id = %last_member_id, last_member_name = %last_member_name
      WHERE board_id = %board_id
      LIMIT 1",
      array(
        'last_msg_id' => array('int', $message['msg_id']),
        'last_member_id' => array('int', $message['member_id']),
        'last_member_name' => array('string', $message['poster_name']),
        'board_id' => array('int', $row['board_id'])
      ));
  }
}

function maintain_errors()
{
  global $base_url, $db, $l, $page, $settings, $user;

  # Just incase someone got passed something else.
  error_screen('view_error_log');

  language_load('admin_maintain');

  # Just a title and thats about it.
  $page['title'] = $l['admin_maintain_error_title'];

  # Deleting any? Should be taken care of first!
  if((!empty($_REQUEST['delete_selected']) && !empty($_REQUEST['delete']) && is_array($_REQUEST['delete'])) || (!empty($_REQUEST['filter_delete']) && !empty($_REQUEST['filter'])))
  {
    security_sc('request');

    # How are you deleting? By ID or type..?
    if(!empty($_REQUEST['filter_delete']))
    {
      # You are deleting a specific type... Hmm. Whatever :P
      $db->query("
        DELETE FROM {$db->prefix}error_log
        WHERE error_type = %error_type",
        array(
          'error_type' => array('string', $_REQUEST['filter']),
        ));

      # Redirect to all...
      redirect('index.php?action=admin;sa=maintenance;area=error_log');
    }
    else
    {
      # Cool, an array of them :P
      $db->query("
        DELETE FROM {$db->prefix}error_log
        WHERE error_id IN(%delete_errors)",
        array(
          'delete_errors' => array('int_array', $_REQUEST['delete']),
        ));
    }
  }
  elseif(isset($_REQUEST['empty']))
  {
    security_sc('request');

    # Remove them ALL!!!
    $db->query("
      TRUNCATE {$db->prefix}error_log",
      array());
  }

  # Get the number of errors in each specific error type group...
  $result = $db->query("
    SELECT
      error_type, COUNT(*) AS num_errors
    FROM {$db->prefix}error_log
    GROUP BY error_type
    ORDER BY error_type ASC",
    array());

  $total_errors = 0;
  $page['error_index_array'] = array(
    'all' => array(
              'id' => 'all',
              'errors' => &$total_errors,
              'text' => $l['admin_maintain_error_all'],
              'href' => $base_url. '/index.php?action=admin;sa=maintenance;area=error_log'. (isset($_GET['asc']) ? ';asc' : ''),
              'selected' => true,
            ),
  );
  while($row = $db->fetch_assoc($result))
  {
    $page['error_index_array'][$row['error_type']] = array(
      'id' => $row['error_type'],
      'errors' => $row['num_errors'],
      'text' => isset($l['admin_maintain_error_'. $row['error_type']]) ? $l['admin_maintain_error_'. $row['error_type']] : $l['admin_maintain_error_unknown'],
      'href' => $base_url. '/index.php?action=admin;sa=maintenance;area=error_log;type='. $row['error_type']. (isset($_GET['asc']) ? ';asc' : ''),
      'selected' => !empty($_GET['type']) && $_GET['type'] == $row['error_type'],
    );

    # All not selected anymore..?
    if($page['error_index_array'][$row['error_type']]['selected'])
      $page['error_index_array']['all']['selected'] = false;

    # Add up the total for all :)
    $total_errors += $row['num_errors'];
  }

  # Optional URL parameters...
  $parameters = array(
    'member' => array('int', 'el.member_id = %member'),
    'member_name' => array('string', 'el.member_name = %member_name'),
    'ip' => array('string', 'el.ip = %ip'),
    'type' => array('string', 'el.error_type = %type'),
    'url' => array('string-40', 'SHA1(el.error_url) = %url'),
    'file' => array('string-40', 'SHA1(el.file) = %file'),
  );

  # Build up the query... For later use :)
  $query = array();
  $where_clause = array();
  $where = array();
  foreach($parameters as $param => $info)
    if(isset($_GET[$param]))
    {
      $query[$param] = $_GET[$param];
      $where_clause[] = $info[1];
      $where[$param] = array($info[0], $query[$param]);
    }

  # Technically we aren't viewing 'All errors'
  if(count($query) && $page['error_index_array']['all']['selected'])
    $page['error_index_array']['all']['selected'] = false;

  $page['error_index'] = array();
  foreach($page['error_index_array'] as $error_type)
    $page['error_index'][] = (!empty($error_type['selected']) ? '<strong>' : ''). '<a href="'. $error_type['href']. '" title="'. $l['admin_maintain_error_view']. ' \''. $error_type['text']. '\'">'. sprintf($l['maintain_error_category'], $error_type['text'], numberformat($error_type['errors'])). '</a>'. (!empty($error_type['selected']) ? '</strong>' : '');

  # Put it all together!
  $page['error_index'] = implode('&nbsp;&nbsp;&nbsp;', $page['error_index']);

  # Complete a couple things...
  $query = strtr(http_build_query($query), array('&amp;' => ';', '&' => ';'));
  $where_clause = implode(' ', $where_clause);
  if(empty($where_clause))
    $where_clause = '1 = 1';

  # Now how many errors in the error log?
  $result = $db->query("
    SELECT
      COUNT(*)
    FROM {$db->prefix}error_log AS el
    WHERE {$where_clause}",
    $where);

  @list($num_errors) = $db->fetch_row($result);

  if(empty($_GET['page']))
    $start = 0;
  else
    $start = $_GET['page'];

  $page['index'] = pagination_create($base_url. '/index.php?action=admin;sa=maintenance;area=error_log'. (!empty($query) ? ';'. $query : ''). (isset($_GET['asc']) ? ';asc' : ''), $start, $num_errors, 20);

  # Add our sort to the where array...
  $where['sort'] = array('raw', isset($_GET['asc']) ? 'ASC' : 'DESC');

  # Where we starting..?
  $where['start'] = array('int', $start);

  # Now we can begin to display the errors :P
  $result = $db->query("
    SELECT
      el.error_id, el.error_time, el.member_id, el.member_name, el.ip, el.error_url,
      el.error, el.error_type, el.file, el.line, mem.member_id AS member_exists
    FROM {$db->prefix}error_log AS el
      LEFT JOIN {$db->prefix}members AS mem ON mem.member_id = el.member_id
    WHERE {$where_clause}
    ORDER BY el.error_id %sort
    LIMIT %start,20",
    $where);

  $page['errors'] = array();
  while($row = $db->fetch_assoc($result))
  {
    $page['errors'][] = array(
      'id' => $row['error_id'],
      'date' => timeformat($row['error_time']),
      'member' => array(
                    'id' => $row['member_id'],
                    'ip' => $row['ip'],
                    'name' => $row['member_name'],
                    'href' => !empty($row['member_exists']) ? $base_url. '/index.php?action=profile;u='. $row['member_id'] : false,
                    'search' => $base_url. '/index.php?action=admin;sa=maintenance;area=error_log;member'. (!empty($row['member_exists']) ? '='. $row['member_id'] : '_name='. $row['member_name']). (isset($_GET['asc']) ? ';asc' : ''),
                    'search_ip' => $base_url. '/index.php?action=admin;sa=maintenance;area=error_log;ip='. $row['ip']. (isset($_GET['asc']) ? ';asc' : ''),
                  ),
      'url' => htmlspecialchars($row['error_url'], ENT_QUOTES, 'UTF-8'),
      'search_url' => $base_url. '/index.php?action=admin;sa=maintenance;area=error_log;url='. sha1($row['error_url']). (isset($_GET['asc']) ? ';asc' : ''),
      'error' => strtr($row['error'], array("\r\n" => '<br />', "\n" => '<br />', "\r" => '<br />')),
      'type' => array(
                  'text' => isset($l['admin_maintain_error_'. $row['error_type']]) ? $l['admin_maintain_error_'. $row['error_type']] : $l['admin_maintain_error_unknown'],
                  'id' => $row['error_type'],
                  'search' => $base_url. '/index.php?action=admin;sa=maintenance;area=error_log;type='. $row['error_type']. (isset($_GET['asc']) ? ';asc' : ''),
                ),
      'file' => htmlspecialchars($row['file'], ENT_QUOTES, 'UTF-8'),
      'search_file' => $base_url. '/index.php?action=admin;sa=maintenance;area=error_log;file='. sha1($row['file']). (isset($_GET['asc']) ? ';asc' : ''),
      'line' => (int)$row['line'],
    );
  }

  # Some JS for checking them all XD.
  $page['scripts'][] = $settings['default_theme_url']. '/js/error_log.js';

  $page['type'] = htmlspecialchars(!empty($_GET['type']) ? $_GET['type'] : '', ENT_QUOTES, 'UTF-8');

  # Load the theme which shows options
  theme_load('admin_maintain', 'maintain_errors_show');
}
?>