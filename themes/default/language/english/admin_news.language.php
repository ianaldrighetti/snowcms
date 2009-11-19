<?php
#
# Admin English file for SnowCMS
# Created by the SnowCMS Dev Team
#       www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $settings;

# Add news stuff
$l['news_add_title'] = 'Add News - '. $settings['site_name'];
$l['news_add_header'] = 'Add News';
$l['news_add_desc'] = 'Create a new news post.';
$l['news_add_subject'] = 'Subject';
$l['news_add_category'] = 'Category';
$l['news_add_uncategorized'] = 'Uncategorized';
$l['news_add_viewable'] = 'Publicly Viewable';
$l['news_add_comments'] = 'Allow Comments';
$l['news_add_submit'] = 'Create News';

# Manage news stuff
$l['admin_news_manage_title'] = 'Manage News - '. $settings['site_name'];
$l['admin_news_manage_header'] = 'Manage News';
$l['admin_news_manage_desc'] = 'Manage your existing news articles here.';
$l['admin_news_manage_subject'] = 'Subject';
$l['admin_news_manage_category'] = 'Category';
$l['admin_news_manage_uncategorized'] = 'Uncategorized';
$l['admin_news_manage_creator'] = 'Creator';
$l['admin_news_manage_time_posted'] = 'Time Posted';
$l['admin_news_manage_comments'] = 'Comments';
$l['news_manage_success_added'] = 'News post successfuly added.';
$l['admin_news_manage_views'] = 'Views';

# Manage news categories stuff
$l['admin_news_manage_categories_title'] = 'Manage News Categories - '. $settings['site_name'];
$l['admin_news_manage_categories_header'] = 'Manage News Categories';
$l['admin_news_manage_categories_desc'] = 'Create a new news category here.';
$l['admin_news_manage_categories_add_new'] = 'New category';
$l['admin_news_manage_categories_add_submit'] = 'Create';
$l['admin_news_manage_categories_add_success'] = 'Category created successfully.';
$l['admin_news_manage_categories_add_error_name_none'] = 'No name for the category entered.';
$l['admin_news_manage_categories_add_error_name_long'] = 'Category name is too long.';
$l['admin_news_manage_categories_add_error_unknown'] = 'An error occurred while trying to add the category.';
$l['admin_news_manage_categories_column_name'] = 'Name';
$l['admin_news_manage_categories_column_num_news'] = 'Total News Articles';
$l['admin_news_manage_categories_delete_success'] = 'Category deleted successfully.';
$l['admin_news_manage_categories_delete_error_doesn_exist'] = 'That category doesn\'t exist.';
$l['admin_news_manage_categories_delete_error_unknown'] = 'Error deleting category.';
?>