----
-- NOTE: This is used by the SnowCMS Installer, you do not need to do
--       anything with this at all!
----

----
-- Banned IPs and crap :P!
----

CREATE TABLE '{$db_prefix}banned_ips' (
  'ip' VARCHAR(16) NOT NULL,
  'notes' TEXT NOT NULL,
  'reason' TEXT NOT NULL,
  PRIMARY KEY ('ip')
);

----
-- IP logs, who used what and when?
----

CREATE TABLE '{$db_prefix}ip_logs' (
  'ip' VARCHAR(16) NOT NULL DEFAULT '',
  'member_id' MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  'first_time' INT UNSIGNED NOT NULL DEFAULT 0,
  'last_time' INT UNSIGNED NOT NULL DEFAULT 0
);

CREATE INDEX '{$db_prefix}ip_logs_index_member_id' ON '{$db_prefix}ip_logs' ('ip', 'member_id');

----
-- Board Log, have you read this? :P!
----

CREATE TABLE '{$db_prefix}board_logs' (
  'board_id' SMALLINT NOT NULL DEFAULT '0',
  'member_id' MEDIUMINT NOT NULL DEFAULT '0',
  PRIMARY KEY ('board_id','member_id')
);

----
-- Board Permissions
----

CREATE TABLE '{$db_prefix}board_permissions' (
  'board_id' SMALLINT NOT NULL DEFAULT '0',
  'group_id' SMALLINT NOT NULL DEFAULT '0',
  'what' VARCHAR(80) NOT NULL,
  'can' TINYINT NOT NULL DEFAULT '1',
  PRIMARY KEY ('board_id','group_id','what')
);

----
-- Boards of the forum xD
----

CREATE TABLE '{$db_prefix}boards' (
  'board_id' INTEGER PRIMARY KEY,
  'cat_id' SMALLINT NOT NULL DEFAULT '0',
  'board_order' SMALLINT NOT NULL DEFAULT '0',
  'child_of' SMALLINT NOT NULL DEFAULT '0',
  'who_view' VARCHAR(255) NOT NULL DEFAULT '-1,0',
  'board_name' VARCHAR(255) NOT NULL,
  'board_desc' TEXT NOT NULL,
  'num_topics' INT NOT NULL DEFAULT '0',
  'num_posts' INT NOT NULL DEFAULT '0',
  'last_msg_id' INT NOT NULL DEFAULT '0',
  'last_member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'last_member_name' VARCHAR(255) NULL
);

CREATE INDEX '{$db_prefix}boards_index_cat_id' ON '{$db_prefix}boards' ('cat_id');
CREATE INDEX '{$db_prefix}boards_index_child_of' ON '{$db_prefix}boards' ('child_of');
CREATE INDEX '{$db_prefix}boards_index_who_view' ON '{$db_prefix}boards' ('who_view');

INSERT INTO '{$db_prefix}boards' ('cat_id', 'board_order', 'board_name', 'board_desc') VALUES (1, 0, '{GENERAL_BOARD}', '{GENERAL_BOARD_DESC}');

----
-- Categories for Forum
----

CREATE TABLE '{$db_prefix}categories' (
  'cat_id' INTEGER PRIMARY KEY,
  'cat_order' SMALLINT NOT NULL DEFAULT '0',
  'cat_name' VARCHAR(255) NOT NULL,
  'is_collapsible' TINYINT NOT NULL DEFAULT '1'
);

INSERT INTO '{$db_prefix}categories' ('cat_order', 'cat_name') VALUES (0, '{GENERAL}');

----
-- Downloads...
----

CREATE TABLE '{$db_prefix}downloads' (
  'download_id' INTEGER PRIMARY KEY,
  'cat_id' SMALLINT NOT NULL DEFAULT '0',
  'member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'member_name' VARCHAR(255) NOT NULL,
  'member_ip' VARCHAR(16) NOT NULL,
  'modified_member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'modified_member_name' VARCHAR(255) NOT NULL,
  'subject' VARCHAR(255) NOT NULL,
  'description' VARCHAR(255) NOT NULL,
  'body' TEXT NOT NULL,
  'post_time' INT NOT NULL DEFAULT '0',
  'modified_time' INT NOT NULL DEFAULT '0',
  'num_comments' MEDIUMINT NOT NULL DEFAULT '0',
  'downloads' INT NOT NULL DEFAULT '0',
  'is_approved' TINYINT NOT NULL DEFAULT '1'
);

CREATE INDEX '{$db_prefix}downloads_index_cat_id' ON '{$db_prefix}downloads' ('cat_id');
CREATE INDEX '{$db_prefix}downloads_index_downloads' ON '{$db_prefix}downloads' ('downloads');
CREATE INDEX '{$db_prefix}downloads_index_is_approved' ON '{$db_prefix}downloads' ('is_approved');

----
-- Download items
----

CREATE TABLE '{$db_prefix}download_items' (
  'item_id' INTEGER PRIMARY KEY,
  'download_id' INT NOT NULL DEFAULT '0',
  'download_type' TINYINT NOT NULL DEFAULT '0',
  'filename' VARCHAR(255) NOT NULL,
  'filesize' INT NOT NULL,
  'downloads' INT NOT NULL DEFAULT '0',
  'mime_type' VARCHAR(255) NOT NULL,
  'file_ext' VARCHAR(20) NOT NULL,
  'checksum' VARCHAR(40) NOT NULL DEFAULT '',
  'base64' TINYINT NOT NULL DEFAULT '0',
  PRIMARY KEY ('item_id'),
  KEY ('download_id')
);

