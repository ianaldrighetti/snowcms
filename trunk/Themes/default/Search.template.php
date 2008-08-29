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
global $l, $settings;
  
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
?>