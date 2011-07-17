<?php
if(!defined('INSNOW'))
{
  die('Nice try...');
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?php theme_title(); ?></title>
  <?php theme_head(); ?>
</head>
<body>
	<div id="container">
		<div id="header-container">
			<h1><a href="<?php echo baseurl; ?>"><?php echo settings()->get('site_name', 'string'); ?></a></h1>
			<div id="member-box">
				<p><?php echo l('Hey, %s!', '<a href="'. baseurl. '/index.php?action=profile">'. member()->display_name(). '</a>'); ?></p>
				<p><?php echo l('<a href="%s" title="View your profile">My Profile</a> | <a href="%s" title="Log out of your account">Log out</a>', baseurl. '/index.php?action=profile', baseurl. '/index.php?action=logout&amp;sc='. member()->session_id()); ?></p>
			</div>
			<div class="break">
			</div>
		</div>
		<div id="link-tree">
			<p><a href="<?php echo baseurl. '/index.php?action=admin'; ?>">Control Panel</a></p>
<?php
// Don't show the notifications area unless they are logged in.
if(!admin_prompt_required())
{
?>
			<div id="notifications">
				<p><a href="javascript:void(0);" onclick="openNotificationWindow();"><?php echo l('Notifications'); ?></a> (0)</p>
			</div>
<?php
}
?>
			<div class="break">
			</div>
		</div>
		<div id="<?php echo api()->apply_filters('admin_theme_container_id', 'content-container'); ?>">
<?php
// Did you want to display the sidebar?
if(!admin_prompt_required() && admin_show_sidebar())
{
	echo '
			<div id="side-bar">';

	foreach($GLOBALS['icons'] as $group_label => $items)
	{
		echo '
				<p class="sidebar-header">', $group_label, '</p>
				<ul>';

		// Now for each link.
		foreach($items as $item)
		{
			echo '
					<li><a href="', $item['href'], '" title="', $item['title'], '">', $item['label'], '</a></li>';
		}

		echo '
				</ul>';
	}
	echo '
			</div>
			<div id="side-content">';
}
?>