CREATE INDEX '{$db_prefix}download_items_download_id' ON '{$db_prefix}download_items' ('download_id');

----
-- Download Categories
----

CREATE TABLE '{$db_prefix}download_categories' (
  'cat_id' INTEGER PRIMARY KEY,
  'cat_order' SMALLINT NOT NULL DEFAULT 0,
  'cat_name' VARCHAR(255) NOT NULL,
  'cat_desc' VARCHAR(255) NOT NULL,
  'num_downloads' INT NOT NULL DEFAULT 0
);

CREATE INDEX '{$db_prefix}download_categories_index_num_downloads' ON '{$db_prefix}download_categories' ('num_downloads');

INSERT INTO '{$db_prefix}download_categories' ('cat_order', 'cat_name', 'cat_desc') VALUES (0, '{GENERAL}', '');

----
-- Download comments XD!
----

CREATE TABLE '{$db_prefix}download_comments' (
  'comment_id' INTEGER PRIMARY KEY,
  'download_id' INT NOT NULL DEFAULT '0',
  'member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'member_name' VARCHAR(255) NOT NULL,
  'member_ip' VARCHAR(255) NOT NULL,
  'modified_member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'modified_member_name' VARCHAR(255) NOT NULL,
  'subject' VARCHAR(255) NOT NULL,
  'rating' TINYINT NOT NULL DEFAULT '0',
  'body' TEXT NOT NULL,
  'post_time' INT NOT NULL DEFAULT '0',
  'modified_time' INT NOT NULL DEFAULT '0',
  'is_approved' TINYINT NOT NULL DEFAULT '1'
);

CREATE INDEX '{$db_prefix}download_categories_index_download_id' ON '{$db_prefix}download_comments' ('download_id');
CREATE INDEX '{$db_prefix}download_categories_index_is_approved' ON '{$db_prefix}download_comments' ('is_approved');

----
-- Emoticons, y'know smileys
----

CREATE TABLE '{$db_prefix}emoticons' (
  'emoticon_id' INTEGER PRIMARY KEY,
  'pack' VARCHAR(255) NOT NULL DEFAULT '',
  'filename' TEXT NOT NULL DEFAULT '',
  'sequences' TEXT NOT NULL DEFAULT '',
  'name' TEXT NOT NULL DEFAULT ''
);

CREATE INDEX '{$db_prefix}emoticons_index_pack' ON '{$db_prefix}emoticons' ('pack');

-- Default emoticons
INSERT INTO '{$db_prefix}emoticons' ('pack','filename','sequences','name') VALUES ('default', 'confused.png', ':S :-S =S', 'Confused');
INSERT INTO '{$db_prefix}emoticons' ('pack','filename','sequences','name') VALUES ('default', 'nerdy.png', '8) 8-) 8] 8-]', 'Nerdy');
INSERT INTO '{$db_prefix}emoticons' ('pack','filename','sequences','name') VALUES ('default', 'grin.png', ':D :-D =D', 'Grin');
INSERT INTO '{$db_prefix}emoticons' ('pack','filename','sequences','name') VALUES ('default', 'happy.png', ':) :-) =) :] :-] =]', 'Happy');
INSERT INTO '{$db_prefix}emoticons' ('pack','filename','sequences','name') VALUES ('default', 'mad.png', '>:( >:-( >=( >:[ >:-[ >=[ >:| >:-| >=|', 'Mad');
INSERT INTO '{$db_prefix}emoticons' ('pack','filename','sequences','name') VALUES ('default', 'sad.png', ':( :-( =( :[ :-[ =[', 'Sad');
INSERT INTO '{$db_prefix}emoticons' ('pack','filename','sequences','name') VALUES ('default', 'stunned.png', ':| :-| =|', 'Stunned');
INSERT INTO '{$db_prefix}emoticons' ('pack','filename','sequences','name') VALUES ('default', 'surprised.png', ':O :-O =O :0 :-0 =0', 'Surprised');
INSERT INTO '{$db_prefix}emoticons' ('pack','filename','sequences','name') VALUES ('default', 'tongue.png', ':P :-P =P', 'Tongue');
INSERT INTO '{$db_prefix}emoticons' ('pack','filename','sequences','name') VALUES ('default', 'wink.png', '%3B) %3B-) %3B] %3B-]', 'Wink');

----
-- Error Log :)
----

CREATE TABLE '{$db_prefix}error_log' (
  'error_id' INTEGER PRIMARY KEY,
  'error_time' INT NOT NULL DEFAULT '0',
  'member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'member_name' VARCHAR(255) NOT NULL,
  'ip' VARCHAR(15) NOT NULL DEFAULT '',
  'error_url' TEXT NOT NULL,
  'error' TEXT NOT NULL,
  'error_type' VARCHAR(255) NOT NULL,
  'file' VARCHAR(255) NOT NULL,
  'line' MEDIUMINT NOT NULL DEFAULT '0'
);

CREATE INDEX '{$db_prefix}error_log_index_error_time' ON '{$db_prefix}error_log' ('error_time');
CREATE INDEX '{$db_prefix}error_log_index_member_id' ON '{$db_prefix}error_log' ('member_id');

