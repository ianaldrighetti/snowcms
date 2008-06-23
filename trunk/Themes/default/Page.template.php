<?php
// default/Page.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");
  
function Main() {
global $cmsurl, $l, $settings, $user;
  echo '
		<h2>'.$settings['page']['title'].'</h2>
		<hr>';
  if ($settings['page']['show_info'] == 1)
		echo '<small> by '.$settings['page']['owner'].' | '.$settings['page']['date'].'</small>';
  echo	'
		<p>'.$settings['page']['content'].'</p> 
		<br />
		';
}

function Error() {
global $cmsurl, $l, $settings, $user;
  echo '
  <h1>'.$l['page_error_header'].'</h1>
  <p>'.$l['page_error_details'].'</p>';

}  
function ListPage() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h2>'.$l['listpage_header'].'</h2>
  <p>'.$l['listepage_desc'].'</p><hr>';
  if($settings['page']['num_pages']>0) {
    echo '
    <table>
      <tr>
        <td>'.$l['adminpages_title_td'].'</td><td>'.$l['adminpages_pageowner'].'</td><td>'.$l['adminpages_datemade'].'</td>
      </tr>';
    foreach($settings['page']['pages'] as $page) {
      echo '
      <tr>
        <td><a href="'.$cmsurl.'index.php?action=page&page_id='.$page['page_id'].'">'.$page['title'].'</a></td><td><a href="'.$cmsurl.'index.php?action=profile&u='.$page['page_owner'].'">'.$page['owner'].'</td><td>'.$page['date'].'</td>
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