<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}
?>	<h<?php echo substr($_SERVER['QUERY_STRING'], 0, 12) == 'action=admin' ? 3 : 1; ?>><?php echo api()->context['error_title']; ?></h<?php echo substr($_SERVER['QUERY_STRING'], 0, 12) == 'action=admin' ? 3 : 1; ?>>
	<p><?php echo api()->context['error_message']; ?></p>