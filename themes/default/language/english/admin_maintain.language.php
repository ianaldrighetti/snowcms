<?php
#
# Admin English file for SnowCMS
# Created by the SnowCMS Dev Team
#       www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $settings;

# Backup database stuff
$l['maintain_backup_title'] = 'Backup Database - '. $settings['site_name'];
$l['maintain_backup_header'] = 'Backup Database';
$l['maintain_backup_desc'] = 'Select some options for your database backup. Most of these can be left on the default.';
$l['maintain_backup_comments'] = 'Comments';
$l['maintain_backup_comments_sub'] = 'SnowCMS version, database name, SQL type and date/time automatically included.';
$l['maintain_backup_structure'] = 'Include table structure';
$l['maintain_backup_noexists'] = 'Action on table already exists';
$l['maintain_backup_noexists_sub'] = 'Drop deletes pre-existing table including its data.';
$l['maintain_backup_noexists_drop'] = 'Drop';
$l['maintain_backup_noexists_ignore'] = 'Ignore';
$l['maintain_backup_noexists_none'] = 'None';
$l['maintain_backup_drop'] = 'Drop table if already exists';
$l['maintain_backup_drop_sub'] = 'Deletes the pre-existing table including its data, if it already exists.';
$l['maintain_backup_ignore'] = 'Don\'t create table if already exists';
$l['maintain_backup_data'] = 'Include data';
$l['maintain_backup_data_sub'] = 'Important!';
$l['maintain_backup_extended_inserts'] = 'Use extended inserts';
$l['maintain_backup_extended_inserts_sub'] = 'Decreases file size but increases chances of failure. Highly <strong>not</strong> recommended.';
$l['maintain_backup_num_extended'] = 'Number of grouped inserts';
$l['maintain_backup_num_extended_sub'] = 'Used only with extended inserts. Low number recommended.';
$l['maintain_backup_gzip'] = 'Use GZip compression';
$l['maintain_backup_gzip_sub'] = 'Compresses the backup in a GZip file.';
$l['maintain_backup_submit'] = 'Download Backup';

# Error log!
$l['admin_maintain_error_title'] = 'Error log - '. $settings['site_name'];
$l['admin_maintain_error_header'] = 'Error log';
$l['admin_maintain_error_desc'] = 'Here you can view all the errors which have occurred on your site. You should always try to fix these errors or report them to <a href="http://www.snowcms.com/" target="_blank">SnowCMS.com</a> if they are from the SnowCMS core.';
$l['admin_maintain_error_all'] = 'All errors';
$l['admin_maintain_error_database'] = 'Database';
$l['admin_maintain_error_8'] = 'Notices';
$l['admin_maintain_error_2'] = 'Warnings';
$l['admin_maintain_error_unknown'] = 'Unknown';
$l['admin_maintain_error_view'] = 'View';
$l['admin_maintain_error_invert'] = 'Invert all boxes';
$l['admin_maintain_error_empty'] = 'Remove all errors';
$l['admin_maintain_error_filter_delete'] = 'Remove all \'%s\'';
$l['admin_maintain_error_delete_selected'] = 'Delete selected';
$l['admin_maintain_error_delete_confirm'] = 'Are you sure you want to continue?\r\nThis cannot be undone!';
$l['admin_maintain_error_none'] = 'No errors to display.';
$l['admin_maintain_error_mark_delete'] = 'Mark this error for deletion';
$l['admin_maintain_error_member_title'] = 'View other errors by %s';
$l['admin_maintain_error_view_profile'] = 'View %s\'s profile';
$l['admin_maintain_error_ip_title'] = 'View other errors by the IP %s';
$l['admin_maintain_error_type'] = 'Error type';
$l['admin_maintain_error_line'] = 'Line';
$l['admin_maintain_error_file'] = 'File';
$l['admin_maintain_error_file_title'] = 'View other errors from this file';
$l['admin_maintain_error_line'] = 'Line';
$l['admin_maintain_error_url_title'] = 'View other errors from this URL';
$l['maintain_error_category'] = '%1$s (%2$s)';
?>