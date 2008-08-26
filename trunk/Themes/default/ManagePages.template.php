<?php
// default/ManagePages.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");

function Main() {
global $cmsurl, $settings, $l, $user, $theme_url;
  echo '
  <h1>'.$l['managepages_header'].'</h1>
  <p>'.$l['managepages_desc'].'</p>';
  if($settings['page']['update_page']==1) {
    echo '
    <div id="page_fail">
      <p>'.$l['managepages_update_success'].'</p>
    </div>';  
  }
  elseif($settings['page']['update_page']==2) {
    echo '
    <div id="page_fail">
      <p>'.$l['managepages_update_failed'].'</p>
    </div>';
  }
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
  echo '
  <form action="'.$cmsurl.'index.php?action=admin;sa=managepages" method="post">
    <p><input type="hidden" name="create_page" value="true"></p>
    <p>'.$l['managepages_pagetitle'].' <input name="page_title" type="text" /> <input type="submit" value="'.$l['managepages_createpage'].'" /></p>
  </form>';
  if($settings['page']['num_pages']>0) {
    echo '
    <table width="100%" style="text-align: center">
      <tr>
        <th style="border-style: solid; border-width: 1px">'.$l['managepages_pagetitle'].'</th>
        <th style="border-style: solid; border-width: 1px">'.$l['managepages_pageowner'].'</th>
        <th style="border-style: solid; border-width: 1px">'.$l['managepages_datemade'].'</th>
        <th></th>
      </tr>';
    foreach($settings['page']['pages'] as $page) {
      echo '
      <tr>
        <td><a href="'.$cmsurl.'index.php?action=admin;sa=editpage;page_id='.$page['page_id'].'">'.$page['title'].'</a></td><td>';
      if ($page['page_owner'] != -1)
        echo '<a href="'.$cmsurl.'index.php?action=profile;u='.$page['page_owner'].'">'.$page['owner'].'</a>';
      else
        echo $page['owner'];
      echo '</td><td>'.$page['date'].'</td><td><a href="'.$cmsurl.'index.php?action=admin;sa=managepages;did='.$page['page_id'].'"><img src="'.$theme_url.'/'.$settings['theme'].'/images/delete.png" alt="'.$l['managepages_delete'].'" width="15" height="15" style="border: 0" /></a></td>
      </tr>';
    }
    echo '
    </table>';
  }
  else {
    echo '<p>'.$l['managepages_no_pages'].'</p>';
  }
}

function NoPage() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['managepages_no_page_header'].'</h1>
  
  <p>'.$l['managepages_no_page_desc'].'</p>';
}

function Editor() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.str_replace('%title%',$settings['page']['edit_page']['title'],$l['managepages_edit_header']).'</h1>
  <p>'.$l['managepages_edit_desc'].'</p>
  <form action="'.$cmsurl.'index.php?action=admin;sa=managepages" method="post">
    <p><input type="hidden" name="update_page" value="true" /></p>
    <table>
      <tr>
        <td>'.$l['managepages_editpage_title'].'</td><td><input name="page_title" type="text" value="'.$settings['page']['edit_page']['title'].'"/></td>
      </tr>
      <tr>
        <td colspan="2">'.$l['managepages_editpage_content'].'</td>
      </tr>
      <tr>
        <td colspan="2"><textarea name="page_content" rows="8" cols="40">'.$settings['page']['edit_page']['content'].'</textarea></td>
      </tr>
      <input name="page_id" type="hidden" value="'.$settings['page']['edit_page']['page_id'].'"/>
      <tr>
        <td>&nbsp;</td><td><input type="submit" value="'.$l['managepages_editpage_button'].'"/></td>
      </tr>
    </table>
  </form>'; 
}
?>