<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//                  Released under the GNU GPL v3 License                 //
//                    www.gnu.org/licenses/gpl-3.0.txt                    //
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

// Title: Blog plugin

// Add the icons to the control panel...
$api->add_filter('admin_icons', 'blog_add_admin_icons');

// Along with some permissions we would like to add.
$api->add_filter('member_group_permissions', 'blog_add_permissions');

// Add some sub actions in the control panel.
$api->add_event('action=admin&sa=blog_add', 'blog_add', dirname(__FILE__). '/blog_add.php');
$api->add_event('action=admin&sa=blog_manage', 'blog_manage', dirname(__FILE__). '/blog_manage.php');
$api->add_event('action=admin&sa=blog_settings', 'blog_settings', dirname(__FILE__). '/blog_settings.php');

// Now for some images.
$api->add_resource('blog', 'icon_add', dirname(__FILE__). '/images/blog_add.png');
$api->add_resource('blog', 'icon_add-small', dirname(__FILE__). '/images/blog_add-small.png');
$api->add_resource('blog', 'icon_manage', dirname(__FILE__). '/images/blog_manage.png');
$api->add_resource('blog', 'icon_manage-small', dirname(__FILE__). '/images/blog_manage-small.png');
$api->add_resource('blog', 'icon_settings', dirname(__FILE__). '/images/blog_settings.png');
$api->add_resource('blog', 'icon_settings-small', dirname(__FILE__). '/images/blog_settings-small.png');
$api->add_resource('blog', 'js_editor', dirname(__FILE__). '/js/editor.js');

/*
	Function: blog_add_admin_icons

	Parameters:
		array $icons

	Returns:
		array
*/
function blog_add_admin_icons($icons)
{
	global $member;

	$icons[l('Blog')] = array(
												array(
													'id' => 'blog_add',
													'href' => baseurl. '/index.php?action=admin&amp;sa=blog_add',
													'title' => l('Add a new blog post'),
													'src' => baseurl. '/index.php?action=resource&amp;area=blog&amp;id=icon_add',
													'label' => l('Add'),
													'show' => $member->can('manage_blog_posts'),
												),
												array(
													'id' => 'blog_manage',
													'href' => baseurl. '/index.php?action=admin&amp;sa=blog_manage',
													'title' => l('Manage blog posts'),
													'src' => baseurl. '/index.php?action=resource&amp;area=blog&amp;id=icon_manage',
													'label' => l('Manage'),
													'show' => $member->can('manage_blog_posts'),
												),
												array(
													'id' => 'blog_settings',
													'href' => baseurl. '/index.php?action=admin&amp;sa=blog_settings',
													'title' => l('Manage blog settings'),
													'src' => baseurl. '/index.php?action=resource&amp;area=blog&amp;id=icon_settings',
													'label' => l('Settings'),
													'show' => $member->can('manage_blog_settings'),
												),
											);

	return $icons;
}

/*
	Function: blog_add_permissions

	Parameters:
		array $permissions

	Returns:
		array
*/
function blog_add_permissions($permissions)
{
	$permissions[] = array(
										 'permission' => 'manage_blog_posts',
										 'label' => l('Add and manage blog posts'),
										 'subtext' => l('Allowed members will be able to create new and edit existing blog posts'),
									 );

	$permissions[] = array(
										 'permission' => 'manage_blog_settings',
										 'label' => l('Manage blog settings'),
										 'subtext' => l(''),
									 );

	return $permissions;
}
?>