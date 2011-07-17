<?php
if(!defined('INSNOW'))
{
  die('Nice try...');
}

if(!admin_prompt_required() && admin_show_sidebar())
{
	echo '
			</div>
			<div class="break">
			</div>';
}
?>
		</div>
		<!-- /END CONTENT -->
		<div id="footer-container">
			<div id="footer-text">
				<p><?php echo l('Powered by <a href="http://www.snowcms.com/" target="_blank" title="SnowCMS">SnowCMS</a> v%s.', settings()->get('version', 'string')); ?></p>
				<p><?php echo l('Page created in %s seconds with %u queries.', round(microtime(true) - starttime, 3), db()->num_queries); ?></p>
			</div>
<?php
if(!admin_prompt_required())
{
?>
			<div id="jump-to">
				<form action="#" method="post" onsubmit="return false;">
					<select name="jump_to_select" onchange="this.form.go.click();">
						<option><?php echo l('Control Panel'); ?></option>
<?php
    // Display the drop down for quick navigation.
    foreach($GLOBALS['icons'] as $icon_group => $icon)
    {
      echo '
        <optgroup label="', htmlchars($icon_group), '">';

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
      <input type="submit" name="go" title="', l('Go'), '" value="', l('Go'), '" onclick="if(this.form.jump_to_select.value == \'', l('Control Panel'), '\') { location.href = \'', baseurl, '/index.php?action=admin\'; } else { location.href = decodeURIComponent(this.form.jump_to_select.value); }" />';
?>
				</form>
			</div>
<?php
}
?>
			<div class="break">
			</div>
		</div>
	</div>
</body>
</html>