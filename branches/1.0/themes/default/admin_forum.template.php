<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#      Admin Forum Layout template, May 9, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function forum_category_add_show()
{
  global $base_url, $l, $page, $settings, $user;

  echo '
      <h1>', $l['add_category_header'], '</h1>
      
      <p>', $l['add_category_desc'], '</p>
      
      <form action="', $base_url, '/index.php?action=admin;sa=forum;area=boards" method="post">
        <fieldset>
          <table class="center">
            <tr>
              <td>', $l['add_category_name'], '</td><td><input name="category_name" type="text" value="" /></td>
            </tr>
            <tr>
              <td>', $l['add_category_position'], '</td><td><select name="category_position">
                                                              <option value="0">', $l['add_category_position_first'], '</option>';

  # Our category listings... If any...
  if(count($page['categories']))
  {
    foreach($page['categories'] as $cat_id => $cat_name)
      echo '
                                                              <option value="', $cat_id, '">', $l['add_category_after'], $cat_name, '</option>';
  }

  echo '
                                                            </select></td>
            </tr>
            <tr>
              <td>', $l['add_category_collapsible'], '</td><td><input name="category_collapsible" type="checkbox" value="1" checked="checked" /></td>
            </tr>
            <tr>
              <td colspan="2"><input name="add_category" type="submit" value="', $l['add_category_submit'], '" /></td>
            </tr>
          </table>
        </fieldset>
      </form>';
}

function forum_board_add_show()
{
  global $base_url, $l, $page, $settings, $user;

  echo '
      <h1>', $l['add_board_header'], '</h1>
      
      <p>', $l['add_board_desc'], '</p>

      <form action="', $base_url, '/index.php?action=admin;sa=forum;area=boards" method="post">
        <fieldset>
          <table class="center" width="70%">
            <tr>
              <td width="50%" align="left"><strong>', $l['add_board_name'], '</strong></td><td width="50%" align="right"><input name="board_name" type="text" value="" /></td>
            </tr>
            <tr>
              <td width="50%" align="left"><strong>', $l['add_board_description'], '</strong></td><td width="50%" align="right"><textarea name="board_desc"></textarea></td>
            </tr>
            <tr>
              <td width="50%" align="left"><strong>', $l['add_board_position'], '</strong><br /><span class="small">', $l['add_board_sub_position'], '</span></td><td align="right">
                                                         <select name="board_position">';

  # Our category listings... If any...
  if(count($page['categories']))
  {
    foreach($page['categories'] as $category)
    {
      echo '
                                                           <option value="c', $category['id'], '">', $category['name'], '</option>';

      # Boards inside the category..?
      if(count($category['boards']))
        foreach($category['boards'] as $board)
          echo '
                                                           <option value="b', $board['id'], '">', $board['name'], '</option>';
    }
  }

  echo '
                                                         </select></td>
            </tr>
            <tr>
              <td width="50%" align="left"><strong>', $l['add_board_who_view'], '</strong><br /><span class="small">', $l['add_board_sub_who_view'], '</span></td>
              <td width="50%" align="right">';

              # List of groups... If any O.o
              if(count($page['groups']))
                foreach($page['groups'] as $group)
                  echo '
                            <label for="group_', $group['id'], '"', !empty($group['post_group']) ? ' class="is_post_group"' : '', '>', $group['name'], '</label> <input name="groups[', $group['id'], ']" id="group_', $group['id'], '" type="checkbox" value="1"'. ($group['checked'] ? ' checked="checked"' : ''). ' /><br />';

  echo '
              </td>
            </tr>
            <tr>
              <td width="50%" align="left" valign="center"><strong>', $l['add_board_moderators'], '</strong><br /><span class="small">', $l['add_board_sub_moderators'], '</span></td><td width="50%" align="right" valign="middle"><input name="board_moderators" type="text" value="" /></td>
            </tr>
            <tr align="center" valign="middle">
              <td colspan="2" align="center"><input name="add_board" type="submit" value="', $l['add_board_submit'], '" /></td>
            </tr>
          </table>
        </fieldset>
      </form>';
}

