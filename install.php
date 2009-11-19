<?php
#########################################################################
#                             SnowCMS v1.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#               Released under the GNU Lesser GPL v3 License            #
#                    www.gnu.org/licenses/lgpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#  SnowCMS v1.0 began in November 2008 by Myles, aldo and antimatter15  #
#                       aka the SnowCMS Dev Team                        #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 1.0                         #
#########################################################################

#
# SnowCMS installer by The SnowCMS Team.
# You don't need to touch anything at all, simply access install.php
# in your browser in order to install SnowCMS.
#

# Get all of the errors
error_reporting(E_ALL);

# Start the session
session_start();

# Clean the query variables
clean_query();

# Get the possible languages
$languages = array(
  'english' => 'English',
);

# Get the selected language
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'english';

# Get the language text
switch($language)
{
  # English
  case 'english':
    $l['powered_by'] = 'Powered By';

    $l['language_title'] = 'Language Selection';
    $l['language_header'] = 'Language Selection';
    $l['language_sidebar'] = 'Language Selection';
    $l['language_desc'] = 'Please select your language.';

    $l['language_invalid'] = 'The language you selected is invalid.';

    $l['language_go'] = 'Go';

    $l['system_title'] = 'System Checkup';
    $l['system_header'] = 'System Checkup';
    $l['system_sidebar'] = 'System Checkup';
    $l['system_desc'] = 'Checking your system\'s capabilities.';

    $l['system_success'] = 'Your system is ready for SnowCMS to be installed.';
    $l['system_error'] = 'Fatal errors detected, the below errors highlighted in red need to be rectified. For installation support, please visit <a href="http://www.snowcms.com/" target="_blank">SnowCMS.com</a>.';

    $l['file'] = 'File';
    $l['installer_message'] = 'Installer Message';
    $l['setting'] = 'Setting';
    $l['value'] = 'Value';
    $l['recommended_value'] = 'Recommended Value';

    $l['ok'] = 'OK';
    $l['not_found'] = 'Not Found!';
    $l['not_writable'] = 'Not Writable!';
    $l['on'] = 'On';
    $l['off'] = 'Off';
    $l['installed'] = 'Installed';
    $l['not_installed'] = 'Not Installed';
    $l['supported'] = 'Supported';
    $l['not_supported'] = 'Not Supported';
    $l['continue'] = 'Continue';

    $l['file_avatars'] = 'Avatars Directory';
    $l['file_avatars_desc'] = 'Holds user uploaded avatars and avatars from the site\'s collection.';
    $l['file_downloads'] = 'Downloads Directory';
    $l['file_downloads_desc'] = 'Holds files available for users to download.';
    $l['file_emoticons'] = 'Emoticons Directory';
    $l['file_emoticons_desc'] = 'Holds emoticon collections for use on the site.';
    $l['file_sources'] = 'Sources Directory';
    $l['file_sources_desc'] = 'Holds the source files that power SnowCMS. (Required)';
    $l['file_themes'] = 'Themes Directory';
    $l['file_themes_desc'] = 'Holds themes for SnowCMS. (Required)';
    $l['file_default_theme'] = 'Default Theme';
    $l['file_default_theme_desc'] = 'The default theme for SnowCMS. (Required)';
    $l['file_cache'] = 'Cache Directory';
    $l['file_cache_desc'] = 'Holds cached data to help reduce server load and speed up the site.';
    $l['file_config'] = 'Configuration File';
    $l['file_config_desc'] = 'Holds configuration information. (Required)';
    $l['file_index'] = 'Main Source';
    $l['file_index_desc'] = 'Controls the main aspects of SnowCMS. (Required)';
    $l['file_forum'] = 'Forum Source';
    $l['file_forum_desc'] = 'Controls the forum aspects of SnowCMS.';

    $l['server_os'] = 'Operating System';
    $l['server_os_recommended'] = 'Any';
    $l['server_php'] = 'PHP Version';
    $l['server_php_recommended'] = '5.0.0 or above';
    $l['server_snowcms'] = 'SnowCMS Version';
    $l['server_snowcms_recommended'] = 'The latest version';
    $l['server_magic_quotes'] = 'Magic Quotes';
    $l['server_magic_quotes_recommended'] = 'Off';
    $l['server_register_globals'] = 'Register Globals';
    $l['server_register_globals_recommended'] = 'Off';
    $l['server_gd'] = 'GD Library';
    $l['server_gd_recommended'] = 'Installed';
    $l['server_freetype'] = 'FreeType Fonts';
    $l['server_freetype_recommended'] = 'Supported';

    $l['database_title'] = 'Setup Database';
    $l['database_header'] = 'Setup Database';
    $l['database_sidebar'] = 'Setup Database';
    $l['database_desc'] = 'Setting up your database.';

    $l['database_database'] = 'Database';

    $l['database_installing'] = 'Your database is installing. This may take a few moments.';

    $l['administrator'] = 'Administrator';
    $l['administrators'] = 'Administrators';
    $l['global_moderator'] = 'Global Moderator';
    $l['global_moderators'] = 'Global Moderators';
    $l['member'] = 'Member';
    $l['members'] = 'Members';
    $l['home'] = 'Home';
    $l['news'] = 'News';
    $l['forum'] = 'Forum';
    $l['profile'] = 'Profile';
    $l['member_list'] = 'Member List';
    $l['stats'] = 'Stats';
    $l['home_title'] = 'Home';
    $l['home_body'] = 'SnowCMS has been installed successfully.';
    $l['general'] = 'General';
    $l['general_board'] = 'General Board';
    $l['general_board_desc'] = 'General discussion for all topics.';

    $l['mysql_host'] = 'MySQL Host';
    $l['mysql_username'] = 'Username';
    $l['mysql_password'] = 'Password';
    $l['mysql_database'] = 'Database Name';
    $l['mysql_persistent'] = 'Persistent Connection';
    $l['mysql_persistent_desc'] = 'Leave unchecked unless you know what you\'re doing.';
    $l['mysql_table_prefix'] = 'Table Prefix';
    $l['mysql_table_prefix_desc'] = 'By giving each installation a different table prefix, you can have multiple SnowCMS installations in the one database.';
    $l['mysql_proceed'] = 'Proceed';

    $l['mysql_error_host_none'] = 'No MySQL host entered. If you don\'t know it, try entering <code>localhost</code> or contact your system administrator.';
    $l['mysql_error_user_none'] = 'No MySQL username entered. If you don\'t know it, try entering <code>root</code> or contact your system administrator.';
    $l['mysql_error_database_none'] = 'No database name entered. If you don\'t already have one, choose a name and SnowCMS will try to create it.';
    $l['mysql_error_host'] = 'Failed connecting to the MySQL server. MySQL may not be installed or you may have entered an incorrect host.';
    $l['mysql_error_login'] = 'Failed logging in to the MySQL server. Username or password may be incorrect.';
    $l['mysql_error_unknown'] = 'Unknown error while connecting to the MySQL server.';
    $l['mysql_error_database'] = 'Error while connecting to database. Database may not exist or permissions may be set incorrectly.';
    $l['mysql_error_tables'] = 'Some tables already exist with the same names as SnowCMS requires. Consider changing the table prefix. To override the existing tables, including their data, <a href="javascript:void(0);" onclick="database_check_mysql(document.getElementById(\'db_form\'), true);">click here</a>.';
    $l['mysql_error_file'] = 'MySQL database setup file (<code>snowcms_mysql.sql</code>) missing.';
    $l['mysql_error_engine'] = 'MySQL database engine (<code>mysql.engine.php</code>) missing.';
    $l['mysql_error_queries'] = 'Error while installing MySQL tables. Technical information:';
    $l['mysql_error_queries_unknown'] = 'Unknown error while installing MySQL tables.';
    $l['mysql_error_admin'] = 'Error while creating admin account. Technical information:';

    $l['sqlite_path'] = 'Database Path';
    $l['sqlite_persistent'] = 'Persistent Connection';
    $l['sqlite_persistent_desc'] = 'Leave unchecked unless you know what you\'re doing.';
    $l['sqlite_table_prefix'] = 'Table Prefix';
    $l['sqlite_table_prefix_desc'] = 'By giving each installation a different table prefix, you can have multiple SnowCMS installations in the one database (Not recommended).';
    $l['sqlite_proceed'] = 'Proceed';

    $l['sqlite_error_path_none'] = 'You didn\'t enter a path to your SQLite database. If you don\'t already have one, enter a name and SnowCMS will try to create it.';
    $l['sqlite_error_tables'] = 'Some tables already exist with the same names as SnowCMS requires. Consider changing the database path or table prefix. To override the existing tables, including their data, <a href="javascript:void(0);" onclick="database_check_sqlite(document.getElementById(\'db_form\'), true);">click here</a>.';
    $l['sqlite_error_writable_file'] = 'Permissions do not allow for writing to the SQLite database. Please change the permissions of the SQLite database.';
    $l['sqlite_error_writable_dir'] = 'Permissions do not allow for the creation of an SQLite database. Please change the permissions in the target directory of the SQLite database.';
    $l['sqlite_error_connect'] = 'Failed connecting to SQLite database.';
    $l['sqlite_error_file'] = 'SQLite database setup file (<code>snowcms_sqlite.sql</code>) missing.';
    $l['sqlite_error_engine'] = 'SQLite database engine (<code>sqlite.engine.php</code>) missing.';
    $l['sqlite_error_queries'] = 'Error while installing SQLite tables. Technical information:';

    $l['settings_title'] = 'Basic Settings';
    $l['settings_header'] = 'Basic Settings';
    $l['settings_sidebar'] = 'Basic Settings';
    $l['settings_desc'] = 'Setting SnowCMS\'s basic settings.';

    $l['settings_site_name'] = 'Site Name';
    $l['settings_site_name_default'] = 'My Website';
    $l['settings_site_slogan'] = 'Site Slogan';
    $l['settings_site_slogan_default'] = 'Powered By SnowCMS';
    $l['settings_gz_compression'] = 'Use GZip Compression';
    $l['settings_gz_compression_desc'] = 'Compresses output and saves bandwidth.';
    $l['settings_gz_compression_supported'] = 'Not supported by your server.';
    $l['settings_cookie_name'] = 'Cookie Name';
    $l['settings_cookie_name_desc'] = 'If you have multiple SnowCMS installations on the one server, make sure this is different for each one.';
    $l['settings_meta_description'] = 'Site Description';
    $l['settings_meta_keywords'] = 'Site Keywords';
    $l['settings_site_email'] = 'Site Email';
    $l['settings_site_email_desc'] = 'When email is sent from SnowCMS, this will appear in the from field.';

    $l['settings_save'] = 'Save Settings';

    $l['admin_title'] = 'Create Admin Account';
    $l['admin_header'] = 'Create Admin Account';
    $l['admin_sidebar'] = 'Create Admin Account';
    $l['admin_desc'] = 'Creating your administrator account.';

    $l['admin_username'] = 'Username';
    $l['admin_email'] = 'Email';
    $l['admin_password'] = 'Password';
    $l['admin_vpassword'] = 'Verify Password';

    $l['admin_create'] = 'Create Account';

    $l['admin_error_username_none'] = 'You didn\'t enter a username.';
    $l['admin_error_username_short'] = 'Username is too short.';
    $l['admin_error_username_long'] = 'Username is too long.';
    $l['admin_error_email_none'] = 'You didn\'t enter an email.';
    $l['admin_error_email_invalid'] = 'Email address is invalid.';
    $l['admin_error_password_none'] = 'You didn\'t enter a password';
    $l['admin_error_password_short'] = 'Password is too short';
    $l['admin_error_password_verify'] = 'Password verification was incorrect.';

    $l['done_title'] = 'Installation Complete';
    $l['done_header'] = 'Installation Complete';
    $l['done_sidebar'] = 'Installation Complete';
    $l['done_desc'] = 'Your SnowCMS installation is complete.';

    $l['done_success'] = 'Congratulations! SnowCMS has been installed successfully. Make sure you delete <code>install.php</code>.';
    $l['done_manual_before'] = 'SnowCMS was unable to update your configuration file because permissions were set incorrectly. To fix this all you have to do is open up your <code>config.php</code> file as a text file and change it\'s contents to the following:';
    $l['done_manual_after'] = 'Once you\'ve done that, make sure you delete <code>install.php</code> and you\'re done.';
    $l['done_continue'] = 'Continue to your site.';
    break;
}

