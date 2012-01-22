CREATE TABLE '{db->prefix}auth_tokens'
(
	'member_id' INT(11) UNSIGNED NOT NULL DEFAULT '0',
	'token_id' VARCHAR(255) NOT NULL,
	'token_assigned' INT(10) UNSIGNED NOT NULL DEFAULT '0',
	'token_expires' INT(10) UNSIGNED NOT NULL DEFAULT '0',
	'token_data' TEXT NOT NULL,
	PRIMARY KEY ('member_id', 'token_id')
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE UNIQUE INDEX '{db->prefix}auth_tokens_token_id' ON '{db->prefix}auth_tokens' ('token_id');

CREATE TABLE '{db->prefix}error_log'
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

CREATE INDEX '{db->prefix}error_log_error_time' ON '{db->prefix}error_log' ('error_time');
CREATE INDEX '{db->prefix}error_log_member_name' ON '{db->prefix}error_log' ('member_name');
CREATE INDEX '{db->prefix}error_log_member_ip' ON '{db->prefix}error_log' ('member_ip');
CREATE INDEX '{db->prefix}error_log_error_type' ON '{db->prefix}error_log' ('error_type');

CREATE TABLE '{db->prefix}member_data'
(
	'member_id' INT NOT NULL,
	'variable' VARCHAR(255) NOT NULL,
	'value' TEXT NULL,
	PRIMARY KEY ('member_id', 'variable')
);

CREATE TABLE '{db->prefix}members'
(
	'member_id' INTEGER PRIMARY KEY,
	'member_name' VARCHAR(80) NOT NULL,
	'member_pass' VARCHAR(40) NOT NULL,
	'member_groups' VARCHAR(255) NOT NULL,
	'display_name' VARCHAR(255) NOT NULL,
	'member_email' VARCHAR(100) NOT NULL,
	'member_last_active' INT NULL,
	'member_registered' INT NULL,
	'member_ip' VARCHAR(150) NOT NULL,
	'member_activated' SMALLINT NOT NULL DEFAULT '0',
	'member_acode' VARCHAR(40) NULL
);

CREATE INDEX '{db->prefix}members_member_name' ON '{db->prefix}members' ('member_name');
CREATE INDEX '{db->prefix}members_display_name' ON '{db->prefix}members' ('display_name');
CREATE INDEX '{db->prefix}members_member_activated' ON '{db->prefix}members' ('member_activated');

CREATE TABLE '{db->prefix}messages'
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

CREATE UNIQUE INDEX '{db->prefix}messages_area' ON '{db->prefix}messages' ('area_name', 'area_id', 'message_id');
CREATE INDEX '{db->prefix}messages_poster_time' ON '{db->prefix}messages' ('poster_time');
CREATE INDEX '{db->prefix}messages_modified_time' ON '{db->prefix}messages' ('modified_time');
CREATE INDEX '{db->prefix}messages_message_status' ON '{db->prefix}messages' ('message_status');
CREATE INDEX '{db->prefix}messages_extra' ON '{db->prefix}messages' ('extra');

CREATE TABLE '{db->prefix}permissions'
(
	'group_id' VARCHAR(255) NOT NULL,
	'permission' VARCHAR(255) NOT NULL,
	'status' SMALLINT NOT NULL DEFAULT '1',
	PRIMARY KEY ('group_id', 'permission')
);

INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('member', 'manage_system_settings', 0);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('member', 'manage_themes', 0);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('member', 'update_system', 0);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('member', 'view_error_log', 0);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('member', 'add_new_member', 0);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('member', 'manage_members', 0);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('member', 'search_members', 0);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('member', 'manage_member_settings', 0);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('member', 'manage_permissions', 0);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('member', 'add_plugins', 0);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('member', 'manage_plugins', 0);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('member', 'manage_plugin_settings', 0);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('member', 'view_other_profiles', 0);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('member', 'edit_other_profiles', 0);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('guest', 'manage_system_settings', -1);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('guest', 'manage_themes', -1);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('guest', 'update_system', -1);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('guest', 'view_error_log', -1);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('guest', 'add_new_member', -1);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('guest', 'manage_members', -1);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('guest', 'search_members', -1);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('guest', 'manage_member_settings', -1);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('guest', 'manage_permissions', -1);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('guest', 'add_plugins', -1);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('guest', 'manage_plugins', -1);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('guest', 'manage_plugin_settings', -1);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('guest', 'view_other_profiles', 0);
INSERT INTO '{db->prefix}permissions' ('group_id', 'permission', 'status') VALUES('guest', 'edit_other_profiles', -1);

CREATE TABLE '{db->prefix}plugins'
(
	'directory' VARCHAR(255) NOT NULL,
	'runtime_error' SMALLINT NOT NULL DEFAULT '0',
	'error_message' TEXT NULL,
	'is_activated' SMALLINT NOT NULL DEFAULT '0',
	PRIMARY KEY ('directory')
);

CREATE INDEX '{db->prefix}plugins_runtime_error' ON '{db->prefix}plugins' ('runtime_error');
CREATE INDEX '{db->prefix}plugins_is_activated' ON '{db->prefix}plugins' ('is_activated');

CREATE TABLE '{db->prefix}settings'
(
	'variable' VARCHAR(255) NOT NULL,
	'value' TEXT NULL,
	PRIMARY KEY ('variable')
);

INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('show_version', 1);
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('version', '2.0-beta');
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('password_security', 1);
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('disallowed_names', '');
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('disallowed_emails', '');
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('default_event', '');
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('enable_tasks', 1);
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('site_name', 'SnowCMS');
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('site_email', '');
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('theme', 'default');
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('max_tasks', 2);
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('registration_type', 1);
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('enable_utf8', 6);
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('members_min_name_length', 3);
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('members_max_name_length', 80);
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('errors_log', 1);
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('mail_handler', 'mail');
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('smtp_host', 'localhost');
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('smtp_port', 25);
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('smtp_is_tls', 0);
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('smtp_timeout', 5);
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('smtp_user', '');
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('smtp_pass', '');
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('mail_additional_parameters', '');
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('default_member_groups', 'member');
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('disable_admin_security', 0);
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('admin_login_timeout', 15);
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('admin_news_fetch_every', 43200);
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('date_format', '%B %d, %Y');
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('time_format', '%I:%M:%S %p');
INSERT INTO '{db->prefix}settings' ('variable', 'value') VALUES('datetime_format', '%B %d, %Y, %I:%M:%S %p');

CREATE TABLE '{db->prefix}tasks'
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

CREATE INDEX '{db->prefix}tasks_last_ran' ON '{db->prefix}tasks' ('last_ran');
CREATE INDEX '{db->prefix}tasks_queued' ON '{db->prefix}tasks' ('queued');
CREATE INDEX '{db->prefix}tasks_enabled' ON '{db->prefix}tasks' ('enabled');

CREATE TABLE '{db->prefix}tokens'
(
	'session_id' VARCHAR(150) NOT NULL,
	'token_name' VARCHAR(100) NOT NULL,
	'token' VARCHAR(255) NOT NULL,
	'token_registered' INT NOT NULL DEFAULT '0',
	PRIMARY KEY ('session_id', 'token_name')
);

CREATE INDEX '{db->prefix}tokens_token_registered' ON '{db->prefix}tokens' ('token_registered');

CREATE TABLE '{db->prefix}uploads'
(
	'area_name' VARCHAR(255) NOT NULL,
	'area_id' INT NOT NULL,
	'upload_id' INTEGER PRIMARY KEY,
	'upload_time' INT NOT NULL,
	'member_id' INT NOT NULL DEFAULT '0',
	'member_name' VARCHAR(255) NULL,
	'member_email' VARCHAR(255) NULL,
	'member_ip' VARCHAR(150) NULL,
	'modified_time' INT NULL DEFAULT '0',
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

CREATE UNIQUE INDEX '{db->prefix}uploads_area' ON '{db->prefix}uploads' ('area_name', 'area_id', 'upload_id');
CREATE INDEX '{db->prefix}uploads_member_id' ON '{db->prefix}uploads' ('member_id');
CREATE INDEX '{db->prefix}uploads_member_name' ON '{db->prefix}uploads' ('member_name');
CREATE INDEX '{db->prefix}uploads_member_ip' ON '{db->prefix}uploads' ('member_ip');