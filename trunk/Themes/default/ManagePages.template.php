<?php
// default/ManagePages.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['managepages_header'].'</h1>
  <p>'.$l['managepages_desc'].'</p>';
  if($settings['page']['make_page']['do']) {
    if($settings['page']['make_page']['status']) {
      echo '
      <div id="page_success">
        <p>'.$settings['page']['make_page']['info'].'</p>
      </div>';
    }
    else {
      echo '
      <div id="page_fail">
        <p>'.$settings['page']['make_page']['info'].'</p>
      </div>';    
    }
  }
  else {
    echo '
    <form action="" method="post">
      <table>
        <tr>
          <td>'.$l['managepages_pagetitle'].'</td><td><input name="page_title" type="text" value=""/></td>
        </tr>
        <tr>
          <td>&nbsp;</td><td><input name="make_page" type="submit" value="'.$l['managepages_makepage'].'"/></td>
        </tr>
      </table>
    </form>';
  }
  if($settings['page']['num_pages']>0) {
    echo '
    <table>
      <tr>
        <td>'.$l['adminpages_title_td'].'</td><td>'.$l['adminpages_pageowner'].'</td><td>'.$l['adminpages_datemade'].'</td>
      </tr>';
    foreach($settings['page']['pages'] as $page) {
      echo '
      <tr>
        <td><a href="'.$cmsurl.'index.php?action=admin&sa=editpage&page_id='.$page['page_id'].'">'.$page['title'].'</a></td><td><a href="'.$cmsurl.'index.php?action=profile&u='.$page['page_owner'].'">'.$page['owner'].'</td><td>'.$page['date'].'</td>
      </tr>';
    }
    echo '
    </table>';
  }
  else {
    echo '<p>'.$l['adminpages_no_pages'].'</p>';
  }
}
?>