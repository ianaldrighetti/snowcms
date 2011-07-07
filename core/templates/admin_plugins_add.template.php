<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

    echo '
  <h1><img src="', theme()->url(), '/style/images/plugins_add-small.png" alt="" /> ', l('Add a new plugin'), '</h1>
  <p>', l('Plugins can be added to your site by entering the plugins globally unique identifier (the address at which the plugins package is downloaded) or by uploading the plugin package.'), '</p>';

    api()->context['form']->show('add_plugins_form');
?>