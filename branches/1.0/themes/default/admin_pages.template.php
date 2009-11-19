<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#   Manage Pages Layout template, March 22, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function pages_add_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $l['admin_pages_create_header'], '</h1>
      <p>', $l['admin_pages_create_desc'], '</p>
      ';
  
  if($page['title_empty'])
    echo '
      <div class="generic_error">
        <p>', $l['admin_pages_edit_error_title_empty'], '</p>
      </div>';
  
  echo '
      <br />
      
      <p class="resize_editor"><a href="javascript:void(0);" onClick="increaseEditor(\'page_editor\', 20);" title="', $l['admin_pages_editor_increase'], '">+</a> <a href="javascript:void(0);" onclick="decreaseEditor(\'page_editor\', 20);" title="', $l['admin_pages_editor_decrease'], '">-</a></p>

      <form action="', $base_url, '/index.php?action=admin;sa=pages;area=create" method="post">
        <p>', $l['admin_pages_editor_page_title'], ' <input type="text" name="page_title" value="', $page['page']['title'], '" /></p>
        
        <br />
        
        <p><textarea class="page_editor" id="page_editor" name="content">', $page['page']['content'], '</textarea></p>

        <div style="text-align: right; margin-right: 5px;">
          <strong>', $l['admin_pages_editor_type'], '</strong>:
            <input name="type" type="radio" value="2" id="type_snowtext" title="', $l['admin_pages_editor_snowtext_enabled'], '"', ($page['page']['type'] == 2 ? ' checked="checked"' : ''), ' />
            <label for="type_snowtext" title="', $l['admin_pages_editor_snowtext_enabled'], '">', $l['admin_pages_editor_snowtext'], '</label>
            <input name="type" type="radio" value="1" id="type_html" title="', $l['admin_pages_editor_html_enabled'], '"', ($page['page']['type'] == 1 ? ' checked="checked"' : ''), ' />
            <label for="type_html" title="', $l['admin_pages_editor_html_enabled'], '">', $l['admin_pages_editor_html'], '</label>
            <input name="type" type="radio" value="0" id="type_bbcode" title="', $l['admin_pages_editor_bbcode_enabled'], '"', (!$page['page']['type'] ? ' checked="checked"' : ''), ' />
            <label for="type_bbcode" title="', $l['admin_pages_editor_bbcode_enabled'], '">', $l['admin_pages_editor_bbcode'], '</label>
            <input name="add_page" type="submit" value="', $l['admin_pages_create_submit'], '" />
            <br /><br />
          <strong>', $l['admin_pages_editor_who_can_view'], '</strong><br />';

          foreach($page['groups'] as $group)
          {
            echo '
            <label for="group_', $group['id'], '" ', !empty($group['post_group']) ? 'title="'. sprintf($l['admin_pages_editor_post_group'], $group['name']). '" class="is_post_group"' : '', '>', $group['name'], '</label> <input name="groups[', $group['id'], ']" type="checkbox" value="1" id="group_', $group['id'], '" ', !empty($group['post_group']) ? 'title="'. sprintf($l['admin_pages_editor_post_group'], $group['name']). '" ' : '', (in_array($group['id'],$page['page']['who_view']) || $page['page']['who_view'][0] == 'all' ? ' checked="checked"' : ''), ' /><br />';
          }

  echo '
        </div>
      </form>';
}

function pages_list_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  $sort_asc = ' <img src="'. $theme_url. '/'. $settings['theme']. '/images/sort_asc.png" alt="Sorted Ascending" />';
  $sort_desc = ' <img src="'. $theme_url. '/'. $settings['theme']. '/images/sort_desc.png" alt="Sorted Descending" />';
  
  $sort_id = $page['sort'] == 'id' ? $sort_asc : ($page['sort'] == 'id;desc' ? $sort_desc : '');
  $sort_username = $page['sort'] == 'title' ? $sort_asc : ($page['sort'] == 'title;desc' ? $sort_desc : '');
  $sort_email = $page['sort'] == 'created' ? $sort_asc : ($page['sort'] == 'created;desc' ? $sort_desc : '');
  $sort_ip = $page['sort'] == 'modified' ? $sort_asc : ($page['sort'] == 'modified;desc' ? $sort_desc : '');
  $sort_posts = $page['sort'] == 'views' ? $sort_asc : ($page['sort'] == 'views;desc' ? $sort_desc : '');
  
  # Echo the title, table header, etc.
  echo '
      <h1>', $l['admin_pages_list_header'], '</h1>
      <p>', $l['admin_pages_list_desc'], '</p>
      
      <br />
      ';
  
  if($page['page_created'])
    echo '
      <div class="generic_success">
        <p>', $l['admin_pages_create_success'], '</p>
      </div>
      
      <br />
      ';
  elseif($page['page_edited'])
    echo '
      <div class="generic_success">
        <p>', $l['admin_pages_edit_success'], '</p>
      </div>
      
      <br />
      ';
  elseif($page['homepage_delete'])
    echo '
      <div class="generic_error">
        <p>', $l['admin_pages_list_error_delete_homepage'], '</p>
      </div>
      
      <br />
      ';
  
  echo '
      <p>'. $page['pagination']. '</p>
      
      <br />
      
      <table class="htable">
        <tr>
          <th><a href="'. $base_url. '/index.php?action=admin;sa=pages;area=manage;sort=id'. ($page['sort'] == 'id' ? ';desc' : ''). '">', $l['admin_pages_list_id'], '</a>'. $sort_id. '</th>
          <th><a href="'. $base_url. '/index.php?action=admin;sa=pages;area=manage;sort=title'. ($page['sort'] == 'title' ? ';desc' : ''). '">', $l['admin_pages_list_title'], '</a>'. $sort_username. '</th>
          <th><a href="'. $base_url. '/index.php?action=admin;sa=pages;area=manage;sort=created'. ($page['sort'] == 'created' ? ';desc' : ''). '">', $l['admin_pages_list_created'], '</a>'. $sort_email. '</th>
          <th><a href="'. $base_url. '/index.php?action=admin;sa=pages;area=manage;sort=modified'. ($page['sort'] == 'modified' ? ';desc' : ''). '">', $l['admin_pages_list_modified'], '</a>'. $sort_ip. '</th>
          <th><a href="'. $base_url. '/index.php?action=admin;sa=pages;area=manage;sort=views'. ($page['sort'] == 'views' ? ';desc' : ''). '">', $l['admin_pages_list_views'], '</a>'. $sort_posts. '</th>
        </tr>';
  
  # Echo the pages
  foreach($page['pages'] as $pge)
    echo '
        <tr>
          <td>', numberformat($pge['page_id']), '</td>
          <td><a href="'. $base_url, '/index.php?action=admin;sa=pages;area=manage;id=', $pge['page_id'], '">', $pge['page_title'], '</a></td>
          <td>', timeformat($pge['created_time']), '</td>
          <td>', timeformat($pge['modified_time']), '</td>
          <td>', numberformat($pge['num_views']), '</td>
          <td><a href="', $base_url, '/index.php?action=admin;sa=pages;area=manage;delete=', $pge['page_id'], '"><img src="'. $theme_url. '/'. $settings['theme']. '/images/delete.png" alt="', $l['admin_page_manage_delete'], '" title="', $l['admin_page_manage_delete'], '" /></a></td>
        </tr>';
  
  # Echo the footer stuff
  echo '
      </table>
      
      <br />
      
      <p>'. $page['pagination']. '</p>';
}

