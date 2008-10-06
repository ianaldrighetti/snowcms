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
//                  Search.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));
  
function fSearch() {
global $l, $settings, $user, $db_prefix;
  
  // Does this member even have permission to search?
  if (can('search')) {
    // Translate post data into get data
    if (@$_POST['q'])
      redirect('forum.php?action=search;q='.str_replace('=','%3D',str_replace(';','%3B',str_replace(' ','+',clean_header($_POST['q'])))));
    
    // If a search query has been entered, start searching
    if (@$_REQUEST['q']) {
      // Get the search query ready
      $q = explode('+',str_replace('%3D','=',str_replace('%3B',';',clean($_REQUEST['q']))));
      // Add regex to the search query words
      foreach ($q as $key => $value) {
        $q[$key] = '/'.$value.'/i';
      }
      
      // Get all the messages, ready to search them
      $result = sql_query("SELECT * FROM {$db_prefix}messages LEFT JOIN {$db_prefix}boards AS b ON `messages`.`bid` = `b`.`bid` WHERE {$user['board_query']}") or die(mysql_error());
      $results = array();
      $amount = 0;
      // Search one message at a time
      while ($row = mysql_fetch_assoc($result)) {
        $i = 0;
        // Search one keyword at a time
        while ($i < count($q)) {
          // Is it a match?
          if (preg_match($q[$i],$row['body'])) {
            // It is, yay!
            // Now we have to see if this topic has already been found with a different keyword/message
            $already = false;
            foreach ($results as $key => $value) {
              if ($value['tid'] == $row['tid'])
                $already = $key;
            }
            // Was it already found?
            if ($already !== false)
              // It was, raise the amount of times it has been found
              $results[$already]['times'] += 1;
            else {
              // It wasn't, add it to the found search results
              $topic = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}topics WHERE `tid` = {$row['tid']}"));
              $first_msg = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}messages LEFT JOIN {$db_prefix}members ON `uid` = `id` LEFT JOIN {$db_prefix}membergroups ON `group` = `group_id` WHERE `mid` = '{$topic['first_msg']}'"));
              $results[$amount] = $first_msg;
              $results[$amount]['times'] = 1;
              $amount += 1;
            }
          }
          $i += 1;
        }
      }
      
      // Sort search results in the order of relevency
      $ordered = array();
      $i = 0;
      while ($i < count($results)) {
        $highest['times'] = 0;
       // Sort each one, one at a time
        foreach ($results as $key => $value) {
          // Is thisone's relevency higher then the highest so far?
          if ($value['times'] > $highest['times']) {
            // It is!
            $highest = $value;
            // Set it's relevency to zero, to stop it from coming up next time
            $results[$key]['times'] = 0;
          }
        }
       // Is the highest's relevency even higher then zero?
        if ($highest['times'] > 0) {
          // Add the highest valued on to the ordered results
          $ordered[] = $highest;
        }
        else
          // It isn't, so stop this insanity from continuing
          break;
        $i += 1;
      }
      
      // Remove results not on this page
      $results = array();
      $page = @$_REQUEST['pg'];
      $i = 0;
      foreach ($ordered as $key => $value) {
        if ($i >= $page * $settings['num_search_results'] && $i < ($page + 1) * $settings['num_search_results'])
          $results[] = $ordered[$key];
        $i += 1;
      }
      
      // Transfer which variable contains the search results
      $settings['page']['results'] = $results;
      
      // Set the default query
      foreach ($q as $key => $value) {
        $q[$key] = substr($value,1,strlen($value)-3);
      }
      $settings['page']['query'] = $q ? implode(' ',$q) : '';
      
      // The total amount of topics
      $settings['page']['total_topics'] = mysql_num_rows(sql_query("SELECT * FROM {$db_prefix}topics"));
      // The previous page number
      $settings['page']['previous_page'] = $page - 1;
      // The current page number
      $settings['page']['current_page'] = (int)$page;
      // The next page number
      $settings['page']['next_page'] = $page + 1;
      
      // The query for use in URLs
      $settings['page']['query_url'] = clean(@$_REQUEST['q']);
      
      // Were there any results?
      if ($settings['page']['results']) {
        // There were, load theme
        $settings['page']['title'] = str_replace('%query%',$settings['page']['query'],$l['forumsearch_results_title']);
        LoadForum('Search','Results');
      }
      else {
        // There weren't, load this theme instead then
        $settings['page']['title'] = str_replace('%query%',$settings['page']['query'],$l['forumsearch_noresults_title']);
        LoadForum('Search','NoResults');
      }
    }
    // No search query yet
    else {
      // Load theme
      $settings['page']['title'] = $l['forumsearch_title'];
      LoadForum('Search');
    }
  }
  // They're not allowed to search
  else {
    // Load theme
    $settings['page']['title'] = $l['forumsearch_notallowed_title'];
    LoadForum('Search','NotAllowed');
  }
}
?>