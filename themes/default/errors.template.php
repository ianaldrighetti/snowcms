<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#     Errors Layout template, February 1, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function errors_screen()
{
  global $l;

  echo '
      <h1>', $l['error_screen_header'], '</h1>
      <p>', $l['error_screen_desc'], '</p>';
}

function errors_session()
{
  global $l, $page;

  echo '
      <h1>', $l['error_session_header'], '</h1>
      <p>', $l['error_session_desc'], '</p>';
}
?>