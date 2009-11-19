<?php
#
# Default SnowCMS Theme (Snowy) By the SnowCMS developers
#
#     Settings Layout template, April 11, 2009
#

# No direct Access! >_<
if(!defined('InSnow'))
  die;

function maintain_backup_show()
{
  global $base_url, $l, $page, $settings, $theme, $theme_url, $user;
  
  echo '
      <h1>', $l['maintain_backup_header'], '</h1>
      <p>', $l['maintain_backup_desc'], '</p>
      <br />
      <fieldset>
        <form action="" method="post">
          <input type="hidden" name="process" value="backup" />
          <table cellspacing="0" cellpadding="4" width="100%" class="backup">
            <tr>
              <td class="left"><label for="comments">', $l['maintain_backup_comments'], '</label><br /><span class="small subtext">', $l['maintain_backup_comments_sub'], '</span></td>
              <td class="right"><textarea name="comments" id="comments" cols="40" rows="4"></textarea></td>
            </tr>
            <tr>
              <td class="left"><label for="structure">', $l['maintain_backup_structure'], '</label></td>
              <td class="right"><input type="checkbox" name="structure" id="structure" value="1" checked="checked" /></td>
            </tr>';
  
  if($page['options']['table_drop'] && $page['options']['table_ignore'])
  {
    echo '
            <tr>
              <td class="left"><label>', $l['maintain_backup_noexists'], '</label><br /><span class="small subtext">', $l['maintain_backup_noexists_sub'], '</span></td>
              <td class="right">
                <input type="radio" name="onexists" id="onexists_drop" value="drop" checked="checked" /> <label for="onexists_drop">', $l['maintain_backup_noexists_drop'], '</label>
                <input type="radio" name="onexists" id="onexists_ignore" value="ignore" /> <label for="onexists_ignore">', $l['maintain_backup_noexists_ignore'], '</label>
                <input type="radio" name="onexists" id="onexists_none" value="" /> <label for="onexists_none">', $l['maintain_backup_noexists_none'], '</label>
              </td>
            </tr>';
  }
  elseif($page['options']['table_drop'])
  {
    echo '
            <tr>
              <td class="left"><label for="onexists">', $l['maintain_backup_drop'], '</label><br /><span class="small subtext"', $l['maintain_backup_drop_sub'], '</span></td>
              <td class="right"><input type="checkbox" name="onexists" id="onexists" value="drop" checked="checked" /></td>
            </tr>';
  }
  elseif($page['options']['table_ignore'])
  {
    echo '
            <tr>
              <td class="left"><label for="onexists">', $l['maintain_backup_ignore'], '</label></td>
              <td class="right"><input type="checkbox" name="onexists" id="onexists" value="ignore" checked="checked" /></td>
            </tr>';
  }
  
  echo '
            <tr>
              <td class="left"><label for="data">', $l['maintain_backup_data'], '</label><br /><span class="small subtext" style="font-weight: bold;">', $l['maintain_backup_data_sub'], '</span></td>
              <td class="right"><input type="checkbox" name="data" id="data" value="1" checked="checked" /></td>
            </tr>';
  
  if($page['options']['extended_inserts'])
  {
    echo '
            <tr>
              <td class="left"><label for="extended_inserts">', $l['maintain_backup_extended_inserts'], '</label><br /><span class="small subtext">', $l['maintain_backup_extended_inserts_sub'], '</span></td>
              <td class="right"><input type="checkbox" name="extended_inserts" id="extended_inserts" /></td>
            </tr>
            <tr>
              <td class="left"><label for="num_extended">', $l['maintain_backup_num_extended'], '</label><br /><span class="small subtext">', $l['maintain_backup_num_extended_sub'], '</span></td>
              <td class="right"><input type="text" name="num_extended" id="num_extended" value="10" /></td>
            </tr>';
  }
  
  if($page['options']['gzip'])
  {
    echo '
            <tr>
              <td class="left"><label for="gzip">', $l['maintain_backup_gzip'], '</label><br /><span class="small subtext">', $l['maintain_backup_gzip_sub'], '</span></td>
              <td class="right"><input type="checkbox" name="gzip" id="gzip" /></td>
            </tr>';
  }
  
  echo '
          </table>
          <br />
          <p class="center"><input type="submit" value="', $l['maintain_backup_submit'], '" /></p>
        </form>
      </fieldset>';
}


