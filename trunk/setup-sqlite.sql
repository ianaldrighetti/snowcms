CREATE TABLE '{$db_prefix}error_log'
(
  'error_id' INTEGER PRIMARY KEY,
  'error_time' INT(10) NOT NULL DEFAULT '0',
  'member_id' INT(11) NOT NULL DEFAULT '0',
  'member_name' VARCHAR(80) NULL,
  'member_ip' VARCHAR(150) NULL,
  'error_type' VARCHAR(40) NULL,
  'error_message' TEXT NULL,
  'error_file' VARCHAR(255) NULL,
  'error_line' INT(11) NOT NULL DEFAULT '0',
  'error_url' VARCHAR(255) NULL
);

CREATE INDEX '{$db_prefix}error_log_error_time' ON '{$db_prefix}error_log' ('error_time');
CREATE INDEX '{$db_prefix}error_log_member_name' ON '{$db_prefix}error_log' ('member_name');
CREATE INDEX '{$db_prefix}error_log_member_ip' ON '{$db_prefix}error_log' ('member_ip');
CREATE INDEX '{$db_prefix}error_log_error_type' ON '{$db_prefix}error_log' ('error_type');

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
  'message_status' VARCHAR(40) NULL DEFAULT 'unapproved',
  'extra' TEXT NOT NULL
);

CREATE UNIQUE INDEX '{$db_prefix}messages_area' ON '{$db_prefix}messages' ('area_name', 'area_id', 'message_id');
CREATE INDEX '{$db_prefix}messages_poster_time' ON '{$db_prefix}messages' ('poster_time');
CREATE INDEX '{$db_prefix}messages_modified_time' ON '{$db_prefix}messages' ('modified_time');
CREATE INDEX '{$db_prefix}messages_message_status' ON '{$db_prefix}messages' ('message_status');
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
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('default_event', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('enable_tasks', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('site_name', 'SnowCMS');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('site_email', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('theme', 'default');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('max_tasks', 2);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('registration_enabled', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('registration_type', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('enable_utf8', 6);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('members_min_name_length', 3);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('members_max_name_length', 80);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('errors_log', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('mail_handler', 'mail');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('smtp_host', 'localhost');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('smtp_port', 25);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('smtp_is_tls', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('smtp_timeout', 5);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('smtp_user', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('smtp_pass', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('mail_additional_parameters', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('default_member_groups', 'member');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES('disable_admin_security', 0);

CREATE TABLE '{$db_prefix}tasks'
(
  'task_name' VARCHAR(255) NOT NULL,
  'last_ran' INT NOT NULL DEFAULT '0',
  'run_every' INT NOT NULL DEFAULT '86400',
  'file' VARCHAR(255) NULL DEFAULT '',
  'func' VARCHAR(255) NULL DEFAULT '',
  'queued' SMALLINT NOT NULL DEFAULT '0',
  'enabled' SMALLINT NOT NULL DEFAULT '1',
  PRIMARY KEY ('task_name')
);

CREATE INDEX '{$db_prefix}tasks_last_ran' ON '{$db_prefix}tasks' ('last_ran');
CREATE INDEX '{$db_prefix}tasks_queued' ON '{$db_prefix}tasks' ('queued');
CREATE INDEX '{$db_prefix}tasks_enabled' ON '{$db_prefix}tasks' ('enabled');

CREATE TABLE '{$db_prefix}tokens'
(
  'session_id' VARCHAR(150) NOT NULL,
  'token_name' VARCHAR(100) NOT NULL,
  'token' VARCHAR(255) NOT NULL,
  'token_registered' INT NOT NULL DEFAULT '0',
  PRIMARY KEY ('session_id', 'token_name')
);

CREATE INDEX '{$db_prefix}tokens_token_registered' ON '{$db_prefix}tokens' ('token_registered');

CREATE TABLE '{$db_prefix}uploads'
(
  'area_name' VARCHAR(255) NOT NULL,
  'area_id' INT NOT NULL,
  'upload_id' INTEGER PRIMARY KEY,
  'member_id' INT NOT NULL DEFAULT '0',
  'member_name' VARCHAR(255) NULL,
  'member_email' VARCHAR(255) NULL,
  'member_ip' VARCHAR(150) NULL,
  'modified_id' INT NOT NULL DEFAULT '0',
  'modified_name' VARCHAR(255) NULL,
  'modified_email' VARCHAR(255) NULL,
  'modified_ip' VARCHAR(150) NULL,
  'filename' VARCHAR(255) NOT NULL,
  'file_ext' VARCHAR(100) NULL,
  'filelocation' VARCHAR(255) NOT NULL,
  'filesize' INT NOT NULL DEFAULT '0',
  'downloads' INT NOT NULL DEFAULT '0',
  'upload_type' VARCHAR(100) NULL,
  'mime_type' VARCHAR(255) NULL,
  'checksum' VARCHAR(40) NOT NULL
);

CREATE UNIQUE INDEX '{$db_prefix}uploads_area' ON '{$db_prefix}uploads' ('area_name', 'area_id', 'upload_id');
CREATE INDEX '{$db_prefix}uploads_member_id' ON '{$db_prefix}uploads' ('member_id');
CREATE INDEX '{$db_prefix}uploads_member_name' ON '{$db_prefix}uploads' ('member_name');
CREATE INDEX '{$db_prefix}uploads_member_ip' ON '{$db_prefix}uploads' ('member_ip');