----
-- Flood control table
----

CREATE TABLE '{$db_prefix}flood_control' (
  'type' VARCHAR(50) NOT NULL,
  'identifier' VARCHAR(20) NOT NULL,
  'ttl' INT NOT NULL DEFAULT '0'
);

CREATE INDEX '{$db_prefix}flood_control_index_type' ON '{$db_prefix}flood_control' ('type');
CREATE INDEX '{$db_prefix}flood_control_index_identifier' ON '{$db_prefix}flood_control' ('identifier');
CREATE INDEX '{$db_prefix}flood_control_index_ttl' ON '{$db_prefix}flood_control' ('ttl');

----
-- Mail Queue table which holds emails that need to be sent ;)
----

CREATE TABLE '{$db_prefix}mail_queue' (
  'mail_id' INTEGER PRIMARY KEY,
  'time_added' INT NOT NULL DEFAULT '0',
  'to_address' VARCHAR(255) NOT NULL,
  'subject' VARCHAR(255) NOT NULL,
  'message' TEXT NOT NULL,
  'is_html' TINYINT NOT NULL DEFAULT '0',
  'priority' TINYINT NOT NULL DEFAULT '3',
  'word_wrap' TINYINT NOT NULL DEFAULT '80',
  'attempted_times' SMALLINT NOT NULL DEFAULT '0'
);

CREATE INDEX '{$db_prefix}mail_queue_index_time_added' ON '{$db_prefix}mail_queue' ('time_added');
CREATE INDEX '{$db_prefix}mail_queue_index_priority' ON '{$db_prefix}mail_queue' ('priority');
CREATE INDEX '{$db_prefix}mail_queue_index_attempted_times' ON '{$db_prefix}mail_queue' ('attempted_times');

----
-- Membergroups...
----

CREATE TABLE '{$db_prefix}membergroups' (
  'group_id' INTEGER PRIMARY KEY,
  'group_name' VARCHAR(255) NOT NULL,
  'group_name_plural' VARCHAR(255) NOT NULL,
  'group_color' VARCHAR(20) NOT NULL DEFAULT '',
  'min_posts' INT NOT NULL DEFAULT -1,
  'stars' VARCHAR(255) NOT NULL,
  'members' INT(11) NOT NULL DEFAULT 0,
  'allowed_pm_size' INT NOT NULL DEFAULT 0
);

CREATE INDEX '{$db_prefix}membergroups_index_min_posts' ON '{$db_prefix}membergroups' ('min_posts');

INSERT INTO '{$db_prefix}membergroups' ('group_name','group_name_plural','group_color','stars') VALUES ('{ADMINISTRATOR}', '{ADMINISTRATORS}', '#B20000', '5|admin_star.png');
INSERT INTO '{$db_prefix}membergroups' ('group_name','group_name_plural','group_color','stars') VALUES ('{GLOBAL_MODERATOR}', '{GLOBAL_MODERATORS}', '#00B200', '5|gmod_star.png');
INSERT INTO '{$db_prefix}membergroups' ('group_name','group_name_plural','group_color','stars') VALUES ('{MEMBER}', '{MEMBERS}', '', '5|member_star.png');

----
-- Table with all members
----

