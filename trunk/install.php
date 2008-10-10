<?php
//                      SnowCMS
//     Founded by soren121 & co-founded by aldo
// Developed by Myles, aldo, antimatter15 & soren121
//              http://www.snowcms.com/
//
//   SnowCMS is released under the GPL v3 License
//       which means you are free to edit and
//           redistribute it as you wish!
//
//                  install.php file

/*
 *  This is the installer file for SnowCMS (http://www.snowcms.com/)
 *  Access this file in your browser to install SnowCMS
 *  Once you are done, make sure you delete this file immediately
 */

@session_start();

// Check if already installed
define("Snow", true);
require_once('./config.php');
if ($scms_installed)
 die(header("HTTP/1.1 404 Not Found"));
// Define some files that we should check if they exist, as they are pretty important ;)
$files = array(
  './Languages',
  './Languages/English.language.php',
  './Sources/Admin.php',
  './Sources/Board.php',
  './Sources/BoardIndex.php',
  './Sources/Captcha.php',
  './Sources/Core.php',
  './Sources/IPs.php',
  './Sources/Login.php',
  './Sources/Mail.php',
  './Sources/Main.php',
  './Sources/ManageForum.php',
  './Sources/Members.php',
  './Sources/Menus.php',
  './Sources/News.php',
  './Sources/Online.php',
  './Sources/Page.php',
  './Sources/Permissions.php',
  './Sources/PersonalMessages.php',
  './Sources/PHPMailer.php',
  './Sources/Post.php',
  './Sources/Profile.php',
  './Sources/Register.php',
  './Sources/Search.php',
  './Sources/Settings.php',
  './Sources/SMTP.php',
  './Sources/Topic.php',
  './Sources/TOS.php',
  './Sources/fonts',
  './Sources/fonts/Vera.ttf',
  './Sources/fonts/VeraBd.ttf',
  './Sources/fonts/VeraIt.ttf',
  './Themes',
  './Themes/default',
  './Themes/default/info.php',
  './Themes/default/Main.template.php',
  './Themes/default/Admin.template.php',
  './Themes/default/BoardIndex.template.php',
  './Themes/default/Board.template.php',
  './Themes/default/Error.template.php',
  './Themes/default/Forum.template.php',
  './Themes/default/Login.template.php',
  './Themes/default/ManageForum.template.php',
  './Themes/default/ManageIPs.template.php',
  './Themes/default/ManageMembers.template.php',
  './Themes/default/ManageMenus.template.php',
  './Themes/default/ManageNews.template.php',
  './Themes/default/ManagePages.template.php',
  './Themes/default/MemberList.template.php',
  './Themes/default/ModeratePMs.template.php',
  './Themes/default/News.template.php',
  './Themes/default/Online.template.php',
  './Themes/default/Page.template.php',
  './Themes/default/Permissions.template.php',
  './Themes/default/PersonalMessages.template.php',
  './Themes/default/Post.template.php',
  './Themes/default/Profile.template.php',
  './Themes/default/Register.template.php',
  './Themes/default/Search.template.php',
  './Themes/default/Settings.template.php',
  './Themes/default/Topic.template.php',
  './Themes/default/TOS.template.php',
  './Themes/default/style.css',
  './Themes/default/iefix.css',
  './Themes/default/images',
  './Themes/default/images/bbc_bold.png',
  './Themes/default/images/bbc_code.png',
  './Themes/default/images/bbc_image.png',
  './Themes/default/images/bbc_italic.png',
  './Themes/default/images/bbc_link.png',
  './Themes/default/images/bbc_quote.png',
  './Themes/default/images/bbc_strikethrough.png',
  './Themes/default/images/bbc_underline.png',
  './Themes/default/images/board_new.png',
  './Themes/default/images/board_old.png',
  './Themes/default/images/containerbg.png',
  './Themes/default/images/delete.png',
  './Themes/default/images/edit_post.png',
  './Themes/default/images/email.png',
  './Themes/default/images/female.png',
  './Themes/default/images/headbar.png',
  './Themes/default/images/male.png',
  './Themes/default/images/modify.png',
  './Themes/default/images/quote.png',
  './Themes/default/images/split.png',
  './Themes/default/images/star.png',
  './Themes/default/images/status_offline.png',
  './Themes/default/images/status_online.png',
  './Themes/default/images/site_logo.png',
  './Themes/default/images/topic_locked.png',
  './Themes/default/images/topic_new.png',
  './Themes/default/images/topic_old.png',
  './Themes/default/images/topic_own_new.png',
  './Themes/default/images/topic_own_old.png',
  './Themes/default/images/www.png',
  './Themes/default/emoticons',
  './Themes/default/emoticons/emoticons.php',
  './Themes/default/scripts',
  './Themes/default/scripts/bbcode.js',
  './Themes/default/scripts/bbcode_mini.js',
  './Themes/default/scripts/jquery.js',
  './Themes/default/scripts/jquery-pstrength.js',
  './config.php',
  './image.php',
  './index.php',
  './install.sql'
);
// Because HTML is sent before the script finishes, we need to buffer the output (It will be sent later)
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>Install - SnowCMS</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="powered-by" content="SnowCMS 0.7" />
  <link rel="stylesheet" href="Themes/default/style.css" type="text/css" media="screen" />
  <!--[if lte IE 6]><link rel="stylesheet" href="Themes/default/iefix.css" type="text/css" media="screen" /><![endif]-->
  <!--[if lte IE 7]><style type="text/css">#content {padding-left: 6px !important;}</style><![endif]-->
