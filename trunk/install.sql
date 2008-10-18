--                      SnowCMS
--     Founded by soren121 & co-founded by aldo
-- Developed by Myles, aldo, antimatter15 & soren121
--              http://www.snowcms.com/
--
--   SnowCMS is released under the GPL v3 License
--       which means you are free to edit and
--          redistribute it as your wish!
--
--                  install.sql file

DROP TABLE IF EXISTS `{$db_prefix}topic_logs`;

CREATE TABLE `{$db_prefix}topic_logs` (
  `tid` INT(11) NOT NULL DEFAULT '0',
  `uid` INT(11) NOT NULL DEFAULT '0',
  UNIQUE KEY (`tid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}board_logs`;

CREATE TABLE `{$db_prefix}board_logs` (
  `bid` INT(11) NOT NULL DEFAULT '0',
  `uid` INT(11) NOT NULL DEFAULT '0',
  UNIQUE KEY (`bid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}board_permissions`;

CREATE TABLE `{$db_prefix}board_permissions` (
  `bid` INT(11) NOT NULL DEFAULT '0',
  `group_id` INT(11) NOT NULL DEFAULT '0',
  `what` VARCHAR(50) NOT NULL,
  `can` INT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`bid`,`group_id`,`what`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}boards`;

CREATE TABLE `{$db_prefix}boards` (
  `bid` INT(11) NOT NULL AUTO_INCREMENT,
  `cid` INT(11) NOT NULL DEFAULT '0',
  `border` INT(11) NOT NULL DEFAULT '0',
  `who_view` VARCHAR(255) NOT NULL DEFAULT '',
  `name` TEXT NOT NULL,
  `bdesc` TEXT NOT NULL,
  `numtopics` INT(11) NOT NULL DEFAULT '0',
  `numposts` INT(11) NOT NULL DEFAULT '0',
  `last_msg` INT(11) NOT NULL DEFAULT '0',
  `last_uid` INT(11) NOT NULL DEFAULT '0',
  `last_name` TEXT NOT NULL,
  PRIMARY KEY  (`bid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}boards` (`bid`,`cid`,`border`,`who_view`,`name`,`bdesc`,`numtopics`,`numposts`,`last_msg`,`last_uid`,`last_name`) VALUES
('1','1','1','-1,2','General Chat','Chat about anything!','0','0','0','0','0');

DROP TABLE IF EXISTS `{$db_prefix}categories`;

CREATE TABLE `{$db_prefix}categories` (
  `cid` INT(11) NOT NULL AUTO_INCREMENT,
  `corder` INT(11) NOT NULL DEFAULT '0',
  `cname` TEXT NOT NULL,
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}categories` (`cid`,`corder`,`cname`) VALUES
('1','1','General Category');

DROP TABLE IF EXISTS `{$db_prefix}membergroups`;

CREATE TABLE `{$db_prefix}membergroups` (
  `group_id` INT(11) NOT NULL AUTO_INCREMENT,
  `groupname` TEXT NOT NULL,
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}membergroups` VALUES
('-1','Guest'),
('1','Administrator'),
('2','Regular Member');

DROP TABLE IF EXISTS `{$db_prefix}members`;

CREATE TABLE `{$db_prefix}members` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` TEXT NOT NULL,
  `password` TEXT NOT NULL,
  `email` TEXT NOT NULL,
  `display_name` TEXT NOT NULL,
  `reg_date` INT(10) NOT NULL DEFAULT '0',
  `reg_ip` TEXT NOT NULL,
  `last_login` INT(10) NOT NULL DEFAULT '0',
  `last_ip` TEXT NOT NULL,
  `group` INT(11) NOT NULL DEFAULT '0',
  `numposts` INT(11) NOT NULL DEFAULT '0',
  `num_topics` INT(11) NOT NULL DEFAULT '0',
  `birthdate` INT(10) NOT NULL,
  `site_name` TEXT NOT NULL,
  `site_url` TEXT NOT NULL,
  `icq` TEXT NOT NULL,
  `aim` TEXT NOT NULL,
  `msn` TEXT NOT NULL,
  `yim` TEXT NOT NULL,
  `gtalk` TEXT NOT NULL,
  `avatar` TEXT NOT NULL,
  `signature` TEXT NOT NULL,
  `profile` TEXT NOT NULL,
  `activated` INT(1) NOT NULL DEFAULT '0',
  `suspension` INT(10) NOT NULL DEFAULT '0',
  `banned` INT(1) NOT NULL DEFAULT '0',
  `language` TEXT NOT NULL,
  `unread_pms` INT(11) NOT NULL DEFAULT '0',
  `pms_lastread` INT(10) NOT NULL DEFAULT '0',
  `acode` TEXT NOT NULL,
  `sc` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}menus`;

CREATE TABLE `{$db_prefix}menus` (
  `link_id` INT(11) NOT NULL AUTO_INCREMENT,
  `order` INT(11) NOT NULL DEFAULT '0',
  `link_name` TEXT NOT NULL,
  `href` TEXT NOT NULL,
  `target` INT(1) NOT NULL DEFAULT '0',
  `menu` INT(1) NOT NULL DEFAULT '0',
  `permission` INT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}menus` VALUES
('1','1','Home','index.php','0','3','1'),
('2','2','News','index.php?action=news','0','3','1'),
('3','3','Forum','forum.php','0','3','1'),
('4','4','Profile','index.php?action=profile','0','3','3'),
('5','5','Messages','forum.php?action=pm','0','3','5'),
('6','6','Messages [%unread_pms%]','forum.php?action=pm','0','3','6'),
('7','7','Member List','forum.php?action=members','0','3','3'),
('8','8','Forum Search','forum.php?action=search','0','3','3'),
('9','9','SnowCMS','http://www.snowcms.com/','1','3','1'),
('10','10','Logout','index.php?action=logout%semicolon%sc=%sc%','0','3','3'),
('11','11','Login','index.php?action=login','0','3','2'),
('12','12','Register','index.php?action=register','0','3','2'),
('13','13','Control Panel','index.php?action=admin','0','3','4');

DROP TABLE IF EXISTS `{$db_prefix}messages`;

CREATE TABLE `{$db_prefix}messages` (
  `mid` INT(11) NOT NULL AUTO_INCREMENT,
  `tid` INT(11) NOT NULL DEFAULT '0',
  `bid` INT(11) NOT NULL DEFAULT '0',
  `uid` INT(11) NOT NULL DEFAULT '0',
  `uid_editor` INT(11) NOT NULL DEFAULT '0',
  `edit_reason` TEXT NOT NULL,
  `edit_time` INT(10) NOT NULL DEFAULT '0',
  `editor_name` TEXT NOT NULL,
  `subject` TEXT NOT NULL,
  `post_time` INT(10) NOT NULL DEFAULT '0',
  `poster_name` TEXT NOT NULL,
  `poster_email` TEXT NOT NULL,
  `ip` TEXT NOT NULL,
  `body` TEXT NOT NULL,
  PRIMARY KEY  (`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}online`;

CREATE TABLE `{$db_prefix}online` (
  `user_id` INT(11) NOT NULL DEFAULT '0',
  `sc` VARCHAR(50) NOT NULL DEFAULT '',
  `ip` TINYTEXT NOT NULL,
  `url_data` TEXT NOT NULL,
  `inForum` INT(1) NOT NULL default '0',
  `last_active` INT(10) NOT NULL DEFAULT '0',
  UNIQUE KEY (`sc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}pages`;

CREATE TABLE `{$db_prefix}pages` (
  `page_id` INT(11) NOT NULL AUTO_INCREMENT,
  `page_owner` INT(11) NOT NULL DEFAULT '0',
  `owner_name` TEXT NOT NULL,
  `create_date` INT(10) NOT NULL DEFAULT '0',
  `modify_date` INT(10) NOT NULL DEFAULT '0',
  `title` TEXT NOT NULL,
  `content` TEXT NOT NULL,
  `html` INT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}pages` (`page_owner`, `owner_name`, `create_date`, `title`, `content`, `html`) VALUES
('-1','The SnowCMS Team','%current_time%','Welcome to SnowCMS','<p>Congratulations! Your SnowCMS installation has been successfully completed. Please delete your <code>install.php</code> file now. You can start modifying the default settings and adding content to your site right now. If you get stuck, you can always get support at the <a href="http://www.snowcms.com/">official SnowCMS website</a>.</p>

<p><b>Common tasks:</b></p>

<ul>
<li>You can change your site logo for most themes by replacing the file <code>/Themes/<i>theme</i>/images/site_logo.png</code>.</li>
<li>You can change your theme at <a href="index.php?action=admin%semicolon%sa=basic-settings">basic settings</a> or download more at <a href="http://www.snowcms.com/">SnowCMS.com</a>.</li>
<li>You can modify what things say by changing the file <code>/Languages/<i>English</i>.language.php</code></li>
<li>SnowCMS is open source, you can modify any file you want. (May not apply to all themes.)</li>
<li>Remember to set your <a href="index.php?action=admin%semicolon%sa=mail-settings">mail settings</a> before enabling any email features.</li>
</ul>

<p><br /></p>

<p>Thank you for choosing SnowCMS,</p>

<p>The SnowCMS Team</p>','1');

DROP TABLE IF EXISTS `{$db_prefix}permissions`;

CREATE TABLE `{$db_prefix}permissions` (
  `group_id` INT(11) NOT NULL DEFAULT '0',
  `what` VARCHAR(50) NOT NULL DEFAULT '',
  `can` INT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`group_id`,`what`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}permissions` VALUES
('-1','view_forum','1'),
('-1','view_online','1'),
('-1','view_profile','1'),
('2','view_forum','1'),
('2','view_online','1'),
('2','view_profile','1'),
('2','search','1'),
('2','pm_view','1'),
('2','pm_compile','1'),
('2','pm_delete','1'),
('2','change_display_name','1'),
('2','change_email','1'),
('2','change_birthdate','1'),
('2','change_avatar','1'),
('2','change_icq','1'),
('2','change_aim','1'),
('2','change_msn','1'),
('2','change_yim','1'),
('2','change_gtalk','1'),
('2','change_site','1'),
('2','change_signature','1'),
('2','change_profile','1'),
('2','change_password','1');

DROP TABLE IF EXISTS `{$db_prefix}settings`;

CREATE TABLE `{$db_prefix}settings` (
  `variable` VARCHAR(100) NOT NULL,
  `value` TEXT NOT NULL,
  UNIQUE KEY (`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}settings` VALUES
('site_name','SnowCMS'),
('slogan','It&#39%semicolon%s a CMS alright!'),
('language','English'),
('theme','default'),
('account_activation','0'),
('login_threshold','15'),
('version','0.7'),
('main_page','1'),
('remember_time','120'),
('timeformat','H:i:s'),
('dateformat','F jS, Y'),
('dateshort','j/n/y'),
('mail_with_fsockopen','0'),
('smtp_host',''),
('smtp_user',''),
('smtp_pass',''),
('from_email',''),
('smtp_port','25'),
('board_posts_per_page','20'),
('hot_posts','20'),
('num_posts','10'),
('num_topics','20'),
('num_news_items','6'),
('num_search_results','20'),
('num_members','20'),
('num_pages','20'),
('num_pms','20'),
('enable_tos','0'),
('homepage','1'),
('default_group','2'),
('login_detection_time','15'),
('page_type','1'),
('captcha','5'),
('age_youngest','13'),
('search_length','255'),
('avatar_width','100'),
('avatar_height','100'),
('avatar_size','20'),
('username_short','3'),
('username_long','30'),
('display_name_short','3'),
('display_name_long','30'),
('password_short','4'),
('password_long','255'),
('email_short','6'),
('email_long','255'),
('avatar_short','4'),
('avatar_long','255'),
('icq_short','1'),
('icq_long','255'),
('aim_short','1'),
('aim_long','255'),
('msn_short','1'),
('msn_long','255'),
('yim_short','1'),
('yim_long','255'),
('gtalk_short','1'),
('gtalk_long','255'),
('site_short','1'),
('site_long','255'),
('site_url_short','4'),
('site_url_long','255'),
('signature_short','1'),
('signature_long','500'),
('profile_short','1'),
('profile_long','2000'),
('post_subject_short','3'),
('post_subject_long','50'),
('post_short','3'),
('post_long','2000'),
('pm_subject_short','3'),
('pm_subject_long','50'),
('pm_short','3'),
('pm_long','2000'),
('page_title_short','3'),
('page_title_long','50'),
('page_short','1'),
('page_long','5000'),
('menu_short','3'),
('menu_long','20'),
('menu_url_short','4'),
('menu_url_long','255'),
('tos_short','3'),
('tos_long','5000'),
('ip_short','1'),
('ip_long','200'),
('news_cat_short','3'),
('news_cat_long','30'),
('news_subject_short','3'),
('news_subject_long','50'),
('news_short','3'),
('news_long','2000'),
('news_comment_short','3'),
('news_comment_long','1000'),
('group_short','3'),
('group_long','30'),
('board_cat_short','3'),
('board_cat_long','30'),
('board_short','3'),
('board_long','50'),
('board_desc_short','3'),
('board_desc_long','200');

DROP TABLE IF EXISTS `{$db_prefix}topics`;

CREATE TABLE `{$db_prefix}topics` (
  `tid` INT(11) NOT NULL AUTO_INCREMENT,
  `sticky` INT(1) NOT NULL DEFAULT '0',
  `locked` INT(1) NOT NULL DEFAULT '0',
  `bid` INT(11) NOT NULL DEFAULT '0',
  `first_msg` INT(11) NOT NULL DEFAULT '0',
  `last_msg` INT(11) NOT NULL DEFAULT '0',
  `starter_id` INT(11) NOT NULL DEFAULT '0',
  `topic_starter` TEXT NOT NULL,
  `ender_id` INT(11) NOT NULL DEFAULT '0',
  `topic_ender` TEXT NOT NULL,
  `num_replies` INT(11) NOT NULL DEFAULT '0',
  `numviews` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}news`;

CREATE TABLE `{$db_prefix}news` (
  `news_id` INT(11) NOT NULL AUTO_INCREMENT,
  `poster_id` INT(11) NOT NULL DEFAULT '0',
  `cat_id` INT(11) NOT NULL DEFAULT '0',
  `poster_name` TEXT NOT NULL,
  `subject` TEXT NOT NULL,
  `body` TEXT NOT NULL,
  `post_time` INT(10) NOT NULL,
  `modify_time` INT(10) NOT NULL DEFAULT'0',
  `num_comments` INT(11) NOT NULL DEFAULT '0',
  `num_views` INT(11) NOT NULL DEFAULT '0',
  `allow_comments` INT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY(`news_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}news_categories`;

CREATE TABLE `{$db_prefix}news_categories` (
  `cat_id` INT(11) NOT NULL AUTO_INCREMENT,
  `cat_name` TEXT NOT NULL,
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
  `modify_time` INT(10) NOT NULL DEFAULT '0',
  `isApproved` INT(1) NOT NULL DEFAULT '1',
  `isSpam` INT(1) NOT NULL DEFAULT '0',
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
  `pm_id` INT(11) NOT NULL AUTO_INCREMENT,
  `uid_to` INT(11) NOT NULL DEFAULT '0',
  `uid_from` INT(11) NOT NULL DEFAULT '0',
  `subject` TEXT NOT NULL,
  `sent_time` INT(10) NOT NULL DEFAULT '0',
  `name_from` TEXT NOT NULL,
  `email_from` TEXT NOT NULL,
  `ip` TEXT NOT NULL,
  `body` TEXT NOT NULL,
  `deleted_to` INT(1) NOT NULL DEFAULT '0',
  `deleted_from` INT(1) NOT NULL DEFAULT '0',
  `reported` INT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`pm_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$db_prefix}banned_ips`;

CREATE TABLE `{$db_prefix}banned_ips` (
  `ip` VARCHAR(15) NOT NULL,
  `reason` TEXT NOT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;