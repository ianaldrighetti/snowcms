DROP TABLE IF EXISTS `{$db_prefix}membergroups`;

CREATE TABLE `{$db_prefix}membergroups` (
  `group_id` int(11) NOT NULL auto_increment,
  `groupname` text NOT NULL,
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO `{$db_prefix}membergroups` VALUES ('0','Guest'),('1','Administrator'),('2','Regular Member');

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
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `{$db_prefix}menus`;

CREATE TABLE `{$db_prefix}menus` (
  `link_id` int(11) NOT NULL auto_increment,
  `order` int(11) NOT NULL default '0',
  `link_name` text NOT NULL,
  `href` text NOT NULL,
  `target` int(1) NOT NULL default '0',
  `menu` int(1) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO `{$db_prefix}menus` VALUES ('1','1','SnowCMS','http://snowcms.northsalemcrew.net/','0','1');

DROP TABLE IF EXISTS `{$db_prefix}online`;

CREATE TABLE `{$db_prefix}online` (
  `user_id` int(11) NOT NULL default '0',
  `ip` text NOT NULL,
  `page` text NOT NULL,
  `last_active` int(10) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `{$db_prefix}pages`;

CREATE TABLE IF NOT EXISTS `{$db_prefix}pages` (
  `page_id` int(11) NOT NULL auto_increment,
  `page_owner` int(11) NOT NULL default '0',
  `owner_name` text NOT NULL,
  `create_date` int(10) NOT NULL default '0',
  `modify_date` int(10) NOT NULL default '0',
  `show_info` tinyint(1) NOT NULL,
  `title` text NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY  (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `{$db_prefix}permissions`;

CREATE TABLE `{$db_prefix}permissions` (
  `group_id` int(11) NOT NULL default '0',
  `what` text NOT NULL,
  `can` int(1) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `{$db_prefix}permissions` VALUES ('1','admin','1'),('1','view_online','1'),('0','view_online','1'),('1','view_online_special','1');

DROP TABLE IF EXISTS `{$db_prefix}settings`;

CREATE TABLE `{$db_prefix}settings` (
  `variable` text NOT NULL,
  `value` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `{$db_prefix}settings` VALUES ('site_name','SnowCMS'),('slogan','Its a CMS all right...'),('language','english'),('theme','default'),('login_threshold','15'),('version','0.7'),('main_page','1'),('main_page_id','1'),('remember_time','120'),('timeformat','F j, Y, g:i:sA');

