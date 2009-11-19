<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#     Settings Layout template, April 11, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function news_add_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # Echo the title, table header, etc.
  echo '
      <h1>', $l['news_add_header'], '</h1>
      <p>', $l['news_add_desc'], '</p>';
  
  # Show any errors
  if($page['errors'])
  {
    echo '
      <div class="generic_error">';
    
    foreach($page['errors'] as $error)
      echo '
        <p>', $error, '</p>';
    
    echo '
      </div>';
  }
  
  echo '
       <br />
       <fieldset>
        <form action="', $page['submit_url'], '" method="post">
          <input type="hidden" name="process" value="news_add" />
          <table cellspacing="0" cellpadding="4" width="80%" class="news_add">
            <tr>
              <td class="left" style="vertical-align: top;"><label for="subject">', $l['news_add_subject'], '</label></td>
              <td class="right" style="width: 50%;"><input type="text" name="subject" id="subject" value="" /></td>
            </tr>
            <tr>
              <td class="left" style="vertical-align: top;"><label for="category">', $l['news_add_category'], '</label></td>
              <td class="right" style="width: 50%;">
                <select name="category" id="category">';
  
  foreach($page['categories'] as $category)
  {
    echo '
                  <option value="', $category['cat_id'], '">', $category['cat_name'], '</option>';
  }
  
  echo '
                </select>
              </td>
            </tr>
            <tr>
              <td class="left" style="vertical-align: top;"><label for="viewable">', $l['news_add_viewable'], '</label></td>
              <td class="right" style="width: 50%;"><input type="checkbox" name="viewable" id="viewable" checked="checked" /></td>
            </tr>
            <tr>
              <td class="left" style="vertical-align: top;"><label for="comments">', $l['news_add_comments'], '</label></td>
              <td class="right" style="width: 50%;"><input type="checkbox" name="comments" id="comments" checked="checked" /></td>
            </tr>
            <tr>
              <td class="center" colspan="2"><textarea name="body" id="body" cols="80" rows="14" style="width: 100%;"></textarea></td>
            </tr>
            <tr>
              <td class="center" colspan="3" align="center"><input type="submit" value="', $l['news_add_submit'], '" /></td>
            </tr>
          </table>
        </form>
      </fieldset>';
}

function news_manage_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # Echo the title, table header, etc.
  echo '
      <h1>', $l['admin_news_manage_header'], '</h1>
      <p>', $l['admin_news_manage_desc'], '</p>';
  
  if($page['success'])
  {
    echo '
      <div class="generic_success">
        <p>', $page['success'], '</p>
      </div>';
  }
  
  echo '
      <br />
      <div class="pagination top">', $page['pagination'], '</div>
      <br />
      <table class="htable">
        <tr>
          <th><a href="'. $base_url. '/index.php?action=admin;sa=news;area=manage;sort=subject', $page['sort'] == 'subject' && $page['sort_asc'] ? ';desc' : '', '">', $l['admin_news_manage_subject'], '</a>',
          $page['sort'] == 'subject' ? ' <img src="'. $theme_url. '/'. $settings['theme']. '/images/sort_'. ($page['sort_asc'] ? 'asc' : 'desc'). '.png" alt="'. $l[$page['sort_asc'] ? 'asc' : 'desc']. '" />' : '',
          '</th>
          <th><a href="'. $base_url. '/index.php?action=admin;sa=news;area=manage;sort=category', $page['sort'] == 'category' && $page['sort_asc'] ? ';desc' : '', '">', $l['admin_news_manage_category'], '</a>',
          $page['sort'] == 'category' ? ' <img src="'. $theme_url. '/'. $settings['theme']. '/images/sort_'. ($page['sort_asc'] ? 'asc' : 'desc'). '.png" alt="'. $l[$page['sort_asc'] ? 'asc' : 'desc']. '" />' : '',
          '</th>
          <th><a href="'. $base_url. '/index.php?action=admin;sa=news;area=manage;sort=creator', $page['sort'] == 'creator' && $page['sort_asc'] ? ';desc' : '', '">', $l['admin_news_manage_creator'], '</a>',
          $page['sort'] == 'creator' ? ' <img src="'. $theme_url. '/'. $settings['theme']. '/images/sort_'. ($page['sort_asc'] ? 'asc' : 'desc'). '.png" alt="'. $l[$page['sort_asc'] ? 'asc' : 'desc']. '" />' : '',
          '</th>
          <th><a href="'. $base_url. '/index.php?action=admin;sa=news;area=manage;sort=time', $page['sort'] == 'time' && $page['sort_asc'] ? ';desc' : '', '">', $l['admin_news_manage_time_posted'], '</a>',
          $page['sort'] == 'time' ? ' <img src="'. $theme_url. '/'. $settings['theme']. '/images/sort_'. ($page['sort_asc'] ? 'asc' : 'desc'). '.png" alt="'. $l[$page['sort_asc'] ? 'asc' : 'desc']. '" />' : '',
          '</th>
          <th><a href="'. $base_url. '/index.php?action=admin;sa=news;area=manage;sort=comments', $page['sort'] == 'comments' && $page['sort_asc'] ? ';desc' : '', '">', $l['admin_news_manage_comments'], '</a>',
          $page['sort'] == 'comments' ? ' <img src="'. $theme_url. '/'. $settings['theme']. '/images/sort_'. ($page['sort_asc'] ? 'asc' : 'desc'). '.png" alt="'. $l[$page['sort_asc'] ? 'asc' : 'desc']. '" />' : '',
          '</th>
          <th><a href="'. $base_url. '/index.php?action=admin;sa=news;area=manage;sort=views', $page['sort'] == 'views' && $page['sort_asc'] ? ';desc' : '', '">', $l['admin_news_manage_views'], '</a>',
          $page['sort'] == 'views' ? ' <img src="'. $theme_url. '/'. $settings['theme']. '/images/sort_'. ($page['sort_asc'] ? 'asc' : 'desc'). '.png" alt="'. $l[$page['sort_asc'] ? 'asc' : 'desc']. '" />' : '',
          '</th>
          <td></td>
        </tr>';
  
  # Echo the news
  foreach($page['news'] as $news)
    echo '
        <tr>
          <td><a href="'. $base_url. '/index.php?action=admin;sa=news;area=manage;id='. $news['news_id']. '">'. $news['subject']. '</a></td>
          <td>', $news['cat_id'] ? $news['cat_name'] : $l['admin_news_manage_uncategorized'], '</td>
          <td><a href="'. $base_url. '/index.php?action=profile;u='. $news['member_id']. '">'. $news['displayName']. '</a></a></td>
          <td>'. timeformat($news['poster_time']). '</td>
          <td>'. numberformat($news['num_comments']). '</td>
          <td>'. numberformat($news['num_views']). '</td>
          <td></td>
        </tr>';
  
  # Echo the footer stuff
  echo '
      </table>
      <br />
      <div class="pagination bottom">', $page['pagination'], '</div>';
}

