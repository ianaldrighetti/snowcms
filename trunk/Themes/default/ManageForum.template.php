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
    <p class="main"><a href="', $cmsurl, 'index.php?action=admin;sa=forum;fa=categories" title="', $l['mf_link_cats'], '">', $l['mf_link_cats'], '</a></p>
    <p class="desc">', $l['mf_link_cats_desc'], '</p>
  </div>
  <div class="acp_right">
    <p class="main"><a href="', $cmsurl, 'index.php?action=admin;sa=forum;fa=boards" title="', $l['mf_link_boards'], '">', $l['mf_link_boards'], '</a></p>
    <p class="desc">', $l['mf_link_boards_desc'], '</p>  
  </div>';
}

function ShowCats() {
global $cmsurl, $db_prefix, $l, $settings, $user, $theme_url;
  echo '<h1>', $l['managecats_header'], '</h1>
        <p>', $l['managecats_desc'], '</p>
        <form action="', $cmsurl, 'index.php?action=admin;sa=forum;fa=categories" method="post">
          <table width="100%" id="mc">
            <tr>
              <th class="border" width="80%">', $l['mc_tr_cn'], '</th><th class="border" width="10%">', $l['mc_tr_order'], '</th><th></th>
            </tr>';
          foreach($settings['cats'] as $cat) {
            echo '
            <tr>
              <td><input name="cat_name[', $cat['id'], ']" type="text" class="name" value="', $cat['name'], '"/></td>
              <td><input name="cat_order[', $cat['id'], ']" type="text" class="order" value="', $cat['order'], '"/></td>
              <td class="delete"><a href="', $cmsurl, 'index.php?action=admin;sa=forum;fa=categories;delete=', $cat['id'], ';sc=', $user['sc'], '" onClick="return confirm(\'', $l['managecats_are_you_sure'], '\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/delete.png" alt="'.$l['managecats_category_delete'].'" width="15" height="15" /></td>
            </tr>';
          }
          echo '
            <tr>
              <td></td><td><input name="update_cats" type="submit" value="', $l['managecats_update'], '"/></td><td></td>
            </tr>
          </table>
        </form>
        <br />
        <form action="', $cmsurl, 'index.php?action=admin;sa=forum;fa=categories" method="post">
          <table id="add_cat">
            <tr>
              <td colspan="2"><p class="add_header">', $l['managecats_add_header'], '</p></td>
            </tr>
            <tr>
              <td>', $l['managecats_catname'], ' <input class="cat_name" name="cat_name" type="text" value="', $l['mf_new_category'], '"/></td><td>', $l['managecats_order'], ' <input class="order" name="order" type="text" value=""/></td>
            </tr>
            <tr>
              <td></td><td><input name="add_cat" type="submit" value="', $l['managecats_addbutton'], '"/></td>
            </tr>
          </table>
        </form>';
}

function ShowBoards() {
global $cmsurl, $db_prefix, $l, $settings, $user, $theme_url;
  echo '
  <h2>', $l['manageboards_header'], '</h2>
  <p>', $l['manageboards_desc'], '</p>';
  if(count($settings['cats'])) {
    echo '
  <form action="', $cmsurl, 'index.php?action=admin;sa=forum;fa=boards" method="post">
    <div id="board_list">';
    foreach($settings['cats'] as $cat) {
      echo '
      <div class="category">
        <p>', $cat['name'], '</p>
      </div>';
      if(count($cat['boards'])) {
        foreach($cat['boards'] as $board) {
          echo '
          <div class="board">
            <p><input class="board_name" name="board_name[', $board['id'], ']" type="text" value="', $board['name'], '"/> <input class="board_order" name="board_order[', $board['id'], ']" type="text" value="', $board['order'], '"/> <a href="', $cmsurl, 'index.php?action=admin;sa=forum;fa=boards;do=edit;id=', $board['id'], '"><img src="'.$theme_url.'/'.$settings['theme'].'/images/modify.png" alt="'.$l['manageboards_delete'].'" width="15" height="15" /></a> <a class="del" href="', $cmsurl, 'index.php?action=admin;sa=forum;fa=boards;delete=', $board['id'], ';sc=', $user['sc'], '" onClick="return confirm(\'', $l['manageboards_are_you_sure_del'], '\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/delete.png" alt="'.$l['manageboards_delete'].'" width="15" height="15" /></a></p>
          </div>';
        }
      }
    }
    echo '
    </div>
    <table width="100%">
      <tr align="right">
        <td><a href="', $cmsurl, 'index.php?action=admin;sa=forum;fa=boards;do=add">', $l['manageboards_add_button'], '</a>  <input name="update_boards" type="submit" value="', $l['manageboards_add_update'], '"/></td>
      </tr>
    </table>
  </form>';  
  }
  else {
    echo '<p style="error">', $l['manageboards_no_cats'], '</p>';
  }
}

