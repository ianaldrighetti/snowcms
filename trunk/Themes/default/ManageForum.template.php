<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//        ManageForum.template.php

if(!defined('Snow'))
  die("Hacking Attempt..");

function Main() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  
  echo '
  <h1>',$l['manageforum_title'],'</h1>
  
  <p>', $l['manageforum_desc'], '</p>
  
  ';
  
  ForumOptions();
}

function ShowCats() {
global $cmsurl, $db_prefix, $l, $settings, $user, $theme_url;
  
  echo '
  <h1>', $l['managecats_title'], '</h1>
  ';
  
  if (@$_SESSION['error'])
	  echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
	else
    echo '<p>', $l['managecats_desc'], '</p>';
  
  if (can('manage_forum_edit') || can('manage_forum_delete')) {
    echo '
    <form action="', $cmsurl, 'index.php?action=admin;sa=forum;fa=categories" method="post">
      <table id="mc" width="100%" style="text-align: center">
        <tr>
          <th class="border" width="90%">'.$l['mc_tr_cn'].'</th>
          <th class="border">'.$l['mc_tr_order'].'</th>
          <th class="no-border" width="18px"></th>
        </tr>';
      
      if (can('manage_forum_edit')) {
        foreach ($settings['cats'] as $cat) {
          echo '
          <tr>
            <td><input name="cat_name['.$cat['id'].']" type="text" class="name" value="'.$cat['name'].'" /></td>
            <td><input name="cat_order['.$cat['id'].']" type="text" class="order" value="'.$cat['order'].'" style="text-align: center" /></td>';
          if (can('manage_forum_delete'))
            echo '<td class="delete"><a href="', $cmsurl, 'index.php?action=admin;sa=forum;fa=categories;did=', $cat['id'], ';sc=', $user['sc'], '" onclick="return confirm(\'', $l['managecats_are_you_sure'], '\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/delete.png" alt="'.$l['managecats_category_delete'].'" width="15" height="15" /></td>';
          echo '
          </tr>';
        }
        echo '
          <tr>
            <td></td><td><input name="update_cats" type="submit" value="', $l['managecats_update'], '"/></td><td></td>
          </tr>
        </table>
      </form>';
      }
      elseif (can('manage_forum_delete'))
        foreach ($settings['cats'] as $cat) {
          echo '
          <tr>
            <td>'.$cat['name'].'</td>
            <td>'.$cat['order'].'</td>';
          if (can('manage_forum_delete'))
            echo '<td class="delete"><a href="', $cmsurl, 'index.php?action=admin;sa=forum;fa=categories;did=', $cat['id'], ';sc=', $user['sc'], '" onclick="return confirm(\'', $l['managecats_are_you_sure'], '\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/delete.png" alt="'.$l['managecats_category_delete'].'" width="15" height="15" /></td>';
          echo '
          </tr>
        </table>
      </form>';
        }
  }
  
  if (can('manage_forum_create'))
   echo '
  <form action="', $cmsurl, 'index.php?action=admin;sa=forum;fa=categories" method="post">
    <table id="add_cat">
      <tr>
        <td colspan="2"><p><b>'.$l['managecats_add_header'].'</b></p></td>
      </tr>
      <tr>
        <td>', $l['managecats_catname'], ' <input class="cat_name" name="cat_name" type="text" value="', $l['mf_new_category'], '"/></td><td>', $l['managecats_order'], ' <input class="order" name="order" type="text" value=""/></td>
      </tr>
      <tr>
        <td colspan="2">
          <br />
          <input name="add_cat" type="submit" value="'.$l['managecats_addbutton'].'"/>
        </td>
      </tr>
    </table>
  </form>';
}

