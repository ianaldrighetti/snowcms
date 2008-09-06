DROP TABLE IF EXISTS `{$db_prefix}topic_logs`;

CREATE TABLE `{$db_prefix}topic_logs` (
  `tid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  UNIQUE KEY (`tid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}board_logs`;

CREATE TABLE `{$db_prefix}board_logs` (
  `bid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  UNIQUE KEY (`bid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}board_permissions`;

CREATE TABLE `{$db_prefix}board_permissions` (
  `bid` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `what` VARCHAR(50) NOT NULL,
  `can` int(1) NOT NULL default '1',
  PRIMARY KEY (`bid`,`group_id`,`what`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}boards`;

CREATE TABLE `{$db_prefix}boards` (
  `bid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `border` int(11) NOT NULL default '0',
  `who_view` varchar(255) NOT NULL default '',
  `name` text NOT NULL,
  `bdesc` text NOT NULL,
  `numtopics` int(11) NOT NULL default '0',
  `numposts` int(11) NOT NULL default '0',
  `last_msg` int(11) NOT NULL default '0',
  `last_uid` int(11) NOT NULL default '0',
  `last_name` text NOT NULL,
  PRIMARY KEY  (`bid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}boards` (`bid`,`cid`,`border`,`who_view`,`name`,`bdesc`,`numtopics`,`numposts`,`last_msg`,`last_uid`,`last_name`) VALUES('1','1','1','1,2','General Chat','Chat about anything!','0','0','0','0','0');

DROP TABLE IF EXISTS `{$db_prefix}categories`;

CREATE TABLE `{$db_prefix}categories` (
  `cid` int(11) NOT NULL auto_increment,
  `corder` int(11) NOT NULL default '0',
  `cname` tinytext NOT NULL,
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}categories` (`cid`,`corder`,`cname`) VALUES('1','1','General Category');

DROP TABLE IF EXISTS `{$db_prefix}membergroups`;

CREATE TABLE `{$db_prefix}membergroups` (
  `group_id` int(11) NOT NULL auto_increment,
  `groupname` text NOT NULL,
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}membergroups` VALUES ('-1','Guest'),('1','Administrator'),('2','Regular Member');

DROP TABLE IF EXISTS `{$db_prefix}members`;

CREATE TABLE `{$db_prefix}members` (
  `id` int(11) NOT NULL auto_increment,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `email` text NOT NULL,
  `display_name` text NOT NULL,
  `reg_date` int(10) NOT NULL default '0',
  `reg_ip` text NOT NULL,
  `last_login` int(10) NOT NULL default '0',
  `last_ip` text NOT NULL,
  `group` int(11) NOT NULL default '0',
  `numposts` int(11) NOT NULL default '0',
  `birthdate` int(10) NOT NULL,
  `avatar` text NOT NULL,
  `signature` text NOT NULL,
  `profile` text NOT NULL,
  `activated` int(1) NOT NULL default '0',
  `suspension` int(10) NOT NULL default '0',
  `banned` int(1) NOT NULL default '0',
  `language` text NOT NULL,
  `acode` text NOT NULL,
  `sc` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}menus`;

CREATE TABLE `{$db_prefix}menus` (
  `link_id` int(11) NOT NULL auto_increment,
  `order` int(11) NOT NULL default '0',
  `link_name` text NOT NULL,
  `href` text NOT NULL,
  `target` int(1) NOT NULL default '0',
  `menu` int(1) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}menus` VALUES ('1','1','Home','index.php','0','3');
INSERT INTO `{$db_prefix}menus` VALUES ('2','1','News','index.php?action=news','0','3');
INSERT INTO `{$db_prefix}menus` VALUES ('3','2','Forum','forum.php','0','3');
INSERT INTO `{$db_prefix}menus` VALUES ('4','3','SnowCMS','http://www.snowcms.com/','1','2');

DROP TABLE IF EXISTS `{$db_prefix}messages`;

CREATE TABLE `{$db_prefix}messages` (
  `mid` int(11) NOT NULL auto_increment,
  `tid` int(11) NOT NULL default '0',
  `bid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `uid_editor` int(11) NOT NULL default '0',
  `edit_reason` text NOT NULL,
  `edit_time` int(10) NOT NULL default '0',
  `editor_name` text NOT NULL,
  `subject` text NOT NULL,
  `post_time` int(10) NOT NULL default '0',
  `poster_name` text NOT NULL,
  `poster_email` text NOT NULL,
  `ip` text NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY  (`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}online`;

CREATE TABLE `{$db_prefix}online` (
  `user_id` int(11) NOT NULL default '0',
  `sc` VARCHAR(50) NOT NULL default '',
  `ip` text NOT NULL,
  `page` text NOT NULL,
  `last_active` int(10) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}pages`;

CREATE TABLE `{$db_prefix}pages` (
  `page_id` int(11) NOT NULL auto_increment,
  `page_owner` int(11) NOT NULL default '0',
  `owner_name` text NOT NULL,
  `create_date` int(10) NOT NULL default '0',
  `modify_date` int(10) NOT NULL default '0',
  `title` text NOT NULL,
  `content` text NOT NULL,
  `html` int(1) NOT NULL default '0',
  PRIMARY KEY  (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}pages` (`page_owner`, `owner_name`, `create_date`, `title`, `content`, `html`) VALUES ('-1','The SnowCMS Team','%current_time%','Welcome to SnowCMS','<p>Start modifing the default settings and creating pages now. Thank you for choosing SnowCMS.</p>\n\n<p>The SnowCMS Team</p>','1');

DROP TABLE IF EXISTS `{$db_prefix}permissions`;

CREATE TABLE `{$db_prefix}permissions` (
  `group_id` int(11) NOT NULL default '0',
  `what` varchar(50) NOT NULL default '',
  `can` int(1) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`what`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}permissions` VALUES ('-1','view_forum','1'),('-1','view_online','1'),('-1','view_profile','1'),('2','view_forum','1'),('2','view_online','1'),('2','view_profile','1'),('2','search','1'),('2','change_display_name','1'),('2','change_email','1'),('2','change_birthdate','1'),('2','change_avatar','1'),('2','change_signature','1'),('2','change_profile','1'),('2','change_password','1');

DROP TABLE IF EXISTS `{$db_prefix}settings`;

CREATE TABLE `{$db_prefix}settings` (
  `variable` VARCHAR(100) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY (`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}settings` VALUES ('site_name','SnowCMS'),('slogan','Its a CMS alright...'),('language','English'),('theme','default'),('account_activation','0'),('login_threshold','15'),('version','0.7'),('main_page','1'),('remember_time','120'),('timeformat','H:i:s'),('dateformat','F jS, Y'),('mail_with_fsockopen','0'),('smtp_host',''),('smtp_user',''),('smtp_pass',''),('from_email',''),('smtp_port','25'),('board_posts_per_page','20'),('num_posts','10'),('num_topics','20'),('num_news_items','6'),('num_search_results','20'),('num_members','20'),('num_pages','20'),('enable_tos','0'),('homepage','1'),('default_group','2'),('login_detection_time','15'),('page_type','1');

DROP TABLE IF EXISTS `{$db_prefix}topics`;

CREATE TABLE `{$db_prefix}topics` (
  `tid` int(11) NOT NULL auto_increment,
  `sticky` int(1) NOT NULL default '0',
  `locked` int(1) NOT NULL default '0',
  `bid` int(11) NOT NULL default '0',
  `first_msg` int(11) NOT NULL default '0',
  `last_msg` int(11) NOT NULL default '0',
  `starter_id` int(11) NOT NULL default '0',
  `topic_starter` text NOT NULL,
  `ender_id` int(11) NOT NULL default '0',
  `topic_ender` text NOT NULL,
  `num_replies` int(11) NOT NULL default '0',
  `numviews` int(11) NOT NULL default '0',
  PRIMARY KEY  (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}news`;

CREATE TABLE `{$db_prefix}news` (
  `news_id` INT(11) NOT NULL AUTO_INCREMENT,
  `poster_id` INT(11) NOT NULL default '0',
  `cat_id` INT(11) NOT NULL default '0',
  `poster_name` TEXT NOT NULL,
  `subject` TEXT NOT NULL,
  `body` TEXT NOT NULL,
  `post_time` INT(10) NOT NULL,
  `modify_time` INT(10) NOT NULL default '0',
  `numViews` INT(11) NOT NULL default '0',
  `allow_comments` INT(1) NOT NULL default '1',
  PRIMARY KEY(`news_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}news_categories`;

CREATE TABLE `{$db_prefix}news_categories` (
  `cat_id` INT(11) NOT NULL AUTO_INCREMENT,
  `cat_name` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO {$db_prefix}news_categories (`cat_name`) VALUES ('Main');

DROP TABLE IF EXISTS `{$db_prefix}news_comments`;

CREATE TABLE `{$db_prefix}news_comments` (
  `post_id` INT(11) NOT NULL AUTO_INCREMENT,
  `nid` INT(11) NOT NULL,
  `poster_id` INT(11) NOT NULL default '0',
  `poster_name` TEXT NOT NULL,
  `subject` TEXT NOT NULL,
  `body` TEXT NOT NULL,
  `post_time` INT(10) NOT NULL,
  `modify_time` INT(10) NOT NULL default '0',
  `isApproved` INT(1) NOT NULL default '1',
  `isSpam` INT(1) NOT NULL default '0',
  PRIMARY KEY (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}tos`;

CREATE TABLE `{$db_prefix}tos` (
  `tos_lang` VARCHAR(255) NOT NULL,
  `body` TEXT NOT NULL,
  PRIMARY KEY (`tos_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}pms`;

CREATE TABLE `{$db_prefix}pms` (
  `pm_id` int(11) NOT NULL auto_increment,
  `uid_to` int(11) NOT NULL default '0',
  `uid_from` int(11) NOT NULL default '0',
  `subject` text NOT NULL,
  `sent_time` int(10) NOT NULL default '0',
  `name_from` text NOT NULL,
  `email_from` text NOT NULL,
  `ip` text NOT NULL,
  `body` text NOT NULL,
  `deleted_to` INT(1) NOT NULL default '0',
  `deleted_from` INT(1) NOT NULL default '0',
  PRIMARY KEY  (`pm_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;