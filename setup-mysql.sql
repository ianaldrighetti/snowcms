----
-- Holds registered forms...
----
CREATE TABLE `{$db_prefix}forms`
(
  `session_id` VARCHAR(150) NOT NULL,
  `form_name` VARCHAR(100) NOT NULL,
  `form_token` VARCHAR(255) NOT NULL,
  `form_registered` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`,`form_name`),
  KEY (`form_registered`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

----
-- Holds members extra information, this is what a plugin should use to store extra member stuffs!
----
CREATE TABLE `{$db_prefix}member_data`
(
  `member_id` INT(11) UNSIGNED NOT NULL,
  `variable` VARCHAR(255) NOT NULL,
  `value` TEXT NOT NULL,
  PRIMARY KEY (`member_id`, `variable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

----
--  The members table, holding, you guessed it! MEMBERS!
---
CREATE TABLE `{$db_prefix}members`
(
  `member_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_name` VARCHAR(80) NOT NULL,
  `member_pass` VARCHAR(40) NOT NULL,
  `member_hash` VARCHAR(16) NOT NULL,
  `display_name` VARCHAR(255) NOT NULL,
  `member_email` VARCHAR(100) NOT NULL,
  `member_groups` VARCHAR(255) NOT NULL,
  `member_registered` INT(10) UNSIGNED NOT NULL,
  `member_ip` VARCHAR(150) NOT NULL,
  `member_activated` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `member_acode` VARCHAR(40) NOT NULL,
  PRIMARY KEY (`member_id`),
  KEY (`member_name`),
  KEY (`display_name`),
  KEY (`member_activated`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

----
-- Used via the Messages API
----
CREATE TABLE `{$db_prefix}messages`
(
  `area_name` VARCHAR(255) NOT NULL,
  `area_id` INT(11) UNSIGNED NOT NULL,
  `message_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_name` VARCHAR(255) NOT NULL,
  `member_email` VARCHAR(255) NOT NULL,
  `member_ip` VARCHAR(150) NOT NULL,
  `modified_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `modified_name` VARCHAR(255) NOT NULL DEFAULT '',
  `modified_email` VARCHAR(255) NOT NULL DEFAULT '',
  `modified_ip` VARCHAR(150) NOT NULL DEFAULT '',
  `subject` VARCHAR(255) NOT NULL DEFAULT '',
  `poster_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `modified_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `message` TEXT NOT NULL,
  `message_type` VARCHAR(16) NOT NULL DEFAULT '',
  `message_status` VARCHAR(40) NOT NULL DEFAULT 'unapproved',
  `extra` TEXT NOT NULL,
  PRIMARY KEY (`area_name`, `area_id`, `message_id`),
  KEY (`poster_time`),
  KEY (`modified_time`),
  KEY (`message_status`),
  KEY (`extra`(255))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

----
-- This is where currently enabled plugins are held
----
CREATE TABLE `{$db_prefix}plugins`
(
  `dependency_name` VARCHAR(255) NOT NULL,
  `dependency_names` TEXT NOT NULL,
  `dependencies` TINYINT(3) UNSIGNED NOT NULL,
  `directory` VARCHAR(255) NOT NULL,
  `runtime_error` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `is_activated` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`dependency_name`),
  KEY (`dependencies`),
  KEY (`runtime_error`),
  KEY (`is_activated`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

----
-- A table holding various settings and what not xD
----
CREATE TABLE `{$db_prefix}settings`
(
  `variable` VARCHAR(255) NOT NULL,
  `value` TEXT NOT NULL,
  PRIMARY KEY (`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `{$db_prefix}settings` (`variable`, `value`) VALUES('show_version', 1),('version', '2.0 SVN'),('password_security', 1),('reserved_names', ''),('disallowed_emails', '');
INSERT INTO `{$db_prefix}settings` (`variable`, `value`) VALUES('disallowed_email_domains', ''),('enable_tasks', 1),('site_name', 'SnowCMS'),('theme', 'default'),('max_tasks', 2);

----
-- Need to do something?
----
CREATE TABLE `{$db_prefix}tasks`
(
  `task_name` VARCHAR(255) NOT NULL,
  `last_ran` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `run_every` INT(10) UNSIGNED NOT NULL DEFAULT '86400',
  `file` VARCHAR(255) NOT NULL DEFAULT '',
  `func` VARCHAR(255) NOT NULL DEFAULT '',
  `queued` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`task_name`),
  KEY (`last_ran`),
  KEY (`queued`),
  KEY (`enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;