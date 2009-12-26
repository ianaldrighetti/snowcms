CREATE TABLE '{$db_prefix}member_data'
(
  'member_id' INT NOT NULL,
  'variable' VARCHAR(255) NOT NULL,
  'value' TEXT NULL,
  PRIMARY KEY ('member_id', 'variable')
);

CREATE TABLE '{$db_prefix}members'
(
  'member_id' INTEGER PRIMARY KEY,
  'member_name' VARCHAR(80) NOT NULL,
  'member_pass' VARCHAR(40) NOT NULL,
  'member_hash' VARCHAR(10) NULL,
  'member_groups' VARCHAR(255) NOT NULL,
  'display_name' VARCHAR(255) NOT NULL,
  'member_email' VARCHAR(100) NOT NULL,
  'member_registered' INT NOT NULL,
  'member_ip' VARCHAR(150) NOT NULL,
  'member_activated' SMALLINT NOT NULL DEFAULT '0',
  'member_acode' VARCHAR(40) NULL
);

CREATE INDEX '{$db_prefix}members_member_name' ON '{$db_prefix}members' ('member_name');
CREATE INDEX '{$db_prefix}members_display_name' ON '{$db_prefix}members' ('display_name');
CREATE INDEX '{$db_prefix}members_member_activated' ON '{$db_prefix}members' ('member_activated');

CREATE TABLE '{$db_prefix}messages'
(
  'area_name' VARCHAR(255) NOT NULL,
  'area_id' INT NOT NULL,
  'message_id' INTEGER PRIMARY KEY,
  'member_id' INT NOT NULL DEFAULT '0',
  'member_name' VARCHAR(255) NOT NULL,
  'member_email' VARCHAR(255) NOT NULL,
  'member_ip' VARCHAR(150) NOT NULL,
  'modified_id' INT NULL DEFAULT '0',
  'modified_name' VARCHAR(255) NULL DEFAULT '',
  'modified_email' VARCHAR(255) NULL DEFAULT '',
  'modified_ip' VARCHAR(150) NULL DEFAULT '',
  'subject' VARCHAR(255) NULL,
  'poster_time' INT NOT NULL DEFAULT '0',
  'modified_time' INT NULL DEFAULT '0',
  'message' TEXT NOT NULL,
  'message_type' VARCHAR(16) NULL,
  'is_approved' SMALLINT NOT NULL DEFAULT '0',
  'extra' TEXT NOT NULL
);

CREATE UNIQUE INDEX '{$db_prefix}messages_area' ON '{$db_prefix}messages' ('area_name', 'area_id', 'message_id');
CREATE INDEX '{$db_prefix}messages_poster_time' ON '{$db_prefix}messages' ('poster_time');
CREATE INDEX '{$db_prefix}messages_modified_time' ON '{$db_prefix}messages' ('modified_time');
CREATE INDEX '{$db_prefix}messages_is_approved' ON '{$db_prefix}messages' ('is_approved');
CREATE INDEX '{$db_prefix}messages_extra' ON '{$db_prefix}messages' ('extra');

CREATE TABLE '{$db_prefix}plugins'
(
  'dependency_name' VARCHAR(255) NOT NULL,
  'dependency_names' TEXT NULL,
  'dependencies' SMALLINT NOT NULL DEFAULT '0',
  'directory' VARCHAR(255) NOT NULL,
  'runtime_error' SMALLINT NOT NULL DEFAULT '0',
  'is_activated' SMALLINT NOT NULL DEFAULT '0',
  PRIMARY KEY ('dependency_name')
);

CREATE INDEX '{$db_prefix}plugins_dependencies' ON '{$db_prefix}plugins' ('dependencies');
CREATE INDEX '{$db_prefix}plugins_runtime_error' ON '{$db_prefix}plugins' ('runtime_error');
CREATE INDEX '{$db_prefix}plugins_is_activated' ON '{$db_prefix}plugins' ('is_activated');

CREATE TABLE '{$db_prefix}settings'
(
  'variable' VARCHAR(255) NOT NULL,
  'value' TEXT NULL,
  PRIMARY KEY ('variable')
);

INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('show_version', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('version', '2.0 SVN');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('password_security', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('reserved_names', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('disallowed_emails', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('disallowed_email_domains', '');