function news_manage_show_edit()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # To do: display news editing form
}

function news_manage_show_invalid()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # To do: display invalid news message
}

function news_categories_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  # Echo the title, table header, etc.
  echo '
      <h1>', $l['admin_news_manage_categories_header'], '</h1>
      <p>', $l['admin_news_manage_categories_desc'], '</p>';
  
  if($page['success'])
  {
    echo '
      <div class="generic_success">
        <p>', $page['success'], '</p>
      </div>';
  }
  elseif($page['error'])
  {
    echo '
      <div class="generic_error">
        <p>', $page['error'], '</p>
      </div>';
  }
  
  echo '
      <form action="" method="post">
        <fieldset>
          <input type="hidden" name="process" value="category-add" />
          <table class="center">
            <tr>
              <td>', $l['admin_news_manage_categories_add_new'], ':</td><td><input name="category_name" type="text" value="', $page['category_name'], '" /></td>
              <td colspan="2" align="center"><input name="add_category" type="submit" value="', $l['admin_news_manage_categories_add_submit'], '" /></td>
            </tr>
          </table>
        </fieldset>
      </form>
      
      <table class="manage_news_categories htable">
        <tr>
          <th>', $l['admin_news_manage_categories_column_name'], '</th>
          <th>', $l['admin_news_manage_categories_column_num_news'], '</th>
          <th class="blank"></th>
        </tr>';
  
  foreach($page['categories'] as $key => $category)
  {
    echo '
        <tr class="'. ($key % 2 ? 'even' : 'odd'). '">
          <td>'. $category['cat_name']. '</td>
          <td>'. $category['num_news']. '</td>
          <td>
            <a href="javascript:void(0);" onclick="editCategory(', $category['cat_id'], ');"><img src="'. $theme_url. '/'. $settings['theme']. '/images/edit.png" alt="', $l['admin_news_manage_categories_edit'], '" title="', $l['admin_news_manage_categories_edit'], '" /></a>
            <a href="', $base_url, '/index.php?action=admin;sa=news;area=categories;del=', $category['cat_id'], '"><img src="'. $theme_url. '/'. $settings['theme']. '/images/delete.png" alt="', $l['admin_news_manage_categories_delete'], '" title="', $l['admin_news_manage_categories_delete'], '" /></a>
          </td>
        </tr>';
  }
  
  echo '
      </table>';
}
?>