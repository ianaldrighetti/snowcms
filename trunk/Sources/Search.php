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
//                Search.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");
  
function fSearch() {
global $l, $settings, $db_prefix;
  
  // Does this member even have permission to search?
  if (can('search')) {
    // Translate post data into get data
    if (@$_POST['q'])
      redirect('forum.php?action=search;q='.str_replace(' ','+',clean_header($_POST['q'])));
    
    // If a search query has been entered, start searching
    if (@$_REQUEST['q']) {
      // Get the search query ready
      $q = explode('+',clean($_REQUEST['q']));
      // Add regex to the search query words
      foreach ($q as $key => $value) {
        $q[$key] = '/'.$value.'/i';
      }
      
      // Get all the messages, ready to search them
      $result = sql_query("SELECT * FROM {$db_prefix}messages");
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
              $first_msg = mysql_fetch_assoc(sql_query("SELECT * FROM {$db_prefix}messages LEFT JOIN {$db_prefix}members ON uid = id WHERE `mid` = {$topic['first_msg']}"));
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
      $i = (int)@$_REQUEST['pg'];
      while ($i < $settings['num_search_results']) {
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
      
      // Transfer which variable contains the search results
      $settings['page']['results'] = $ordered;
      
      // Set the default query
      foreach ($q as $key => $value) {
        $q[$key] = substr($value,1,strlen($value)-3);
      }
      $settings['page']['query'] = $q ? implode(' ',$q) : '';
      
      // Were there any results?
      if ($settings['page']['results']) {
        // There were, load theme
        $settings['page']['title'] = str_replace('%query%',$settings['page']['query'],$l['forum_search_results_title']);
        LoadForum('Search','Results');
      }
      else {
        // There weren't, load this theme instead then
        $settings['page']['title'] = str_replace('%query%',$settings['page']['query'],$l['forum_search_noresults_title']);
        LoadForum('Search','NoResults');
      }
    }
    // No search query yet
    else {
      // Load theme
      $settings['page']['title'] = $l['forum_search_title'];
      LoadForum('Search');
    }
  }
  // They're not allowed to search
  else {
    // Load theme
    $settings['page']['title'] = $l['forum_search_notallowed_title'];
    LoadForum('NotAllowed');
  }
}
?>