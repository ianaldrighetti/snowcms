<?php
if(!defined('INSNOW'))
{
  die('Nice try...');
}
?></div>
<div id="footer">
  <div id="version">
    <p><?php echo l('Powered by <a href="http://www.snowcms.com/" target="_blank" title="SnowCMS">SnowCMS</a> v%s.', settings()->get('version', 'string')); ?></p>
    <p><?php echo l('Page created in %s seconds with %u queries.', round(microtime(true) - starttime, 3), db()->num_queries); ?></p>
  </div>
  <div id="jump_to">
    <form action="#" method="post" onsubmit="return false;">
      <select name="jump_to_select" onchange="this.form.go.click();">
        <option value=""><?php echo l('Control Panel'); ?></option>';
<?php
    // Anything we need to display? :P
    foreach($GLOBALS['icons'] as $icon_group => $icon)
    {
      echo '
        <optgroup label="', $icon_group, '">';

      foreach($icon as $i)
      {
        echo '
          <option value="', urlencode(htmlspecialchars_decode($i['href'])), '"', (!empty($i['id']) && admin_current_area() == $i['id'] ? ' selected="selected"' : ''), '>', $i['label'], '</option>';
      }

      echo '
        </optgroup>';
    }

    echo '
      </select>
      <input type="button" name="go" title="Go" value="Go" onclick="if(this.form.jump_to_select.value == \'\') { location.href = \'', baseurl, '/index.php?action=admin\'; } else { location.href = decodeURIComponent(this.form.jump_to_select.value); }" />';
?>
    </form>
  </div>
  <div class="break">
  </div>
</div>
</body>
</html>