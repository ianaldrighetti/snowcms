<?php
// Search.template.php by SnowCMS Dev's

if(!defined('Snow')) 
  die('Hacking Attempt...');

function Main() {
global $l, $settings;
  
  echo '
  <table class="search">
    <tr>
      <td>
        <form action="forum.php?action=search" method="post" style="text-align: center">
          <p>
            <input name="q" size="50" />
            <input type="submit" value="'.$l['forum_search_submit'].'" />
          </p>
        </form>
      </td>
    </tr>
  </table>
  ';
}

function Results() {
global $l, $settings, $cmsurl;
  
  // Show search bar
  echo '
  <table class="search">
    <tr>
      <td>
        <form action="forum.php?action=search" method="post" style="text-align: center">
          <p>
            <input name="q" value="'.$settings['page']['query'].'" size="50" />
            <input type="submit" value="'.$l['forum_search_submit'].'" />
          </p>
        </form>
        <hr />
        ';
    
    $prev_page = $settings['page']['previous_page'];
    $page = $settings['page']['current_page'];
    $next_page = $settings['page']['next_page'];
    $total_members = $settings['page']['total_members'];
    $query = $settings['page']['query_url'];
    
    // Show the pervious page link if it is at least page two
    if ($prev_page > 0)
      echo '<table width="100%">
        <tr><td><a href="'.$cmsurl.'forum.php?action=search;q='.$query.';pg='.$prev_page.'">'.$l['memberlist_previous_page'].'</a></td>
        ';
    // Show the previous page link if it is page one
    elseif ($prev_page == 0)
      echo '<table width="100%">
        <tr><td><a href="'.$cmsurl.'forum.php?action=search;q='.$query.'">'.$l['memberlist_previous_page'].'</a></td>
        ';
    // Don't show the previous page link, because it is the first page
    else
      echo '<table width="100%">
        <tr><td></td>
        ';
    // Show the next page link
    if (@($total_members / $settings['num_search_results']) > $next_page)
      echo '<td style="text-align: right"><a href="'.$cmsurl.'forum.php?action=search;q='.$query.';pg='.$next_page.'">'.$l['memberlist_next_page'].'</a></td></tr>
        </table>
        ';
    // Don't show the next page link, because it is the last page
    else
      echo '<td style="text-align: right"></td></tr>
        </table>
        ';
  
  // Show search results
  
  if ($settings['page']['results']) {
  foreach ($settings['page']['results'] as $result) {
    echo '
      <table class="results" width="100%">
      <tr>
        <td width="80%" class="header">
          <b><a href="forum.php?topic='.$result['tid'].'">'.$result['subject'].'</a></b>
        </td>
        <td rowspan="2" class="sidebar">
          <a href="index.php?action=profile;u='.$result['uid'].'">'.$result['display_name'].'</a>
        </td>
      </tr>
      <tr>
        <td class="body">
          '.bbc(substr($result['body'],0,300)).'...
          <br />
          <br />
        </td>
      </tr>
    </table>
    ';
  }
  }
  
  // Show search footer
  echo '
      </td>
    </tr>
  </table>
  ';
}

function NoResults() {
global $l, $settings;
  
  echo '
  <table class="search">
    <tr>
      <td>
        <form action="forum.php?action=search" method="post" style="text-align: center">
          <p>
            <input name="q" value="'.$settings['page']['query'].'" size="50" />
            <input type="submit" value="'.$l['forum_search_submit'].'" />
          </p>
        </form>
        <hr />
        <p>'.str_replace('%query%',$settings['page']['query'],$l['forum_search_noresults']).'</p>
      </td>
    </tr>
  </table>
  ';
}

function NotAllowed() {
global $l;
  
  echo '
  <table class="search">
    <tr>
      <td>
        <p>'.$l['forum_search_notallowed'].'</p>
      </td>
    </tr>
  </table>
  ';
}
?>