# Databases
$databases = array(
  'mysql' => 'MySQL',
  'sqlite' => 'SQLite',
);

# Steps
# array($handling_function, $processing_function)
$steps = array(
  'language' => array('language', 'language_process'),
  'system' => array('system_check'),
  'database' => array('database', 'database_process'),
  'settings' => array('settings', 'settings_process'),
  'admin' => array('admin', 'admin_process'),
  'done' => array('done'),
);

# AJAX actions
$ajax_actions = array(
  'database-change' => 'database_change_ajax',
  'check-database' => 'database_check_ajax',
);

# Form fields on database setup for any and all supported databases
$fields = array('db_type', 'mysql_host', 'mysql_user', 'mysql_pass', 'mysql_db', 'mysql_persist', 'tbl_prefix', 'sqlite_path', 'sqlite_persist'
);

# Get the current step
$step = !empty($_GET['step']) ? preg_replace('/^([^;]+)(;process)?$/', '$1', $_GET['step']) : 'language';
$step = in_array($step, array_keys($steps)) ? $step : 'language';

# Get whether processing is occuring
$process = !empty($_GET['step']) && preg_match('/^[^;]+;process$/', $_GET['step']) ? true : false;

# Get the AJAX being performed
$ajax = !empty($_GET['ajax']) && in_array($_GET['ajax'], array_keys($ajax_actions)) ? $_GET['ajax'] : '';

# Get the next step
$next = false;
foreach($steps as $stp => $function)
{
  # If this is the current step then the next is the one we want
  if($stp == $step)
    $next = true;
  # If this is the next step...
  elseif($next)
  {
    # Set the next step and since foreachs can't be broken from,
    # we'll set $next back to false
    $next_step = $stp;
    $next = false;
  }
}

# Check if we're processing for AJAX
if($ajax)
{
  # Run the AJAX handling function
  $ajax_actions[$ajax]();
}
# Check if we're processing this step
elseif($process && isset($steps[$step][1]))
{
  # Run the step's processing function
  $steps[$step][1]();
}
else
{
  # Run the step's handling function
  $steps[$step][0]();
}

# Setting the language, that is the 'language' step
function language($invalid = false)
{
  global $l, $languages;

  # The header
  install_header();

  if($invalid)
    echo '
      <div class="error">
        <p>', $l['language_invalid'], '</p>
      </div>
      ';

  echo '
      <br /><br />

      <form class="language_selection" action="install.php?step=language;process" method="post">
        <select name="language">';

  foreach($languages as $key => $name)
    echo '
          <option value="', $key, '">', $name, '</option>';

  echo '
        </select>
        <input type="submit" value="', $l['language_go'], '" />
      </form>';

  # The footer
  install_footer();
}

# Processing the setting of the language, that is the 'language;process' step
function language_process()
{
  global $next_step, $languages;

  # Get the language they selected
  $language = isset($_SESSION['language']) ? $_SESSION['language'] : 'english';

  # Check if they selected a valid language
  if(in_array($language, array_keys($languages)))
  {
    # Set the language
    $_SESSION['language'] = $language;

    # Redirect to the next step
    header('Location: install.php?step='. $next_step);
    exit;
  }
  else
  {
    # Invalid language
    language(true);
  }
}

# System check, that is the 'system' step
function system_check()
{
  global $l, $next_step;

  # No fatal error yet
  $fatal = false;

  # All the main files and folders
  # array($file_path, $existence_required, $writable_recommended, $writable_required)
  $files = array(
    'avatars' => array('avatars', false, true, false),
    'downloads' => array('downloads', false, true, false),
    'emoticons' => array('emoticons', false, false, false),
    'sources' => array('sources', true, false, false),
    'themes' => array('themes', true, true, false),
    'default_theme' => array('themes/default', true, true, false),
    'cache' => array('cache', false, true, false),
    'config' => array('config.php', true, true, true),
    'index' => array('index.php', true, false, false),
    'forum' => array('forum.php', false, false, false),
  );

  # Check the files
  foreach($files as $file => $options)
  {
    # Check if it exists
    $exists = file_exists($options[0]);

    # Check if it's writable, if it's required to do so
    if($options[2])
      $writable = is_writable($options[0]);
    # It isn't required to be writable, so we won't even bother checking
    else
      $writable = true;

    # Doesn't exist and is required?
    if(!$exists && $options[1])
    {
      $files[$file] = array('not_found', 'fatal');
      $fatal = true;
    }
    # Doesn't exist and isn't required?
    elseif(!$exists)
      $files[$file] = array('not_found', 'warning');
    # Isn't writable and must be?
    elseif(!$writable && $options[3])
    {
      $files[$file] = array('not_writable', 'fatal');
      $fatal = true;
    }
    # Isn't writable but isn't required to be?
    elseif(!$writable)
      $files[$file] = array('not_writable', 'warning');
    # No problems?
    else
      $files[$file] = array('ok', 'success');
  }

  # Get the GD library's info
  if($gd = extension_loaded('gd'))
    $gd_info = gd_info();
  else
    $gd_info = array('FreeType Support' => false);

  # Check the server information and capabilities
  $server = array(
    'os' => array(
              'value' => substr(PHP_OS, 0, 3) == 'WIN' ? 'Windows' : PHP_OS,
              'status' => 'success',
            ),
    'php' => array(
               'value' => PHP_VERSION,
               'status' => version_compare(PHP_VERSION, '5.0.0') != -1 ? 'success' : 'fatal' && $fatal = true,
             ),
    'snowcms' => array(
                   'value' => 'v1.0',
                   'status' => 'success',
                 ),
    'magic_quotes' => array(
                   'value' => function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() ? $l['on'] : $l['off'],
                   'status' => function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() ? 'warning' : 'success',
                 ),
    'register_globals' => array(
                   'value' => @ini_get('register_globals') ? $l['on'] : $l['off'],
                   'status' => @ini_get('register_globals') ? 'warning' : 'success',
                 ),
    'gd' => array(
                   'value' =>  $gd ? $l['installed'] : $l['not_installed'],
                   'status' => $gd ? 'success' : 'warning',
                 ),
    'freetype' => array(
                   'value' => $gd_info['FreeType Support'] ? $l['supported'] : $l['not_supported'],
                   'status' => $gd_info['FreeType Support'] ? 'success' : 'warning',
                 ),
  );

  # The header
  install_header();

  if(!$fatal)
  {
    echo '
      <div class="message">
        <p>', $l['system_success'], '</p>
        <br />
        <input type="button" value="', $l['continue'], '" onclick="window.location = \'install.php?step=', $next_step, '\'" />
      </div>';
  }
  else
  {
    echo '
      <br />

      <div class="error">
        <p>', $l['system_error'], '</p>
      </div>

      <br />';
  }

  echo '
    <table class="file_check" width="100%" cellspacing="0" cellpadding="4px" style="text-align: center;">
      <tr>
        <th style="width: 145px;">', $l['file'], '</th><th></th><th style="width: 145px;">', $l['installer_message'], '</th>
      </tr>';

  foreach($files as $file => $status)
  {
    echo '
      <tr class="', $status[1], '">
        <td>', $l['file_'. $file], '</td><td>', $l['file_'. $file. '_desc'], '</td><td>', $l[$status[0]], '</td>
      </tr>';
  }

  echo '
    </table>';

  echo '
    <table class="server_check" width="100%" cellspacing="0" cellpadding="4px" style="text-align: center;">
      <tr>
        <th style="width: 30%;">', $l['setting'], '</th><th style="width: 30%;">', $l['value'], '</th><th style="width: 30%;">', $l['recommended_value'], '</th>
      </tr>';

  foreach($server as $name => $info)
  {
    echo '
      <tr class="', $info['status'], '">
        <td>', $l['server_'. $name], '</td><td>', $info['value'], '</td><td>', $l['server_'. $name. '_recommended'], '</td>
      </tr>';
  }

  echo '
    </table>

    <br />';

  # The footer
  install_footer();
}

