<?php
// default/ManageThemes.template.php by SnowCMS Dev's

function Main() {
global $settings, $l, $cmsurl, $user, $db_prefix;
  
  echo '<h1>'.$settings['page']['title'].'</h1>
    
    <h2>'.$l['managethemes_language_title'].'</h2>
    ';
  
  if (@$settings['error'])
    echo '<p>'.$settings['error'].'</p>
    ';
  
  echo '<form action="'.$cmsurl.'index.php?action=admin;sa=theme" method="post">
     <p>
      <input name="install-language" />
      <input type="submit" value="'.$l['managethemes_language_install'].'" />
     </p>
    </form>';
  
  $result = sql_query("SELECT * FROM {$db_prefix}languages");
  if (mysql_num_rows($result)) {
    while ($row = mysql_fetch_assoc($result))
      $languages[$row['lang_id']] = $row['lang_name'];
    
    
	  echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post"><p>
	  <select name="delete-language">
	  ';
	  
    foreach ($languages as $id => $name) {
      echo ' <option value="'.$id.'">'.$name.'</option>
	  ';
	  }
  	
  	echo '</select>
	  <input type="submit" value="'.$l['managethemes_language_delete'].'" />
	  </p></form>
	';
	}
	
	$result = sql_query("SELECT * FROM {$db_prefix}languages");
  if (mysql_num_rows($result)) {
    while ($row = mysql_fetch_assoc($result))
      $languages[$row['lang_id']] = $row['lang_name'];
    
    
	  echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post"><p>
	  <select name="default-language">
	  ';
	  
    foreach ($languages as $id => $name) {
      if ($id == $settings['language'])
        echo ' <option value="'.$id.'" selected="selected">'.$name.'</option>
	  ';
	    else
        echo ' <option value="'.$id.'">'.$name.'</option>
	  ';
	  }
  	
  	echo '</select>
	  <input type="submit" value="'.$l['managethemes_language_default'].'" />
	  </p></form>
	';
	}
}

?>