CREATE TABLE '{$db_prefix}members' (
  'member_id' INTEGER PRIMARY KEY,
  'loginName' VARCHAR(80) NOT NULL,
  'passwrd' VARCHAR(40) NOT NULL,
  'email' VARCHAR(255) NOT NULL,
  'displayName' VARCHAR(255) NOT NULL,
  'reg_time' INT NOT NULL DEFAULT 0,
  'reg_ip' VARCHAR(255) NOT NULL DEFAULT '',
  'last_login' INT NOT NULL DEFAULT 0,
  'last_online' INT NOT NULL DEFAULT 0,
  'last_ip' VARCHAR(255) NOT NULL DEFAULT '',
  'time_online' INT NOT NULL DEFAULT 0,
  'group_id' SMALLINT NOT NULL DEFAULT 0,
  'post_group_id' SMALLINT NOT NULL DEFAULT 0,
  'num_posts' INT NOT NULL DEFAULT 0,
  'num_topics' INT NOT NULL DEFAULT 0,
  'birthdate' VARCHAR(10) NOT NULL DEFAULT '',
  'avatar' VARCHAR(255) NOT NULL DEFAULT '',
  'signature' TEXT NOT NULL DEFAULT '',
  'profile_text' TEXT NOT NULL DEFAULT '',
  'custom_title' TEXT NOT NULL DEFAULT '',
  'location' TEXT NOT NULL DEFAULT '',
  'gender' TINYINT NOT NULL DEFAULT 0,
  'is_activated' TINYINT NOT NULL DEFAULT 0,
  'is_banned' TINYINT NOT NULL DEFAULT 0,
  'suspended' INT NOT NULL DEFAULT 0,
  'reminder_requested' TINYINT NOT NULL DEFAULT 0,
  'language' VARCHAR(255) NOT NULL DEFAULT '',
  'acode' VARCHAR(255) NOT NULL DEFAULT '',
  'unread_pms' SMALLINT NOT NULL DEFAULT 0,
  'total_pms' INT NOT NULL DEFAULT 0,
  'pm_size' INT NOT NULL DEFAULT 0,
  'site_name' VARCHAR(255) NOT NULL DEFAULT '',
  'site_url' VARCHAR(255) NOT NULL DEFAULT '',
  'receive_email' TINYINT NOT NULL DEFAULT 1,
  'show_email' TINYINT NOT NULL DEFAULT 1,
  'icq' VARCHAR(255) NOT NULL DEFAULT '',
  'aim' VARCHAR(255) NOT NULL DEFAULT '',
  'msn' VARCHAR(255) NOT NULL DEFAULT '',
  'yim' VARCHAR(255) NOT NULL DEFAULT '',
  'gtalk' VARCHAR(255) NOT NULL DEFAULT '',
  'theme' VARCHAR(255) NOT NULL DEFAULT 'default',
  'format_datetime' VARCHAR(255) NOT NULL DEFAULT 'MMMM D, YYYY, h:mm:ss P',
  'format_date' VARCHAR(255) NOT NULL DEFAULT 'MMMM D, YYYY',
  'format_time' VARCHAR(255) NOT NULL DEFAULT 'h:mm:ss P',
  'timezone' SMALLINT UNSIGNED NOT NULL DEFAULT 32,
  'dst' TINYINT UNSIGNED NOT NULL DEFAULT 2,
  'preference_quick_reply' TINYINT UNSIGNED NOT NULL DEFAULT 1,
  'preference_avatars' TINYINT UNSIGNED NOT NULL DEFAULT 1,
  'preference_signatures' TINYINT UNSIGNED NOT NULL DEFAULT 1,
  'preference_post_images' TINYINT UNSIGNED NOT NULL DEFAULT 1,
  'preference_emoticons' TINYINT UNSIGNED NOT NULL DEFAULT 1,
  'preference_return_topic' TINYINT UNSIGNED NOT NULL DEFAULT 0,
  'preference_pm_display' TINYINT UNSIGNED NOT NULL DEFAULT 0,
  'preference_recently_online' TINYINT UNSIGNED NOT NULL DEFAULT 2,
  'preference_thousands_separator' VARCHAR(1) NOT NULL DEFAULT ',',
  'preference_decimal_point' VARCHAR(1) NOT NULL DEFAULT '.',
  'preference_today_yesterday' TINYINT UNSIGNED NOT NULL DEFAULT 2,
  'per_page_topics' TINYINT UNSIGNED NOT NULL DEFAULT 20,
  'per_page_posts' TINYINT UNSIGNED NOT NULL DEFAULT 10,
  'per_page_news' TINYINT UNSIGNED NOT NULL DEFAULT 10,
  'per_page_downloads' TINYINT UNSIGNED NOT NULL DEFAULT 10,
  'per_page_comments' TINYINT UNSIGNED NOT NULL DEFAULT 10,
  'per_page_members' TINYINT UNSIGNED NOT NULL DEFAULT 20,
  'menus_last_cached' INT NOT NULL DEFAULT 0,
  'adminSc' VARCHAR(40) NOT NULL DEFAULT ''
);

CREATE INDEX '{$db_prefix}members_index_loginName' ON '{$db_prefix}members' ('loginName');
CREATE INDEX '{$db_prefix}members_index_passwrd' ON '{$db_prefix}members' ('passwrd');
CREATE INDEX '{$db_prefix}members_index_group_id' ON '{$db_prefix}members' ('group_id');
CREATE INDEX '{$db_prefix}members_index_is_activated' ON '{$db_prefix}members' ('is_activated');
CREATE INDEX '{$db_prefix}members_index_is_banned' ON '{$db_prefix}members' ('is_banned');
CREATE INDEX '{$db_prefix}members_index_is_suspended' ON '{$db_prefix}members' ('suspended');
CREATE INDEX '{$db_prefix}members_index_reminderRequested' ON '{$db_prefix}members' ('reminder_requested');

----
-- Menu table
----

CREATE TABLE '{$db_prefix}menus' (
  'link_id' INTEGER PRIMARY KEY,
  'link_name' VARCHAR(255) NOT NULL,
  'link_order' MEDIUMINT NOT NULL DEFAULT '0',
  'link_href' VARCHAR(255) NOT NULL,
  'link_target' TINYINT NOT NULL DEFAULT '0',
  'link_menu' TINYINT NOT NULL DEFAULT '0',
  'link_follow' TINYINT NOT NULL DEFAULT '1',
  'who_view' VARCHAR(255) NOT NULL DEFAULT '-1,2'
);

CREATE INDEX '{$db_prefix}menus_index_link_menu' ON '{$db_prefix}menus' ('link_menu');
CREATE INDEX '{$db_prefix}menus_index_who_view' ON '{$db_prefix}menus' ('who_view');

