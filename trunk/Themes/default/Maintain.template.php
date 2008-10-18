<?php
//              Snowy Theme
// By The SnowCMS Team (www.snowcms.com)
//        Maintain.template.php

if(!defined('Snow')) 
  die('Hacking Attempt...');

function Menu() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>', $l['maintain_header'], '</h1>
  <p>', $l['maintain_desc'], '</p>
  <br />';
  if(!empty($settings['alert'])) 
    echo '
  <script type="text/javascript">
    setTimeout(\'alert(\\\'', $settings['alert'], '\\\')\', 100);
  </script>';
  echo '
  <table width="100%" id="maintain_table">
    <tr>
      <td><a href="', $cmsurl, 'index.php?action=admin;sa=maintain;do=optimize">', $l['maintain_optimize'], '</a></td>
    </tr>
    <tr>
      <td><a href="', $cmsurl, 'index.php?action=admin;sa=maintain;do=recount">', $l['maintain_recount'], '</a></td>
    </tr>
  </table>
  <h2>', $l['maintain_backup'], '</h2>
  <form action="', $cmsurl, 'index.php?action=admin;sa=maintain;do=backup" method="post">
    <fieldset>
      <table>
        <tr>
          <td>', $l['maintain_backup_structure'], '</td><td><input name="struc" type="checkbox" value="1" checked="checked"/></td>
        </tr>
        <tr>
          <td>', $l['maintain_backup_data'], '</td><td><input name="data" type="checkbox" value="1" checked="checked"/></td>
        </tr>
        <tr>
          <td>', $l['maintain_backup_extended'], '</td><td><input name="extended" type="checkbox" value="1"/></td>
        </tr>
        <tr>
          <td>', $l['maintain_backup_gz'], '</td><td><input name="gz" type="checkbox" value="1" checked="checked"/></td>
        </tr>
        <tr>
          <td></td><td><input type="submit" value="', $l['maintain_backup_download'], '"/></td>
        </tr>
      </table>
    </fieldset>
  </form>';
}

function Optimize() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>', $l['maintain_optimize_header'], '</h1>
  <br />';
  if($settings['num_optimized']) {
    echo '
    <table width="100%">';
    foreach($settings['tables'] as $table)
      echo '
      <tr>
        <td>Table ', $table['name'], ' optimized ', $table['optimized'], ' bytes</td>
      </tr>';
    echo '
      <tr>
        <td>', $settings['it_took'], '</td>
      </tr>
    </table>';
  }
  else {
    echo '
    <p style="text-align: center;">', $l['maintain_optimize_none'], '</p>';
  }
  echo '
  <p style="text-align: right;">', $l['maintain_go_back'], '</p>';
}
?>