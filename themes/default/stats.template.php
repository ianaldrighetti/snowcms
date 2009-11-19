<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#      Stats Layout template, January 16, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function stats_display_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
       <h1>', $l['stats_header'], '</h1>
       <p>', $l['stats_desc'], '</p>
       <br />';
  
  $odd = false;
  
  foreach($page['stats'] as $table => $rows)
  {
    echo '
       <table class="stats-table" style="float: ', ($odd ? 'right' : 'left'), ';">
         <caption>', $l['stats_'. $table], '</caption>';
    
    foreach($rows as $row => $value)
      echo '
         <tr><th>', $l['stats_'. $table. '_'. $row], '</th><td>', $value, '</td></tr>';
    
    echo '
       </table>';
    
    if($odd)
    {
     echo '
       <div style="clear: both;"></div>';
    }
    
    $odd = !$odd;
  }
  
  echo '
       <br />';
  
  $odd = false;
  
  foreach($page['top'] as $top => $values)
  {
    echo '
       <table class="stats-table" style="float: ', ($odd ? 'right' : 'left'), ';">
         <caption>', $l['stats_top_'. $top], '</caption>';
    
    foreach($values as $value)
      echo '
         <tr><th>'. $value['left']. '</th><th><div class="poll" style="width: ', $value['percent'], 'px;"></div></th><td>'. $value['right']. '</td></tr>';
    
    echo '
       </table>';
    
    if($odd)
    {
     echo '
       <div style="clear: both;"></div>';
    }
    
    $odd = !$odd;
  }
}
?>