function AddBoard() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  echo '
  <h2>', $l['manageboards_add_header'], '</h2>
  <form action="', $cmsurl, 'index.php?action=admin;sa=forum;fa=boards" method="post">  
    <table id="add_board">
      <tr class="category">
        <td>', $l['manageboards_add_category'], '</td>
        <td align="right"><select name="in_category">';
            foreach($settings['cats'] as $cat) 
              echo '<option value="', $cat['id'], '">', $cat['name'], '</option>';
        echo '
        </select></td>
      </tr>
      <tr class="board_name">
        <td>', $l['manageboards_add_boardname'], '</td><td align="right"><input name="board_name" type="text" value=""/></td>
      </tr>
      <tr class="board_desc">
        <td>', $l['manageboards_add_boarddesc'], '</td><td align="right"><textarea name="board_desc" cols="22" rows="2"></textarea></td>
      </tr>
      <tr class="who_view">
        <td valign="top">', $l['manageboards_add_whoview'], '</td>
        <td align="right">
          <label for="g0">', $l['manageboards_add_guests'], '</label> <input id="g0" name="groups[]" type="checkbox" value="-1"/><br />';
        foreach($settings['groups'] as $group)
          echo '<label for="g', $group['id'], '">', $group['name'], '</label> <input id="g', $group['id'], '" name="groups[]" type="checkbox" value="', $group['id'], '"/><br />';
        echo '
        </td>
      </tr>
      <tr>
        <td colspan="2" align="right"><input name="add_board" type="submit" value="', $l['manageboards_add_button'], '"/></td>
      </tr>
    </table>
  </form>';
}

function EditBoard() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  echo '
  <h2>', $l['manageboards_edit_header'], '</h2>
  <form action="', $cmsurl, 'index.php?action=admin;sa=forum;fa=boards" method="post">  
    <table id="add_board">
      <tr class="category">
        <td>', $l['manageboards_add_category'], '</td>
        <td align="right"><select name="in_category">';
            foreach($settings['cats'] as $cat) 
              echo '<option value="', $cat['id'], '"', $cat['selected'] ? ' selected="yes"' : '', '>', $cat['name'], '</option>';
        echo '
        </select></td>
      </tr>
      <tr class="board_name">
        <td>', $l['manageboards_add_boardname'], '</td><td align="right"><input name="board_name" type="text" value="', $settings['board']['name'], '"/></td>
      </tr>
      <tr class="board_desc">
        <td>', $l['manageboards_add_boarddesc'], '</td><td align="right"><textarea name="board_desc" cols="22" rows="2">', $settings['board']['desc'], '</textarea></td>
      </tr>
      <tr class="who_view">
        <td valign="top">', $l['manageboards_add_whoview'], '</td>
        <td align="right">
          <label for="g0">', $l['manageboards_add_guests'], '</label> <input id="g0" name="groups[]" type="checkbox" value="-1" ', $settings['groups']['-1']['checked'] ? 'checked="checked"' : '', '/><br />';
        foreach($settings['groups'] as $group_id => $group)
          if($group_id != -1)
            echo '<label for="g', $group['id'], '">', $group['name'], '</label> <input id="g', $group['id'], '" name="groups[]" type="checkbox" value="', $group['id'], '"', $group['checked'] ? ' checked="checked"' : '', '/><br />';
        echo '
        </td>
      </tr>
      <tr>
        <td colspan="2" align="right"><input name="update_board" type="submit" value="', $l['manageboards_edit_button'], '"/></td>
      </tr>
    </table>
    <input name="board_id" type="hidden" value="', $settings['board']['bid'], '"/>
  </form>';
}
?>