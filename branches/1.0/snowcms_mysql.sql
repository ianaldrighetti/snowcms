##
# NOTE: This is used by the SnowCMS Installer, you do not need to do
#       anything with this at all!
##

##
# Banned IPs and crap :P!
##
DROP TABLE IF EXISTS `{$db_prefix}banned_ips`;
CREATE TABLE `{$db_prefix}banned_ips` (
  `ip` VARCHAR(16) NOT NULL,
  `notes` TEXT NOT NULL,
  `reason` TEXT NOT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# IP logs, who used what and when?
##
DROP TABLE IF EXISTS `{$db_prefix}ip_logs`;
CREATE TABLE `{$db_prefix}ip_logs` (
  `ip` VARCHAR(16) NOT NULL DEFAULT '',
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  `first_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  KEY (`ip`, `member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Board Log, have you read this? :P!
##
DROP TABLE IF EXISTS `{$db_prefix}board_logs`;
CREATE TABLE `{$db_prefix}board_logs` (
  `board_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`board_id`,`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Board Permissions
##
DROP TABLE IF EXISTS `{$db_prefix}board_permissions`;
CREATE TABLE `{$db_prefix}board_permissions` (
  `board_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `group_id` SMALLINT(5) NOT NULL DEFAULT '0',
  `what` VARCHAR(80) NOT NULL,
  `can` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`board_id`,`group_id`,`what`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Boards of the forum xD
##
DROP TABLE IF EXISTS `{$db_prefix}boards`;
CREATE TABLE `{$db_prefix}boards` (
  `board_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `board_order` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `child_of` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `who_view` VARCHAR(255) NOT NULL DEFAULT '-1,0',
  `board_name` VARCHAR(255) NOT NULL,
  `board_desc` TEXT NOT NULL,
  `num_topics` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `num_posts` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `last_msg_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `last_member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  `last_member_name` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`board_id`),
  KEY (`cat_id`),
  KEY (`child_of`),
  KEY (`who_view`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}boards` (`cat_id`,`board_order`,`board_name`,`board_desc`) VALUES
(1, 0, '{GENERAL_BOARD}', '{GENERAL_BOARD_DESC}');

##
# Categories for Forum
##
DROP TABLE IF EXISTS `{$db_prefix}categories`;
CREATE TABLE `{$db_prefix}categories` (
  `cat_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_order` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `cat_name` VARCHAR(255) NOT NULL,
  `is_collapsible` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}categories` (`cat_order`,`cat_name`) VALUES
(0, '{GENERAL}');

##
# Downloads...
##
DROP TABLE IF EXISTS `{$db_prefix}downloads`;
CREATE TABLE `{$db_prefix}downloads` (
  `download_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `member_name` VARCHAR(255) NOT NULL,
  `member_ip` VARCHAR(16) NOT NULL,
  `modified_member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `modified_member_name` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `body` TEXT NOT NULL,
  `post_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `modified_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `num_comments` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `downloads` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `is_approved` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`download_id`),
  KEY (`cat_id`),
  KEY (`downloads`),
  KEY (`is_approved`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Download items
##
DROP TABLE IF EXISTS `{$db_prefix}download_items`;
CREATE TABLE `{$db_prefix}download_items` (
  `item_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `download_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `download_type` TINYINT(1) NOT NULL DEFAULT '0',
  `filename` VARCHAR(255) NOT NULL,
  `filesize` INT(11) UNSIGNED NOT NULL,
  `downloads` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `mime_type` VARCHAR(255) NOT NULL,
  `file_ext` VARCHAR(20) NOT NULL,
  `checksum` VARCHAR(40) NOT NULL DEFAULT '',
  `base64` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`),
  KEY (`download_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Download Categories
##
DROP TABLE IF EXISTS `{$db_prefix}download_categories`;
CREATE TABLE `{$db_prefix}download_categories` (
  `cat_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_order` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `cat_name` VARCHAR(255) NOT NULL,
  `cat_desc` VARCHAR(255) NOT NULL,
  `num_downloads` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`cat_id`),
  KEY (`num_downloads`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}download_categories` (`cat_order`,`cat_name`,`cat_desc`) VALUES
(0, '{GENERAL}', '');

##
# Download comments XD!
##
DROP TABLE IF EXISTS `{$db_prefix}download_comments`;
CREATE TABLE `{$db_prefix}download_comments` (
  `comment_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `download_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `member_name` VARCHAR(255) NOT NULL,
  `member_ip` VARCHAR(255) NOT NULL,
  `modified_member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `modified_member_name` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `rating` TINYINT(2) UNSIGNED NOT NULL DEFAULT '0',
  `body` TEXT NOT NULL,
  `post_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `modified_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `is_approved` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`comment_id`),
  KEY (`download_id`),
  KEY (`is_approved`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Emoticons, y'know smileys
##
DROP TABLE IF EXISTS `{$db_prefix}emoticons`;
CREATE TABLE `{$db_prefix}emoticons` (
  `emoticon_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pack` VARCHAR(255) NOT NULL DEFAULT '',
  `filename` TEXT NOT NULL DEFAULT '',
  `sequences` TEXT NOT NULL DEFAULT '',
  `name` TEXT NOT NULL DEFAULT '',
  PRIMARY KEY (`emoticon_id`),
  KEY (`pack`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Default emoticons
INSERT INTO `{$db_prefix}emoticons` (`pack`,`filename`,`sequences`,`name`) VALUES
('default', 'confused.png', ':S :-S =S', 'Confused'),
('default', 'nerdy.png', '8) 8-) 8] 8-]', 'Nerdy'),
('default', 'grin.png', ':D :-D =D', 'Grin'),
('default', 'happy.png', ':) :-) =) :] :-] =]', 'Happy'),
('default', 'mad.png', '>:( >:-( >=( >:[ >:-[ >=[ >:| >:-| >=|', 'Mad'),
('default', 'sad.png', ':( :-( =( :[ :-[ =[', 'Sad'),
('default', 'stunned.png', ':| :-| =|', 'Stunned'),
('default', 'surprised.png', ':O :-O =O :0 :-0 =0', 'Surprised'),
('default', 'tongue.png', ':P :-P =P', 'Tongue'),
('default', 'wink.png', '%3B) %3B-) %3B] %3B-]', 'Wink');

##
# Error Log :)
##
DROP TABLE IF EXISTS `{$db_prefix}error_log`;
CREATE TABLE `{$db_prefix}error_log` (
  `error_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `error_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `member_name` VARCHAR(255) NOT NULL,
  `ip` VARCHAR(15) NOT NULL DEFAULT '',
  `error_url` TEXT NOT NULL,
  `error` TEXT NOT NULL,
  `error_type` VARCHAR(255) NOT NULL,
  `file` VARCHAR(255) NOT NULL,
  `line` MEDIUMINT(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`error_id`),
  KEY (`error_time`),
  KEY (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Flood control table
##
DROP TABLE IF EXISTS `{$db_prefix}flood_control`;
CREATE TABLE `{$db_prefix}flood_control` (
  `type` VARCHAR(50) NOT NULL,
  `identifier` VARCHAR(20) NOT NULL,
  `ttl` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  KEY (`type`),
  KEY (`identifier`),
  KEY (`ttl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Mail Queue table which holds emails that need to be sent ;)
##
DROP TABLE IF EXISTS `{$db_prefix}mail_queue`;
CREATE TABLE `{$db_prefix}mail_queue` (
  `mail_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `time_added` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `to_address` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
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

##
# Membergroups...
##
DROP TABLE IF EXISTS `{$db_prefix}membergroups`;
CREATE TABLE `{$db_prefix}membergroups` (
  `group_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_name` VARCHAR(255) NOT NULL,
  `group_name_plural` VARCHAR(255) NOT NULL,
  `group_color` VARCHAR(20) NOT NULL DEFAULT '',
  `min_posts` INT(11) NOT NULL DEFAULT -1,
  `stars` VARCHAR(255) NOT NULL,
  `members` INT(11) NOT NULL DEFAULT 0,
  `allowed_pm_size` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`group_id`),
  KEY (`min_posts`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}membergroups` (`group_name`,`group_name_plural`,`group_color`,`stars`) VALUES
('{ADMINISTRATOR}', '{ADMINISTRATORS}', '#B20000', '5|admin_star.png'),
('{GLOBAL_MODERATOR}', '{GLOBAL_MODERATORS}', '#00B200', '5|gmod_star.png'),
('{MEMBER}', '{MEMBERS}', '', '5|member_star.png');

##
# Table with all members
##
DROP TABLE IF EXISTS `{$db_prefix}members`;
CREATE TABLE `{$db_prefix}members` (
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `loginName` VARCHAR(80) NOT NULL,
  `passwrd` VARCHAR(40) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `displayName` VARCHAR(255) NOT NULL,
  `reg_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `reg_ip` VARCHAR(255) NOT NULL DEFAULT '',
  `last_login` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_online` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_ip` VARCHAR(255) NOT NULL DEFAULT '',
  `time_online` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `group_id` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `post_group_id` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `num_posts` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `num_topics` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `birthdate` VARCHAR(10) NOT NULL DEFAULT '',
  `avatar` VARCHAR(255) NOT NULL DEFAULT '',
  `signature` TEXT NOT NULL DEFAULT '',
  `profile_text` TEXT NOT NULL DEFAULT '',
  `custom_title` TEXT NOT NULL DEFAULT '',
  `location` VARCHAR(255) NOT NULL DEFAULT '',
  `gender` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_activated` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_banned` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `suspended` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `reminder_requested` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `language` VARCHAR(255) NOT NULL DEFAULT '',
  `acode` VARCHAR(255) NOT NULL DEFAULT '',
  `unread_pms` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `total_pms` INT UNSIGNED NOT NULL DEFAULT 0,
  `pm_size` INT UNSIGNED NOT NULL DEFAULT 0,
  `site_name` VARCHAR(255) NOT NULL DEFAULT '',
  `site_url` VARCHAR(255) NOT NULL DEFAULT '',
  `receive_email` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `show_email` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `icq` VARCHAR(255) NOT NULL DEFAULT '',
  `aim` VARCHAR(255) NOT NULL DEFAULT '',
  `msn` VARCHAR(255) NOT NULL DEFAULT '',
  `yim` VARCHAR(255) NOT NULL DEFAULT '',
  `gtalk` VARCHAR(255) NOT NULL DEFAULT '',
  `theme` VARCHAR(255) NOT NULL DEFAULT 'default',
  `format_datetime` VARCHAR(255) NOT NULL DEFAULT 'MMMM D, YYYY, h:mm:ss P',
  `format_date` VARCHAR(255) NOT NULL DEFAULT 'MMMM D, YYYY',
  `format_time` VARCHAR(255) NOT NULL DEFAULT 'h:mm:ss P',
  `timezone` SMALLINT UNSIGNED NOT NULL DEFAULT 32,
  `dst` TINYINT UNSIGNED NOT NULL DEFAULT 2,
  `preference_quick_reply` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `preference_avatars` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `preference_signatures` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `preference_post_images` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `preference_emoticons` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `preference_return_topic` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `preference_pm_display` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `preference_recently_online` TINYINT UNSIGNED NOT NULL DEFAULT 2,
  `preference_thousands_separator` VARCHAR(1) NOT NULL DEFAULT ',',
  `preference_decimal_point` VARCHAR(1) NOT NULL DEFAULT '.',
  `preference_today_yesterday` TINYINT UNSIGNED NOT NULL DEFAULT 2,
  `per_page_topics` TINYINT UNSIGNED NOT NULL DEFAULT 20,
  `per_page_posts` TINYINT UNSIGNED NOT NULL DEFAULT 10,
  `per_page_news` TINYINT UNSIGNED NOT NULL DEFAULT 10,
  `per_page_downloads` TINYINT UNSIGNED NOT NULL DEFAULT 10,
  `per_page_comments` TINYINT UNSIGNED NOT NULL DEFAULT 10,
  `per_page_members` TINYINT UNSIGNED NOT NULL DEFAULT 20,
  `menus_last_cached` INT UNSIGNED NOT NULL DEFAULT 0,
  `adminSc` VARCHAR(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`member_id`),
  KEY (`loginName`),
  KEY (`passwrd`),
  KEY (`group_id`),
  KEY (`is_activated`),
  KEY (`is_banned`),
  KEY (`suspended`),
  KEY (`reminder_requested`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Menu table
##
DROP TABLE IF EXISTS `{$db_prefix}menus`;
CREATE TABLE `{$db_prefix}menus` (
  `link_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `link_name` VARCHAR(255) NOT NULL,
  `link_order` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `link_href` VARCHAR(255) NOT NULL,
  `link_target` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `link_menu` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  `link_follow` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  `who_view` VARCHAR(255) NOT NULL DEFAULT '-1,2',
  PRIMARY KEY (`link_id`),
  KEY (`link_menu`),
  KEY (`who_view`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Default menus
INSERT INTO `{$db_prefix}menus` (`link_name`,`link_order`,`link_href`,`link_menu`,`who_view`) VALUES
('{HOME}', 1, '{$base_url}/index.php', 1, '-1,2,3'),
('{NEWS}', 2, '{$base_url}/index.php?action=news', 1, '-1,2,3'),
('{FORUM}', 3, '{$base_url}/forum.php', 1, '-1,2,3'),
('{PROFILE}', 4, '{$base_url}/index.php?action=profile', 1, '2,3'),
('{HOME}', 5, '{$base_url}/index.php', 2, '-1,2,3'),
('{NEWS}', 6, '{$base_url}/index.php?action=news', 2, '-1,2,3'),
('{FORUM}', 7, '{$base_url}/forum.php', 2, '-1,2,3'),
('{PROFILE}', 8, '{$base_url}/index.php?action=profile', 2, '2,3'),
('{MEMBER_LIST}', 9, '{$base_url}/index.php?action=memberlist', 2, '-1,2,3'),
('{STATS}', 10, '{$base_url}/index.php?action=stats', 2, '-1,2,3');

##
# This does the whether you have posted in this topic =D
##
DROP TABLE IF EXISTS `{$db_prefix}message_logs`;
CREATE TABLE `{$db_prefix}message_logs` (
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `topic_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `msg_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`member_id`,`topic_id`),
  KEY (`msg_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Message table for topics :P!
##
DROP TABLE IF EXISTS `{$db_prefix}messages`;
CREATE TABLE `{$db_prefix}messages` (
  `msg_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `topic_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `board_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `modified_member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `modified_name` VARCHAR(255) NOT NULL,
  `modified_reason` VARCHAR(255) NOT NULL,
  `modified_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `subject` VARCHAR(255) NOT NULL,
  `poster_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `poster_name` VARCHAR(255) NOT NULL,
  `poster_email` VARCHAR(255) NOT NULL,
  `poster_ip` VARCHAR(150) NOT NULL,
  `body` TEXT NOT NULL,
  `parse_bbc` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  `parse_smileys` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  `is_locked` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`msg_id`),
  KEY (`topic_id`),
  KEY (`board_id`),
  KEY (`member_id`),
  KEY (`poster_ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Moderators table, for the forum
##
DROP TABLE IF EXISTS `{$db_prefix}moderators`;
CREATE TABLE `{$db_prefix}moderators` (
  `board_id` SMALLINT(5) UNSIGNED NOT NULL,
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`board_id`,`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# News table
##
DROP TABLE IF EXISTS `{$db_prefix}news`;
CREATE TABLE `{$db_prefix}news` (
  `news_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `modified_member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `modified_name` VARCHAR(255) NOT NULL,
  `modified_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `subject` VARCHAR(255) NOT NULL,
  `poster_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `poster_name` VARCHAR(255) NOT NULL,
  `poster_email` VARCHAR(255) NOT NULL,
  `body` TEXT NOT NULL,
  `num_comments` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `num_views` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `allow_comments` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  `is_viewable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`news_id`),
  KEY (`cat_id`),
  KEY (`num_comments`),
  KEY (`num_views`),
  KEY (`is_viewable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# News Categories
##
DROP TABLE IF EXISTS `{$db_prefix}news_categories`;
CREATE TABLE `{$db_prefix}news_categories` (
  `cat_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_name` VARCHAR(255) NOT NULL,
  `num_news` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Comments for News :P
##
DROP TABLE IF EXISTS `{$db_prefix}news_comments`;
CREATE TABLE `{$db_prefix}news_comments` (
  `comment_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `news_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `subject` VARCHAR(255) NOT NULL,
  `poster_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `poster_name` VARCHAR(255) NOT NULL,
  `poster_email` VARCHAR(255) NOT NULL,
  `poster_ip` VARCHAR(150) NOT NULL,
  `body` TEXT NOT NULL,
  `is_approved` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  `is_spam` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`),
  KEY (`news_id`),
  KEY (`is_approved`),
  KEY (`is_spam`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Online loggy thing XD
##
DROP TABLE IF EXISTS `{$db_prefix}online`;
CREATE TABLE `{$db_prefix}online` (
  `session_id` VARCHAR(32) NOT NULL,
  `last_active` INT(10) UNSIGNED NOT NULL,
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `ip` VARCHAR(150) NOT NULL,
  `data` TEXT NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY (`last_active`),
  KEY (`member_id`),
  KEY (`data`(255))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Pages ^^
##
DROP TABLE IF EXISTS `{$db_prefix}pages`;
CREATE TABLE `{$db_prefix}pages` (
  `page_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `member_name` VARCHAR(255) NOT NULL,
  `creator_ip` VARCHAR(16) NOT NULL DEFAULT '0',
  `modified_member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `modified_name` VARCHAR(255) NOT NULL,
  `created_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `modified_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `page_title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `type` TINYINT(2) UNSIGNED NOT NULL DEFAULT '2',
  `is_viewable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  `num_views` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `who_view` VARCHAR(255) NOT NULL DEFAULT '-1,0',
  PRIMARY KEY (`page_id`),
  KEY (`member_id`),
  KEY (`modified_member_id`),
  KEY (`is_viewable`),
  KEY (`num_views`),
  KEY (`who_view`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Home page
INSERT INTO `{$db_prefix}pages` (`member_name`,`modified_name`,`created_time`,`modified_time`,`page_title`,`content`,`type`) VALUES
('SnowCMS Team', 'SnowCMS Team', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), '{HOME_TITLE}', '{HOME_BODY}', 2);

##
# Permissions for main setup :P
##
DROP TABLE IF EXISTS `{$db_prefix}permissions`;
CREATE TABLE `{$db_prefix}permissions` (
  `group_id` SMALLINT(5) NOT NULL DEFAULT '0',
  `what` VARCHAR(80) NOT NULL,
  `can` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`group_id`,`what`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}permissions` (`group_id`, `what`, `can`) VALUES
# Guests' permissions
(-1, 'view_profiles', 1),
(-1, 'view_memberlist', 1),
(-1, 'view_stats', 1),
(-1, 'download_downloads', 1),
(-1, 'view_forum', 1),
# Global moderators' permissions
(2, 'view_profiles', 1),
(2, 'view_memberlist', 1),
(2, 'view_stats', 1),
(2, 'post_news_comments', 1),
(2, 'post_download_comments', 1),
(2, 'edit_news_comments', 1),
(2, 'edit_download_comments', 1),
(2, 'download_downloads', 1),
(2, 'view_pms', 1),
(2, 'send_pms', 1),
(2, 'edit_profile', 1),
(2, 'edit_display_name', 1),
(2, 'edit_email', 1),
(2, 'edit_avatar', 1),
(2, 'edit_signature', 1),
(2, 'edit_profile_text', 1),
(2, 'upload_avatars', 1),
(2, 'view_forum', 1),
(2, 'post_topic', 1),
(2, 'post_reply', 1),
(2, 'post_poll', 1),
(2, 'edit_post', 1),
(2, 'edit_poll', 1),
(2, 'delete_post', 1),
(2, 'delete_topic', 1),
(2, 'remove_poll', 1),
(2, 'moderate_pms', 1),
(2, 'ban_member', 1),
(2, 'unban_member', 1),
(2, 'suspend_member', 1),
(2, 'unsuspend_member', 1),
(2, 'add_poll_any', 1),
(2, 'edit_post_any', 1),
(2, 'edit_news_comment_any', 1),
(2, 'edit_download_comment_any', 1),
(2, 'edit_poll_any', 1),
(2, 'delete_post_any', 1),
(2, 'delete_news_comment_any', 1),
(2, 'delete_download_comment_any', 1),
(2, 'delete_topic_any', 1),
(2, 'remove_poll_any', 1),
# Basic members' permissions
(3, 'view_profiles', 1),
(3, 'view_memberlist', 1),
(3, 'view_stats', 1),
(3, 'post_news_comments', 1),
(3, 'post_download_comments', 1),
(3, 'edit_news_comments', 1),
(3, 'edit_download_comments', 1),
(3, 'download_downloads', 1),
(3, 'view_pms', 1),
(3, 'send_pms', 1),
(3, 'edit_profile', 1),
(3, 'edit_display_name', 1),
(3, 'edit_email', 1),
(3, 'edit_avatar', 1),
(3, 'edit_signature', 1),
(3, 'edit_profile_text', 1),
(3, 'upload_avatars', 1),
(3, 'view_forum', 1),
(3, 'post_topic', 1),
(3, 'post_reply', 1),
(3, 'post_poll', 1),
(3, 'edit_post', 1),
(3, 'edit_poll', 1),
(3, 'delete_post', 1),
(3, 'remove_poll', 1);

##
# Personal Messages, Need I explain?
##
DROP TABLE IF EXISTS `{$db_prefix}personal_messages`;
CREATE TABLE `{$db_prefix}personal_messages` (
  `pm_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `folder` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `recipients` TEXT NOT NULL DEFAULT '',
  `sender_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `sender_ip` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `body` TEXT NOT NULL,
  `time_sent` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `status` SMALLINT(4) UNSIGNED NOT NULL DEFAULT '0',
  `flagged` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `read_receipt` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `reported` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`pm_id`),
  KEY (`sender_id`),
  KEY (`folder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# This stores settings :)
##
DROP TABLE IF EXISTS `{$db_prefix}settings`;
CREATE TABLE `{$db_prefix}settings` (
  `variable` VARCHAR(255) NOT NULL,
  `value` TEXT NOT NULL,
  PRIMARY KEY (`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# {$db_prefix}settings values
INSERT INTO `{$db_prefix}settings` (`variable`,`value`) VALUES
('site_name', 'My Website'),
('site_slogan', 'Powered By SnowCMS'),
('gz_compressed', 1),
('cookie_name', 'SCMS000'),
('allow_cookie_subdomain', 0),
('allow_cookie_https', 0),
('default_language', 'english'),
('online_timeout', 15),
('enable_tasks', 1),
('homepage', 1),
('default_page', 1),
('create_meta', 1),
('meta_description', ''),
('meta_keywords', ''),
('keyword_appears', 2),
('num_keywords', 16),
('log_errors', 1),
('num_tasks', 3),
('cache_enabled', 1),
('cache_type', 'file'),
('scmsVersion', '1.0'),
('registration_enabled', 1),
('reservedNames',''),
('disallowed_emails',''),
('disallowed_email_domains',''),
('mail_type', 0),
('smtp_host',''),
('smtp_port', 25),
('smtp_user', ''),
('smtp_pass', ''),
('site_email','admin@localhost'),
('account_activation', 0),
('maintenance_mode', 0),
('maintenance_mode_title', ''),
('maintenance_mode_reason', ''),
('enable_mail_queue', 1),
('mail_queue_num_send', 5),
('captcha_chars', 5),
('captcha_strength', 3),
('show_query_count', 0),
('forum_enabled', 1),
('downloads_enabled', 1),
('registration_group', 3),
('most_online_ever', 0),
('most_online_today', 0),
('total_members', 0),
('total_pages', 1),
('total_boards', 0),
('total_topics', 0),
('total_posts', 0),
('total_news', 0),
('total_news_comments', 0),
('total_downloads', 0),
('total_downloads_hits', 0),
('total_downloads_comments', 0),
('total_page_views', 0),
('members_posted', 0),
('members_commented', 0),
('emoticon_pack', 'default'),
('install_time', UNIX_TIMESTAMP()),
('guest_menus_last_cached', 0),
('menus_last_updated', UNIX_TIMESTAMP()),
('current_version', '1.0'),
('current_news', ''),
('change_theme', 0),
('uploaded_avatars_enabled', 1),
('avatar_filetypes', 'bmp,gif,png,jpg,tif'),
('avatar_filesize', 100),
('avatar_size', '100x100'),
('avatar_resize', 1),
('database_sessions', 1),
('forum_recent_posts', 12),
('password_minimum', 4),
('password_recommended', 8),
('password_strength', 1),
('theme', 'default'),
('format_datetime', 'MMMM D, YYYY, h:mm:ss P'),
('format_date', 'MMMM D, YYYY'),
('format_time', 'h:mm:ss P'),
('timezone', 32),
('dst', 2),
('preference_quick_reply', 1),
('preference_avatars', 1),
('preference_signatures', 1),
('preference_post_images', 1),
('preference_emoticons', 1),
('preference_return_topic', 0),
('preference_pm_display', 0),
('preference_recently_online', 2),
('preference_thousands_separator', ','),
('preference_decimal_point', '.'),
('preference_today_yesterday', 2),
('per_page_topics', 20),
('per_page_posts', 10),
('per_page_news', 10),
('per_page_downloads', 10),
('per_page_comments', 10),
('per_page_members', 20),
('menus_last_cached', 0);

##
# Sessions table ;)
##
DROP TABLE IF EXISTS `{$db_prefix}sessions`;
CREATE TABLE `{$db_prefix}sessions` (
  `session_id` VARCHAR(40) NOT NULL,
  `data` TEXT NOT NULL,
  `saved` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`),
  KEY (`saved`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Tasks, like crons :P
##
DROP TABLE IF EXISTS `{$db_prefix}tasks`;
CREATE TABLE `{$db_prefix}tasks` (
  `task_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `last_ran` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `run_every` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `file` VARCHAR(255) NOT NULL,
  `call_func` VARCHAR(255) NOT NULL,
  `queued` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`task_id`),
  KEY (`last_ran`),
  KEY (`queued`),
  KEY (`enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Predefined SnowCMS tasks :)
INSERT INTO `{$db_prefix}tasks` (`last_ran`,`run_every`,`file`,`call_func`,`queued`,`enabled`) VALUES
(0, 43200, 'tasks.php', 'tasks_tables', 0, 1),
(0, 300, 'mail.php', 'mail_queue_send', 0, 1),
(0, 21600, 'tasks.php', 'tasks_files', 0, 1);

##
# This table handles storing the topic information :P
##
DROP TABLE IF EXISTS `{$db_prefix}topics`;
CREATE TABLE `{$db_prefix}topics` (
  `topic_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_sticky` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `is_locked` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `board_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `poll_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `first_msg_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `last_msg_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `starter_member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `starter_member_name` VARCHAR(255) NOT NULL,
  `last_member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `last_member_name` VARCHAR(255) NOT NULL,
  `num_replies` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `num_views` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`topic_id`),
  KEY (`is_sticky`),
  KEY (`board_id`),
  KEY (`starter_member_id`),
  KEY (`last_member_id`),
  KEY (`num_replies`),
  KEY (`num_views`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Have you read this topic or not..?
##
DROP TABLE IF EXISTS `{$db_prefix}topic_logs`;
CREATE TABLE `{$db_prefix}topic_logs` (
  `topic_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`topic_id`,`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Holds polls in topics :)
##
DROP TABLE IF EXISTS `{$db_prefix}topic_polls`;
CREATE TABLE `{$db_prefix}topic_polls` (
  `poll_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `question` VARCHAR(255) NOT NULL,
  `closed` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `allowed_votes` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `expires` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `allow_change` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `result_access` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `poster_name` VARCHAR(255) NOT NULL,
  `voters` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`poll_id`),
  KEY (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Logs the vote you cast...
##
DROP TABLE IF EXISTS `{$db_prefix}topic_poll_logs`;
CREATE TABLE `{$db_prefix}topic_poll_logs` (
  `poll_id` INT(11) UNSIGNED NOT NULL,
  `member_id` MEDIUMINT(8) UNSIGNED NOT NULL,
  `option_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`poll_id`,`member_id`,`option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

##
# Holds polls options for the topics :D!
##
DROP TABLE IF EXISTS `{$db_prefix}topic_poll_options`;
CREATE TABLE `{$db_prefix}topic_poll_options` (
  `option_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `poll_id` INT(11) UNSIGNED NOT NULL,
  `value` VARCHAR(255) NOT NULL,
  `votes` SMALLINT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`option_id`),
  KEY (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;