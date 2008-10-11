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
    alert(\'', $settings['alert'], '\');
  </script>';
  echo '
  <table width="100%" id="maintain_table">
    <tr>
      <td><a href="', $cmsurl, 'index.php?action=admin;sa=maintain;do=optimize">', $l['maintain_optimize'], '</a></td>
    </tr>
    <tr>
      <td><a href="', $cmsurl, 'index.php?action=admin;sa=maintain;do=recount">', $l['maintain_recount'], '</a></td>
    </tr>
  </table>';
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