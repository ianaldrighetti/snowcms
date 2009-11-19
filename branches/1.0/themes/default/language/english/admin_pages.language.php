<?php
#
# Forum English file for SnowCMS
# Created by the SnowCMS Dev Team
#       www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $settings;

# Create a Page
$l['admin_pages_create_title'] = 'Create a Page - '. $settings['site_name'];
$l['admin_pages_create_header'] = 'Create a Page';
$l['admin_pages_create_desc'] = 'Create a new page for the site.';
$l['admin_pages_create_submit'] = 'Create Page';
$l['admin_pages_create_success'] = 'Page created successfully';

# Edit a Page
$l['admin_pages_edit_title'] = 'Edit a Page - '. $settings['site_name'];
$l['admin_pages_edit_header'] = 'Edit a Page';
$l['admin_pages_edit_desc'] = 'Edit the selected page of your site.';
$l['admin_pages_edit_submit'] = 'Edit Page';
$l['admin_pages_edit_success'] = 'Changes saved successfully';

# Page Editor
$l['admin_pages_editor_page_title'] = 'Page Title:';
$l['admin_pages_editor_increase'] = 'Increase editor size';
$l['admin_pages_editor_decrease'] = 'Decrease editor size';
$l['admin_pages_editor_type'] = 'Page type';
$l['admin_pages_editor_bbcode'] = 'BBCode';
$l['admin_pages_editor_bbcode_enabled'] = 'BBCode enabled page. (HTML and SnowText will not be parsed)';
$l['admin_pages_editor_html'] = 'HTML';
$l['admin_pages_editor_html_enabled'] = 'HTML enabled page. (BBCode and SnowText will not be parsed)';
$l['admin_pages_editor_snowtext'] = 'SnowText';
$l['admin_pages_editor_snowtext_enabled'] = 'SnowText enabled page. (BBCode will not be parsed)';
$l['admin_pages_editor_who_can_view'] = 'Which groups can access this page?';
$l['admin_pages_editor_post_group'] = '%s is a post group';

# Page list
$l['admin_pages_list_title'] = 'Page List - '. $settings['site_name'];
$l['admin_pages_list_header'] = 'Page List';
$l['admin_pages_list_desc'] = 'All the pages on the site can be found here.';
$l['admin_pages_list_id'] = 'ID';
$l['admin_pages_list_title'] = 'Title';
$l['admin_pages_list_created'] = 'Date Created';
$l['admin_pages_list_modified'] = 'Last Modified';
$l['admin_pages_list_views'] = 'Views';
$l['admin_pages_list_delete'] = 'Delete';

# Page list errors
$l['admin_pages_edit_error_title_empty'] = 'Please enter a page title.';
$l['admin_pages_list_error_delete_homepage'] = 'You can\'t delete your homepage.';
?>