function ShowBoards() {
global $cmsurl, $db_prefix, $l, $settings, $user, $theme_url;
  
  echo '
  <h1>', $l['manageboards_title'], '</h1>
  ';
  
  if (@$_SESSION['error'])
	  echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
	else
    echo '<p>', $l['manageboards_desc'], '</p>';
  
  if (count($settings['cats'])) {
    echo '
  <form action="', $cmsurl, 'index.php?action=admin;sa=forum;fa=boards" method="post">
    <div id="board_list">';
    foreach ($settings['cats'] as $cat) {
      echo '
      <div class="category">
        '.$cat['name'].'
      </div>';
      if (count($cat['boards'])) {
        foreach ($cat['boards'] as $board) {
          echo '
          <div class="board">
            ';
          if (can('manage_forum_edit'))
            echo '<table width="100%">
              <tr><td><input class="board_name" name="board_name[', $board['id'], ']" type="text" value="', $board['name'], '"/> <input class="board_order" name="board_order[', $board['id'], ']" type="text" value="', $board['order'], '"/></td><td style="text-align: right"><a href="', $cmsurl, 'index.php?action=admin;sa=forum;fa=boards;do=edit;id=', $board['id'], '"><img src="'.$theme_url.'/'.$settings['theme'].'/images/modify.png" alt="'.$l['manageboards_modify'].'" width="15" height="15" /></a>';
          else
            echo '<table width="100%">
              <tr><td>'.$board['name'].'</td><td style="text-align: right">';
          if (can('manage_forum_delete'))
            echo ' <a class="del" href="'.$cmsurl.'index.php?action=admin;sa=forum;fa=boards;did='.$board['id'].';sc='.$user['sc'].'" onclick="return confirm(\''.$l['manageboards_are_you_sure_del'].'\');"><img src="'.$theme_url.'/'.$settings['theme'].'/images/delete.png" alt="'.$l['manageboards_delete'].'" width="15" height="15" /></a></td></tr>
            </table>';
          else
            echo '</td></tr>
            </table>';
      echo '
          </div>';
        }
      }
    }
    echo '
    </div>';
    if (can('manage_forum_edit'))
      echo '
    <br />
    <table width="100%">
      <tr align="right">
        <td>'.(can('manage_forum_create')
              ? '<a href="'.$cmsurl.'index.php?action=admin;sa=forum;fa=boards;do=add">'.$l['manageboards_add_button'].'</a>'
              : '')
             .' <input name="update_boards" type="submit" value="'.$l['manageboards_add_update'],'"/></td>
      </tr>
    </table>';
    elseif (can('manage_forum_create'))
      echo '
    <br />
    <table width="100%">
      <tr>
        <td>
          <a href="'.$cmsurl.'index.php?action=admin;sa=forum;fa=boards;do=add">'.$l['manageboards_add_button'].'</a>
        </td>
      </tr>
    </table>';
    echo '
  </form>';  
  }
  else {
    echo '<p style="error">', $l['manageboards_no_cats'], '</p>';
  }
}

function AddBoard() {
global  $l, $settings, $user, $cmsurl;
  
  echo '
  <h1>'.$l['manageboards_add_header'].'</h1>
  ';
  
  if (!empty($_SESSION['error']))
    echo '<p><b>'.$l['main_error'].':</b> '.$_SESSION['error'].'</p>';
  else
    echo '<p>'.$l['manageboards_add_desc'].'</p>';
  
  echo '
  <form action="'.$cmsurl.'index.php?action=admin;sa=forum;fa=boards" method="post">  
    <table id="add_board" class="no-border">
      <tr class="category">
        <th style="text-align: left">'.$l['manageboards_add_category'].':</th>
        <td style="text-align: right"><select name="in_category">';
            foreach($settings['cats'] as $cat) 
              echo '<option value="'.$cat['id'].'">'.$cat['name'].'</option>';
        echo '
        </select></td>
      </tr>
      <tr class="board_name">
        <th style="text-align: left">'.$l['manageboards_add_boardname'].':</th>
        <td style="text-align: right"><input name="board_name" type="text" value="" /></td>
      </tr>
      <tr class="board_desc">
        <th style="text-align: left; vertical-align: top">'.$l['manageboards_add_boarddesc'].':</th>
        <td style="text-align: right"><textarea name="board_desc" cols="35" rows="3"></textarea></td>
      </tr>
      <tr class="who_view">
        <th style="text-align: left; vertical-align: top">'.$l['manageboards_add_whoview'].':</th>
        <td align="right">
          ';
        foreach ($settings['groups'] as $group)
          echo '<label for="g'.$group['id'].'">'.$group['name'].'</label> <input id="g'.$group['id'].'" name="groups[]" type="checkbox" value="'.$group['id'].'"/><br />
          ';
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
  <form action="', $cmsurl, 'index.php?action=admin;sa=forum;fa=boards" method="post">  
    <table id="add_board">
      <tr class="category">
        <td>', $l['manageboards_add_category'], '</td>
        <td align="right"><select name="in_category">';
            foreach ($settings['cats'] as $cat) 
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
        foreach ($settings['groups'] as $group_id => $group)
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

function ForumOptions() {
global $l, $settings, $cmsurl;
  
  $options = $settings['page']['options'];
  
  $odd = true;
  foreach ($options as $option) {
    echo '
  <div class="acp_'.($odd ? 'left' : 'right').'">
    <p class="main"><a href="'.$cmsurl.'index.php?action=admin;sa=forum;fa='.$option.'" title="'.$l['mf_link_'.$option].'">'.$l['mf_link_'.$option].'</a></p>
    <p class="desc">'.$l['mf_link_'.$option.'_desc'].'</p>
  </div>
  ';
    $odd = !$odd;
  }
}
?>