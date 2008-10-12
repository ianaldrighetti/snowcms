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

// Are they not trying to get an image or CSS
if (!@$_REQUEST['type']) {
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
    './Themes/default/images/site.png',
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
  <link rel="stylesheet" href="install.php?type=style.css" type="text/css" media="screen" />
  <!--[if lte IE 6]><link rel="stylesheet" href="install.php?type=iefix.css" type="text/css" media="screen" /><![endif]-->
  <!--[if lte IE 7]><style type="text/css">#content {padding-left: 6px !important;}</style><![endif]-->
</head>
<body>
<div class="container">
  <div class="sidebar">
  <a href="install.php" title="'.$settings['site_name'].'">
    <img class="site_logo" src="install.php?type=site_logo.png" alt="'.$settings['site_name'].'" />
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
}
else {
  // Check if they are trying to get an image or the CSS
  switch (@$_REQUEST['type']) {
   case 'style.css': header('Content-Type: text/css'); echo '/*
                  Snowy Theme
     By The SnowCMS Team (www.snowcms.com)
                   style.css
  */
   
  body {
    background: #ededed;
    font-family: Verdana,Arial,Sans;
    font-size: 12px;
  }
  .container {
    width: 800px;
    margin-left: auto;
    margin-right: auto;
    background: url(\'install.php?type=containerbg.png\') #fff;
  }
  .header-right {
    background: url(\'install.php?type=headbar.png\') no-repeat;
    width: 619px;
    height: 35px;
    margin: 0px;
    padding: 0px;
    float: right;
  }
  .sidebar {
    color: #fff;
    float: left;
    width: 181px;
    overflow: hidden;
  }
  .content {
    float: right;
    background: #fff;
    color: #252525;
    padding-top: 10px;
    width: 579px;
    margin: 20px;
    margin-top: 0;
    display: inline;
  }
  .footer {
    padding: 5px 0 5px 0;
    margin-top: 10px;
    text-align: center;
    color: #000;
    background: #fff;
    width: 800px;
    clear: both;
  }
  h1 {
    font-size: large;
  }
  h2 {
    font-size: small;
  }
  h3 {
    font-size: x-small;
  }
  a {
    color: #1133EE;
  }
  img {
    border: 0;
  }
  textarea {
    font-family: Verdana,Arial,Sans;
    font-size: 12px;
  }
  .content .post-title {
    color: #252525;
    text-decoration: none;
    font-size: 1.2em;
    font-family: "Trebutchet MS",Verdana,Sans;
  }
  .content a:hover .post-title {
    text-decoration: none;
  }
  .content span.date {
    font-size: 0.8em;
    margin-right: 15px;
  }
  .content span.commentnum {
    font-size: 0.8em;
  }
  .content p.post {
    padding: 5px;
    margin-right: 10px;
    border: 1px solid #F0A21B;
  }
  .sidebar li {
    color: #fff;
    padding-top: 3px;
    font-size: 0.9em;
    list-style-type: square;
  }
  .sidebar a {
    color: #fff;
    font-size: 0.9em;
    text-decoration: none;
  }
  .sidebar a:hover {
    color: #5CA7DE;
  }
  .sidebar li:hover {
    color: #5CA7DE;
    font-weight: bold;
  }
  img.emoticon {
    vertical-align: middle;
  }
  th.border {
    border-style: solid;
    border-width: 1px;
  }
  input, textarea {
    border: 1px solid #DDDDDD;
  }
  select {
    width: 153px;
  }
  '; exit;
   case 'iefix.css': header('Content-Type: text/css'); echo '/*
                  Snowy Theme
     By The SnowCMS Team (www.snowcms.com)
                   iefix.css
  */
   
  #content, #sidebar {
    margin-top: -2px !important;
  }'; exit;
   case 'site_logo.png': header('Content-Type: image/png'); echo base64_decode('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'); exit;
   case 'headbar.png': header('Content-Type: image/png'); echo base64_decode('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'); exit;
   case 'containerbg.png': header('Content-Type: image/png'); echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAyAAAAABCAIAAACpCl0xAAAACXBIWXMAAA7DAAAOwwHHb6hkAAAAAXNSR0IArs4c6QAAAAd0SU1FB9gGDBAbAEI/FhAAAAAZdEVYdENvbW1lbnQAQ3JlYXRlZCB3aXRoIEdJTVBXgQ4XAAAAK0lEQVRIS2OUMXZjGAWjIYAjBB6f2TkaNqMhMBoCoyEwGgKjITAaAqSGAAD6VwL/0EFLHwAAAABJRU5ErkJggg=='); exit;
  }
}

// Send the output that was buffered
ob_end_flush();
?>