</head>
<body>
<div class="container">
  <div class="sidebar">
  <a href="install.php" title="'.$settings['site_name'].'">
    <img class="site_logo" src="Themes/default/images/site_logo.png" alt="'.$settings['site_name'].'" />
  </a>
  <ul>
    <li><a href="http://www.snowcms.com/">SnowCMS.com</a></li>
    <li><a href="http://snowcms.google.com/">Google Code</a></li>
    <li><a href="http://www.snowcms.com/forum.php">Support Forum</a></li>
    <li><a href="http://www.snowcms.com/forum.php?board=4">Developer Blogs</a></li>
  </ul>
  </div>
  <div class="header-right"></div>
  <div class="content">
    <?php
  $step = $_REQUEST['step'] ? $_REQUEST['step'] : 1;
  // Check to see if all default files and directories exist
  $nofile = array();
  foreach ($files as $file) {
    if (!file_exists($file)) {
      $nofile[] = $file;
    }
  }
  if (count($nofile) > 0) {
    echo '<p>The following files and directories do not exist. Please upload them before continuing:</p>
    <ul>
    ';
    foreach ($nofile as $file)
      echo '  <li>'.$file.'</li>
    ';
    echo '</ul>';
  }
  else {
    if ($step == 1) {
      if (!@$_SESSION['error'])
        echo '<h1>Welcome!</h1>
      <p>Welcome to the SnowCMS installer! Here is where you install your version of SnowCMS, which is quick, and easy. If you have any support questions, you can ask us at the <a href="http://www.snowcms.com/" target="_blank">SnowCMS</a> site.</p>
      <div align="center">
        <form action="install.php?step=2" method="post">
          <table>
            <tr>
              <td>MySQL Host:</td><td><input name="mysql_host" value="localhost" /></td>
            </tr>
            <tr>
              <td>MySQL Username:</td><td><input name="mysql_user" /></td>
            </tr>
            <tr>
              <td>MySQL Password:</td><td><input type="password" name="mysql_pass" /></td>
            </tr>            
            <tr>
              <td>MySQL Database:</td><td><input name="mysql_db" /></td>
            </tr>
            <tr>
              <td>MySQL Prefix:</td><td><input name="mysql_prefix" value="scms_" /></td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: center">
                <br />
                <input type="submit" value="Install" />
              </td>
            </tr>
          </table>
        </form>
      </div>';
      else
        echo '<h1>Step 1</h1>
      <p>We were unable to connect to your MySQL database. Your database information may have been incorrect, please re-enter it.</p>
      <p><b>Technical information:</b> '.$_SESSION['error'].'</p>
      <div align="center">
        <form action="install.php?step=2" method="post">
          <table>
            <tr>
              <td>MySQL Host:</td><td><input name="mysql_host" value="localhost" /></td>
            </tr>
            <tr>
              <td>MySQL Username:</td><td><input name="mysql_user" /></td>
            </tr>
            <tr>
              <td>MySQL Password:</td><td><input type="password" name="mysql_pass" /></td>
            </tr>            
            <tr>
              <td>MySQL Database:</td><td><input name="mysql_db" /></td>
            </tr>
            <tr>
              <td>MySQL Prefix:</td><td><input name="mysql_prefix" value="scms_" /></td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: center">
                <br />
                <input type="submit" value="Install" />
              </td>
            </tr>
          </table>
        </form>
      </div>';
      unset($_SESSION['error2']);
    }
    elseif ($step == 2) {
      if (@$_SESSION['step'] == 2 && empty($_REQUEST['mysql_host'])) {
        // Get the current directory for settings
        $currentdir = dirname(__FILE__);
        $iurl = explode('/', $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
        unset($iurl[count($iurl)-1]);
        $iurl = implode('/', $iurl);
        $installpath = 'http://'.$iurl.'/';
        // Show the form
        echo '<h1>Step 2</h1>
        
        ';
        if (empty($_SESSION['error2']))
          echo '<p>Your MySQL database has been populated with the initial data. Now you need to create your administrator account and a few other settings.</p>';
        else
          echo '<p><b>Error:</b> '.$_SESSION['error2'].'</p>';
        echo '
        <div align="center">
          <form action="install.php?step=3" method="post">
            <table>
              <tr><td>Path to Source Directory</td><td><input name="source_dir" value="'.$currentdir.'/Sources" /></td></tr>
              <tr><td>Path to Theme Directory</td><td><input name="theme_dir" value="'.$currentdir.'/Themes" /></td></tr>
              <tr><td>Path to Language Directory</td><td><input name="language_dir" value="'.$currentdir.'/Languages" /></td></tr>
              <tr><td>URL of SnowCMS Installation</td><td><input name="cmsurl" value="'.$installpath.'" /></td></tr>
              <tr><td>URL of Themes Directory</td><td><input name="theme_url" value="'.$installpath.'Themes" /></td></tr>
              <tr><td>Cookie Prefix:</td><td><input name="cookie_prefix" value="scms_" /></td></tr>
              <tr><td>Admin Username:</td><td><input name="admin_user" /></td></tr>
              <tr><td>Admin Password:</td><td><input type="password" name="admin_pass" /></td></tr>
              <tr><td>Admin Password (Again):</td><td><input type="password" name="admin_pass2" /></td></tr>
              <tr><td>Admin Email:</td><td><input name="admin_email" type="text" /></td></tr>
              <tr><td colspan="2"><input name="step3" type="submit" value="Finish Installation" /></tr>
            </table>
            <p>
              <input name="mysql_host" type="hidden" value="'.$_SESSION['mysql_host'].'" />
              <input name="mysql_user" type="hidden" value="'.$_SESSION['mysql_user'].'" />
              <input name="mysql_pass" type="hidden" value="'.$_SESSION['mysql_pass'].'" />
              <input name="mysql_db" type="hidden" value="'.$_SESSION['mysql_db'].'" />
              <input name="mysql_prefix" type="hidden" value="'.$_SESSION['mysql_prefix'].'" />
            </p>
          </form>
        </div>';
        
        // shouldn't store mysql user/pass in the form? security risk almost? encryption?
      }
      else {
        // Can we even connect to MySQL? ._.
        if ($mysql_connect = @mysql_connect(@$_REQUEST['mysql_host'], @$_REQUEST['mysql_user'], @$_REQUEST['mysql_pass']))
          $mysql_connect = @mysql_select_db($_REQUEST['mysql_db']);
        // We can't connect to the database
        if (!$mysql_connect) {
          $_SESSION['error'] = mysql_error();
          header('location: install.php');
          exit;
        }
        // We can connect to the database
        else {
          unset($_SESSION['error']);
          // Get the MySQL Queries from the SQL file :D
          $db_prefix = $_REQUEST['mysql_prefix'];
          $sqls = file_get_contents('./install.sql');
          // Replace a couple things so it is done right
          $sqls = str_replace('%current_time%',time(),str_replace('{$db_prefix}', $db_prefix, $sqls));
          // Separate the Queries the easy way xD
          $mysql_queries = explode(";", $sqls);
          // %semicolon% is used in place of semi colons to prevent them being exploded, so let's convert them now
          foreach ($mysql_queries as $key => $value)
            $mysql_queries[$key] = str_replace('%semicolon%',';',$value);
          // MySQL Errors? No thanks!
          $mysql_errors = array();
          $num_queries = count($mysql_queries);
          foreach($mysql_queries as $query) {
            $i++;
            if($i!=$num_queries) {
              $check = mysql_query($query);
              if(!$check)
                $mysql_errors[] = mysql_error();
            }
          }
          if (count($mysql_errors)) {
            echo '
            <h1>Step 1</h1>
            <p>The following MySQL errors occurred:<br />';
            foreach($mysql_errors as $error) {
              echo $error.'<br />';
            }
            echo '</p>';
          }
          else {
            // Now get details, such as directory paths, and administrative details
            $_SESSION['step'] = 2;
            $_SESSION['mysql_host'] = @$_REQUEST['mysql_host'];
            $_SESSION['mysql_user'] = @$_REQUEST['mysql_user'];
            $_SESSION['mysql_pass'] = @$_REQUEST['mysql_pass'];
            $_SESSION['mysql_db'] = @$_REQUEST['mysql_db'];
            $_SESSION['mysql_prefix'] = @$_REQUEST['mysql_prefix'];
            header('location: install.php?step=2');
            exit;
          }
        }
      }
    }
    elseif ($step == 3) {
      if (@$_SESSION['step'] == 3 && empty($_REQUEST['mysql_host']))
        echo '<h1>Step 3</h1>
        
        <p>Your config.php file was not writable. To make your SnowCMS installation work, please open up your config.php file and put this in it:</p><br />
        
        <textarea cols="90" rows="30" readonly="readonly">'.$_SESSION['config'].'</textarea>
        
        <p><a href=".">Click here</a> once you are done.</p>';
      else {
        // Create the config.php file which holds details about MySQL and paths we will need all the time
        $config = '<?php
//                      SnowCMS
//     Founded by soren121 & co-founded by aldo
// Developed by Myles, aldo, antimatter15 & soren121
//              http://www.snowcms.com/
//
//   SnowCMS is released under the GPL v3 License
//       which means you are free to edit and
//           redistribute it as you wish!
//
//                  config.php file


if (!defined("Snow"))
  die(header("HTTP/1.1 404 Not Found"));

// Your MySQL information
$mysql_host = \''.$_REQUEST['mysql_host'].'\'; # Your MySQL Host, doubt you will change this
$mysql_user = \''.$_REQUEST['mysql_user'].'\'; # Your MySQL Username
$mysql_passwd = \''.$_REQUEST['mysql_pass'].'\'; # Your MySQL Password
$mysql_db = \''.$_REQUEST['mysql_db'].'\'; # Your MySQL DB
$mysql_prefix = \''.$_REQUEST['mysql_prefix'].'\'; # Prefix for your database

// Misc
$cookie_prefix = \''.$_REQUEST['cookie_prefix'].'\'; # Prefix for cookies

// Some SnowCMS specific settings
$source_dir = \''.$_REQUEST['source_dir'].'\'; # Path to your Source directory without trailing /
$theme_dir = \''.$_REQUEST['theme_dir'].'\'; # Path to your Themes directory without trailing /
$language_dir = \''.$_REQUEST['language_dir'].'\'; # Path to your Languages directory without trailing /
$cmsurl = \''.$_REQUEST['cmsurl'].'\'; # URL to your SnowCMS Installation
$theme_url = \''.$_REQUEST['theme_url'].'\'; # URL to your SnowCMS Themes folder

// Don\'t touch the stuff below
$db_prefix = \'`\'.$mysql_db.\'`.\'.$mysql_prefix;
$scms_installed = true;
?>';
        if (@$_REQUEST["admin_user"]) {
          if (strlen(@$_REQUEST["admin_pass"]) > 4) {
            if (@$_REQUEST["admin_pass"] == @$_REQUEST["admin_pass2"]) {
              if (preg_match("/^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$/i",@$_REQUEST['admin_email'])) {
                unset($_SESSION['error']);
                unset($_SESSION['step']);
                unset($_SESSION['mysql_host']);
                unset($_SESSION['mysql_user']);
                unset($_SESSION['mysql_pass']);
                unset($_SESSION['mysql_db']);
                unset($_SESSION['mysql_prefix']);
                unset($_SESSION['error2']);
                // Now make the admin account
                mysql_connect($_REQUEST['mysql_host'], $_REQUEST['mysql_user'], $_REQUEST['mysql_pass']);
                mysql_select_db($_REQUEST['mysql_db']);
                $admin = mysql_query("INSERT INTO {$_REQUEST['mysql_prefix']}members (`username`,`display_name`,`password`,`email`,`reg_date`,`reg_ip`,`group`,`activated`) VALUES('".addslashes(mysql_real_escape_string($_REQUEST['admin_user']))."','".addslashes(mysql_real_escape_string($_REQUEST['admin_user']))."','".md5($_REQUEST['admin_pass'])."','".addslashes(mysql_real_escape_string($_REQUEST['admin_email']))."','".time()."','".$_SERVER['REMOTE_ADDR']."','1','1')");
                echo '
                <h1>You\'re done!</h1>';
                if($admin) {
                  echo '<p>Your settings have been sent to the MySQL database.</p>';
                }
                else
                  echo '<p>Something went wrong while trying to create your admin account. Info: '.mysql_error().'</p>';
              }
              else {
                $_SESSION['error2'] = 'Your email address was invalid.';
                header('location: install.php?step=2"');
                exit;
              }
            }
            else {
              $_SESSION['error2'] = 'Your password verification was wrong.';
              header('location: install.php?step=2"');
              exit;
            }
          }
          else {
            $_SESSION['error2'] = 'Your password is too short.';
            header('location: install.php?step=2"');
            exit;
          }
        }
        else {
          $_SESSION['error2'] = 'You didn\'t enter a username.';
          header('location: install.php?step=2');
          exit;
        }
        // Oh noes! The config.php file was NOT writable, so we will let them copy it and paste it without having to start all over again :)
        if (!is_writeable('./config.php')) {
          $_SESSION['step'] = 3;
          $_SESSION['config'] = htmlentities($config);
          header('location: install.php?step=3');
          exit;
        }
        else {
          // Yay! Write it to the config.php file, and your good to go!
          $check = file_put_contents('./config.php', $config);
          header('location: index.php');
          exit;
        }
      }
    }
  }
  ?>

  </div>
  <div class="footer">
    <p>Powered by <a href="http://www.snowcms.com/" onClick="window.open(this.href); return false;">SnowCMS 0.7</a>
       | Theme by <a href="http://www.snowcms.com/" onclick="window.open(this.href); return false;">The SnowCMS Team</a></p>
  </div>
</div>
</body>
</html>
<?php
// Send the output that was buffered
ob_end_flush();
?>