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

// Title: Blog plugin - Add

/*
	Function: blog_add

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function.
*/
function blog_add()
{
	global $api, $member, $theme;

	$api->run_hooks('blog_add');

	# Trying to access something you can't? Not if I can help it!
	if(!$member->can('manage_blog_posts'))
	{
		admin_access_denied();
	}

	# Generate that form!
	blog_add_generate_form();
	$form = $api->load_class('Form');

	$theme->set_current_area('blog_add');
	$theme->add_js_file(array('src' => baseurl. '/index.php?action=resource&amp;area=blog&amp;id=js_editor'));

	$theme->set_title(l('Add new post'));

	$theme->header();

	echo '
	<h1><img src="', baseurl, '/index.php?action=resource&amp;area=blog&amp;id=icon_add-small" alt="" /> ', l('Add new post'), '</h1>';

	$form->show('blog_add_post');

	$theme->footer();
}

/*
	Function: blog_add_generate_form

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function.
*/
function blog_add_generate_form()
{
	global $api;

	$form = $api->load_class('Form');

	$form->add('blog_add_post', array(
																'action' => baseurl. '/index.php?action=admin&amp;sa=blog_add',
																'callback' => 'blog_add_handle',
																'method' => 'post',
																'submit' => l('Add post'),
															));

	$form->add_field('blog_add_post', 'subject', array(
																								 'type' => 'full-string',
																								 'length' => array(
																															 'min' => 1,
																															 'max' => 255,
																														 ),
																								 'value' => create_function('', '
																															return \'<input type="text" name="subject" value="\'. htmlchars(!empty($_REQUEST[\'subject\']) ? $_REQUEST[\'subject\'] : \'\'). \'" title="\'. l(\'Post subject\'). \'" style="width: 100%; font-size: 180%;" />\';'),
																							 ));

	$form->add_field('blog_add_post', 'message', array(
																								 'type' => 'full-string-html',
																								 'length' => array(
																															 'min' => 1,
																														 ),
																								 'value' => create_function('', '
																															return \'<a href="javascript:void(0);" onclick="ta.replaceSelection(\\\'yayness!!!\\\');">CLICK!!!</a>
																																			 <textarea name="message" id="message" style="width: 100%; height: 200px;">\'. htmlchars(!empty($_REQUEST[\'message\']) ? $_REQUEST[\'message\'] : \'\'). \'</textarea>
																																			 <script type="text/javascript">
																																				 var ta = new textarea(\\\'message\\\');
																																			 </script>\';'),
																							 ));
}

/*
	Function: blog_add_handle

	Parameters:
		array $data
		array &$errors

	Returns:
		bool
*/
function blog_add_handle($data, &$errors = array())
{
	global $api;
}
?>