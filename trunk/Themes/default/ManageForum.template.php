<?php
// ManageForum.template.php by the SnowCMS Team
if(!defined('Snow'))
  die("Hacking Attempt..");

function Main() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  echo '
  <h2>', $l['manageforum_header'], '</h2>
  <p>', $l['manageforum_desc'], '</p>
  <br />
  <div class="acp_left">
    <p class="main"><a href="', $cmsurl, 'index.php?action=admin&sa=forum&fa=categories" title="', $l['mf_link_cats'], '">', $l['mf_link_cats'], '</a></p>
    <p class="desc">', $l['mf_link_cats_desc'], '</p>
  </div>
  <div class="acp_right">
    <p class="main"><a href="', $cmsurl, 'index.php?action=admin&sa=forum&fa=boards" title="', $l['mf_link_boards'], '">', $l['mf_link_boards'], '</a></p>
    <p class="desc">', $l['mf_link_boards_desc'], '</p>  
  </div>';
}

function ShowCats() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  echo '<h2>', $l['managecats_header'], '</h2>
        <p>', $l['managecats_desc'], '</p>
        <form action="" method="post">
        <table width="100%" id="mc">
          <tr>
            <td width="80%">', $l['mc_tr_cn'], '</td><td width="10%">', $l['mc_tr_order'], '</td><td width="9%">', $l['mc_tr_del'], '</td>
          </tr>';
        foreach($settings['cats'] as $cat) {
          echo '
          <tr>
            <td><input name="cat_name[', $cat['id'], ']" type="text" class="name" value="', $cat['name'], '"/></td><td><input name="cat_order[', $cat['id'], ']" type="text" class="order" value="', $cat['order'], '"/></td><td class="delete"><a href="', $cmsurl, 'index.php?action=admin&sa=forum&fa=categories&delete=', $cat['id'], '" onClick="return confirm(\'', $l['managecats_are_you_sure'], '\');">X</td>
          </tr>';
        }
        echo '
          <tr>
            <td></td><td><input name="update_cats" type="submit" value="', $l['managecats_update'], '"/></td><td></td>
          </tr>
        </table>
        </form>';
}
?>