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
//                 Maintain.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));
/*
  Your site running slow? Or things just not right?
  Maybe your board says it has more topics or boards 
  in it then it really does?
  
  Good :P Thats what this is for! You can optimize 
  your MySQL tables, recount forum totals and
  statistics, and more...! =D All for just $9.95
  oh... xD
*/

function Maintain() {
global $cmsurl, $db_prefix, $l, $mysql_db, $settings, $user;
  
  // Add link to the link tree
  AddTree('Maintain','index.php?action=admin;sa=maintain');
  
  // Can they Maintain the site?
  if(can('maintain')) {
    // So what are they running? Optimizing? Recounting?
    $do = !empty($_REQUEST['do']) ? $_REQUEST['do'] : '';
    if($do == 'optimize') {
      // Optimize needs its own layout thing... So we can show it xD
      // Lets see how long it took... xD
      $start_op = microtime(true);
      // Show table status pl0x
      $result = sql_query("SHOW TABLE STATUS FROM `{$mysql_db}`");
      // We want to build a list of tables :D
      $tables = array();
      while($row = mysql_fetch_assoc($result)) {
        // MySQL OPTIMIZE TABLE only works on some ENGINEs,
        // Though I doubt we should worry, but hey, we care :P
        if($row['Engine'] == 'MyISAM' || $row['Engine'] == 'InnoDB')
          $tables[] = array(
                        'name' => $row['Name'],
                        'data_free' => $row['Data_free'],
                        'optimized' => 0
                      );
      }
      mysql_free_result($result);
      if(!count($tables)) {
        echo '<html><head><title>zOMG!</title><meta http-equiv="refresh" content="2;url=index.php?action=admin"></head><body><script type="text/javascript">alert(\'ZOMG! NO TABLES!?!?! WHAT DID YOU DO!\');</script></body></html>';
        die;
      }
      // Go through each table and optimize! WEEE!
      foreach($tables as $table) {
        // Optimize it!
        sql_query("OPTIMIZE TABLE `$mysql_db`.`$table[name]`");
      }
      // Now Table Status...
      $result = sql_query("SHOW TABLE STATUS FROM `{$mysql_db}`");
      $tables_freed = array();
      while($row = mysql_fetch_assoc($result)) {
        if($row['Engine'] == 'MyISAM' || $row['Engine'] == 'InnoDB') {
          $tables_freed[$row['Name']] = $row['Data_free'];
        }
      }
      mysql_free_result($result);
      // So now go through and see how much it was Optimized...
      foreach($tables as $i => $table) {
        // If data_free is 0, it can't get anymore optimized...
        if($table['data_free'] == 0)
          unset($tables[$i]);
        else
          $tables[$i]['optimized'] = $table['data_free'] - $tables_freed[$table['name']];
      }
      // So how long did it take?
      $replace = array(
                   '%num_tables%' => count($tables),
                   '%seconds%' => round(microtime(true) - $start_op, 5)
                 );
      $settings['it_took'] = str_replace(array_keys($replace), array_values($replace), $l['maintain_optimized_template']);
      // Cool... Done...
      $settings['tables'] = $tables;
      $settings['num_optimized'] = count($tables);
      // Now load the theme...
      $settings['page']['title'] = $l['maintain_optimize_title'];
      loadTheme('Maintain','Optimize');
    }
    else {
      // Really only Optimize Needs its own template...
      // other then that, the rest leech off this one :P
      if($do == 'recount') {
        // Recount forum totals and statistics!!!
        recountStats();
        $settings['alert'] = $l['maintain_recount_alert'];
      }
      /*
        The MySQL Database Backup also doesn't need its own page
        its just a download... But of course, it still has a few
        options you can do :P
      */
      elseif($do == 'backup') {
        // Simple as this... call on it xD!
        backupDB();
      }
      elseif($do == 'fix') {
        // Fix a couple possible board errors
        fixForumErrors();
        $settings['alert'] = $l['maintain_fixforum_alert']; 
      }
      elseif(!empty($_REQUEST['save_maintenance'])) {
        // Saving Maintenance Mode!
        $mode = (int)isset($_REQUEST['enable_maintenance']) ? 1 : 0;
        // Reason?
        $reason = !empty($_REQUEST['maintenance_reason']) ? clean($_REQUEST['maintenance_reason']) : '';
        // Now save it!
        sql_query("REPLACE INTO {$db_prefix}settings (`variable`,`value`) VALUES('maintenance_mode', $mode),('maintenance_reason','$reason')");
        loadSettings();
      }
      // Load the layout =D
      $settings['page']['title'] = $l['admin_maintain_title'];
      loadTheme('Maintain','Menu');
    }
  }
  else {
    // I dont think so...
    $settings['page']['title'] = $l['admin_error_title'];
    loadTheme('Admin','Error');
  }
}