# Setting up the database, that is the 'database' step
function database($error = '')
{
  global $l, $databases;

  # The header
  install_header();

  echo '
      <br /><br />

      <form id="db_form" class="database_selection" action="install.php?step=database;process" method="post">
        <table width="100%" style="text-align: center;">
          <tr>
            <td class="right" style="width: 50%;">
              <label for="db_type">', $l['database_database'], '</label>
            </td>
            <td class="left" style="width: 50%;">
              <select name="db_type" id="db_type" onchange="database_change(this);">';

  foreach($databases as $key => $name)
    echo '
                <option value="', $key, '">', $name, '</option>';

  echo '
              </select>
            </td>
          </tr>
        </table>

        <div id="db_settings" style="padding-top: 0; padding-bottom: 0;">
          ';

  # Get the first database's settings
  reset($databases);
  list($database) = each($databases);
  database_change_ajax($database);

  echo '
        </div>

        <br />

        <div id="message" class="', $error ? 'error' : 'message', '">
          ';

  if($error)
    echo '<p>', $error, '</p>';
  else
    echo '&nbsp;';

  echo '
        </div>
      </form>';

  # The footer
  install_footer();
}

# Handling the changing of a database via AJAX
function database_change_ajax($db = null)
{
  global $l, $databases;

  # If they didn't specify a database, we'll get it from POST data
  if($db === null)
    $db = isset($_POST['db']) && in_array($_POST['db'], array_keys($databases)) ? $_POST['db'] : (isset($databases[0]) ? $databases[0] : '');

  # Get the selected database
  switch($db)
  {
    # MySQL
    case 'mysql': database_change_mysql(); break;
    # SQLite
    case 'sqlite': database_change_sqlite(); break;
  }
}

# Getting the MySQL settings output
function database_change_mysql()
{
  global $l;

  echo '
          <table class="mysql_settings" width="100%" style="text-align: center;">
            <tr>
              <td class="right" style="width: 50%;">', $l['mysql_host'], '</td><td class="left" style="width: 50%;"><input name="mysql_host" id="mysql_host" type="text" value="localhost" /></td>
            </tr>
            <tr>
              <td class="right">', $l['mysql_username'], '</td><td class="left"><input name="mysql_user" id="mysql_user" type="text" value="" /></td>
            </tr>
            <tr>
              <td class="right">', $l['mysql_password'], '</td><td class="left"><input name="mysql_pass" id="mysql_pass" type="password" style="width: 150px;" value="" /></td>
            </tr>
            <tr>
              <td class="right">', $l['mysql_database'], '</td><td class="left"><input name="mysql_db" id="mysql_db" type="text" value="" /></td>
            </tr>
            <tr>
              <td class="right"><abbr title="', $l['mysql_persistent_desc'], '">', $l['mysql_persistent'], '</abbr></td><td class="left"><input name="mysql_persist" id="mysql_persist" type="checkbox" value="1" /></td>
            </tr>
            <tr>
              <td class="right"><abbr title="', $l['mysql_table_prefix_desc'], '">', $l['mysql_table_prefix'], '</abbr></td><td class="left"><input name="tbl_prefix" id="tbl_prefix" type="text" value="scms_" /></td>
            </tr>
            <tr>
              <td class="center" colspan="2"><input type="submit" name="proceed" id="proceed" value="', $l['mysql_proceed'], '" onclick="return database_check_mysql(this.form, false);" /></td>
            </tr>
          </table>';
}

# Getting the SQLite settings output
function database_change_sqlite()
{
  global $l;

  echo '
          <table class="`sqlite_settings" width="100%" style="text-align: center;">
            <tr>
              <td class="right" style="width: 50%;">', $l['sqlite_path'], '</td><td class="left" style="width: 50%;"><input name="sqlite_path" id="sqlite_path" type="text" value="sqlite_', substr(sha1(mt_rand()), 0, 5), '.db" /></td>
            </tr>
            <tr>
              <td class="right"><abbr title="', $l['sqlite_persistent_desc'], '">', $l['sqlite_persistent'], '</abbr></td><td class="left"><input name="sqlite_persist" id="sqlite_persist" type="checkbox" value="1" /></td>
            </tr>
            <tr>
              <td class="right"><abbr title="', $l['sqlite_table_prefix_desc'], '">', $l['sqlite_table_prefix'], '</abbr></td><td class="left"><input name="tbl_prefix" id="tbl_prefix" type="text" value="scms_" /></td>
            </tr>
            <tr>
              <td class="center" colspan="2"><input type="submit" name="proceed" id="proceed" value="', $l['sqlite_proceed'], '" onclick="return database_check_sqlite(this.form, false);" /></td>
            </tr>
          </table>';

}

# Checking if the database can be created properly
function database_check_ajax()
{
  # Get the selected database
  $db_type = !empty($_POST['db_type']) ? $_POST['db_type'] : '';

  switch($db_type)
  {
    # MySQL
    case 'mysql': database_check_mysql(); break;
    # SQLite
    case 'sqlite': database_check_sqlite(); break;
  }
}

# Checking if the MySQL database can be created properly
function database_check_mysql()
{
  global $l;

  # Get the MySQL information
  $mysql_host = !empty($_POST['mysql_host']) ? $_POST['mysql_host'] : '';
  $mysql_user = !empty($_POST['mysql_user']) ? $_POST['mysql_user'] : '';
  $mysql_pass = !empty($_POST['mysql_pass']) ? $_POST['mysql_pass'] : '';
  $mysql_db = !empty($_POST['mysql_db']) ? $_POST['mysql_db'] : '';
  $mysql_persist = !empty($_POST['mysql_persist']) ? true : false;
  $tbl_prefix = !empty($_POST['tbl_prefix']) ? $_POST['tbl_prefix'] : '';

  # Whether existing tables should be overridden
  $override = !empty($_POST['override']) && $_POST['override'] == 'true' ? true : false;

  # Check that the settings are filled out
  if(!$mysql_host)
    exit($l['mysql_error_host_none']);
  elseif(!$mysql_user)
    exit($l['mysql_error_user_none']);
  elseif(!$mysql_db)
    exit($l['mysql_error_database_none']);
  else
  {
    # Try logging in to the MySQL server
    if($mysql_persist)
      $mysql_con = @mysql_pconnect($mysql_host, $mysql_user, $mysql_pass);
    else
      $mysql_con = @mysql_connect($mysql_host, $mysql_user, $mysql_pass);

    # Check if there was an error
    if(!$mysql_con)
    {
      # Format the error in a user friendly manner, also in their language
      switch(substr(mysql_error(), 0, 20))
      {
        case 'Unknown MySQL server': exit($l['mysql_error_host']); break;
        case 'Access denied for us': exit($l['mysql_error_login']); break;
        default: exit($l['mysql_error_unknown']);
      }
    }
    else
    {
      # Try selecting the database
      if(!mysql_select_db($mysql_db, $mysql_con))
      {
        # Try creating the database
        mysql_query("CREATE DATABASE `{$mysql_db}`", $mysql_con);

        # Try selecting the database again
        if(!mysql_select_db($mysql_db, $mysql_con))
        {
          # Failed to connect and no permissions to create (Probably)
          exit($l['mysql_error_database']);
        }
        else
        {
          # The database can be created, now that we know, we need to delete it
          # First let's make sure it's empty, just in case of a weird error, we
          # don't want to waste an entire database.
          $result = mysql_query("SHOW TABLES IN `{$mysql_db}`", $mysql_con);
          if(@!mysql_num_rows($result))
          {
            # Delete the database we temporarily created
            mysql_query("DROP DATABASE `{$mysql_db}`", $mysql_con);
          }
        }
      }
      # If we're not overriding existing tables, we need to check for them
      elseif(!$override)
      {
        # All the tables that need to be created
        $reserved_tables = array('banned_ips','boards','board_logs','board_permissions','categories','downloads',
                                 'download_categories','download_comments','error_log','emoticons','flood_control',
                                 'mail_queue','membergroups','members','menus','messages','message_logs','moderators',
                                 'news','news_categories','news_comments','online','pages','permissions',
                                 'personal_messages','settings','tasks','topics','topic_logs','topic_polls',
                                 'topic_poll_options', 'topic_poll_logs');

        # Now all the tables from the database
        $result = mysql_query("SHOW TABLES", $mysql_con);
        $tables = array();
        while($row = mysql_fetch_array($result))
          $tables[] = $row[0];

        # Now check if any of the tables to be created already exist
        $num_found = 0;
        foreach($reserved_tables as $table)
        {
          if(in_array($tbl_prefix. $table, $tables))
            $num_found += 1;
        }

        # And display the final message
        if($num_found)
          exit($l['mysql_error_tables']);
      }
    }
  }
}

