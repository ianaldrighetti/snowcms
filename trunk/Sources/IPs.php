<?php
//                      SnowCMS
//     Founded by soren121 & co-founded by aldo
// Developed by Myles, aldo, antimatter15 & soren121
//              http://www.snowcms.com/
//
//   SnowCMS is released under the GPL v3 License
//       which means you are free to edit and
//          redistribute it as your wish!
//
//                    IPs.php file

if(!defined("Snow"))
  die("Hacking Attempt...");

function ManageIPs() {
global $l, $settings, $db_prefix, $cmsurl;
  
  // To do: check if permissions allow IP (un)banning
  
  // Change IPs' details
  if (@$_REQUEST['change_ips']) {
    // Check if the IPs are valid
    foreach ($_REQUEST as $key => $value)
      if (substr($key,0,6) == 'ip_ip_') {
        $ip = clean(substr($key,6,strlen($key)));
        $new_ip = clean(@$_REQUEST['ip_ip_'.$ip]);
        if (!preg_match('/^[12]?[0-9]{1,2}\.[12]?[0-9]{1,2}\.[12]?[0-9]{1,2}\.[12]?[0-9]{1,2}$/',$new_ip) &&
            !preg_match('/^[12]?[0-9]{1,2}\.[12]?[0-9]{1,2}\.[12]?[0-9]{1,2}\.$/',$new_ip)) {
          $_SESSION['error'] = $l['manageips_error_invalid'];
          redirect('index.php?action=admin;sa=ips');
        }
      }
    // Save changes to current banned IPs
    foreach ($_REQUEST as $key => $value)
      if (substr($key,0,6) == 'ip_ip_') {
        $ip = clean(substr($key,6,strlen($key)));
        $new_ip = clean(@$_REQUEST['ip_ip_'.$ip]);
        $reason = clean(@$_REQUEST['ip_reason_'.$ip]);
        $ip = str_replace('_','.',$ip);
        sql_query("
        UPDATE {$db_prefix}banned_ips
        SET
          `ip` = '$new_ip',
          `reason` = '$reason'
        WHERE
          `ip` = '$ip'
        ");
      }
    // Ban new IP
    if (@$_REQUEST['new_ip']) {
      // To do: check if IP had already been banned
      $ip = clean($_REQUEST['new_ip']);
      $reason = clean($_REQUEST['new_ip_reason']);
      // Now the query for the banned IP :-D
      sql_query("
      INSERT INTO {$db_prefix}banned_ips
      (
        `ip`,
        `reason`
      )
      VALUES (
        '$ip',
        '$reason'
      )
      ") or die(mysql_error());
    }
    // Redirect back home
    redirect('index.php?action=admin;sa=ips');
  }
  // Unban an IP
  if (!empty($_REQUEST['uip'])) {
    // Get the IP ID they want to delete
    $uip = clean($_REQUEST['uip']);
    sql_query("DELETE FROM {$db_prefix}banned_ips WHERE `ip` = '$uip'");
    redirect('index.php?action=admin;sa=ips');
  }
  
  $settings['page']['title'] = @$l['manageips_title'];
  $result = sql_query("SELECT * FROM {$db_prefix}banned_ips");
  if ($settings['menus']['total'] = mysql_num_rows($result)) {
    while($row = mysql_fetch_assoc($result))
      $ips[] = $row;
    $settings['page']['ips'] = $ips;
    loadTheme('ManageIPs');
  }
  else
    loadTheme('ManageIPs','NoIPs'); 
}
?>