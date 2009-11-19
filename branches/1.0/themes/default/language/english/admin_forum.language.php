<?php
#
# Admin Forum English file for SnowCMS
#   Created by the SnowCMS Dev Team
#          www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $settings;

# Add a category
$l['add_category_title'] = 'Add new category - '. $settings['site_name'];
$l['add_category_header'] = 'Add new category';
$l['add_category_desc'] = 'Adding a new board category.';
$l['add_category_name'] = 'Category name';
$l['add_category_position'] = 'Category position';
$l['add_category_position_first'] = 'In the first place';
$l['add_category_after'] = 'After ';
$l['add_category_collapsible'] = 'Allow category to be collapsible?';
$l['add_category_submit'] = 'Add category';
$l['add_category_no_name'] = 'No name for the category entered.';
$l['add_category_couldnt_add'] = 'An error occurred while trying to add the category.';

# Add a board
$l['add_board_title'] = 'Add new board - '. $settings['site_name'];
$l['add_board_header'] = 'Add new board';
$l['add_board_desc'] = 'Adding a new forum board.';
$l['add_board_name'] = 'Board name';
$l['add_board_description'] = 'Board description';
$l['add_board_position'] = 'Board position';
$l['add_board_sub_position'] = 'Where the board will be placed after. Boards are preceeded with a dash (-).';
$l['add_board_who_view'] = 'Allowed groups';
$l['add_board_sub_who_view'] = 'Check the groups that you want to allow to have access to this board. <em>Those underlined are post groups.</em>';
$l['add_board_moderators'] = 'Moderators';
$l['add_board_sub_moderators'] = 'Enter the members name into the box at the right, multiple names separated by commas. By adding them they will have special moderation powers in this board.';
$l['add_board_submit'] = 'Add board';
$l['add_board_no_name'] = 'No board name entered.';
$l['add_board_couldnt_add'] = 'An error occurred while trying to add the board.';

# Delete categories
$l['category_deleted'] = 'Category successfully deleted.';
$l['error_delete_category'] = 'An error occurred while trying to delete the category.';

# Delete boards
$l['board_deleted'] = 'Board successfully deleted.';
$l['error_delete_board'] = 'An error occurred while trying to delete the board.';

# Manage boards page.
$l['manageboards_title'] = 'Manage Boards - '. $settings['site_name'];
$l['manageboards_header'] = 'Manage Boards'; $settings['site_name'];
$l['manageboards_desc'] = 'Rename, modify and delete forum boards and categories.';
$l['manageboards_board'] = 'Boards (%s):';
$l['manageboards_nothing'] = 'No categories or boards configured.';

# AJAX Category edit ;)
$l['manageboards_ajax_not_allowed'] = 'Sorry, but you aren\'t allowed to access this.';
$l['manageboards_ajax_check_allow_collapse'] = 'Allow the category to be collapsed';
$l['manageboards_ajax_category_not_found'] = 'The category you have requested does not exist.';
$l['manageboards_ajax_cat_name_error'] = 'The category name is to short.';
$l['manageboards_ajax_cat_not_updated'] = 'The category couldn\'t be updated!';

# AJAX Board edit...
$l['manageboards_ajax_board_not_found'] = 'The board you have requested does not exist.';
$l['manageboards_ajax_board_name_error'] = 'The board name is to short.';
$l['manageboards_ajax_board_not_updated'] = 'The board couldn\'t be updated!';
?>