# Checking if the SQLite database can be created properly
function database_check_sqlite()
{
  global $l;

  # Get the SQLite information
  $sqlite_path = !empty($_POST['sqlite_path']) ? $_POST['sqlite_path'] : '';
  $sqlite_persist = !empty($_POST['sqlite_persist']) ? true : false;
  $tbl_prefix = !empty($_POST['tbl_prefix']) ? $_POST['tbl_prefix'] : '';

  # Whether existing tables should be overridden
  $override = !empty($_POST['override']) && $_POST['override'] == 'true' ? true : false;

  # Check if a database path was entered
  if(!$sqlite_path)
    exit($l['sqlite_error_path_none']);
  # Check if the database already exists
  elseif(file_exists($sqlite_path))
  {
    # Check if the database is writable
    if(is_writable($sqlite_path))
    {
      # Connect to the database
      if($sqlite_persist)
        $sqlite_con = @sqlite_open($sqlite_path);
      else
        $sqlite_con = @sqlite_popen($sqlite_path);

      # Check if it worked
      if(!$sqlite_con)
      {
        # Close the database
        @sqlite_close($sqlite_con);

        # Connection error
        exit($l['sqlite_error_writable_connect']);
      }
      # If we're not overriding existing tables, we need to check for them
      elseif(!$override)
      {
        # All the tables that need to be created
        $reserved_tables = array('banned_ips','boards','board_logs','board_permissions','categories','downloads',
                                 'download_categories','download_comments','error_log','emoticons','flood_control',
                                 'mail_queue','membergroups','members','menus','messages','message_logs','moderators',
                                 'news','news_categories','news_comments','online','pages','permissions',
                                 'personal_messages','settings','tasks','topics','topic_logs','topic_polls',
                                 'topic_poll_options', 'topic_poll_logs');

        # Now all the tables from the database
        $result = @sqlite_query($sqlite_con, "SELECT * FROM sqlite_master WHERE type = 'table'");
        $tables = array();
        while($row = sqlite_fetch_array($result))
          $tables[] = $row[1];

        # Now check if any of the tables to be created already exist
        $num_found = 0;
        foreach($reserved_tables as $table)
        {
          if(in_array($tbl_prefix. $table, $tables))
            $num_found += 1;
        }

        # Close the database
        @sqlite_close($sqlite_con);

        # And display the final message
        if($num_found)
          exit($l['sqlite_error_tables']);
      }
    }
    else
      exit($l['sqlite_error_writable_file']);
  }
  else
  {
    # No database, yet, let's see if we can create it
    if(is_writable(dirname($sqlite_path)))
    {
      # Let's try creating it then
      if($sqlite_persist)
        $sqlite_con = @sqlite_open($sqlite_path);
      else
        $sqlite_con = @sqlite_popen($sqlite_path);

      # Close the database
      @sqlite_close($sqlite_con);

      # Now we need to delete it, but first let's make sure it's empty,
      # we don't want a weird error destroying an entire database
      if(file_exists($sqlite_path) && !filesize($sqlite_path))
      {
        unlink($sqlite_path);
      }

      # Check if it worked
      if(!$sqlite_con)
        exit($l['sqlite_error_writable_connect']);
    }
    else
      exit($l['sqlite_error_writable_dir']);
  }
}

# Processing the database settings
function database_process()
{
  # Get the selected database
  $db_type = !empty($_POST['db_type']) ? $_POST['db_type'] : '';

  switch($db_type)
  {
    # MySQL
    case 'mysql': database_process_mysql(); break;
    # SQLite
    case 'sqlite': database_process_sqlite(); break;
  }
}

