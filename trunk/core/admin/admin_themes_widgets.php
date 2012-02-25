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

// Title: Manage Widgets

if(!function_exists('admin_themes_widgets'))
{
	/*
		Function: admin_themes_widgets

		Provides the interface for managing the widgets displayed within the
		current theme.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.
	*/
	function admin_themes_widgets()
	{
		api()->run_hooks('admin_themes_widgets');

		// You can only manage widgets if you're allowed to, of course!
		if(!member()->can('manage_themes'))
		{
			admin_access_denied();
		}

		// Does this theme support any widgets?
		$theme_info = theme_load(themedir. '/'. settings()->get('theme', 'string', 'default'));

		if(count($theme_info['widgets']) > 0)
		{
			// Good, the theme supports widgets. Not that hard to support anyways.
			// ;-)
			api()->context['widget_support'] = true;

			// Now we need to get a list of the widgets currently installed.
			api()->context['widgets'] = array();
			$widgets = settings()->get('widgets', 'array', array());
			foreach(api()->return_widgets() as $widget_class)
			{
				$widget = new $widget_class();
				api()->context['widgets'][$widget_class] = array(
																										 'name' => $widget->name(),
																										 'description' => $widget->description(),
																										 'object' => $widget,
																									 );
			}

			// Hmm... Are we receiving an AJAX request? Perhaps they are adding or
			// moving a widget.
			if(!empty($_POST['request_type']) && $_POST['request_type'] == 'ajax')
			{
				$response = array('error' => false);

				// Make sure the session ID's are okay.
				if(empty($_POST['sid']) || $_POST['sid'] != member()->session_id())
				{
					$response['error'] = l('Session verification failed');
				}
				// Do you want to delete a widget?
				elseif(!empty($_POST['delete']) && $_POST['delete'] == 'true')
				{
					// We will need to make sure that this widget exists.
					$allocated_widgets = settings()->get('allocated_widgets', 'array', array());

					if(!array_key_exists('widget_id', $_POST) || empty($allocated_widgets[$_POST['widget_id']]))
					{
						$response['error'] = l('The specified widget does not exist. %s', $_POST['widget_id']);
					}
					else
					{
						// We will need to remove the widget from its assigned area.
						$widgets = settings()->get('widgets', 'array', array());

						// Just delete it, from the widgets and allocated widgets array.
						// This will free up that ID for use by another widget.
						unset($widgets[$allocated_widgets[$_POST['widget_id']]['area']][$_POST['widget_id']]);
						unset($allocated_widgets[$_POST['widget_id']]);

						settings()->set('allocated_widgets', $allocated_widgets);
						settings()->set('widgets', $widgets);
						settings()->save();

						$response['deleted'] = true;
						$response['widgetId'] = $_POST['widget_id'];
					}
				}
				// So, are we adding the widget?
				elseif(empty($_POST['move']) || $_POST['move'] == 'false')
				{
					// Nope, we're adding it, for the first time.
					// Make sure the widget they want to add is even supported.
					if(empty($_POST['widget_class']) || !isset(api()->context['widgets'][$_POST['widget_class']]))
					{
						$response['error'] = empty($_POST['widget_class']) ? l('No widget specified.') : l('The widget %s is not supported.', htmlchars($_POST['widget_class']));
					}
					// Make sure the specified area exists.
					elseif(empty($_POST['area_id']) || !isset($theme_info['widgets'][$_POST['area_id']]))
					{
						$response['error'] = empty($_POST['area_id']) ? l('No area specified.') : l('The area %s is not supported by the current theme.', htmlchars($_POST['area_id']));
					}
					else
					{
						// Which means we need to get this new widget a unique
						// identifier.
						$allocated_widgets = settings()->get('allocated_widgets', 'array', array());

						// Find a number which isn't used yet.
						$unique_id = 0;
						while(isset($allocated_widgets[$unique_id]))
						{
							$unique_id++;
						}

						// Now we will need to save a few things.
						$allocated_widgets[$unique_id] = array(
																							 'id' => $unique_id,
																							 'class' => $_POST['widget_class'],
																							 'area' => $_POST['area_id'],
																						 );

						// Save it back to the database.
						settings()->set('allocated_widgets', $allocated_widgets);

						// But save it, now!
						settings()->save();

						// Set up the instance of this widget.
						$widget_info = array(
														 'id' => $unique_id,
														 'class' => $_POST['widget_class'],
														 'options' => api()->context['widgets'][$_POST['widget_class']]['object']->default_options(),
													 );

						// Get ready to place the widget in the proper location.
						if(!isset($widgets[$_POST['area_id']]))
						{
							// Looks like there are no widgets here yet.
							$widgets[$_POST['area_id']] = array();
						}

						// If there are no widgets yet, then it doesn't matter where we
						// are told to place it.
						if(count($widgets[$_POST['area_id']]) == 0)
						{
							$widgets[$_POST['area_id']][$widget_info['id']] = $widget_info;
						}
						// Do they want the widget to go at the top?
						elseif(empty($_POST['after']) || $_POST['after'] == 'top')
						{
							$tmp_widgets[$widget_info['id']] = $widget_info;

							// Move all the current widgets in this area after the new
							// widget just added.
							foreach($widgets[$_POST['area_id']] as $tmp_id => $tmp)
							{
								$tmp_widgets[$tmp_id] = $tmp;
							}

							$widgets[$_POST['area_id']] = $tmp_widgets;
						}
						else
						{
							// We will insert the widget after the specified index.
							$widgets[$_POST['area_id']] = array_ainsert($widgets[$_POST['area_id']], $_POST['after'], $widget_info['id'], $widget_info);
						}

						// Save the updated widget information.
						settings()->set('widgets', $widgets);
						settings()->save();

						// We will need to provide them with the widgets information so
						// that it can add the widget without requiring a page reload.
						$response['widget_info'] = array(
																				 'id' => $widget_info['id'],
																				 'name' => api()->context['widgets'][$widget_info['class']]['name'],
																				 'description' => api()->context['widgets'][$widget_info['class']]['description'],
																				 'title' => !empty($widget_info['options']['title']) ? (strpos($widget_info['options']['title'], '<') !== false || strpos($widget_info['options']['title'], '>') !== false ? htmlchars($widget_info['options']['title']) : $widget_info['options']['title']) : api()->context['widgets'][$widget_info['class']]['name'],
																				 'form' => admin_themes_widgets_widget_form($widget_info, api()->context['widgets']),
																			 );

						// We will want to tell them where to place the item.
						$response['after'] = ($_POST['after'] == 'top') ? $_POST['area_id']. '_top' : $_POST['after'];
						$response['widget_area'] = $_POST['area_id'];
					}
				}
				else
				{
					die('not implemented');
				}

				// Send the response in JSON-form.
				echo json_encode($response);

				// We won't want to continue executing beyond this point.
				exit;
			}

			// Let's lay out the areas that the theme supports widgets in.
			api()->context['widget_areas'] = array();
			foreach($theme_info['widgets'] as $widget_area => $area_info)
			{
				// Make things a bit easier.
				$widget_area = $area_info['id'];

				api()->context['widget_areas'][$widget_area] = array(
																												 'label' => $area_info['label'],
																												 'widgets' => array(),
																											 );

				// Now load the widgets that are in this area into the widgets
				// array. That is if there are any.
				if(isset($widgets[$widget_area]))
				{
					foreach($widgets[$widget_area] as $widget_info)
					{
						// That is if the widget will even work.
						if(!isset(api()->context['widgets'][$widget_info['class']]))
						{
							// In which case we will just skip it -- we won't delete it
							// though, because you never know, it could come back!
							continue;
						}

						api()->context['widget_areas'][$widget_area]['widgets'][] = array(
																																					'id' => $widget_info['id'],
																																					'name' => api()->context['widgets'][$widget_info['class']]['name'],
																																					'description' => api()->context['widgets'][$widget_info['class']]['description'],
																																					'title' => !empty($widget_info['options']['title']) ? (strpos($widget_info['options']['title'], '<') !== false || strpos($widget_info['options']['title'], '>') !== false ? htmlchars($widget_info['options']['title']) : $widget_info['options']['title']) : api()->context['widgets'][$widget_info['class']]['name'],
																																					'form' => admin_themes_widgets_widget_form($widget_info, api()->context['widgets']),
																																				);
					}

					// Now that we have gone through the widgets in this area, we will
					// remove it -- that way we can see if there are any 'orphaned'
					// widgets, which are widgets that belong to an area that the
					// current theme doesn't support.
					unset($widgets[$widget_area]);
				}
			}

			// Alright, anything left over?
			api()->context['orphan_widgets'] = array();
			if(count($widgets) > 0)
			{
				foreach($widgets as $widget_area => $widgets)
				{
					foreach($widgets as $widget_info)
					{
						if(!isset(api()->context['widgets'][$widget_info['class']]))
						{
							// In which case we will just skip it -- we won't delete it
							// though, because you never know, it could come back!
							continue;
						}

						// Poor widget... It has no place to be displayed! We won't
						// delete it though, as each widget has set options, and the
						// administrator may not want to recreate the widget with the
						// new options. If we don't delete it, we can allow the
						// administrator to simply move the widget to a new and
						// supported widget area.
						api()->context['orphan_widgets'][] = array(
																									 'id' => $widget_info['id'],
																									 'name' => api()->context['widgets'][$widget_info['class']]['name'],
																									 'description' => api()->context['widgets'][$widget_info['class']]['description'],
																									 'title' => !empty($widget_info['options']['title']) ? (strpos($widget_info['options']['title'], '<') !== false || strpos($widget_info['options']['title'], '>') !== false ? htmlchars($widget_info['options']['title']) : $widget_info['options']['title']) : '',
																									 'form' => admin_themes_widgets_widget_form($widget_info, api()->context['widgets']),
																								 );
					}
				}
			}
		}
		else
		{
			// I guess there is no widget support in this theme. That sucks!
			api()->context['widget_support'] = false;
		}

		admin_current_area('widgets_manage_themes');

		theme()->add_link(array('rel' => 'stylesheet', 'type' => 'text/css', 'href' => theme()->url(). '/style/widgets.css'));
		theme()->add_js_file(array('src' => theme()->url(). '/js/widgets.js?'.time()));
		theme()->add_js_var('themeurl', theme()->url());
		theme()->add_js_var('widgets_pleaseWait', l('Please wait...'));
		theme()->add_js_var('widgets_moveHere', '&laquo; '. l('Move Widget Here'). ' &raquo;');
		theme()->add_js_var('widget_expand', l('Expand this widget'));
		theme()->add_js_var('widget_collapse', l('Collapse this widget'));
		theme()->add_js_var('widget_moveThis', l('Move this widget'));
		theme()->set_title(l('Manage Widgets'));

		theme()->render('admin_themes_widgets');
	}
}