-- Default menus
INSERT INTO '{$db_prefix}menus' ('link_id', 'link_name', 'link_order', 'link_href', 'link_menu', 'who_view') VALUES (1, '{HOME}', 1, '{$base_url}/index.php', 1, '-1,2,3');
INSERT INTO '{$db_prefix}menus' ('link_id', 'link_name', 'link_order', 'link_href', 'link_menu', 'who_view') VALUES (2, '{NEWS}', 2, '{$base_url}/index.php?action=news', 1, '-1,2,3');
INSERT INTO '{$db_prefix}menus' ('link_id', 'link_name', 'link_order', 'link_href', 'link_menu', 'who_view') VALUES (3, '{FORUM}', 3, '{$base_url}/forum.php', 1, '-1,2,3');
INSERT INTO '{$db_prefix}menus' ('link_id', 'link_name', 'link_order', 'link_href', 'link_menu', 'who_view') VALUES (4, '{PROFILE}', 4, '{$base_url}/index.php?action=profile', 1, '2,3');
INSERT INTO '{$db_prefix}menus' ('link_id', 'link_name', 'link_order', 'link_href', 'link_menu', 'who_view') VALUES (5, '{HOME}', 5, '{$base_url}/index.php', 2, '-1,2,3');
INSERT INTO '{$db_prefix}menus' ('link_id', 'link_name', 'link_order', 'link_href', 'link_menu', 'who_view') VALUES (6, '{NEWS}', 6, '{$base_url}/index.php?action=news', 2, '-1,2,3');
INSERT INTO '{$db_prefix}menus' ('link_id', 'link_name', 'link_order', 'link_href', 'link_menu', 'who_view') VALUES (7, '{FORUM}', 7, '{$base_url}/forum.php', 2, '-1,2,3');
INSERT INTO '{$db_prefix}menus' ('link_id', 'link_name', 'link_order', 'link_href', 'link_menu', 'who_view') VALUES (8, '{PROFILE}', 8, '{$base_url}/index.php?action=profile', 2, '2,3');
INSERT INTO '{$db_prefix}menus' ('link_id', 'link_name', 'link_order', 'link_href', 'link_menu', 'who_view') VALUES (9, '{MEMBER_LIST}', 9, '{$base_url}/index.php?action=memberlist', 2, '-1,2,3');
INSERT INTO '{$db_prefix}menus' ('link_id', 'link_name', 'link_order', 'link_href', 'link_menu', 'who_view') VALUES (10, '{STATS}', 10, '{$base_url}/index.php?action=stats', 2, '-1,2,3');

----
-- This does the whether you have posted in this topic =D
----

CREATE TABLE '{$db_prefix}message_logs' (
  'member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'topic_id' INT NOT NULL DEFAULT '0',
  'msg_id' INT NOT NULL DEFAULT '0'
);

CREATE INDEX '{$db_prefix}message_logs_index_msg_id' ON '{$db_prefix}message_logs' ('msg_id');

----
-- Message table for topics :P!
----

CREATE TABLE '{$db_prefix}messages' (
  'msg_id' INTEGER PRIMARY KEY,
  'topic_id' INT NOT NULL DEFAULT '0',
  'board_id' SMALLINT NOT NULL DEFAULT '0',
  'member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'modified_member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'modified_name' VARCHAR(255) NULL,
  'modified_reason' VARCHAR(255) NULL,
  'modified_time' INT NOT NULL DEFAULT '0',
  'subject' VARCHAR(255) NOT NULL,
  'poster_time' INT NOT NULL DEFAULT '0',
  'poster_name' VARCHAR(255) NOT NULL,
  'poster_email' VARCHAR(255) NOT NULL,
  'poster_ip' VARCHAR(150) NOT NULL,
  'body' TEXT NOT NULL,
  'parse_bbc' SMALLINT NOT NULL DEFAULT '1',
  'parse_smileys' SMALLINT NOT NULL DEFAULT '1',
  'is_locked' SMALLINT NOT NULL DEFAULT '0'
);

CREATE INDEX '{$db_prefix}messages_index_topic_id' ON '{$db_prefix}messages' ('topic_id');
CREATE INDEX '{$db_prefix}messages_index_board_id' ON '{$db_prefix}messages' ('board_id');
CREATE INDEX '{$db_prefix}messages_index_member_id' ON '{$db_prefix}messages' ('member_id');
CREATE INDEX '{$db_prefix}messages_index_poster_ip' ON '{$db_prefix}messages' ('poster_ip');

----
-- Moderators table, for the forum
----

CREATE TABLE '{$db_prefix}moderators' (
  'board_id' SMALLINT NOT NULL,
  'member_id' MEDIUMINT NOT NULL,
  PRIMARY KEY ('board_id','member_id')
);

----
-- News table
----

CREATE TABLE '{$db_prefix}news' (
  'news_id' INTEGER PRIMARY KEY,
  'cat_id' SMALLINT NOT NULL DEFAULT '0',
  'member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'modified_member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'modified_name' VARCHAR(255) NOT NULL,
  'modified_time' INT NOT NULL DEFAULT '0',
  'subject' VARCHAR(255) NOT NULL,
  'poster_time' INT NOT NULL DEFAULT '0',
  'poster_name' VARCHAR(255) NOT NULL,
  'poster_email' VARCHAR(255) NOT NULL,
  'body' TEXT NOT NULL,
  'num_comments' MEDIUMINT NOT NULL DEFAULT '0',
  'num_views' INT NOT NULL DEFAULT '0',
  'allow_comments' TINYINT NOT NULL DEFAULT '1',
  'is_viewable' TINYINT NOT NULL DEFAULT '1'
);

CREATE INDEX '{$db_prefix}news_index_cat_id' ON '{$db_prefix}news' ('cat_id');
CREATE INDEX '{$db_prefix}news_index_num_comments' ON '{$db_prefix}news' ('num_comments');
CREATE INDEX '{$db_prefix}news_index_num_views' ON '{$db_prefix}news' ('num_views');
CREATE INDEX '{$db_prefix}news_index_is_viewable' ON '{$db_prefix}news' ('is_viewable');

