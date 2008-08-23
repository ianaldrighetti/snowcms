<?php
// Check if already installed
define("Snow", true);
require_once('./config.php');
if ($scms_installed)
 die("Hacking Attempt...");

$files = array(
  './Languages',
  './Languages/english.language.php',
  './Sources',
  './Themes',
  './Themes/default',
  './config.php',
  './image.php',
  './index.php',
  './install.sql'
);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>SnowCMS - SnowCMS - Error</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="SnowCMS 0.7" />
	<link rel="stylesheet" href="Themes/default/style.css" type="text/css" media="screen" />
	<!--[if lte IE 6]><link rel="stylesheet" href="Themes//default/iefix.css" type="text/css" media="screen" /><![endif]-->
	<!--[if lte IE 7]><style type="text/css">#content {padding-left: 6px !important;}</style><![endif]-->
</head>
<body>
<div id="container">
	<div id="header">
		<div id="headerimg">
			<a class="headerlink" href="http://www.snowcms.com/" title="SnowCMS"><img class="headerimg" src="Themes/default/images/title.png" alt="SnowCMS" /></a>
		</div>
	</div>
	<div id="sidebar">
	<ul>
	  <li><a href="http://www.snowcms.com/" >SnowCMS Support</a></li>
	  <li><a href="http://code.google.com/p/snowcms/">Google Code</a></li>
    <li><a href="http://www.snowcms.com/forum/index.php?board=3.0">Dev Blogs</a></li>
	</ul>
	</div>
	<div id="content">
  <?php
  $step = $_REQUEST['step'] ? $_REQUEST['step'] : 0;
  // Check to see if some main files/directories are there...
  $nofile = array();
  foreach($files as $file) {
    if(!file_exists($file)) {
      $nofile[] = $file;
    }
  }
  if(count($nofile)>0) {
    echo '<p>The Following files/directories do not exist. Please make sure they are uploaded...</p>
    <p>';
    foreach($nofile as $file)
      echo $file.'<br />';
    echo '</p>';
  }
  else {
    if($step==0) {
      echo '
      <h1>Welcome!</h1>
      <p>Welcome to the SnowCMS Installer! Here is where you install your version of SnowCMS, which is quick, and easy! If you have any support questions, you can ask us at the <a href="http://www.snowcms.com/" target="_blank">SnowCMS</a> site.</p>
      <div align="center">  
        <form action="install.php?step=1" method="post">
          <table>
            <tr>
              <td>MySQL Host:</td><td><input name="mysql_host" type="text" value="localhost"/></td>
            </tr>
            <tr>
              <td>MySQL Username:</td><td><input name="mysql_user" type="text" value=""/></td>
            </tr>
            <tr>
              <td>MySQL Password:</td><td><input name="mysql_pass" type="password" value=""/></td>
            </tr>            
            <tr>
              <td>MySQL Database:</td><td><input name="mysql_db" type="text" value=""/></td>
            </tr>
            <tr>
              <td>MySQL Prefix:</td><td><input name="mysql_prefix" type="text" value="scms_"/></td>
            </tr>
            <tr>
              <td colspan="2"><input name="install" type="submit" value="Install!"/></td>
            </tr>
          </table>
        </form>
      </div>';
    }
    elseif($step==1) {
      // Can we even connect to MySQL? ._.
      $mysql_connect = @mysql_connect(@$_REQUEST['mysql_host'], @$_REQUEST['mysql_user'], @$_REQUEST['mysql_pass']);
      if(!$mysql_connect) {
        echo '
        <h1>Step 1</h1>
        <p>We were unable to connect to your MySQL server. Here is the error message: '.mysql_error().'<br />
        <a href="install.php">Go Back</a></p>';
      }
      else {
        // Sure we don't actually use mysql_select_db, but lets just check if they are allowed to access it ;)
        $mysql_select_db = @mysql_select_db($_REQUEST['mysql_db']);
        if(!$mysql_select_db) {
        echo '
        <h1>Step 1</h1>
        <p>We were unable to connect to your MySQL Database. Here is the error message: '.mysql_error().'<br />
        <a href="install.php">Go Back</a></p>';        
        }
        else {
          // Get the MySQL Queries from the SQL file :D
          $db_prefix = $_REQUEST['mysql_prefix'];
          $sqls = file_get_contents('./install.sql');
          $sqls = str_replace('{$db_prefix}', $db_prefix, $sqls);
          $mysql_queries = explode(";", $sqls);
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
          if(count($mysql_errors)>0) {
            echo '
            <h1>Step 1</h1>
            <p>The following MySQL errors occurred:<br />';
            foreach($mysql_errors as $error) {
              echo $error.'<br />';
            }
            echo '</p>';
          }
          else {
            $currentdir = dirname(__FILE__);
            $iurl = explode('/', $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
			unset($iurl[count($iurl)-1]);
			$iurl = implode('/', $iurl);
			$installpath = 'http://'.$iurl.'/';
            echo '<h1>You\'re almost done!</h1>
            <p>Your MySQL database has been populated with the initial data. Now you need to create your administrator account and a few other settings.</p>';
            echo '
            <div align="center">
            <form action="install.php?step=2" method="post">
              <table>
                <tr>
                  <td>Path to Source Directory</td><td><input name="source_dir" type="text" value="'.$currentdir.'/Sources"/></td>
                </tr>
                <tr>
                  <td>Path to Theme Directory</td><td><input name="theme_dir" type="text" value="'.$currentdir.'/Themes"/></td>
                </tr>
                <tr>
                  <td>Path to Language Directory</td><td><input name="language_dir" type="text" value="'.$currentdir.'/Languages"/></td>
                </tr>
                <tr>
                  <td>URL of SnowCMS Installation</td><td><input name="cmsurl" type="text" value="'.$installpath.'"/></td>
                </tr>
                <tr>
                  <td>URL of Themes Directory</td><td><input name="theme_url" type="text" value="'.$installpath.'Themes"/></td>
                </tr>
                <tr>
                  <td>Admin Username:</td><td><input name="admin_user" type="text" value=""/></td>
                </tr>
                <tr>
                  <td>Admin Password:</td><td><input name="admin_pass" type="password" value=""/></td>
                </tr>
                <tr>
                  <td>Admin Email:</td><td><input name="admin_email" type="text" value=""/></td>
                </tr>
                <tr>
                  <td colspan="2"><input name="step2" type="submit" value="Go to Step 2"/>
                </tr>
              </table>
              <input name="mysql_host" type="hidden" value="'.$_REQUEST['mysql_host'].'"/>
              <input name="mysql_user" type="hidden" value="'.$_REQUEST['mysql_user'].'"/>
              <input name="mysql_pass" type="hidden" value="'.$_REQUEST['mysql_pass'].'"/>
              <input name="mysql_db" type="hidden" value="'.$_REQUEST['mysql_db'].'"/>
              <input name="mysql_prefix" type="hidden" value="'.$_REQUEST['mysql_prefix'].'"/>
            </form>
            </div>';
          }
        }
      }
    }
    elseif($step==2) {
$config = '<?php
//                 SnowCMS
//           By aldo and soren121
//  Founded by soren121 & co-founded by aldo
//    http://snowcms.northsalemcrew.net
//
// SnowCMS is released under the GPL v3 License
// Which means you are free to edit it and then
//       redistribute it as your wish!
// 
//            config.php file 


if(!defined("Snow"))
  die("Hacking Attempt...");
  
// Your MySQL Information
$mysql_host = \''.$_REQUEST['mysql_host'].'\'; # Your MySQL Host, doubt you will change this
$mysql_user = \''.$_REQUEST['mysql_user'].'\'; # Your MySQL Username
$mysql_passwd = \''.$_REQUEST['mysql_pass'].'\'; # Your MySQL Password
$mysql_db = \''.$_REQUEST['mysql_db'].'\'; # Your MySQL DB
$mysql_prefix = \''.$_REQUEST['mysql_prefix'].'\'; # Prefix for your database

// Some SnowCMS Specific Settings
$source_dir = \''.$_REQUEST['source_dir'].'\'; # Path to your Source directory without trailing /!
$theme_dir = \''.$_REQUEST['theme_dir'].'\'; # Path to your Themes directory without trailing /!
$language_dir = \''.$_REQUEST['language_dir'].'\'; # Path to your Languages directory without trailing /!
$cmsurl = \''.$_REQUEST['cmsurl'].'\'; # URL to your SnowCMS Installation
$theme_url = \''.$_REQUEST['theme_url'].'\'; # URL to your SnowCMS Themes folder

/* Don\'t touch the stuff below! */
$db_prefix = \'`\'.$mysql_db.\'`.\'.$mysql_prefix;
$scms_installed = true;
?>';
    mysql_connect($_REQUEST['mysql_host'], $_REQUEST['mysql_user'], $_REQUEST['mysql_pass']);
    mysql_select_db($_REQUEST['mysql_db']);
    $admin = mysql_query("INSERT INTO {$_REQUEST['mysql_prefix']}members (`username`,`display_name`,`password`,`email`,`reg_date`,`reg_ip`,`group`,`activated`) VALUES('".addslashes(mysql_real_escape_string($_REQUEST['admin_user']))."','".addslashes(mysql_real_escape_string($_REQUEST['admin_user']))."','".md5($_REQUEST['admin_pass'])."','".addslashes(mysql_real_escape_string($_REQUEST['admin_email']))."','".time()."','".$_SERVER['REMOTE_ADDR']."','1','1')");
    echo '
    <h1>You\'re done!</h1>';
    if($admin) {
      echo '<p>Your settings have been sent to the MySQL database.</p>';
    }
    else {
      echo '<p>Something went wrong while trying to create your admin account. Info: '.mysql_error().'</p>';
    }
    if(!is_writeable('./config.php')) {
      echo '
      <p>Your config.php file was not writeable. To make your SnowCMS installation work, please open up your config.php file and put this in it:</p><br />
      <textarea cols="60" rows="30" readonly="readonly">
      '.htmlentities($config).'
      </textarea>';
    }
    else {
      $check = file_put_contents('./config.php', $config);
      echo '<p>Your config.php file has been set! You\'re ready to go!</p>';
    }
    echo '<p>Once you are done, please delete this file (install.php) and CHMOD config.php to 644. Thank you for using SnowCMS!</p>';

    }
  }
  ?>
	</div>
	<div id="footer">
	  <p>Powered by <a href="http://www.snowcms.com/" onClick="window.open(this.href); return false;">SnowCMS</a> 0.7 | Theme by <a href="http://www.snowcms.com/" onclick="window.open(this.href); return false;">the SnowCMS team</a></p>
	</div>
</div>
</body>
</html>