if(!function_exists('admin_themes_widgets_widget_form'))
{
	/*
		Function: admin_themes_widgets_widget_form

		Generates the options form for the specified widget.

		Parameters:
			array $widget_info - An array containing the widgets class, ID and
													 options.
			array $widgets - An array containing all the currently loaded widgets
											 and their information.

		Returns:
			mixed - Returns a string containing the options form for the specified
							widget, and false on failure (the widget information supplied
							does not reference a valid widget within the $widgets
							parameter).
	*/
	function admin_themes_widgets_widget_form($widget_info, $widgets)
	{
		if(empty($widget_info['class']) || !array_key_exists('id', $widget_info) || empty($widgets[$widget_info['class']]))
		{
			return false;
		}

		// Generate the options form, with the proper options form and buttons.
		return '<form action="'. baseurl('index.php?action=admin&amp;sa=themes&amp;section=widgets&amp;widget='. urlencode($widget_info['class']). '&amp;id='. (int)$widget_info['id']). '" method="post" onsubmit="return Widgets.Save(this, \''. urlencode($widget_info['class']). '\', '. (int)$widget_info['id']. ');">'.
						 $widgets[$widget_info['class']]['object']->form($widget_info['options']).
					 '<p class="buttons"><input type="submit" name="save_widget" value="'. l('Save'). '" /> <input type="button" value="'. l('Delete'). '" onclick="Widgets.Delete(this, '. $widget_info['id']. ');" /></p>
					 </form>';
	}
}
?>