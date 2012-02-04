<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//            Released under the Microsoft Reciprocal License             //
//                 www.opensource.org/licenses/ms-rl.html                 //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//       SnowCMS originally pawned by soren121 started in early 2008      //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//                  SnowCMS v2.0 began in November 2009                   //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                       File version: SnowCMS 2.0                        //
////////////////////////////////////////////////////////////////////////////

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
<?php
if(member()->is_logged())
{
	echo '
				<p>', l('Hey, %s!', '<a href="'. baseurl. '/index.php?action=profile">'. member()->display_name(). '</a>'), '</p>
				<p>',  l('<a href="%s" title="View your profile">My Profile</a> | <a href="%s" title="Log out of your account">Log out</a>', baseurl. '/index.php?action=profile', baseurl. '/index.php?action=logout&amp;sc='. member()->session_id()), '</p>';
}
else
{
	echo '
				<p>', l('Hey, Guest!'), '</p>
				<p>', l('<a href="%s" title="Create a new account">Register</a>', baseurl. '/index.php?action=register'), '</p>';
}
?>
			</div>
			<div class="break">
			</div>
		</div>
<?php
if(!admin_prompt_required(true) && empty(api()->context['cp_access_denied']))
{
	// Any important notifications?
	$important_notifications = api()->apply_filters('admin_important_notifications', array());
	if(count($important_notifications) > 0)
	{
		// Display them...
		foreach($important_notifications as $notification)
		{
			// A message is required.
			if(empty($notification['message']))
			{
				continue;
			}

			echo '
		<div style="margin: 3px auto;" class="', !empty($notification['attr_class']) ? $notification['attr_class'] : 'alert-message', '">
			', $notification['message'], '
		</div>';
		}
	}
}
?>
		<div id="link-tree">
			<p><?php echo empty(api()->context['cp_access_denied']) ? admin_link_tree(admin_prompt_required()) : '<a href="'. baseurl. '/">'. settings()->get('site_name', 'string'). '</a> &raquo; '. api()->context['error_title']; ?></p>
<?php
// Don't show the notifications area unless they are logged in.
if(!admin_prompt_required() && empty(api()->context['cp_access_denied']))
{
?>
			<div id="notifications">
				<p><a href="javascript:void(0);" onclick="openNotificationWindow();"><?php echo l('Notifications'); ?></a> (<?php echo count($GLOBALS['notifications']); ?>)</p>
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
if(!admin_prompt_required() && admin_show_sidebar() && empty(api()->context['cp_access_denied']))
{
	$groups_state = member()->data('admin_groups_state', 'array', array());

	echo '
			<div id="side-bar">
				<p class="cp-link"><a href="', baseurl('index.php?action=admin'), '" title="', l('Control Panel'), '">', l('Control Panel'), '</a></p>';

	$group_id = 0;
	foreach($GLOBALS['icons'] as $group_label => $items)
	{
		echo '
				<p class="sidebar-header" onclick="expandGroup(', $group_id, ');">', $group_label, '</p>
				<ul id="group_', $group_id, '" style="display: ', !empty($groups_state[$group_id]) ? 'block' : 'none', ';">';

		// Now for each link.
		foreach($items as $item)
		{
			echo '
					<li', !empty($item['children']) && count($item['children']) > 0 ? ' onmouseover="s.id(\''. $item['id']. '_dropdown\').style.display = \'block\';" onmouseout="s.id(\''. $item['id']. '_dropdown\').style.display = \'none\';"' : '', '><a href="', $item['href'], '" title="', !empty($item['title']) ? $item['title'] : '', '">', $item['label'], '</a>';

			// Does this section have children?
			if(!empty($item['children']) && count($item['children']) > 0)
			{
				echo '
						<ul id="', $item['id'], '_dropdown" style="display: none;">';

				$total = count($item['children']);
				$current = 0;
				foreach($item['children'] as $child)
				{
					$classes = array();
					if($current == 0)
					{
						$classes[] = 'is-first';
					}
					elseif($current + 1 == $total)
					{
						$classes[] = 'is-last';
					}

					echo '
							<li><a href="', $child['href'], '" title="', !empty($child['title']) ? $child['title'] : '', '"', count($classes) > 0 ? ' class="'. implode(' ', $classes). '"' : '', '>', $child['label'], '</a></li>';

					$current++;
				}

				echo '
						</ul>';
			}

			echo '</li>';
		}

		echo '
				</ul>';

		$group_id++;
	}

	echo '
			</div>
			<div id="side-content">';
}
?>