/*
  Call on this, and it will recount the number
  of posts and topics in each board you have
  hopefully at some time it will do more :P
*/
function recountStats() {
global $db_prefix, $settings, $user;
  // Only if they can xD
  if(can('maintain')) {
    // Get a list of boards
    $result = sql_query("SELECT * FROM {$db_prefix}boards");
    // Don't try to hard, especially if there are no boards
    if(mysql_num_rows($result)) {
      // Go through them =D
      // This is quick and dirty now, the more boards, the more queries :P
      while($row = mysql_fetch_assoc($result)) {
        $board_id = $row['bid'];
        // How many topics? XD!
        $request = sql_query("SELECT * FROM {$db_prefix}topics WHERE bid = $board_id");
        $num_topics = mysql_num_rows($request);
        mysql_free_result($request);
        // Now posts...
        $request = sql_query("SELECT * FROM {$db_prefix}messages WHERE bid = $board_id");
        $num_posts = mysql_num_rows($request);
        mysql_free_result($request);
        // Now set it, and forget it! XD
        sql_query("UPDATE {$db_prefix}boards SET `numtopics` = $num_topics, `numposts` = $num_posts WHERE bid = $board_id");
        unset($board_id, $num_posts, $num_topics);
      }
    }
  }
}

/*
  This fixes a couple board errors,
  like if in case you delete topics
  or messages and the last post by
  info on the board is not right,
  you should run this option and it
  will be fixed
*/
function fixForumErrors() {
global $db_prefix;
  if(can('maintain')) {
    // Get all the boards...
    $result = sql_query("SELECT * FROM {$db_prefix}boards");
    // Any boards in the first place?
    if(mysql_num_rows($result)) {
      // Loop through the boards
      // And now we need to get the row
      // and then of course get the latest
      // topic info and such...
      while($row = mysql_fetch_assoc($result)) {
        $request = mysql_query("
          SELECT
            msg.mid, msg.tid, msg.bid, msg.uid, msg.subject, msg.poster_name
          FROM {$db_prefix}messages AS msg
          WHERE msg.bid = $row[bid]
          ORDER BY msg.mid DESC
          LIMIT 1");
          // Anything? :O
          if(mysql_num_rows($request)) {
            $msg = mysql_fetch_assoc($request);
            // Update the board...
            sql_query("UPDATE {$db_prefix}boards SET `last_msg` = $msg[mid], `last_uid` = $msg[uid], `last_name` = '$msg[poster_name]' WHERE bid = $row[bid]");
          }
          elseif($row['last_msg'] != 0 || $row['last_uid'] != 0 || $row['last_name'] != '') {
            // something is set, though we 
            // found nothing, so do just that
            sql_query("UPDATE {$db_prefix}boards SET `last_msg` = 0, `last_uid` = 0, `last_name` = '' WHERE bid = $row[bid]");
          }
          // Otherwise do nothing ;)
          mysql_free_result($request);
      }
    }
  }
}

/*
  This thing backs up your MySQL database
  with all your dataz and structure and
  everything you need :P
*/
function backupDB() {
global $mysql_db;  
  // What options?
  // !!! Needs improvement, this is a temporary fix
  // The structure
  if(isset($_REQUEST['struc']) && $_REQUEST['struc'] == 1)
    $option['struc'] = true;
  else
    $option['struc'] = false;
  // The data? The important stuff
  if(isset($_REQUEST['data']) && $_REQUEST['data'] == 1)
    $option['data'] = true;
  else
    $option['data'] = false;
  // Extended inserts?
  if(isset($_REQUEST['extended']) && $_REQUEST['extended'] == 1)
    $option['extended'] = true;
  else
    $option['extended'] = false;
  // Gzip it?
  if(isset($_REQUEST['gz']) && $_REQUEST['gz'] == 1)
    $option['gz'] = true;
  else
    $option['gz'] = false;
  // So yeah... Get the tables...
  $tables = getTables();
  // Loop through them... If anything to do so
  if(count($tables)) {
    $sql = 
      "---- \r\n".
      "-- MySQL Dump of `". $mysql_db. "` \r\n".
      "-- on ". date("F j, Y @ g:i:sA"). " \r\n".
      "---- \r\n";
    foreach($tables as $table) {
      // Well, did they want the structure?
      if($option['struc']) {
        $sql .=
          "\r\n\r\n".
          "---- \r\n".
          "-- Table structure for `". $mysql_db. "`.`". $table. "` \r\n".
          "---- \r\n";
        $sql .= showCreate($table). "\r\n";
      }
      // Show the data? :S
      if($option['data']) {
        $tmp = showData($table, $option['extended']);
        if(!empty($tmp)) {
          $sql .=
            "\r\n\r\n".
            "---- \r\n".
            "-- Table Data for `". $mysql_db. "`.`". $table. "` \r\n".
            "---- \r\n";
          // Now the dataz! xD
          $sql .= $tmp;
        }
      }
    }
    ob_clean();
    $file_name = $mysql_db.date('n-d-Y-g');
    // GZ output? If so selected master...
    if(function_exists('gzencode') && $option['gz']) {
      $sql = gzencode($sql);
      $ext = '.sql.gz';
      header('Content-Type: application/x-gzip');
    }
    else {
      $ext = '.sql';
		  header("Content-Type: text/sql");
		}
		header("Content-Encoding: none");
	  header("Content-Disposition:  filename=\"{$file_name}{$ext}\"");
	  header("Cache-Control: private");
	  header("Connection: close");
	  echo $sql;
	  ob_end_flush();
	  exit;
  }
  else
    exit;
}

// These following functions assist the function backupDB!
function getTables() {
global $mysql_db;
  $request = sql_query("SHOW TABLES FROM `$mysql_db`");
  $tables = array();
  while($row = mysql_fetch_assoc($request))
    $tables[] = $row['Tables_in_'. $mysql_db];
  return $tables;
}
// Show Create Table for the table given...
function showCreate($tbl_name) {
global $mysql_db;
  $result = sql_query("SHOW CREATE TABLE `$mysql_db`.`$tbl_name`");
  $table = '';
  while($row = mysql_fetch_assoc($result)) {
    $table = $row['Create Table']. ';';
  }
  return $table;
}
// Get the data from a table... Extended or not ;)
function showData($table, $extended = false) {
global $mysql_db;
  // Lets give us some time :)
  @set_time_limit(600);
  // Select all the data...
  $result = mysql_query("SELECT * FROM `$mysql_db`.`$table`");
  // Loop through it...
  $rows = array();
  while($row = mysql_fetch_assoc($result)) {
    $data = array();
    $keys = array_keys($row);
    foreach($keys as $key)
      $data[] = $row[$key];
    $rows[] = $data;
  }
  // Lets make the field declarations...
  $fields = array();
  // How many?
  $num_fields = mysql_num_fields($result);
  // Gotta get them all
  for($i = 0; $i < $num_fields; $i++)
    $fields[] = mysql_field_name($result, $i);
  // Free it!
  mysql_free_result($result);
  $field_list = '(`'. implode('`,`', $fields). '`)';
  // So this can go two ways now...
  // Extended Inserts or not (not recommended)
  $insert = '';
  if(!$extended && count($rows)) {
    // Not extended Inserts :)
    // Go through all the rows saved...
    foreach($rows as $row) {
      $insert .= "INSERT INTO `{$table}` {$field_list} VALUES('". implode("','", $row). "'); \r\n";
    }
    return $insert;
  }
  elseif($extended && count($rows)) {
    // Extended Inserts :(
    // Hold on, this might be a dumpy ride! D:!
    // Make the $values array...
    $values = array();
    // Loop through all the data...
    foreach($rows as $row) {
      $values[] = "('". implode("','", $row). "')";
    }
    $insert = "INSERT INTO `{$table}` {$field_list} VALUES". implode(",", $values). "; \r\n";
    return $insert;
  }
  else
    return $insert;
}
?>