----
-- News Categories
----

CREATE TABLE '{$db_prefix}news_categories' (
  'cat_id' INTEGER PRIMARY KEY,
  'cat_name' VARCHAR(255) NOT NULL,
  'num_news' INT NOT NULL DEFAULT 0
);

----
-- Comments for News :P
----

CREATE TABLE '{$db_prefix}news_comments' (
  'comment_id' INTEGER PRIMARY KEY,
  'news_id' INT NOT NULL DEFAULT '0',
  'member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'subject' VARCHAR(255) NOT NULL,
  'poster_time' INT NOT NULL DEFAULT '0',
  'poster_name' VARCHAR(255) NOT NULL,
  'poster_email' VARCHAR(255) NOT NULL,
  'poster_ip' VARCHAR(150) NOT NULL,
  'body' TEXT NOT NULL,
  'is_approved' TINYINT NOT NULL DEFAULT '1',
  'is_spam' TINYINT NOT NULL DEFAULT '0'
);

CREATE INDEX '{$db_prefix}news_comments_index_news_id' ON '{$db_prefix}news_comments' ('news_id');
CREATE INDEX '{$db_prefix}news_comments_index_is_approved' ON '{$db_prefix}news_comments' ('is_approved');
CREATE INDEX '{$db_prefix}news_comments_index_is_spam' ON '{$db_prefix}news_comments' ('is_spam');

----
-- Online loggy thing XD
----

CREATE TABLE '{$db_prefix}online' (
  'session_id' VARCHAR(32) NOT NULL,
  'last_active' INT NOT NULL,
  'member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'ip' VARCHAR(150) NOT NULL,
  'data' TEXT NOT NULL,
  PRIMARY KEY ('session_id')
);

CREATE INDEX '{$db_prefix}online_index_last_active' ON '{$db_prefix}online' ('last_active');
CREATE INDEX '{$db_prefix}online_index_member_id' ON '{$db_prefix}online' ('member_id');
CREATE INDEX '{$db_prefix}online_index_data' ON '{$db_prefix}online' ('data');

----
-- Pages ^^
----

CREATE TABLE '{$db_prefix}pages' (
  'page_id' INTEGER PRIMARY KEY,
  'member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'member_name' VARCHAR(255) NOT NULL,
  'modified_member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'modified_name' VARCHAR(255) NOT NULL,
  'created_time' INT NOT NULL DEFAULT '0',
  'modified_time' INT NOT NULL DEFAULT '0',
  'page_title' VARCHAR(255) NOT NULL,
  'content' TEXT NOT NULL,
  'type' TINYINT NOT NULL DEFAULT '0',
  'is_viewable' TINYINT NOT NULL DEFAULT '1',
  'num_views' INT NOT NULL DEFAULT '0',
  'who_view' VARCHAR(255) NOT NULL DEFAULT '-1,0'
);

CREATE INDEX '{$db_prefix}pages_index_member_id' ON '{$db_prefix}pages' ('member_id');
CREATE INDEX '{$db_prefix}pages_index_modified_member_id' ON '{$db_prefix}pages' ('modified_member_id');
CREATE INDEX '{$db_prefix}pages_index_is_viewable' ON '{$db_prefix}pages' ('is_viewable');
CREATE INDEX '{$db_prefix}pages_index_num_views' ON '{$db_prefix}pages' ('num_views');
CREATE INDEX '{$db_prefix}pages_index_who_view' ON '{$db_prefix}pages' ('who_view');

-- Home page
INSERT INTO '{$db_prefix}pages' ('page_id', 'member_name','modified_name','created_time','modified_time','page_title','content','type') VALUES (1, 'SnowCMS Team', 'SnowCMS Team', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), '{HOME_TITLE}', '{HOME_BODY}', 2);

----
-- Permissions for main setup :P
----

CREATE TABLE '{$db_prefix}permissions' (
  'group_id' SMALLINT NOT NULL DEFAULT '0',
  'what' VARCHAR(80) NOT NULL,
  'can' TINYINT NOT NULL DEFAULT '1',
  PRIMARY KEY ('group_id','what')
);

