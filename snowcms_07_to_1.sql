##
# This SQL File will convert your SnowCMS v0.7 database to v1.0
##

# scms_banned_ips table changes
ALTER TABLE `scms_banned_ips` CHANGE `ip` `ip` VARCHAR(16) NOT NULL;
ALTER TABLE `scms_banned_ips` ADD `notes` TEXT NOT NULL AFTER `ip`;

# scms_boards table changes
ALTER TABLE `scms_boards` CHANGE `bid` `board_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `scms_boards` CHANGE `cid` `cat_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_boards` CHANGE `border` `board_order` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_boards` ADD `child_of` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' AFTER `board_order`;
ALTER TABLE `scms_boards` CHANGE `name` `board_name` TINYTEXT NOT NULL;
ALTER TABLE `scms_boards` CHANGE `bdesc` `board_desc` TEXT NOT NULL;
ALTER TABLE `scms_boards` CHANGE `numtopics` `num_topics` INT(11) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_boards` CHANGE `numposts` `num_posts` INT(11) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_boards` CHANGE `last_msg` `last_msg_id` INT(11) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_boards` CHANGE `last_uid` `last_member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_boards` CHANGE `last_name` `last_member_name` TINYTEXT NOT NULL;
ALTER TABLE `scms_boards` ADD INDEX (`cat_id`);
ALTER TABLE `scms_boards` ADD INDEX (`child_of`);
ALTER TABLE `scms_boards` ADD INDEX (`who_view`);

# scms_board_logs table changes
ALTER TABLE `scms_board_logs` CHANGE `bid` `board_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_board_logs` CHANGE `uid` `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_board_logs` DROP KEY `bid`;
ALTER TABLE `scms_board_logs` ADD PRIMARY KEY (`board_id`,`member_id`);

# scms_board_permissions table changes
ALTER TABLE `scms_board_permissions` CHANGE `bid` `board_id` SMALLINT(5) NOT NULL DEFAULT '0';
ALTER TABLE `scms_board_permissions` CHANGE `group_id` `group_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_board_permissions` CHANGE `what` `what` VARCHAR(80) NOT NULL;
ALTER TABLE `scms_board_permissions` CHANGE `can` `can` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1';

# scms_categories table changes
ALTER TABLE `scms_categories` CHANGE `cid` `cat_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `scms_categories` CHANGE `corder` `cat_order` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_categories` CHANGE `cname` `cat_name` TINYTEXT NOT NULL;

##
# Downloads...
##
CREATE TABLE `{$db_prefix}downloads` (
  `download_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `member_name` TINYTEXT NOT NULL,
  `member_ip` TINYTEXT NOT NULL,
  `modified_member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `modified_member_name` TINYTEXT NOT NULL,
  `subject` TINYTEXT NOT NULL,
  `description` TINYTEXT NOT NULL,
  `body` TEXT NOT NULL,
  `post_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `modified_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `num_comments` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `download_type` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `filename` TINYTEXT NOT NULL,
  `filesize` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `downloads` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `mime_type` TINYTEXT NOT NULL,
  `file_ext` VARCHAR(10) NOT NULL,
  `checksum` VARCHAR(40) NOT NULL DEFAULT '',
  `gzencoded` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `isApproved` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`download_id`),
  KEY (`cat_id`),
  KEY (`isApproved`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Download Categories
##
CREATE TABLE `{$db_prefix}download_categories` (
  `cat_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_order` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `cat_name` TINYTEXT NOT NULL,
  `cat_desc` TINYTEXT NOT NULL,
  `num_downloads` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`cat_id`),
  KEY (`num_downloads`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Download comments XD!
##
CREATE TABLE `{$db_prefix}download_comments` (
  `comment_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `download_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `member_name` TINYTEXT NOT NULL,
  `member_ip` TINYTEXT NOT NULL,
  `modified_member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `modified_member_name` TINYTEXT NOT NULL,
  `subject` TINYTEXT NOT NULL,
  `rating` TINYINT(2) UNSIGNED NOT NULL DEFAULT '0',
  `body` TEXT NOT NULL,
  `post_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `modified_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `isApproved` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`comment_id`),
  KEY (`download_id`),
  KEY (`isApproved`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Error Log :)
