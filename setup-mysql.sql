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
  `member_activated` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `member_acode` VARCHAR(40) NOT NULL,
  PRIMARY KEY (`member_id`),
  KEY (`member_name`),
  KEY (`display_name`),
  KEY (`member_activated`)
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
  PRIMARY KEY (`dependency_name`),
  KEY (`dependencies`),
  KEY (`runtime_error`)
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