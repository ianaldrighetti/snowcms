DROP TABLE IF EXISTS `scms_board_logs`;

CREATE TABLE `scms_board_logs` (
  `bid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scms_board_permissions`;

CREATE TABLE `scms_board_permissions` (
  `group_id` int(11) NOT NULL default '0',
  `what` text NOT NULL,
  `can` int(1) NOT NULL default '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `scms_board_permissions` VALUES ('1','post_new','1');

DROP TABLE IF EXISTS `scms_boards`;

CREATE TABLE `scms_boards` (
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scms_categories`;

CREATE TABLE `scms_categories` (
  `cid` int(11) NOT NULL auto_increment,
  `corder` int(11) NOT NULL default '0',
  `cname` tinytext NOT NULL,
  `cdesc` text NOT NULL,
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scms_membergroups`;

CREATE TABLE `scms_membergroups` (
  `group_id` int(11) NOT NULL auto_increment,
  `groupname` text NOT NULL,
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO `scms_membergroups` VALUES ('1','Administrator'),('2','Regular Member');

DROP TABLE IF EXISTS `scms_members`;

CREATE TABLE `scms_members` (
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
  `signature` text NOT NULL,
  `activated` int(1) NOT NULL default '0',
  `acode` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scms_menus`;

CREATE TABLE `scms_menus` (
  `link_id` int(11) NOT NULL auto_increment,
  `order` int(11) NOT NULL default '0',
  `link_name` text NOT NULL,
  `href` text NOT NULL,
  `target` int(1) NOT NULL default '0',
  `menu` int(1) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scms_messages`;

CREATE TABLE `scms_messages` (
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scms_online`;

CREATE TABLE `scms_online` (
  `user_id` int(11) NOT NULL default '0',
  `ip` text NOT NULL,
  `page` text NOT NULL,
  `last_active` int(10) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scms_pages`;

CREATE TABLE `scms_pages` (
  `page_id` int(11) NOT NULL auto_increment,
  `page_owner` int(11) NOT NULL default '0',
  `owner_name` text NOT NULL,
  `create_date` int(10) NOT NULL default '0',
  `modify_date` int(10) NOT NULL default '0',
  `title` text NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY  (`page_id`)
) ENGINE=MyISAMDEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `scms_permissions`;

CREATE TABLE `scms_permissions` (
  `group_id` int(11) NOT NULL default '0',
  `what` varchar(50) NOT NULL,
  `can` int(1) NOT NULL default '0',
  PRIMARY KEY (`group_id`,`what`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `scms_permissions` VALUES ('1','admin','1'),('1','view_online','1'),('0','view_online','1'),('1','view_online_special','1'),('0','view_forum','1'),('1','view_forum','1'),('1','view_forum','1');

DROP TABLE IF EXISTS `scms_settings`;

CREATE TABLE `scms_settings` (
  `variable` text NOT NULL,
  `value` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `scms_settings` VALUES ('site_name','SnowCMS'),('slogan','Its a CMS alright...'),('language','english'),('theme','default'),('login_threshold','15'),('version','0.7'),('main_page','1'),('main_page_id','1'),('remember_time','120'),('timeformat','jS'),('mail_with_fsockopen','0'),('smtp_host','mail.northsalemcrew.net'),('smtp_user','admin@northsalemcrew.net'),('smtp_pass','4487699'),('smtp_from','admin@northsalemcrew.net'),('smtp_port','25'),('account_activation','1'),('webmaster_email','me@goaway.com'),('board_posts_per_page','20'),('topic_posts_per_page','10');

DROP TABLE IF EXISTS `scms_topics`;

CREATE TABLE `scms_topics` (
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;