##
CREATE TABLE `{$db_prefix}error_log` (
  `error_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `error_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `member_name` TINYTEXT NOT NULL,
  `ip` VARCHAR(15) NOT NULL DEFAULT '',
  `error_url` TEXT NOT NULL,
  `error` TEXT NOT NULL,
  `error_type` TINYTEXT NOT NULL,
  `file` TINYTEXT NOT NULL,
  `line` MEDIUMINT(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`error_id`),
  KEY (`error_time`),
  KEY (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Flood control table
##
CREATE TABLE `{$db_prefix}flood_control` (
  `type` VARCHAR(50) NOT NULL,
  `identifier` VARCHAR(20) NOT NULL,
  `ttl` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`type`,`identifier`),
  KEY (`ttl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Mail Queue table which holds emails that need to be sent ;)
##
CREATE TABLE `{$db_prefix}mail_queue` (
  `mail_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `time_added` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `to_address` TINYTEXT NOT NULL,
  `subject` TINYTEXT NOT NULL,
  `message` TEXT NOT NULL,
  `is_html` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `priority` TINYINT(1) UNSIGNED NOT NULL DEFAULT '3',
  `word_wrap` TINYINT(3) UNSIGNED NOT NULL DEFAULT '80',
  `attempted_times` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`mail_id`),
  KEY (`time_added`),
  KEY (`priority`),
  KEY (`attempted_times`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# scms_membergroups table changes
ALTER TABLE `scms_membergroups` CHANGE `group_id` `group_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `scms_membergroups` CHANGE `groupname` `group_name` TINYTEXT NOT NULL;
ALTER TABLE `scms_membergroups` ADD `group_color` VARCHAR(20) NOT NULL DEFAULT '' AFTER `group_name`;
ALTER TABLE `scms_membergroups` ADD `min_posts` INT(11) NOT NULL DEFAULT '-1' AFTER `group_color`;
ALTER TABLE `scms_membergroups` ADD `stars` TINYTEXT NOT NULL AFTER `min_posts`;
ALTER TABLE `scms_membergroups` ADD INDEX (`min_posts`);
UPDATE `scms_membergroups` SET group_color = '#FF0000' WHERE group_id = 1;

# scms_members table changes
ALTER TABLE `scms_members` CHANGE `id` `member_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `scms_members` CHANGE `username` `loginName` VARCHAR(80) NOT NULL;
ALTER TABLE `scms_members` CHANGE `password` `passwrd` VARCHAR(40) NOT NULL;
ALTER TABLE `scms_members` CHANGE `email` `email` TINYTEXT NOT NULL;
ALTER TABLE `scms_members` CHANGE `display_name` `displayName` TINYTEXT NOT NULL;
ALTER TABLE `scms_members` CHANGE `reg_date` `reg_time` INT(10) UNSIGNED NOT NULL;
ALTER TABLE `scms_members` CHANGE `reg_ip` `reg_ip` TINYTEXT NOT NULL;
ALTER TABLE `scms_members` CHANGE `last_login` `last_login` INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_members` CHANGE `last_ip` `last_ip` TINYTEXT NOT NULL;
ALTER TABLE `scms_members` CHANGE `group` `group_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_members` ADD `post_group_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' AFTER `group_id`;
ALTER TABLE `scms_members` CHANGE `numposts` `num_posts` INT(11) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_members` CHANGE `birthdate` `birthdate` DATE NOT NULL DEFAULT '0000-00-00';
ALTER TABLE `scms_members` CHANGE `avatar` `avatar` TINYTEXT NOT NULL AFTER `birthdate`;
ALTER TABLE `scms_members` CHANGE `signature` `signature` TEXT NOT NULL AFTER `avatar`;
ALTER TABLE `scms_members` CHANGE `activated` `isActivated` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `signature`;
ALTER TABLE `scms_members` CHANGE `suspension` `isSuspended` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `isActivated`;
ALTER TABLE `scms_members` CHANGE `banned` `isBanned` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `isSuspended`;
ALTER TABLE `scms_members` ADD `reminderRequested` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `isBanned`;
ALTER TABLE `scms_members` CHANGE `language` `language` TINYTEXT NOT NULL AFTER `reminderRequested`;
ALTER TABLE `scms_members` CHANGE `acode` `acode` TINYTEXT NOT NULL AFTER `language`;
ALTER TABLE `scms_members` CHANGE `unread_pms` `unread_pms` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' AFTER `acode`;
ALTER TABLE `scms_members` CHANGE `site_name` `site_name` TINYTEXT NOT NULL AFTER `unread_pms`;
ALTER TABLE `scms_members` CHANGE `site_url` `site_url` TINYTEXT NOT NULL AFTER `site_name`;
ALTER TABLE `scms_members` ADD `show_email` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `site_url`;
ALTER TABLE `scms_members` CHANGE `icq` `icq` TINYTEXT NOT NULL AFTER `show_email`;
ALTER TABLE `scms_members` CHANGE `aim` `aim` TINYTEXT NOT NULL AFTER `icq`;
ALTER TABLE `scms_members` CHANGE `msn` `msn` TINYTEXT NOT NULL AFTER `aim`;
ALTER TABLE `scms_members` CHANGE `yim` `yim` TINYTEXT NOT NULL AFTER `msn`;
ALTER TABLE `scms_members` CHANGE `gtalk` `gtalk` TINYTEXT NOT NULL AFTER `yim`;
ALTER TABLE `scms_members` ADD `adminSc` VARCHAR(40) NOT NULL AFTER `gtalk`;
ALTER TABLE `scms_members` DROP `sc`;
ALTER TABLE `scms_members` DROP `pms_lastread`;
ALTER TABLE `scms_members` DROP `profile`;
ALTER TABLE `scms_members` DROP `num_topics`;
ALTER TABLE `scms_members` ADD INDEX (`loginName`);
ALTER TABLE `scms_members` ADD INDEX (`passwrd`);
ALTER TABLE `scms_members` ADD INDEX (`group_id`);
ALTER TABLE `scms_members` ADD INDEX (`isActivated`);
ALTER TABLE `scms_members` ADD INDEX (`isSuspended`);
ALTER TABLE `scms_members` ADD INDEX (`isBanned`);
ALTER TABLE `scms_members` ADD INDEX (`reminderRequested`);

# scms_menus table changes
ALTER TABLE `scms_menus` CHANGE `link_id` `link_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `scms_menus` CHANGE `order` `link_order` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_menus` CHANGE `link_name` `link_name` TINYTEXT NOT NULL;
ALTER TABLE `scms_menus` CHANGE `href` `link_href` TINYTEXT NOT NULL;
ALTER TABLE `scms_menus` CHANGE `target` `link_target` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_menus` CHANGE `menu` `link_menu` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_menus` DROP `permission`;
ALTER TABLE `scms_menus` ADD `link_follow` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `link_menu`;
ALTER TABLE `scms_menus` ADD `who_view` VARCHAR(255) NOT NULL DEFAULT '-1,0' AFTER `link_follow`;
ALTER TABLE `scms_menus` ADD INDEX (`link_menu`);
ALTER TABLE `scms_menus` ADD INDEX (`who_view`);

# scms_message_logs table changes
ALTER TABLE `scms_message_logs` CHANGE `uid` `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_message_logs` CHANGE `tid` `topic_id` INT(11) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_message_logs` CHANGE `mid` `msg_id` INT(11) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_message_logs` DROP INDEX `uid`;
ALTER TABLE `scms_message_logs` DROP INDEX `tid`;
ALTER TABLE `scms_message_logs` ADD PRIMARY KEY (`member_id`,`topic_id`);
ALTER TABLE `scms_message_logs` ADD INDEX (`msg_id`);

# scms_messages table changes
ALTER TABLE `scms_messages` CHANGE `mid` `msg_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `scms_messages` CHANGE `tid` `topic_id` INT(11) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_messages` CHANGE `bid` `board_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_messages` CHANGE `uid` `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_messages` CHANGE `uid_editor` `modified_member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_messages` CHANGE `editor_name` `modified_name` TINYTEXT NOT NULL AFTER `modified_member_id`;
ALTER TABLE `scms_messages` CHANGE `edit_reason` `modified_reason` TINYTEXT NOT NULL AFTER `modified_name`;
ALTER TABLE `scms_messages` CHANGE `edit_time` `modified_time` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `modified_reason`;
ALTER TABLE `scms_messages` CHANGE `subject` `subject` TINYTEXT NOT NULL;
ALTER TABLE `scms_messages` CHANGE `post_time` `poster_time` INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_messages` CHANGE `poster_name` `poster_name` TINYTEXT NOT NULL;
ALTER TABLE `scms_messages` CHANGE `poster_email` `poster_email` TINYTEXT NOT NULL;
ALTER TABLE `scms_messages` CHANGE `ip` `poster_ip` VARCHAR(16) NOT NULL;
ALTER TABLE `scms_messages` ADD INDEX (`topic_id`);
ALTER TABLE `scms_messages` ADD INDEX (`board_id`);
ALTER TABLE `scms_messages` ADD INDEX (`member_id`);
ALTER TABLE `scms_messages` ADD INDEX (`poster_ip`);

##
# Moderators table, for the forum
##
CREATE TABLE `{$db_prefix}moderators` (
  `board_id` SMALLINT(5) UNSIGNED NOT NULL,
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`board_id`,`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# scms_news table changes
ALTER TABLE `scms_news` CHANGE `news_id` `news_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `scms_news` CHANGE `cat_id` `cat_id` SMALLINT(5) UNSIGNED NOT NULL AFTER `news_id`;
ALTER TABLE `scms_news` CHANGE `poster_id` `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0' AFTER `cat_id`;
ALTER TABLE `scms_news` ADD `modified_member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0' AFTER `member_id`;
ALTER TABLE `scms_news` ADD `modified_name` TINYTEXT NOT NULL AFTER `modified_member_id`;
ALTER TABLE `scms_news` ADD `modified_time` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `modified_name`;
ALTER TABLE `scms_news` CHANGE `subject` `subject` TINYTEXT NOT NULL;
ALTER TABLE `scms_news` CHANGE `post_time` `poster_time` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `subject`;
ALTER TABLE `scms_news` CHANGE `poster_name` `poster_name` TINYTEXT NOT NULL AFTER `poster_time`;
ALTER TABLE `scms_news` ADD `poster_email` TINYTEXT NOT NULL AFTER `poster_name`;
ALTER TABLE `scms_news` CHANGE `body` `body` TEXT NOT NULL AFTER `poster_email`;
ALTER TABLE `scms_news` CHANGE `num_comments` `num_comments` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_news` CHANGE `num_views` `num_views` INT(11) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_news` CHANGE `allow_comments` `allow_comments` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `scms_news` ADD INDEX (`cat_id`);
ALTER TABLE `scms_news` ADD INDEX (`num_comments`);
ALTER TABLE `scms_news` ADD INDEX (`num_views`);
ALTER TABLE `scms_news` DROP `modify_time`;

# scms_news_categories table changes
ALTER TABLE `scms_news_categories` CHANGE `cat_id` `cat_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `scms_news_categories` CHANGE `cat_name` `cat_name` TINYTEXT NOT NULL;

# scms_news_comments table changes
ALTER TABLE `scms_news_comments` CHANGE `post_id` `comment_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `scms_news_comments` CHANGE `nid` `news_id` INT(11) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_news_comments` CHANGE `poster_id` `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_news_comments` CHANGE `subject` `subject` TINYTEXT NOT NULL AFTER `member_id`;
ALTER TABLE `scms_news_comments` CHANGE `post_time` `poster_time` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `subject`;
ALTER TABLE `scms_news_comments` CHANGE `poster_name` `poster_name` TINYTEXT NOT NULL AFTER `poster_time`;
ALTER TABLE `scms_news_comments` ADD `poster_email` TINYTEXT NOT NULL AFTER `poster_name`;
ALTER TABLE `scms_news_comments` ADD `poster_ip` VARCHAR(16) NOT NULL AFTER `poster_email`;
ALTER TABLE `scms_news_comments` CHANGE `isApproved` `isApproved` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `scms_news_comments` CHANGE `isSpam` `isSpam` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_news_comments` ADD INDEX (`news_id`);
ALTER TABLE `scms_news_comments` ADD INDEX (`isApproved`);
ALTER TABLE `scms_news_comments` ADD INDEX (`isSpam`);
ALTER TABLE `scms_news_comments` DROP `modify_time`;

# scms_online table changes
ALTER TABLE `scms_online` CHANGE `sc` `session_id` VARCHAR(32) NOT NULL FIRST;
ALTER TABLE `scms_online` CHANGE `last_active` `last_active` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `session_id`;
ALTER TABLE `scms_online` CHANGE `user_id` `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0' AFTER `last_active`;
ALTER TABLE `scms_online` CHANGE `ip` `ip` VARCHAR(16) NOT NULL AFTER `member_id`;
ALTER TABLE `scms_online` CHANGE `url_data` `data` TEXT NOT NULL AFTER `ip`;
ALTER TABLE `scms_online` DROP `inForum`;
ALTER TABLE `scms_online` ADD INDEX (`last_active`);
ALTER TABLE `scms_online` ADD INDEX (`member_id`);

# scms_pages table changes
ALTER TABLE `scms_pages` CHANGE `page_id` `page_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `scms_pages` CHANGE `page_owner` `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_pages` CHANGE `owner_name` `member_name` TINYTEXT NOT NULL;
ALTER TABLE `scms_pages` ADD `modified_member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0' AFTER `member_name`;
ALTER TABLE `scms_pages` ADD `modified_name` TINYTEXT NOT NULL AFTER `modified_member_id`;
ALTER TABLE `scms_pages` CHANGE `create_date` `created_time` INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_pages` CHANGE `modify_date` `modified_time` INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_pages` CHANGE `title` `page_title` TINYTEXT NOT NULL;
ALTER TABLE `scms_pages` CHANGE `html` `is_html` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `scms_pages` ADD `is_viewable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `is_html`;
ALTER TABLE `scms_pages` ADD `num_views` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `is_viewable`;
ALTER TABLE `scms_pages` ADD `who_view` VARCHAR(255) NOT NULL DEFAULT '-1,0' AFTER `num_views`;
ALTER TABLE `scms_pages` ADD INDEX (`member_id`);
ALTER TABLE `scms_pages` ADD INDEX (`modified_member_id`);
ALTER TABLE `scms_pages` ADD INDEX (`is_viewable`);
ALTER TABLE `scms_pages` ADD INDEX (`num_views`);
ALTER TABLE `scms_pages` ADD INDEX (`who_view`);