function maintain_errors_show()
{
  global $l, $page, $settings, $user;

  echo '
      <h1>', $l['admin_maintain_error_header'], '</h1>
      <p>', $l['admin_maintain_error_desc'], '</p>
      <form action="" method="post">
        <table width="100%" class="center error_log" style="margin: auto;" cellspacing="0" cellpadding="4px">
          <tr class="header">
            <th class="left" width="30%">', $page['index'], '</th>
            <th class="right" width="50%">', $page['error_index'], '</th>
          </tr>';

  # Now output all those errors! It is what you came for! If any though...
  if(count($page['errors']))
  {
    echo '
          <tr>
            <td align="left" style="padding-left: 7px !important;" valign="middle"><input type="checkbox" value="0" onclick="invert_boxes(\'delete[]\');" title="', $l['admin_maintain_error_invert'], '" /></td>
            <td>
              <table align="right">
                <tr>
                  <td><input name="empty" type="submit" value="', $l['admin_maintain_error_empty'], '" onclick="return confirm(\'', $l['admin_maintain_error_delete_confirm'], '\');" /></td>
                  <td><input name="delete_selected" type="submit" value="', $l['admin_maintain_error_delete_selected'], '" onclick="return confirm(\'', $l['admin_maintain_error_delete_confirm'], '\');" /></td>', !empty($_GET['type']) ? '
                  <td><input name="filter_delete" type="submit" value="'. sprintf($l['admin_maintain_error_filter_delete'], $page['error_index_array'][$_GET['type']]['text']). '" onclick="return confirm(\''. $l['admin_maintain_error_delete_confirm']. '\');" /></td>' : '', '
                </tr>
              </table>
              <input name="filter" type="hidden" value="', $page['type'], '" />
              <input name="sc" type="hidden" value="', $user['sc'], '" />
            </td>
          </tr>';

    foreach($page['errors'] as $error)
    {
      echo '
          <tr class="error_container">
            <td colspan="2">
              <table width="100%" class="center error_table" style="margin: auto;" cellspacing="0">
                <tr>
                  <td width="4%" valign="top" align="center"><input name="delete[]" type="checkbox" value="', $error['id'], '" title="', $l['admin_maintain_error_mark_delete'], '" /></td>
                  <td width="96%" align="left">
                    <table width="100%">
                      <tr>
                        <td width="33%"><a href="', $error['member']['search'], '" title="', sprintf($l['admin_maintain_error_member_title'], $error['member']['name']), '"><img src="', $settings['images_url'], '/search.png" alt="" title="', sprintf($l['admin_maintain_error_member_title'], $error['member']['name']), '" /></a> ', !empty($error['member']['href']) ? '<a href="'. $error['member']['href']. '" title="'. sprintf($l['admin_maintain_error_view_profile'], $error['member']['name']). '">' : '', $error['member']['name'], !empty($error['member']['href']) ? '</a>' : '', '</td>
                        <td width="33%"><a href="', $error['member']['search_ip'], '" title="', sprintf($l['admin_maintain_error_ip_title'], $error['member']['ip']), '"><img src="', $settings['images_url'], '/search.png" alt="" title="', sprintf($l['admin_maintain_error_ip_title'], $error['member']['ip']), '" /> ', $error['member']['ip'], '</a></td>
                        <td width="33%">', $error['date'], '</td>
                      </tr>
                      <tr>
                        <td><a href="', $error['type']['search'], '" title="', $l['admin_maintain_error_view'], ' \'', $error['type']['text'], '\'"><img src="', $settings['images_url'], '/search.png" alt="" title="', $l['admin_maintain_error_view'], ' \'', $error['type']['text'], '\'" /></a> <strong>', $l['admin_maintain_error_type'], '</strong>: ', $error['type']['text'], '</td>
                        <td></td>
                        <td><strong>', $l['admin_maintain_error_line'], '</strong>: ', $error['line'], '</td>
                      </tr>
                      <tr>
                        <td colspan="3"><a href="', $error['search_file'], '" title="', $l['admin_maintain_error_file_title'], '"><img src="', $settings['images_url'], '/search.png" alt="" title="', $l['admin_maintain_error_file_title'], '" /></a> <strong>', $l['admin_maintain_error_file'], '</strong>: ', $error['file'], '</td>
                      </tr>
                      <tr>
                        <td colspan="3"><a href="', $error['search_url'], '" title="', $l['admin_maintain_error_url_title'], '"><img src="', $settings['images_url'], '/search.png" alt="" title="', $l['admin_maintain_error_url_title'], '" /></a> <a href="', $error['url'], '" target="_blank">', $error['url'], '</a></td>
                      </tr>
                      <tr>
                        <td colspan="3" style="padding: 4px;">', $error['error'], '</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>';
    }

    # No sense in outputting this twice if no errors are there to space it out :P
    echo '
          <tr class="header">
            <th class="left">', $page['index'], '</th><th class="right">', $page['error_index'], '</th>
          </tr>';
  }
  else
    # No errors? How truly sad!!!
    echo '
          <td class="center" style="padding: 4px;" colspan="2">', $l['admin_maintain_error_none'], '</td>';

  echo '
        </table>
      </form>';
}
?>