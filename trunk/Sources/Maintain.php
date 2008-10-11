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

function Main() {
global $cmsurl, $db_prefix, $l, $mysql_db, $settings, $user;
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
?>