function forum_manage_show()
{
  global $base_url, $l, $page, $settings, $user;

  echo '
      <h1>', $l['manageboards_header'], '</h1>
      
      <p>', $l['manageboards_desc'], '</p>
      ';

  # Errors :(
  if(!empty($page['errors']) && count($page['errors']))
  {
    echo '
      <div class="generic_error">';

    foreach($page['errors'] as $error)
      echo '
        <p>', $error, '</p>';

    echo '
      </div>';
  }

  # Lets show our categories and boards!
  echo '
      <table cellspacing="2px" cellpadding="0px" class="admin_table admin_manageboards" width="100%">';
  
  foreach($page['categories'] as $category)
  {
    # Show the category header
    echo '
        <tr>
          <td colspan="3" width="70%" class="admin_category" id="category_', $category['id'], '"><span class="hand_cursor" onclick="editCategory(', $category['id'], ');">', $category['name'], '</span></td><td width="2%"><a href="javascript:void(0);" onclick="editCategory(', $category['id'], ');"><img src="', $settings['images_url'], '/edit.png" alt="', $l['edit'], '" title="', $l['edit'], '" /></a></td><td width="2%"><a href="', $category['link']['delete'], '" onclick="return confirm(\'', $l['are_you_sure'], '\');"><img src="', $settings['images_url'], '/delete.png" alt="', $l['delete'], '" title="', $l['delete'], '" /></a></td><td width="2%"><a href="', $category['link']['raise'], '"><img src="', $settings['images_url'], '/order_raise.png" alt="', $l['raise_order'], '" title="', $l['raise_order'], '" /></a></td><td width="2%"><a href="', $category['link']['lower'], '"><img src="', $settings['images_url'], '/order_lower.png" alt="', $l['lower_order'], '" title="', $l['lower_order'], '" /></a></td>
        </tr>
        <tr>
          <td colspan="5" class="small">', sprintf($l['manageboards_board'], numberformat(count($category['boards']))), '</td>
        </tr>';

    # Any boards at all?
    if(count($category['boards']))
    {
      foreach($category['boards'] as $board)
      {
        echo '
        <tr>
          <td colspan="2" width="2%"></td><td width="68%" class="admin_board" id="board_', $board['id'], '"><span class="hand_cursor" onclick="editBoard(', $board['id'], ', false);">', $board['name'], '</span></td><td width="2%"><a href="', $board['link']['edit'], '"><img src="', $settings['images_url'], '/edit.png" alt="', $l['edit'], '" title="', $l['edit'], '" /></a></td><td width="2%"><a href="', $board['link']['delete'], '" onclick="return confirm(\'', $l['are_you_sure'], '\');"><img src="', $settings['images_url'], '/delete.png" alt="', $l['delete'], '" title="', $l['delete'], '" /></a></td><td width="2%"><a href="', $board['link']['raise'], '"><img src="', $settings['images_url'], '/order_raise.png" alt="', $l['raise_order'], '" title="', $l['raise_order'], '" /></a></td><td width="2%"><a href="', $board['link']['lower'], '"><img src="', $settings['images_url'], '/order_lower.png" alt="', $l['lower_order'], '" title="', $l['lower_order'], '" /></a></td>
        </tr>';

        # Last foreach... Any children..?
        if(count($board['children']))
        {
          foreach($board['children'] as $child)
          {
        echo '
        <tr>
          <td colspan="2"></td><td class="admin_board" id="board_', $child['id'], '">&nbsp;&nbsp; <span class="hand_cursor" onclick="editBoard(', $child['id'], ', true);">', $child['name'], '</span></td><td width="2%"><a href="', $child['link']['edit'], '"><img src="', $settings['images_url'], '/edit.png" alt="', $l['edit'], '" title="', $l['edit'], '" /></a></td><td width="2%"><a href="', $child['link']['delete'], '" onclick="return confirm(\'', $l['are_you_sure'], '\');"><img src="', $settings['images_url'], '/delete.png" alt="', $l['delete'], '" title="', $l['delete'], '" /></a></td><td width="2%"><a href="', $child['link']['raise'], '"><img src="', $settings['images_url'], '/order_raise.png" alt="', $l['raise_order'], '" title="', $l['raise_order'], '" /></a></td><td width="2%"><a href="', $child['link']['lower'], '"><img src="', $settings['images_url'], '/order_lower.png" alt="', $l['lower_order'], '" title="', $l['lower_order'], '" /></a></td>
        </tr>';
          }
        }
      }
    }
  }
  
  echo '
      </table>';
}

function forum_manage_show_nothing()
{
  global $l;
  
  echo '
      <h1>', $l['manageboards_header'], '</h1>
      
      ', $l['manageboards_nothing'];
}
?>