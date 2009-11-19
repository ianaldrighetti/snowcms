<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#     Admin Layout template, February 15, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function admin_home_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h1 class="admin_header">', $l['admin_header'], '</h1>
      <p class="small">', $l['admin_desc'], '</p><br />
      <table width="100%" class="admin_cp" cellspacing="2px" cellpadding="2px">
        <tr>
          <th style="width: 60%">', $l['admin_snowcms_news'], '</th><th style="width: 40%">', $l['admin_system_info'], '</th>
        </tr>
        <tr>
          <td>
            <div style="overflow: auto; height: 80px;" class="small">
              ', $page['snowcms_news'], '
            </div>
          </td>
          <td>
            <p>
              <strong>', $l['admin_software_version'], '</strong> <em>', $page['scmsVersion'], '</em><br />
              <strong>', $l['admin_latest_version'], '</strong> <em>', $page['current_version'], '</em><br />
              <strong>', $l['admin_php_version'], '</strong> <em>', $page['php_version'], '</em><br />
              <strong>', sprintf($l['admin_db_version'], $page['db_type']), '</strong> <em>', $page['db_version'], '</em><br />
              <strong>', $l['admin_operating_system'], '</strong> <em>', $page['operating_system'], '</em>
            </p>
          </td>
        </tr>
      </table>';

  # Upgrade warning?
  if($page['upgrade_needed'])
    echo '
      <div class="generic_error">
        <table class="center" style="text-align: center">
          <tr class="center" style="text-align: center">
            <td class="center" style="vertical-align: middle"><img src="', $settings['images_url'], '/message_alert.png"/></td><td style="font-weight: bold;">', $l['admin_upgrade_message'], '</td>
          </tr>
        </table>
      </div>';

  # Display the cool icons, however many or few their are.
  echo '
      <br />
      <table width="100%" style="text-align: center" class="center tableborder">';
  # Just a place holder...
  $current_row = 0;
  foreach($page['icons'] as $icon)
  {
    $current_row++;
    # Need to start a new row?
    if($current_row == 1)
      echo '
        <tr>';
    echo '
                <td style="width: 10%; text-align: center; vertical-align: middle; padding: 6px;"><a href="', $icon['href'], '" title="', $icon['title'], '"><img src="', $settings['images_url'], '/', $icon['image'], '" alt="" title="', $icon['title'], '"/></a></td>
                <td style="width: 40%; text-align: left; vertical-align: top; padding: 6px;"><h3><a href="', $icon['href'], '" title="', $icon['title'], '">', $icon['name'], '</a></h3><p>', $icon['desc'], '</p></td>';
    # Need to end a row?
    if($current_row == 2)
    {
      echo '
        </tr>';
      # Reset our counter...
      $current_row = 0;
    }
  }
  # Just incase the row was never closed...
  if($current_row == 1)
    echo '
        </tr>';
  echo '
      </table>';
}

function admin_verify_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;

  echo '
      <h3>', $l['admin_verify_header'], '</h3>
      <div class="', $page['login_failed'] ? 'generic_error' : 'generic_success', '">
        <p>', $page['login_failed'] ? $l['admin_verify_failed'] : $l['admin_verify_message'], '</p>
      </div>

      <fieldset>
        <form action="" method="post">
          <div class="center">
            <input name="passwrd" id="admin_passwrd" type="password" value="" /> <input type="submit" onClick="hashPassword(this.form);" value="', $l['admin_verify_button'], '" />
          </div>
          <input name="hashed_passwrd" type="hidden" id="hashed_passwrd" value="" />';

  # Let's not mess up you submitting a form, shall we? :P
  if(!empty($_POST) && count($_POST) > 0)
    foreach($_POST as $key => $value)
      if($key != 'passwrd' && $key != 'hashed_passwrd')
      echo '
          <input name="', htmlspecialchars($key, ENT_QUOTES), '" type="hidden" value="', htmlspecialchars($value, ENT_QUOTES), '" />';

  echo '
          <input name="verify" type="hidden" value="1" />
        </form>
      </fieldset>
      <script type="text/javascript">
        _.G(\'admin_passwrd\').focus();
      </script>';
}
?>