function pages_manage_show_edit()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1>', $l['admin_pages_edit_header'], '</h1>
      <p>', $l['admin_pages_edit_desc'], '</p>
      ';
  
  if($page['title_empty'])
    echo '
      <div class="generic_error">
        <p>', $l['admin_pages_edit_error_title_empty'], '</p>
      </div>';
  
  echo '
      <br />
      
      <p class="resize_editor"><a href="javascript:void(0);" onClick="increaseEditor(\'page_editor\', 20);" title="', $l['admin_pages_editor_increase'], '">+</a> <a href="javascript:void(0);" onClick="decreaseEditor(\'page_editor\', 20);" title="', $l['admin_pages_editor_decrease'], '">-</a></p>

      <form action="', $base_url, '/index.php?action=admin;sa=pages;area=manage;id=', $page['page']['id'], '" method="post">
        <p>', $l['admin_pages_editor_page_title'], ' <input type="text" name="page_title" value="', $page['page']['title'], '" /></p>
        
        <br />
        
        <p><textarea class="page_editor" id="page_editor" name="content">', $page['page']['content'], '</textarea></p>

        <div style="text-align: right; margin-right: 5px;">
          <strong>', $l['admin_pages_editor_type'], '</strong>:
            <input type="hidden" name="edit_page" value="true" />
            <input name="type" type="radio" value="2" id="type_snowtext" title="', $l['admin_pages_editor_snowtext_enabled'], '"', ($page['page']['type'] == 2 ? ' checked="checked"' : ''), ' />
            <label for="type_snowtext" title="', $l['admin_pages_editor_snowtext_enabled'], '">', $l['admin_pages_editor_snowtext'], '</label>
            <input name="type" type="radio" value="1" id="type_html" title="', $l['admin_pages_editor_html_enabled'], '"', ($page['page']['type'] == 1 ? ' checked="checked"' : ''), ' />
            <label for="type_html" title="', $l['admin_pages_editor_html_enabled'], '">', $l['admin_pages_editor_html'], '</label>
            <input name="type" type="radio" value="0" id="type_bbcode" title="', $l['admin_pages_editor_bbcode_enabled'], '"', (!$page['page']['type'] ? ' checked="checked"' : ''), ' />
            <label for="type_bbcode" title="', $l['admin_pages_editor_bbcode_enabled'], '">', $l['admin_pages_editor_bbcode'], '</label>
            <input name="add_page" type="submit" value="', $l['admin_pages_edit_submit'], '" />
            <br /><br />
          <strong>', $l['admin_pages_editor_who_can_view'], '</strong><br />';

          foreach($page['groups'] as $group)
          {
            echo '
            <label for="group_', $group['id'], '" ', !empty($group['post_group']) ? 'title="'. sprintf($l['admin_pages_editor_post_group'], $group['name']). '" class="is_post_group"' : '', '>', $group['name'], '</label> <input name="groups[', $group['id'], ']" type="checkbox" value="1" id="group_', $group['id'], '" ', !empty($group['post_group']) ? 'title="'. sprintf($l['admin_pages_editor_post_group'], $group['name']). '" ' : '', (in_array($group['id'],$page['page']['who_view']) ? ' checked="checked"' : ''), ' /><br />';
          }

  echo '
        </div>
      </form>';
}

function pages_manage_show_invalid()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # To do: display invalid page message
}
?>