-- Guests' permissions
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (-1, 'view_profiles', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (-1, 'view_memberlist', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (-1, 'view_stats', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (-1, 'download_downloads', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (-1, 'view_forum', 1);
-- Global moderators' permissions
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'view_profiles', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'view_memberlist', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'view_stats', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'post_news_comments', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'post_download_comments', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'edit_news_comments', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'edit_download_comments', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'download_downloads', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'view_pms', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'send_pms', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'edit_profile', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'edit_display_name', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'edit_email', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'edit_avatar', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'edit_signature', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'edit_profile_text', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'upload_avatars', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'view_forum', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'post_topic', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'post_reply', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'post_poll', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'edit_post', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'edit_poll', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'delete_post', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'delete_topic', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'remove_poll', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'moderate_pms', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'ban_member', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'unban_member', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'suspend_member', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'unsuspend_member', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'add_poll_any', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'edit_post_any', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'edit_news_comment_any', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'edit_download_comment_any', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'edit_poll_any', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'delete_post_any', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'delete_news_comment_any', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'delete_download_comment_any', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'delete_topic_any', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (2, 'remove_poll_any', 1);
-- Basic members' permissions
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'view_profiles', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'view_memberlist', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'view_stats', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'post_news_comments', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'post_download_comments', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'edit_news_comments', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'edit_download_comments', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'download_downloads', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'view_pms', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'send_pms', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'edit_profile', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'edit_display_name', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'edit_email', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'edit_avatar', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'edit_signature', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'edit_profile_text', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'upload_avatars', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'view_forum', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'post_topic', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'post_reply', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'post_poll', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'edit_post', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'edit_poll', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'delete_post', 1);
INSERT INTO '{$db_prefix}permissions' ('group_id', 'what', 'can') VALUES (3, 'remove_poll', 1);

----
-- Personal Messages, need I explain?
----

CREATE TABLE '{$db_prefix}personal_messages' (
  'pm_id' INTEGER PRIMARY KEY,
  'member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'folder' INT NOT NULL DEFAULT '0',
  'recipients' TEXT NOT NULL DEFAULT '',
  'sender_id' INT NOT NULL DEFAULT '0',
  'sender_ip' VARCHAR(255) NOT NULL,
  'subject' VARCHAR(255) NOT NULL,
  'body' TEXT NOT NULL,
  'time_sent' INT NOT NULL DEFAULT '0',
  'status' SMALLINT NOT NULL DEFAULT '0',
  'flagged' TINYINT NOT NULL DEFAULT '0',
  'read_receipt' TINYINT NOT NULL DEFAULT '0',
  'reported' TINYINT NOT NULL DEFAULT '0'
);

CREATE INDEX '{$db_prefix}personal_messages_index_sender_id' ON '{$db_prefix}personal_messages' ('sender_id');
CREATE INDEX '{$db_prefix}personal_messages_index_folder' ON '{$db_prefix}personal_messages' ('folder');

----
-- This stores settings :)
----

CREATE TABLE '{$db_prefix}settings' (
  'variable' VARCHAR(255) NOT NULL,
  'value' TEXT NOT NULL,
  PRIMARY KEY ('variable')
);

-- {$db_prefix}settings values
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('site_name', 'My Website');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('site_slogan', 'Powered By SnowCMS');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('gz_compressed', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('cookie_name', 'SCMS000');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('allow_cookie_subdomain', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('allow_cookie_https', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('default_language', 'english');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('online_timeout', 15);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('enable_tasks', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('homepage', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('default_page', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('create_meta', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('meta_description', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('meta_keywords', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('keyword_appears', 2);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('num_keywords', 16);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('log_errors', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('num_tasks', 3);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('cache_enabled', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('cache_type', 'file');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('scmsVersion', '1.0');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('registration_enabled', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('reservedNames', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('disallowed_emails', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('disallowed_email_domains', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('mail_type', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('smtp_host', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('smtp_port', 25);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('smtp_user', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('smtp_pass', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('website_email', 'admin@localhost');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('account_activation', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('maintenance_mode', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('maintenance_mode_title', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('maintenance_mode_reason', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('enable_mail_queue', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('mail_queue_num_send', 5);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('captcha_chars', 5);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('captcha_strength', 3);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('show_query_count', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('forum_enabled', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('downloads_enabled', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('registration_group', 3);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('most_online_ever', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('most_online_today', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('total_members', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('total_pages', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('total_boards', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('total_topics', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('total_posts', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('total_news', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('total_news_comments', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('total_downloads', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('total_downloads_hits', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('total_downloads_comments', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('total_page_views', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('members_posted', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('members_commented', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('emoticon_pack', 'default');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('install_time', UNIX_TIMESTAMP());
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('guest_menus_last_cached', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('menus_last_updated', UNIX_TIMESTAMP());
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('current_version', '1.0');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('current_news', '');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('today_yesterday', 2);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('change_theme', 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('uploaded_avatars_enabled', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('avatar_filetypes', 'bmp,gif,png,jpg,tif');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('avatar_filesize', 100);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('avatar_size', '100x100');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('avatar_resize', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('forum_recent_posts', 12);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('password_minimum', 4);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('password_recommended', 8);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('password_strength', 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('theme', 'default');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('format_datetime', 'MMMM D, YYYY, h:mm:ss P');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('format_date', 'MMMM D, YYYY');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('format_time', 'h:mm:ss P');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('timezone', 32);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('dst', 2);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('preference_quick_reply' , 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('preference_avatars' , 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('preference_signatures' , 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('preference_post_images' , 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('preference_emoticons' , 1);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('preference_return_topic' , 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('preference_pm_display' , 0);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('preference_recently_online' , 2);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('preference_thousands_separator', ',');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('preference_decimal_point', '.');
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('preference_today_yesterday' , 2);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('per_page_topics' , 20);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('per_page_posts' , 10);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('per_page_news' , 10);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('per_page_downloads' , 10);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('per_page_comments' , 10);
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('per_page_members' , 20);
----
-- Not an error, SQLite with database driven sessions == B-A-D!!!
----
INSERT INTO '{$db_prefix}settings' ('variable', 'value') VALUES ('database_sessions', 0);

----
-- Sessions table ;)
----

CREATE TABLE '{$db_prefix}sessions' (
  'session_id' VARCHAR(40) NOT NULL,
  'data' TEXT NOT NULL,
  'saved' INT NOT NULL DEFAULT 0
);

CREATE INDEX '{$db_prefix}sessions_index_saved' ON '{$db_prefix}sessions' ('saved');

----
-- Tasks, like crons :P
----

CREATE TABLE '{$db_prefix}tasks' (
  'task_id' INTEGER PRIMARY KEY,
  'last_ran' INT NOT NULL DEFAULT '0',
  'run_every' INT NOT NULL DEFAULT '0',
  'file' VARCHAR(255) NOT NULL,
  'call_func' VARCHAR(255) NOT NULL,
  'queued' TINYINT NOT NULL DEFAULT '0',
  'enabled' TINYINT NOT NULL DEFAULT '1'
);

CREATE INDEX '{$db_prefix}tasks_index_last_ran' ON '{$db_prefix}tasks' ('last_ran');
CREATE INDEX '{$db_prefix}tasks_index_queued' ON '{$db_prefix}tasks' ('queued');
CREATE INDEX '{$db_prefix}tasks_index_enabled' ON '{$db_prefix}tasks' ('enabled');

-- Predefined SnowCMS tasks :)
INSERT INTO '{$db_prefix}tasks' ('task_id', 'last_ran', 'run_every', 'file', 'call_func', 'queued', 'enabled') VALUES (1, 0, 43200, 'tasks.php', 'tasks_tables', 0, 1);
INSERT INTO '{$db_prefix}tasks' ('task_id', 'last_ran', 'run_every', 'file', 'call_func', 'queued', 'enabled') VALUES (2, 0, 300, 'mail.php', 'mail_queue_send', 0, 1);
INSERT INTO '{$db_prefix}tasks' ('task_id', 'last_ran', 'run_every', 'file', 'call_func', 'queued', 'enabled') VALUES (3, 0, 21600, 'tasks.php', 'tasks_fles', 0, 1);

----
-- This table handles storing the topic information :P
----

CREATE TABLE '{$db_prefix}topics' (
  'topic_id' INTEGER PRIMARY KEY,
  'is_sticky' TINYINT NOT NULL DEFAULT '0',
  'is_locked' TINYINT NOT NULL DEFAULT '0',
  'board_id' SMALLINT NOT NULL DEFAULT '0',
  'poll_id' MEDIUMINT NOT NULL DEFAULT '0',
  'first_msg_id' INT NOT NULL DEFAULT '0',
  'last_msg_id' INT NOT NULL DEFAULT '0',
  'starter_member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'starter_member_name' VARCHAR(255) NULL,
  'last_member_id' MEDIUMINT NOT NULL DEFAULT '0',
  'last_member_name' VARCHAR(255) NULL,
  'num_replies' INT NOT NULL DEFAULT '0',
  'num_views' INT NOT NULL DEFAULT '0'
);

CREATE INDEX '{$db_prefix}topics_index_is_sticky' ON '{$db_prefix}topics' ('is_sticky');
CREATE INDEX '{$db_prefix}topics_index_board_id' ON '{$db_prefix}topics' ('board_id');
CREATE INDEX '{$db_prefix}topics_index_starter_member_id' ON '{$db_prefix}topics' ('starter_member_id');
CREATE INDEX '{$db_prefix}topics_index_last_member_id' ON '{$db_prefix}topics' ('last_member_id');
CREATE INDEX '{$db_prefix}topics_index_num_replies' ON '{$db_prefix}topics' ('num_replies');
CREATE INDEX '{$db_prefix}topics_index_num_views' ON '{$db_prefix}topics' ('num_views');

----
-- Have you read this topic or not..?
----

CREATE TABLE '{$db_prefix}topic_logs' (
  'topic_id' INTEGER PRIMARY KEY,
  'member_id' MEDIUMINT NOT NULL DEFAULT '0'
);

----
-- Holds polls in topics :)
----

CREATE TABLE '{$db_prefix}topic_polls' (
  'poll_id' INTEGER PRIMARY KEY,
  'question' VARCHAR(255) NOT NULL,
  'closed' TINYINT NOT NULL DEFAULT 0,
  'allowed_votes' TINYINT NOT NULL DEFAULT 0,
  'expires' INT NOT NULL DEFAULT 0,
  'allow_change' TINYINT NOT NULL DEFAULT 0,
  'result_access'TINYINT NOT NULL DEFAULT 1,
  'member_id' MEDIUMINT NOT NULL DEFAULT 0,
  'poster_name' VARCHAR(255) NOT NULL,
  'voters' MEDIUMINT NOT NULL DEFAULT '0'
);

CREATE INDEX '{$db_prefix}topic_polls_index_member_id' ON '{$db_prefix}topic_polls' ('member_id');

----
-- Logs the vote you cast...
----

CREATE TABLE '{$db_prefix}topic_poll_logs' (
  'poll_id' INT NOT NULL,
  'member_id' INT NOT NULL,
  'option_id' INT NOT NULL,
  PRIMARY KEY ('poll_id', 'member_id', 'option_id')
);

----
-- Holds polls options for the topics :D!
----

CREATE TABLE '{$db_prefix}topic_poll_options' (
  'option_id' INTEGER PRIMARY KEY,
  'poll_id' INT NOT NULL,
  'value' VARCHAR(255) NOT NULL,
  'votes' SMALLINT NOT NULL DEFAULT '0'
);

CREATE INDEX '{$db_prefix}topic_poll_options_index_poll_id' ON '{$db_prefix}topic_poll_options' ('poll_id');