# Setup MySQL
function database_process_mysql()
{
  global $l, $next_step;

  # Get the MySQL information
  $mysql_host = !empty($_POST['mysql_host']) ? $_POST['mysql_host'] : '';
  $mysql_user = !empty($_POST['mysql_user']) ? $_POST['mysql_user'] : '';
  $mysql_pass = !empty($_POST['mysql_pass']) ? $_POST['mysql_pass'] : '';
  $mysql_db = !empty($_POST['mysql_db']) ? $_POST['mysql_db'] : '';
  $mysql_persist = !empty($_POST['mysql_persist']) ? true : false;
  $tbl_prefix = !empty($_POST['tbl_prefix']) ? $_POST['tbl_prefix'] : '';

  # Try logging in to the MySQL server
  if($mysql_persist)
    $mysql_con = @mysql_pconnect($mysql_host, $mysql_user, $mysql_pass);
  else
    $mysql_con = @mysql_connect($mysql_host, $mysql_user, $mysql_pass);

  # No error yet
  $error = '';

  # Check if there was an error
  if(!$mysql_con)
  {
    # Format the error in a user friendly manner, also in their language
    switch(substr(mysql_error(), 0, 20))
    {
      case 'Unknown MySQL server': $error = $l['mysql_error_host']; break;
      case 'Access denied for us': $error = $l['mysql_error_login']; break;
      default: $error = $l['mysql_error_unknown'];
    }
  }
  else
  {
    # Try selecting the database
    if(!mysql_select_db($mysql_db, $mysql_con))
    {
      # Can't be selected, try creating the database
      mysql_query("CREATE DATABASE `{$mysql_db}`", $mysql_con);

      # Try selecting the database again
      if(!mysql_select_db($mysql_db, $mysql_con))
      {
        # Failed to connect and no permissions to create (Probably)
        $error = $l['mysql_error_database'];
      }
    }
  }

  # Try the database setup file
  if(!$error && !file_exists('snowcms_mysql.sql'))
    $error = $l['mysql_error_file'];
  # Try the database engine
  elseif(!$error && !file_exists('sources/engines/mysql.engine.php'))
    $error = $l['mysql_error_engine'];

  # Check if there was an error
  if(!$error)
  {
    # No errors, let's setup the database
    $queries = file_get_contents('snowcms_mysql.sql');

    # Remove comments
    $queries = preg_replace('/^#.+$/m', '', $queries);

    # Get everything that needs to be changed
    $replacements = array(
      '{$db_prefix}' => $tbl_prefix,
      '{ADMINISTRATOR}' => $l['administrator'],
      '{ADMINISTRATORS}' => $l['administrators'],
      '{GLOBAL_MODERATOR}' => $l['global_moderator'],
      '{GLOBAL_MODERATORS}' => $l['global_moderators'],
      '{MEMBER}' => $l['member'],
      '{MEMBERS}' => $l['members'],
      '{HOME}' => $l['home'],
      '{NEWS}' => $l['news'],
      '{FORUM}' => $l['forum'],
      '{PROFILE}' => $l['profile'],
      '{MEMBER_LIST}' => $l['member_list'],
      '{STATS}' => $l['stats'],
      '{HOME_TITLE}' => $l['home_title'],
      '{HOME_BODY}' => $l['home_body'],
      '{GENERAL}' => $l['general'],
      '{GENERAL_BOARD}' => $l['general_board'],
      '{GENERAL_BOARD_DESC}' => $l['general_board_desc'],
      '{$base_url}' => dirname($_SERVER['PHP_SELF']),
      'UNIX_TIMESTAMP()' => time_utc(),
    );

    # Change everything that needs to be changed
    $queries = strtr($queries, $replacements);

    # Separate the queries
    $queries = array_filter(explode(';', $queries));

    # Decode literal semicolons
    foreach($queries as $key => $query)
      $queries[$key] = str_replace('%3B', ';', $query);

    # Run the queries
    foreach($queries as $query)
    {
      # Run the query and check for an error
      if(!mysql_query($query, $mysql_con))
      {
        # Error message
        if($error = mysql_error())
          database($l['mysql_error_queries']. '
<br />
<br />
<textarea cols="50" rows="4">'. $error. '</textarea>');
        # Unknown error
        else
          database($l['mysql_error_queries_unknown']);
        return;
      }
    }

    # Save the database information in the session
    $_SESSION['db_type'] = 'mysql';
    $_SESSION['mysql_host'] = $mysql_host;
    $_SESSION['mysql_user'] = $mysql_user;
    $_SESSION['mysql_pass'] = $mysql_pass;
    $_SESSION['mysql_db'] = $mysql_db;
    $_SESSION['mysql_persist'] = $mysql_persist;
    $_SESSION['tbl_prefix'] = $tbl_prefix;

    # Redirect to the next step
    header('Location: install.php?step='. $next_step);
    exit;
  }
  else
  {
    # There was an error
    database($error);
  }
}

# Setup SQLite
function database_process_sqlite()
{
  global $l, $next_step;

  # Get the SQLite information
  $sqlite_path = !empty($_POST['sqlite_path']) ? $_POST['sqlite_path'] : '';
  $sqlite_persist = !empty($_POST['sqlite_persist']) ? true : false;
  $tbl_prefix = !empty($_POST['tbl_prefix']) ? $_POST['tbl_prefix'] : '';

  # No error yet
  $error = '';

  # Check if the database already exists
  if(file_exists($sqlite_path))
  {
    # Check if the database is writable
    if(is_writable($sqlite_path))
    {
      # Let's try creating it then
      if($sqlite_persist)
        $sqlite_con = @sqlite_open($sqlite_path);
      else
        $sqlite_con = @sqlite_popen($sqlite_path);

      # Check if it worked
      if(!$sqlite_con)
      {
        # Close the database
        @sqlite_close($sqlite_con);

        # Record the error
        $error = $l['sqlite_error_writable_connect'];
      }
    }
    else
      $error = $l['sqlite_error_writable_file'];
  }
  else
  {
    # No database, yet, let's see if we can create it
    if(is_writable(dirname($sqlite_path)))
    {
      # Let's try creating it then
      if($sqlite_persist)
        $sqlite_con = @sqlite_open($sqlite_path);
      else
        $sqlite_con = @sqlite_popen($sqlite_path);

      # Check if it worked
      if(!$sqlite_con)
      {
        # Close the database
        @sqlite_close($sqlite_con);

        # Record the error
        $error = $l['sqlite_error_writable_connect'];
      }
    }
    else
      $error = $l['sqlite_error_writable_dir'];
  }

  # Try the database setup file
  if(!$error && !file_exists('snowcms_sqlite.sql'))
    $error = $l['sqlite_error_file'];
  # Try the database engine
  elseif(!$error && !file_exists('sources/engines/sqlite.engine.php'))
    $error = $l['sqlite_error_engine'];

  # Check if there was an error
  if(!$error)
  {
    # All the tables that need to be created
    $reserved_tables = array('banned_ips','boards','board_logs','board_permissions','categories','downloads',
                             'download_categories','download_comments','error_log','emoticons','flood_control',
                             'mail_queue','membergroups','members','menus','messages','message_logs','moderators',
                             'news','news_categories','news_comments','online','pages','permissions',
                             'personal_messages','settings','tasks','topics','topic_logs','topic_polls',
                             'topic_poll_options');

    # Now all the tables from the database
    # Try the query a maximum of ten times
    $tries = 0;
    $last_error = SQLITE_SCHEMA;
    while($last_error == SQLITE_SCHEMA && $tries < 10)
    {
      $result = @sqlite_query($sqlite_con, "SELECT * FROM sqlite_master WHERE type = 'table'");
      $tries += 1;
      $last_error = sqlite_last_error($sqlite_con);
    }
    $tables = array();
    while($row = sqlite_fetch_array($result))
      $tables[] = $row[1];

    # Now check if any of the tables to be created already exist
    $num_found = 0;
    foreach($reserved_tables as $table)
    {
      # If the table already exists, we need to drop it
      if(in_array($tbl_prefix. $table, $tables))
        sqlite_query($sqlite_con, "DROP TABLE '{$tbl_prefix}{$table}'");
    }

    # No errors, let's setup the database
    $queries = file_get_contents('snowcms_sqlite.sql');

    # Remove comments
    $queries = preg_replace('/^\-\-.+$/m', '', $queries);

    # Get everything that needs to be changed
    $replacements = array(
      '{$db_prefix}' => $tbl_prefix,
      '{ADMINISTRATOR}' => $l['administrator'],
      '{ADMINISTRATORS}' => $l['administrators'],
      '{GLOBAL_MODERATOR}' => $l['global_moderator'],
      '{GLOBAL_MODERATORS}' => $l['global_moderators'],
      '{MEMBER}' => $l['member'],
      '{MEMBERS}' => $l['members'],
      '{HOME}' => $l['home'],
      '{NEWS}' => $l['news'],
      '{FORUM}' => $l['forum'],
      '{PROFILE}' => $l['profile'],
      '{MEMBER_LIST}' => $l['member_list'],
      '{STATS}' => $l['stats'],
      '{HOME_TITLE}' => $l['home_title'],
      '{HOME_BODY}' => $l['home_body'],
      '{GENERAL}' => $l['general'],
      '{GENERAL_BOARD}' => $l['general_board'],
      '{GENERAL_BOARD_DESC}' => $l['general_board_desc'],
      '{$base_url}' => dirname($_SERVER['PHP_SELF']),
      'UNIX_TIMESTAMP()' => time_utc(),
    );

    # Change everything that needs to be changed
    $queries = strtr($queries, $replacements);

    # Separate the queries
    $queries = array_filter(explode(';', $queries));

    # Decode literal semicolons
    foreach($queries as $key => $query)
      $queries[$key] = str_replace('%3B', ';', $query);

    # Run the queries
    foreach($queries as $query)
    {
      # Try the query a maximum of ten times
      $tries = 0;
      $last_error = SQLITE_SCHEMA;
      $query_error = '';
      while($last_error == SQLITE_SCHEMA && $tries < 10)
      {
        # PHP 5.1+ let's us get more information about syntax errors
        if(version_compare(PHP_VERSION, '5.1') >= 0)
          @sqlite_query($sqlite_con, $query, SQLITE_BOTH, $query_error);
        else
          @sqlite_query($sqlite_con, $query);
        $tries += 1;
        $last_error = sqlite_last_error($sqlite_con);
      }

      # Check for an error
      if(($last_error != 0 && $last_error != SQLITE_SCHEMA) || $tries >= 10)
      {
        # Close the database
        @sqlite_close($sqlite_con);
        
        # Display the database setup step again, but with an error message
        database($l['sqlite_error_queries']. '
<br />
<br />
<textarea cols="50" rows="4">Error '. substr('00'. $last_error, -max(strlen($last_error), 3)). ': '. ($query_error ? $query_error : sqlite_error_string($last_error)). '</textarea>');
        return;
      }
    }

    # Close the database
    @sqlite_close($sqlite_con);

    # Save the database information in the session
    $_SESSION['db_type'] = 'sqlite';
    $_SESSION['sqlite_path'] = $sqlite_path;
    $_SESSION['sqlite_persist'] = $sqlite_persist;
    $_SESSION['tbl_prefix'] = $tbl_prefix;

    # Redirect to the next step
    header('Location: install.php?step='. $next_step);
    exit;
  }
  else
  {
    # There was an error
    database($error);
  }
}

# Setting the basic settings, that is the 'settings' step
function settings($error = '')
{
  global $l;

  # The header
  install_header();

  echo '
      <div id="message" class="', $error ? 'error' : 'message', '">
          ';

  if($error)
    echo '<p>', $error, '</p>';
  else
    echo '&nbsp;';

  echo '
        </div>

      <form action="install.php?step=settings;process" method="post">
        <table align="center" width="100%">
          <tr>
            <td class="right" style="width: 50%;">', $l['settings_site_name'], '</td><td class="left" style="width: 50%;"><input type="text" name="site_name" id="site_name" value="', !empty($_POST['site_name']) ? htmlspecialchars($_POST['site_name']) : $l['settings_site_name_default'], '" /></td>
          </tr>
          <tr>
            <td class="right" style="width: 50%;">', $l['settings_site_slogan'], '</td><td class="left" style="width: 50%;"><input type="text" name="site_slogan" id="site_slogan" value="', !empty($_POST['site_slogan']) ? htmlspecialchars($_POST['site_slogan']) : $l['settings_site_slogan_default'], '" /></td>
          </tr>
          <tr>
            <td class="right"><abbr title="', $l['settings_gz_compression_desc'], '">', $l['settings_gz_compression'], '</abbr></td><td class="left"><input type="checkbox" name="gz_compression" id="gz_compression" value="1"', !function_exists('ob_gzhandler') ? ' disabled="disabled" title="'. $l['settings_gz_compression_not_supported']. '"' : ((isset($_POST['gz_compression']) && !$_POST['gz_compression']) || !function_exists('ob_gzhandler') ? '' : ' checked="checked"'), ' /></td>
          </tr>
          <tr>
            <td class="right"><abbr title="', $l['settings_cookie_name_desc'], '">', $l['settings_cookie_name'], '</abbr></td><td class="left"><input type="text" name="cookie_name" id="cookie_name" value="', !empty($_POST['cookie_name']) ? htmlspecialchars($_POST['cookie_name']) : 'SCMS'. mt_rand(100, 999), '" /></td>
          </tr>
          <tr>
            <td class="right">', $l['settings_meta_description'], '</td><td class="left"><input type="text" name="meta_description" id="meta_description" value="', !empty($_POST['meta_description']) ? htmlspecialchars($_POST['meta_description']) : '', '" /></td>
          </tr>
          <tr>
            <td class="right">', $l['settings_meta_keywords'], '</td><td class="left"><input type="text" name="meta_keywords" id="meta_keywords" value="', !empty($_POST['meta_keywords']) ? htmlspecialchars($_POST['meta_keywords']) : '', '"/></td>
          </tr>
          <tr>
            <td class="right"><abbr title="', $l['settings_site_email_desc'], '">', $l['settings_site_email'], '</abbr></td><td class="left"><input type="text" name="site_email" id="site_email" value="', !empty($_POST['site_email']) ? htmlspecialchars($_POST['site_email']) : 'admin@localhost', '" /></td>
          </tr>
          <tr>
            <td class="center" colspan="2"><input type="submit" value="', $l['settings_save'], '" /></td>
          </tr>
        </table>
      </form>';

  # The footer
  install_footer();
}

# Process the setting of the basic settings
function settings_process()
{
  switch($_SESSION['db_type'])
  {
    # MySQL
    case 'mysql': settings_process_mysql(); break;
    # SQLite
    case 'sqlite': settings_process_sqlite(); break;
  }
}

# Process the setting of the basic settings for MySQL
function settings_process_mysql()
{
  global $next_step;

  # Get the basic settings' field names
  $fields = array(
    'site_name',
    'site_description',
    'gz_compression',
    'cookie_name',
    'meta_description',
    'meta_keywords',
    'site_email',
  );

  # Get the basic settings' values
  foreach($fields as $field)
    $settings[$field] = !empty($_POST[$field]) ? $_POST[$field] : '';

  # Add the language
  $settings['default_language'] = $_SESSION['language'];

  # Connect to the MySQL server
  $mysql_con = mysql_connect($_SESSION['mysql_host'], $_SESSION['mysql_user'], $_SESSION['mysql_pass']);

  # Select the database
  mysql_select_db($_SESSION['mysql_db'], $mysql_con);

  # Update the settings
  foreach($settings as $setting => $value)
    mysql_query("UPDATE `{$_SESSION['tbl_prefix']}settings` SET `value` = '{$value}' WHERE `variable` = '{$setting}'", $mysql_con);

  # Redirect to the next step
  header('Location: install.php?step='. $next_step);
  exit;
}

# Process the setting of the basic settings for SQLite
function settings_process_sqlite()
{
  global $next_step;

  # Get the basic settings' field names
  $fields = array(
    'site_name',
    'site_description',
    'gz_compression',
    'cookie_name',
    'meta_description',
    'meta_keywords',
    'site_email',
  );

  # Get the basic settings' values
  foreach($fields as $field)
    $settings[$field] = !empty($_POST[$field]) ? $_POST[$field] : '';

  # Add the language
  $settings['default_language'] = $_SESSION['language'];

  # Open the SQLite database
  $sqlite_con = sqlite_open($_SESSION['sqlite_path']);

  # Update the settings
  foreach($settings as $setting => $value)
    sqlite_query($sqlite_con, "UPDATE {$_SESSION['tbl_prefix']}settings SET value = '{$value}' WHERE variable = '{$setting}'");

  # Redirect to the next step
  header('Location: install.php?step='. $next_step);
  exit;
}

# Creating the administrator account, that is the 'admin' step
function admin($error = '')
{
  global $l;

  # The header
  install_header();

  echo '
      <div id="message" class="', $error ? 'error' : 'message', '">
          ';

  if($error)
    echo '<p>', $error, '</p>';
  else
    echo '&nbsp;';

  echo '
        </div>

      <form action="install.php?step=admin;process" method="post">
        <table align="center" width="100%">
          <tr>
            <td class="right" style="width: 50%;">', $l['admin_username'], '</td><td class="left" style="width: 50%;"><input type="text" name="username" id="username" value="', !empty($_POST['username']) ? htmlspecialchars($_POST['username']) : '', '" /></td>
          </tr>
          <tr>
            <td class="right" style="width: 50%;">', $l['admin_email'], '</td><td class="left" style="width: 50%;"><input type="text" name="email" id="email" value="', !empty($_POST['email']) ? htmlspecialchars($_POST['email']) : '', '" /></td>
          </tr>
          <tr>
            <td class="right" style="width: 50%;">', $l['admin_password'], '</td><td class="left" style="width: 50%;"><input type="password" name="passwrd" id="passwrd" value="', !empty($_POST['password']) ? htmlspecialchars($_POST['password']) : '', '" /></td>
          </tr>
          <tr>
            <td class="right" style="width: 50%;">', $l['admin_vpassword'], '</td><td class="left" style="width: 50%;"><input type="password" name="vpasswrd" id="vpasswrd" value="', !empty($_POST['vpassword']) ? htmlspecialchars($_POST['vpassword']) : '', '" /></td>
          </tr>
          <tr>
            <td class="center" colspan="2"><input type="submit" value="', $l['admin_create'], '" /></td>
          </tr>
        </table>
      </form>';

  # The footer
  install_footer();
}

# Process the creating of the administrator account
function admin_process()
{
  global $l;

  # Get the data
  $username = !empty($_POST['username']) ? $_POST['username'] : '';
  $email = !empty($_POST['email']) ? $_POST['email'] : '';
  $password = !empty($_POST['passwrd']) ? $_POST['passwrd'] : '';
  $vpassword = !empty($_POST['vpasswrd']) ? $_POST['vpasswrd'] : '';
  $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
  $now = time_utc();

  # No errors yet
  $error = '';

  # Check if a username was entered
  if(!$username)
    $error = $l['admin_error_username_none'];
  # Check if the username is too short
  elseif(strlen($username) < 3)
    $error = $l['admin_error_username_short'];
  # Too long?
  elseif(strlen($username) > 80)
    $error = $l['admin_error_username_long'];
  # Check if an email was entered
  elseif(!$email)
    $error = $l['admin_error_email_none'];
  # Check if the email is valid
  elseif(!preg_match('/^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$/i', $email))
    $error = $l['admin_error_email_invalid'];
  # Check if a password was entered
  elseif(!$password)
    $error = $l['admin_error_password_none'];
  # Check if the password is too short
  elseif(strlen($password) < 4)
    $error = $l['admin_error_password_short'];
  # Check if the password verification was correct
  elseif($password != $vpassword)
    $error = $l['admin_error_password_verify'];

  # Check if there was an error
  if($error)
  {
    # Error!
    admin($error);
  }
  else
  {
    # No errors, let's continue
    # Sanitize the username
    $username = str_replace('\\', '\\\\', (htmlspecialchars($username, ENT_QUOTES)));

    # Hash the password
    $password = sha1($password);

    # Get the database's unique function to finish creating the account
    switch($_SESSION['db_type'])
    {
      # MySQL
      case 'mysql': admin_process_mysql($username, $email, $password, $ip, $now); break;
      # SQLite
      case 'sqlite': admin_process_sqlite($username, $email, $password, $ip, $now); break;
    }
  }
}

# Process the creating of the administrator account for MySQL
function admin_process_mysql($username, $email, $password, $ip, $now)
{
  global $l, $next_step;

  # Connect to the MySQL server
  $mysql_con = mysql_connect($_SESSION['mysql_host'], $_SESSION['mysql_user'], $_SESSION['mysql_pass']);

  # Select the database
  mysql_select_db($_SESSION['mysql_db'], $mysql_con);

  # Create the account
  $result = mysql_query("
    REPLACE INTO {$_SESSION['tbl_prefix']}members
    (`member_id`, `loginName`, `passwrd`, `email`, `displayName`, `reg_time`, `reg_ip`, `group_id`, `is_activated`, `language`, `format_datetime`, `format_date`, `format_time`)
    VALUES (1, '$username', '$password', '$email', '$username', '$now', '$ip', 1, 1, '{$_SESSION['language']}', 'MMMM D, YYYY, h:mm:ss P', 'MMMM D, YYYY', 'h:mm:ss P')
  ", $mysql_con);
  
  # Check if it worked
  if(!$result)
  {
    admin($l['mysql_error_admin']. '
<br />
<br />
<textarea cols="50" rows="4">'. mysql_error(). '</textarea>');
    exit;
  }
  
  # Update total members
  $result = mysql_query("
    UPDATE {$_SESSION['tbl_prefix']}settings
    SET
      `value` = 1
    WHERE `variable` = 'total_members'
  ", $mysql_con);

  # Check if it worked
  if(!$result)
  {
    admin($l['mysql_error_admin']. '
<br />
<br />
<textarea cols="50" rows="4">'. mysql_error(). '</textarea>');
    exit;
  }
  
  # Redirect to the next step
  header('Location: install.php?step='. $next_step);
  exit;
}

# Process the creating of the administrator account for SQLite
function admin_process_sqlite($username, $email, $password, $ip, $now)
{
  global $next_step;

  # Open the SQLite database
  $sqlite_con = sqlite_open($_SESSION['sqlite_path']);

  # Create the account
  sqlite_query($sqlite_con, "
    INSERT INTO {$_SESSION['tbl_prefix']}members
    (member_id, loginName, passwrd, email, displayName, reg_time, reg_ip, group_id, is_activated, language, format_datetime, format_date, format_time)
    VALUES (1, '$username', '$password', '$email', '$username', '$now', '$ip', 1, 1, '{$_SESSION['language']}', 'MMMM D, YYYY, h:mm:ss P', 'MMMM D, YYYY', 'h:mm:ss P')
  ");

  # Update total members
  sqlite_query($sqlite_con, "
    UPDATE {$_SESSION['tbl_prefix']}settings
    SET
      value = 1
    WHERE variable = 'total_members'
  ");

  # Redirect to the next step
  header('Location: install.php?step='. $next_step);
  exit;
}

# Finish the installation
function done()
{
  global $l;

  # The header
  install_header();

  # Get config.php file's contents
  $config = '<?php
#########################################################################
#                             SnowCMS v1.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#               Released under the GNU Lesser GPL v3 License            #
#                    www.gnu.org/licenses/lgpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#  SnowCMS v1.0 began in November 2008 by Myles, aldo and antimatter15  #
#                       aka the SnowCMS Dev Team                        #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 1.0                         #
#########################################################################

# No direct access please ^^
if(!defined(\'InSnow\'))
  die(header(\'HTTP/1.1 404 Not Found\'));

#
# config.php holds the configuration of your SnowCMS installation
# from your database settings to file paths needed.
#

# Your database settings
$db_type = \''. $_SESSION['db_type']. '\'; # Your database type';

  # Get database information for config.php file
  switch($_SESSION['db_type'])
  {
    case 'mysql': $config .= done_config_mysql(); break;
    case 'sqlite': $config .= done_config_sqlite(); break;
  }

  # Add the rest of config.php's contents
  $config .= '

# File paths (No trailing slash)
$base_dir = \''. dirname(__FILE__) . '\'; # The base path to your SnowCMS installation
$source_dir = \''. dirname(__FILE__) . '/sources\'; # Path to the sources Folder
$theme_dir = \''. dirname(__FILE__) . '/themes\'; # Path to the themes Folder
$download_dir = \''. dirname(__FILE__) . '/downloads\'; # The path to the downloads directory which contains downloads for your download center
$avatar_dir = \''. dirname(__FILE__) . '/avatars\'; # The path to the avatars directory which has avatars that users, use :P If you enable it
$emoticon_dir = \''. dirname(__FILE__) . '/emoticons\'; # The path to the emoticons directory which has emoticon packs and emoticons
$cache_dir = \''. dirname(__FILE__) . '/cache\'; # The path to where cached files will go unless you are using another caching system. no trailing slash.

# We need some URLs too
$base_url = \'http://'. $_SERVER['HTTP_HOST']. substr($_SERVER['PHP_SELF'], 0, -12). '\'; # The base url to your SnowCMS install. No trailing slash.
$theme_url = \'http://'. $_SERVER['HTTP_HOST']. substr($_SERVER['PHP_SELF'], 0, -12). '/themes\'; # The base url to your SnowCMS themes folder. No trailing slash.
$emoticon_url = \'http://'. $_SERVER['HTTP_HOST']. substr($_SERVER['PHP_SELF'], 0, -12). '/emoticons\'; # The base url to your SnowCMS emoticons folder. No trailing slash.

# Check if a couple things exist. All we really need is the
# base path, source directory and theme directory.
if(!file_exists($base_dir) && file_exists(\'./\'))
  $base_dir = dirname(__FILE__);
if(!file_exists($source_dir) && file_exists(\'./sources\'))
  $source_dir = dirname(__FILE__). \'/sources\';
if(!file_exists($theme_dir) && file_exists(\'./themes\'))
  $theme_dir = dirname(__FILE__). \'/themes\';
if(!file_exists($cache_dir) && file_exists(\'./cache\'))
  $cache_dir = dirname(__FILE__). \'/cache\';

$snowcms_installed = true;
?>';

  # Check if config.php is writable
  if(is_writable('config.php'))
  {
    # Write away
    file_put_contents('config.php', $config);

    # Let them know of the success
    echo '
      <br />

      <p>', $l['done_success'], '</p>

      <br />

      <div class="message">
        <h1><a href="index.php">', $l['done_continue'], '</a></h1>
      </div>

      <br />
      ';
  }
  else
  {
    # We'll have to tell them to do it then
    echo '
      <br />

      <p>', $l['done_manual_before'], '</p>

      <textarea cols="75" rows="20">', htmlentities($config, ENT_QUOTES), '</textarea>

      <p>', $l['done_manual_after'], '</p>

      <br />

      <div class="message">
        <h1><a href="index.php">', $l['done_continue'], '</a></h1>
      </div>

      <br />';
  }

  # The footer
  install_footer();
}

# Get MySQL's config.php file settings
function done_config_mysql()
{
  return '
$db_host = \''. $_SESSION['mysql_host']. '\'; # Your MySQL server host, usually localhost
$db_user = \''. $_SESSION['mysql_user']. '\'; # Your MySQL username
$db_pass = \''. $_SESSION['mysql_pass']. '\'; # Your MySQL password to your MySQL username
$db_name = \''. $_SESSION['mysql_db']. '\'; # Your MySQL database
$tbl_prefix = \''. $_SESSION['tbl_prefix']. '\'; # Your table prefix, which allows you to have multiple installs of SnowCMS on the same DB
$db_persistent = '. (!empty($_SESSION['mysql_persist']) ? 'true' : 'false'). '; # Use a persistent connection to the MySQL server?';
}

# Get SQLite's config.php file settings
function done_config_sqlite()
{
  return '
$db_name = \''. $_SESSION['sqlite_path']. '\'; # Your SQLite database path and name
$tbl_prefix = \''. $_SESSION['tbl_prefix']. '\'; # Your table prefix, which allows you to have multiple installs of SnowCMS on the same DB
$db_persistent = '. (!empty($_SESSION['sqlite_persist']) ? 'true' : 'false'). '; # Use a persistent connection to the SQLite database?';
}

# Clean the query variables
function clean_query()
{
  # We're going to unset all potential register_globals for
  # security purposes, but we can't delete these variables...
  $reserved = array('GLOBALS','_COOKIE','_ENV','_GET','_POST','_REQUEST','_SERVER','_SESSION','reserved');

  # Get all the variables other than the reserved ones
  foreach($GLOBALS as $GKEY => $GVALUE)
    if(!in_array($GKEY, $reserved))
      unset($GLOBALS[$GKEY]);

  # Unset all the register_globals
  unset($GLOBALS['GKEY'], $GLOBALS['GVALUE']);

  # Fix up magic quotes
  if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() > 0)
  {
    $_COOKIE = remove_magic($_COOKIE);
    $_GET = remove_magic($_GET);
    $_POST = remove_magic($_POST);
    $_REQUEST = remove_magic($_REQUEST);
  }
}

# Remove magic quotes
function remove_magic($array)
{
  # Only arrays and only ones that have things
  if(is_array($array) && count($array))
  {
    $return = array();

    # Strip the slashes off the magic quotes
    foreach($array as $key => $value)
      $return[stripslashes($key)] = is_array($value) ? '' : stripslashes($value);

    return $return;
  }
  else
    return array();
}

# Return the current timestamp in UTC
function time_utc()
{
  # Surpress a notice
  if(function_exists('date_default_timezone_set'))
    date_default_timezone_set('UTC');
  
  # Return the current timestamp in UTC
  return time() - date('Z');
}

# Just in case sha1() isn't supported
if(!function_exists('sha1'))
{
  # Thanks to:
  # http://www.tecknik.net/sha-1/
  # http://www.php.net/manual/en/function.sha1.php#47609

  function sha1_str2blks_SHA1($str)
  {
    $strlen_str = strlen($str);
    $nblk = (($strlen_str + 8) >> 6) + 1;

    for($i = 0; $i < $nblk * 16; $i++)
      $blks[$i] = 0;

    for($i = 0; $i < $strlen_str; $i++)
    {
      $blks[$i >> 2] |= ord(substr($str, $i, 1)) << (24 - ($i % 4) * 8);
    }

    $blks[$i >> 2] |= 0x80 << (24 - ($i % 4) * 8);
    $blks[$nblk * 16 - 1] = $strlen_str * 8;

    return $blks;
  }

  function sha1_safe_add($x, $y)
  {
    $lsw = ($x & 0xFFFF) + ($y & 0xFFFF);
    $msw = ($x >> 16) + ($y >> 16) + ($lsw >> 16);

    return ($msw << 16) | ($lsw & 0xFFFF);
  }

  function sha1_rol($num, $cnt)
  {
    return ($num << $cnt) | sha1_zeroFill($num, 32 - $cnt);
  }

  function sha1_zeroFill($a, $b)
  {
    $bin = decbin($a);
    $strlen_bin = strlen($bin);
    $bin = $strlen_bin < $b ? 0 : substr($bin, 0, $strlen_bin - $b);

    for($i = 0; $i < $b; $i++)
      $bin = '0'. $bin;

    return bindec($bin);
  }

  function sha1_ft($t, $b, $c, $d)
  {
    if($t < 20)
      return ($b & $c) | ((~$b) & $d);
    if($t < 40)
      return $b ^ $c ^ $d;
    if($t < 60)
      return ($b & $c) | ($b & $d) | ($c & $d);
    return $b ^ $c ^ $d;
  }

  function sha1_kt($t)
  {
    if($t < 20)
      return 1518500249;
    if($t < 40)
      return 1859775393;
    if($t < 60)
      return -1894007588;

    return -899497514;
  }

  function sha1($str, $raw_output = false)
  {
    if($raw_output === true)
      return pack('H*', sha1($str, false));

    $x = sha1_str2blks_SHA1($str);
    $a =  1732584193;
    $b = -271733879;
    $c = -1732584194;
    $d =  271733878;
    $e = -1009589776;

    $x_count = count($x);

    for($i = 0; $i < $x_count; $i += 16)
    {
      $olda = $a;
      $oldb = $b;
      $oldc = $c;
      $oldd = $d;
      $olde = $e;
      for ($j = 0; $j < 80; $j++)
      {
        $w[$j] = ($j < 16) ? $x[$i + $j] : sha1_rol($w[$j - 3] ^ $w[$j - 8] ^ $w[$j - 14] ^ $w[$j - 16], 1);

        $t = sha1_safe_add(sha1_safe_add(sha1_rol($a, 5), sha1_ft($j, $b, $c, $d)), sha1_safe_add(sha1_safe_add($e, $w[$j]), sha1_kt($j)));
        $e = $d;
        $d = $c;
        $c = sha1_rol($b, 30);
        $b = $a;
        $a = $t;
      }
      $a = sha1_safe_add($a, $olda);
      $b = sha1_safe_add($b, $oldb);
      $c = sha1_safe_add($c, $oldc);
      $d = sha1_safe_add($d, $oldd);
      $e = sha1_safe_add($e, $olde);
    }
    return sprintf('%08x%08x%08x%08x%08x', $a, $b, $c, $d, $e);
  }
}

# In case their PHP version doesn't support scandir()
if(!function_exists('scandir'))
{
  function scandir($directory, $options = 0, $context = null)
  {
    # Its gotta be a directory...
    if(is_dir($directory) && ($dir = @opendir($directory)) !== false)
    {
      $listing = array();
      # Loop and get them ;)
      while(($file = readdir($dir)) !== false)
        $listing[] = $file;

      # Maybe the listing is the other way around? :o
      if($option !== 0)
        $listing = array_reverse($listing);

      return $listing;
    }
    else
      return false;
  }
}

# In case there's no file_get_contents()
if(!function_exists('file_get_contents'))
{
  function file_get_contents($filename, $flags = 0, $context = null, $offset = -1, $maxlen = -1)
  {
    # Try opening the database
    $fp = @fopen($filename, 'r');
    if($fp)
    {
      # Get the contents
      @flock($fp, LOCK_SH);
      $contents = fread($fp, filesize($filename));
      @flock($fp, LOCK_UN);
      @fclose($fp);
      return $contents;
    }
    else
    {
      # The file doesn't exist or failed to be opened
      return false;
    }
  }
}

# Header
function install_header()
{
  global $l, $steps, $step, $fields;

  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>', $l[$step. '_title'], ' - SnowCMS Installer</title>
	<link rel="stylesheet" type="text/css" href="http://localhost/snowcmsv1/Themes/default/style.css" media="all" />
  <meta name="robots" content="noindex"/>
<style type="text/css">
*
{
  margin: 0px;
}
body
{
  font-family: Verdana, sans-serif;
  font-size: 12px;
  background: #EDEDED;
}
#container
{
  width: 850px;
  background: transparent;
  margin: 20px auto auto auto;
}
#container .logo
{
  background: #2CA4DC;
}
#container .logo h1
{
  font-size: 20px;
  padding: 10px 0px 0px 10px;
}
#container .logo h3
 {
  font-size: 14px;
  font-weight: normal;
  font-style: italic;
  padding: 0px 0px 5px 20px;
}

/* Main Menu CSS */
#container .top_menu
{
	margin: 0;
	padding: 0;
	width: 100%;
	height: 22px;
	background: #1C3346;
}
#container .top_menu ul
{
  list-style: none;
  float: right;
  clear: both;
  background: #1C3346;
}
#container .top_menu ul li
{
  float: left;
  margin: 0;
  display: inline;
}
#container .top_menu ul li a
{
  display: block;
  padding: 6px 9px 6px 9px;
  color: #FFFFFF;
  text-decoration: none;
  font-weight: bold;
}
#container .top_menu ul li a:hover
{
  background: #FFFFFF;
  color: #000000;
  border: solid #1C3346;
  border-width: 1px 1px 0px 1px;
  padding: 5px 8px 6px 8px;
}

/* Main Body CSS */
#container .main_body
{
  background: #FFFFFF;
  padding: 2px;
}
#container .sidebar
{
  float: left;
  width: 182px;
}
#container .sidebar .item
{
  padding: 2px;
  border: 1px solid #1C3346;
  text-align: center;
  width: 100%;
  margin: 2px 0 2px 0;
  font-weight: bold;
  background: #FFF4C8;
}
#container .sidebar .done
{
  background: #D9FFB3 !important;
}
#container .sidebar h3
{

}
#container .content
{
  float: right;
  width: 654px;
  background: #FFFFFF;
}

/* Footer CSS */
#container .footer
{
  background: #1C3346;
  text-align: center;
  padding: 4px;
  color: #FFFFFF;
}
#container .footer a
{
  color: #DDDDDD;
  text-decoration: none;
}
#container .footer a:hover
{
  text-decoration: underline;
}

/* Links */
a
{
	color: #0C5BC8;
	background-color: transparent;
	text-decoration: none;
}
a:hover
{
	color: #3170CC;
	background-color: transparent;
	text-decoration: underline;
}

/* Database Selection */
.database_selection table
{
  border-collapse: collapse;
}
.database_selection th, .database_selection td
{
  padding: 2px;
}

/* No borders around linked images! Eww! */
img
{
  border: 0;
}
/* Fieldsets are ugly >_< But you gotta do them I guess :P */
fieldset
{
  border: 0px;
}
.left
{
  text-align: left;
}
.center
{
  text-align: center;
}
.right
{
  text-align: right;
}

/* Header stuff */
h1
{
  font-size: 20px;
}

/* Language and Database Selection */
.language_selection, .database_selection
{
  padding: 4px;
  text-align: center;
  margin: 4px;
}

/* Success */
.success
{
  background: #D9FFB3;
  padding: 4px;
  text-align: center;
  margin: 4px;
}
/* Message */
.message
{
  padding: 4px;
  text-align: center;
  margin: 4px;
}
/* Warning! */
.warning
{
  background: #FFF4C8;
  padding: 4px;
  text-align: center;
  margin: 4px;
}
/* Fatal */
.fatal
{
  background: #FFC6C6;
  padding: 4px;
  text-align: center;
  margin: 4px;
}
/* Error */
.error
{
  background: #FFC6C6;
  padding: 4px;
  text-align: center;
  margin: 4px;
}

/* MySQL Check */
.mysql_check
{
  margin-right: 10px;
}
</style>
<script type="text/javascript">
var _=_?_:{}
_.addclass=_.AC=function(e,c){if(_.HC(e))e.className+=\' \'+c}
_.ajax=_.X=function(u,f,d,x){x=window.ActiveXObject;x=new(x?x:XMLHttpRequest)(\'Microsoft.XMLHTTP\');x.open(d?\'POST\':\'GET\',u,1);x.setRequestHeader(\'Content-type\',\'application/x-www-form-urlencoded\');x.onreadystatechange=function(){x.readyState>3&&f?f(x.responseText,x):0};x.send(d)}
_.fx=_.A=function(v,n,c,u,y){u=0;return y=setInterval(function(){c(u/v);++u>v?clearInterval(y):0},n)}
_.array=_.Y=function(a){for(var b=a.length,c=[];b--;)c.push(a[b]);return c}
_.cls=_.C=function(n,d,y,k,h){y=(d?d:_.d).getElementsByTagName("*");h=[];for(k=y.length;k--;)_.I(n,y[k].className.split(" "))<0?0:h.push(y[k]);return h}
_.clone=_.O=function(j,c){if(c)return _.S(_.S(j),1);function p(){};p.prototype=j;return new p()}
_.on=_.E=function(e,t,f,r){if(e.attachEvent?(r?e.detachEvent(\'on\'+t,e[t+f]):1):(r?e.removeEventListener(t,f,0):e.addEventListener(t,f,0))){e[\'e\'+t+f]=f;e[t+f]=function(){e[\'e\'+t+f](window.event)};e.attachEvent(\'on\'+t,e[t+f])}}
_.extend=_.T=function(o,a,y){for(y in a)o[y]=a[y];return o}
_.fade=_.F=function(d,h,f,i){d=d==\'in\';_.A(f?f:15,i?i:50,function(a){a=(d?0:1)+(d?1:-1)*a;h.style.opacity=a;h.style.filter=\'alpha(opacity=\'+100*a+\')\'})}
_.id=_.G=function(e){return e.style?e:_.d.getElementById(e)}
_.hasclass=_.HC=function(e,c){return _.I(c,e.className.split(" "))>0}
_.entity=_.H=function(s,d,t){t=_.d.createElement(\'textarea\');t.innerHTML=s;return d?t.value:t.innerHTML}
_.include=_.N=function(s,e){e=_.d.createElement(\'script\');e.src=s;_.d.body.appendChild(e)}
_.index=_.I=function(v,a,i){for(i=a.length;i--&&a[i]!=v;);return i}
_.ns=_.N=function(n,p,r){p=n.split(\'.\');r=window;for(i in p){if(!r[p[i]])r[p[i]]={};r=r[p[i]]}return r}
_.query=_.Q=function(j,y,x){y=\'\';for(x in j)y+=\'&\'+x+\'=\'+encodeURIComponent(j[x]);return y.substr(1)}
_.queue=_.U=function(l,n){(n=function(){eval(l.splice(0,1)[0])})();return l}
_.ready=_.R=function(f){/(?!.*?ati|.*?kit)^moz|ope/i.test(navigator.userAgent)?_.E(_.d,\'DOMContentLoaded\',f):setTimeout(f,0)}
_.remove=_.V=function(e,o,x){x=_.I(e,o);x>0?o.splice(x,1):0}
_.d=document
_.json=_.S=function(j,d,t){if(d)return eval(\'(\'+j+\')\');if(!j)return j+\'\';t=[];if(j.pop){for(x in j)t.push(_.S(j[x]));j=\'[\'+t.join(\',\')+\']\'}else if(typeof j==\'object\'){for(x in j)t.push(x+\':\'+_.S(j[x]));j=\'{\'+t.join(\',\')+\'}\'}else if(j.split)j="\'"+j.replace(/\\\'/g,"\\\'")+"\'";return j}
_.tpl=_.M=function(t,d,x){for(x in d)t=t.split("{"+x+"}").join(d[x]);return t}
_.trim=_.TM=function(t){return t.replace(/^\s+|\s+$/g,\'\')}
_.unique=function(a){for(var b=a.length,c=[];b--;)_.I(a[b],c)>0?0:c.push(a[b]);return c}

function database_change(handle)
{
  document.getElementById(\'message\').innerHTML = \'&nbsp;\';
  document.getElementById(\'message\').style.background = \'transparent\';

  _.X(\'install.php?ajax=database-change\',
      function(data) {
        document.getElementById(\'db_settings\').innerHTML = data;
      },
      \'db=\' + handle.options[handle.selectedIndex].value);
}

function database_check_mysql(handle, override)
{
  _.X(\'install.php?ajax=check-database\',
      function(error) {
        if(error == \'', str_replace('\'', '\\\'', $l['mysql_error_tables']), '\')
        {
          document.getElementById(\'message\').innerHTML = \'<p>\' + error + \'</p>\';
          document.getElementById(\'message\').style.background = \'#FFF4C8\';
        }
        else if(error)
        {
          document.getElementById(\'message\').innerHTML = \'<p>\' + error + \'</p>\';
          document.getElementById(\'message\').style.background = \'#FFC6C6\';
        }
        else
          proceed(handle);
      },
      \'db_type=mysql&mysql_host=\' + handle.mysql_host.value + \'&mysql_user=\' + handle.mysql_user.value + \'&mysql_pass=\' + handle.mysql_pass.value + \'&mysql_db=\' + handle.mysql_db.value + \'&mysql_persist=\' + handle.mysql_persist.value + \'&tbl_prefix=\' + handle.tbl_prefix.value + \'&override=\' + Boolean(override));

  return false;
}

function database_check_sqlite(handle, override)
{
  _.X(\'install.php?ajax=check-database\',
      function(error) {
        if(error == \'', str_replace('\'', '\\\'', $l['sqlite_error_tables']), '\')
        {
          document.getElementById(\'message\').innerHTML = \'<p>\' + error + \'</p>\';
          document.getElementById(\'message\').style.background = \'#FFF4C8\';
        }
        else if(error)
        {
          document.getElementById(\'message\').innerHTML = \'<p>\' + error + \'</p>\';
          document.getElementById(\'message\').style.background = \'#FFC6C6\';
        }
        else
          proceed(handle);
      },
      \'db_type=sqlite&sqlite_path=\' + handle.sqlite_path.value + \'&sqlite_persist=\' + handle.sqlite_persist.value + \'&tbl_prefix=\' + handle.tbl_prefix.value + \'&override=\' + Boolean(override));

  return false;
}

function proceed(handle)
{
  handle.submit();

  document.getElementById(\'message\').innerHTML = \'<p>\' + \'', $l['database_installing'], '\' + \'</p>\';
  document.getElementById(\'message\').style.background = \'#D9FFB3\';

  elements = [\'proceed\'';

  foreach($fields as $field)
    echo ', \'', $field, '\'';

  echo '];

  for(var i in elements)
  {
      if(document.getElementById(elements[i]))
        document.getElementById(elements[i]).disabled = \'disabled\';
  }
}
</script>
</head>
<body>
<div id="container">
  <div class="logo">
    <h1>SnowCMS</h1>
    <h3>All the features you need but without the bloat</h3>
  </div>
  <div class="top_menu">
    <div class="break">
    </div>
  </div>
  <div class="main_body">
    <div class="sidebar">';

  $done = true;
  foreach(array_keys($steps) as $stp)
  {
    echo '
      <div class="item', $done ? ' done' : '', '">
        <p>', $l[$stp. '_sidebar'], '</p>
      </div>';

    # If this is the current step, we'll stop marking them as done
    if($step == $stp)
      $done = false;
  }

  echo '
    </div>
    <div class="content">

    <h1>', $l[$step. '_header'], '</h1>

    <p>', $l[$step. '_desc'], '</p>
    ';
}

# Footer
function install_footer()
{
  global $l;

  echo '

    </div>
    <div style="clear: both;"></div>
  </div>
  <div class="footer">
    <p>', $l['powered_by'], ' <a href="http://www.snowcms.com/" target="_blank" title="SnowCMS">SnowCMS v1.0</a></p>
  </div>
</div>
</body>
</html>';
}
?>
