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

/*
	Class: Component

	Installing and updating components are all handled through the Component
	class. A component is either a plugin or theme. The installation and
	updating process of a plugin and theme are very similar so they both can
	be handled by the same class.
*/
class Component
{
	// Variable: is_update
	private $is_update;

	// Variable: update_info
	private $update_info;

	/*
		Constructor: __construct

		Initializes all attributes to blanks.

		Parameters:
			none
	*/
	public function __construct()
	{
		// Just a couple of attributes.
		$this->is_update = false;
		$this->update_info = array();
	}

	/*
		Method: install

		Installs the component from the specified compressed package.

		Parameters:
			string $filename - The name of the file containing the component.
			string $type - The type of the component, either theme or plugin.
			array $options - An array containing options. See notes for details.

		Returns:
			array - Returns an array containing information about how the
							installation process went, or false on failure (if the file
							was not found or if the type is not supported).

		Note:
			The component will be installed in the themedir or plugindir directory
			depending on the type, of course.

			The file containing the component will not be deleted even if the
			installation was a success.

			The following indices will be returned:

				bool completed - Whether the installation was successfully
												 completed.*

				bool validated - Whether the specified file was validated (i.e. the
												 file was a valid plugin or theme and can be
												 extracted).*

				string validate_message - A string containing a message in regards
																	to the validation process.*

				bool status_proceed - Whether the specified file had its status
															checked with the SnowCMS Plugin or Theme
															Database and if it proceeded with
															installation.

				string status_message - A string containing a message in regards to
																the status checking process.

				string status_class - A string containing the value for the messages
															class attribute when displayed.

				bool is_compatible - Whether the component is compatible with the
														 current SnowCMS version, this will be true if
														 the component isn't compatible but the option
														 ignore_compatibility is set to true.

				string compatibility_message - A string containing a message in
																			 regards to the compatibility checking
																			 process.

				bool extracted - Whether the extraction step of the installation
												 process was a success.

				string extract_message - A string containing a message in regards to
																 the extraction process.

				string complete_message - A string containing a message in regards
																	to the last stage of installation. This
																	will have a message whether or not the
																	installation was completed successfully,
																	that is if it got passed the extraction
																	stage.

				array component_info - An array containing the components
															 information which is generated by plugin_load
															 (for a type of plugin) or theme_load (for a
															 type of theme).

			* Indicates that the index will always be set.

			The following are valid options for the $options parameter:

				bool ignore_status - Whether to ignore the status retrieved from the
														 status database and continue with the
														 installation process.

				bool ignore_compatibility - Whether to ignore the compatibility
																		problem and continue with the
																		installation process.
	*/
	public function install($filename, $type, $options = array())
	{
		$type = strtolower($type);

		// Make sure the file exists and that the type is supported.
		if(!file_exists($filename) || !is_file($filename) || !in_array($type, array('plugin', 'theme')))
		{
			return false;
		}

		// The options parameter must be an array.
		if(!is_array($options))
		{
			$options = array();
		}

		// The status array will contain all the information about the
		// installation process.
		$status = array(
								'completed' => false,
							);

		// We will need the Extraction class. Very handy.
		$extraction = api()->load_class('Extraction');

		// Try to validate the component package.
		$status['validated'] = ($type == 'plugin' ? ($is_valid = plugin_package_valid($filename)) : ($is_valid = theme_package_valid($filename))) && $extraction->is_supported($filename);

		// Did it not validate?
		if(empty($status['validated']))
		{
			// Sorry. There is nothing we can do.
			$status['validate_message'] = $is_valid === false ? l('The '. ($this->is_update ? 'update downloaded was' : 'file you have requested to install is'). ' not a valid '. $type. '.') : l('The file '. ($this->is_update ? 'downloaded' : 'you have requested to install'). ' could not be extracted because it is not a supported file type.');

			return $status;
		}

		// That went well.
		$status['validate_message'] = l('The '. $type. ($this->is_update ? ' update' : ''). ' package was successfully validated.');

		// We will need to get the components information file.
		$tmp_filename = tempnam(dirname(__FILE__), 'xml_');

		// So, extract the file.
		if(empty($tmp_filename) || !$extraction->read($filename, $type. '.xml', $tmp_filename))
		{
			$status['status_proceed'] = false;
			$status['status_message'] = l('The '. $type. '.xml file failed to be extracted from the '. $type. ($this->is_update ? ' update' : ''). ' package.');
			$status['status_class'] = 'red';

			// Delete the temporary file.
			unlink($tmp_filename);

			return $status;
		}
		else
		{
			// Load the components information.
			$component_info = $type == 'plugin' ? plugin_get_info($tmp_filename) : theme_get_info($tmp_filename);

			// Now check the components status.
			$component_status = $this->check_status($filename, $reason);

			// We need to interpret the meaning of the status retrieved.
			$response = $this->get_message($component_status, $component_info['name'], $reason, $type);

			// Should we proceed with the installation process?
			$install_proceed = !empty($options['ignore_status']) || $status == 'approved';
			api()->run_hooks('component_install_proceed', array(&$install_proceed, $type, &$options));

			$status['status_proceed'] = !empty($install_proceed);
			$status['status_message'] = $response['message'];
			$status['status_class'] = $response['attr_class'];

			// So did we want to proceed?
			if(empty($status['status_proceed']))
			{
				// Nope, sorry.
				unlink($tmp_filename);

				return $status;
			}
			else
			{
				// Yup, we are continuing! Now to check for compatibility.
				// Not much else to do but check.
				if(empty($options['ignore_compatibility']) && $component_info['is_compatible'] === false)
				{
					$status['is_compatible'] = false;
					$status['compatibility_message'] = l('The '. $type. ($this->is_update ? ' update for' : ''). ' &quot;%s&quot; is not compatible with your version of SnowCMS.', $this->is_update ? $this->update_info['name'] : $component_info['name']);
					unlink($tmp_filename);

					return $status;
				}
				else
				{
					// Everything is okay, or at least you chose to ignore it.
					$status['is_compatible'] = true;

					if($component_info['is_compatible'] !== false)
					{
						$status['compatibility_message'] = l('The '. $type. ($this->is_update ? ' update for' : ''). ' &quot;%s&quot; is compatible with your version of SnowCMS.', $this->is_update ? $this->update_info['name'] : $component_info['name']);
					}
					else
					{
						$status['compatibility_message'] = l('The '. $type. ($this->is_update ? ' update for' : '').' &quot;%s&quot; is not compatible with your version of SnowCMS. Proceeeding with installation anyways.', $this->is_update ? $this->update_info['name'] : $component_info['name']);
					}

					// If this is an update we will extract the package in the
					// components directory.
					if($this->is_update)
					{
						$componentdir = $this->update_info['directory'];

						// Generate a name for the directory which will contain the
						// contents of the compressed package.
						$name = '~temp';

						if(file_exists($componentdir. '/'. $name. '/'))
						{
							$count = 2;
							while(file_exists($componentdir. '/'. $name. $count. '/'))
							{
								$count++;
							}

							$name .= $count;
						}
					}
					else
					{
						// We are just about done. We will now attempt to extract the
						// component from its package. First let's get a directory name
						// that is appropriate, and not taken.
						$name = sanitize_filename($component_info['name']);

						// Which directory are we storing this component in?
						$componentdir = $type == 'plugin' ? plugindir : themedir;

						// As we said, find a name that isn't taken.
						if(file_exists($componentdir. '/'. $name. '/'))
						{
							$count = 2;
							while(file_exists($componentdir. '/'. $name. ' ('. $count. ')/'))
							{
								$count++;
							}

							// Found one.
							$name .= ' ('. $count. ')';
						}
					}

					// Now we shall attempt to extract the package, well, maybe after
					// we try to make the directory.
					if(!file_exists($componentdir. '/'. $name) && !@mkdir($componentdir. '/'. $name, 0755, true))
					{
						$status['extracted'] = false;
						$status['extract_message'] = l('Please make sure the '. $type. ($this->is_update ? '&#039;s' : ''). ' directory is writable and try '. ($this->is_update ? 'updat' : 'install'). 'ing the '. $type. ' again.');
						unlink($tmp_filename);

						return $status;
					}
					// Okay, now we can try to extract the package.
					elseif(!$extraction->extract($filename, $componentdir. '/'. $name))
					{
						// Well, this is embarrassing. This really shouldn't have
						// happened. But oh well.
						$status['extracted'] = false;
						$status['extract_message'] = l('The '. $type. ($this->is_update ? ' update' : ''). ' package could not be extracted due to an unknown error.');
						unlink($tmp_filename);
						recursive_unlink($componentdir. '/'. $name);

						return $status;
					}
					else
					{
						$status['extracted'] = true;
						$status['extract_message'] = l('The '. $type. ($this->is_update ? ' update' : ''). ' was successfully extracted.');

						// Even though we checked already, we want to check again and
						// make sure the component is valid.
						if(($type == 'plugin' && plugin_load($componentdir. '/'. $name) === false) || ($type == 'theme' && theme_load($componentdir. '/'. $name) === false))
						{
							// Darn!
							$status['complete_message'] = l('The '. $type. ' '. ($this->is_update ? 'update' : 'installation'). ' failed because the'. ($this->is_update ? ' update' : ''). ' package is not a valid '. $type. '.');

							// Delete everything, then.
							unlink($tmp_filename);
							recursive_unlink($componentdir. '/'. $name);

							return $status;
						}
						else
						{
							// Sweet! We are done! Well, if this wasn't an update.
							if($this->is_update)
							{
								// In which case we need to copy the contents of the
								// temporary directory to its new home!
								if(!copydir($componentdir. '/'. $name, $componentdir))
								{
									// Uh oh!
									$status['complete_message'] = l('The '. $type. ' update failed due to an unknown error.');
									unlink($tmp_filename);
									recursive_unlink($componentdir. '/'. $name);

									return $status;
								}
								// Alright, is there an install.php file?
								elseif(file_exists($componentdir. '/install.php'))
								{
									// That'd be a yes.
									$GLOBALS['updating_from'] = $this->update_info['version'];

									require($componentdir. '/install.php');

									unlink($componentdir. '/install.php');
								}

								recursive_unlink($componentdir. '/'. $name);
							}
							elseif(file_exists($componentdir. '/'. $name. '/install.php'))
							{
								// Well, I guess we weren't done with installing either!
								require($componentdir. '/'. $name. '/install.php');

								// Delete it. We won't be needing it anymore.
								unlink($componentdir. '/'. $name. '/install.php');
							}

							$status['completed'] = true;
							$status['complete_message'] = l('The '. $type. ' was successfully '. ($this->is_update ? 'updated' : 'installed'). '.');
							$status['component_info'] = $type == 'plugin' ? plugin_load($componentdir. ($this->is_update ? '' : '/'. $name)) : theme_load($componentdir. ($this->is_update ? '' : '/'. $name));
							unlink($tmp_filename);

							// Grrr...
							$extraction->__destruct();

							return $status;
						}
					}
				}
			}
		}
	}

	/*
		Method: update

		Updates the component to the specified version, if no version is
		specified the component will be updated to the latest version available.

		Parameters:
			string $componentdir - The directory containing the component to be
														 updated.
			string $version - The version to update the component to, if updates
												should be checked manually set this to null.
			string $type - The type of the component, either theme or plugin.
			array $options - An array containing options. See notes for details.

		Returns:
			array - Returns an array containing information about the update
							process or false on failure (such as if the directory supplied
							does not contain a valid component of $type).

		Note:
			The following indices are returned:

				string completed - Whether the update process completed without
													 issue.*

				bool update_found - Whether an update was available for the
														component. This could mean one of two things if
														this is false: if a version was specified then
														the version specified was invalid or there was
														no update available.*

				string update_message - A string containing a message in regards to
																the update checking process.*

				bool downloaded - Whether the update was successfully downloaded.

				string download_message - A string containing a message in regards
																	to the update download process.

			Please see <Component::install>'s notes for the rest of the possible
			indices, along with possible values for the $options parameter.
	*/
	public function update($componentdir, $version, $type, $options = array())
	{
		global $func;

		$type = strtolower($type);

		// Make sure the directory exists and that the type is supported.
		if(!file_exists($componentdir) || !in_array($type, array('plugin', 'theme')) || ($type == 'plugin' && plugin_load($componentdir) === false) || ($type == 'theme' && theme_load($componentdir) === false))
		{
			return false;
		}

		// Just because.
		$componentdir = realpath($componentdir);

		// The options parameter must be an array.
		if(!is_array($options))
		{
			$options = array();
		}

		// The status array will contain all the information about the update
		// process.
		$status = array(
								'completed' => false,
							);

		// Load the components information.
		$component_info = $type == 'plugin' ? plugin_load($componentdir) : theme_load($componentdir);

		// Is this a theme? There can't be any updates if there isn't an update
		// URL specified.
		if($type == 'theme' && empty($component_info['update_url']))
		{
			// Sorry!
			$status['update_found'] = false;
			$status['update_message'] = l('Sorry, but this theme does not support automatic updates.');

			return $status;
		}

		// Did you want us to check for updates ourselves?
		if(empty($version))
		{
			// We may need to include a file.
			if($type == 'plugin' && !function_exists('admin_plugins_check_updates'))
			{
				require(coredir. '/admin/admin_plugins_manage.php');
			}
			elseif($type == 'theme' && !function_exists('admin_themes_check_updates'))
			{
				require(coredir. '/admin/admin_themes.php');
			}

			// I guess we will.
			$version = $type == 'plugin' ? admin_plugins_check_updates($componentdir) : admin_themes_check_updates($componentdir);

			// But did anything get returned?
			if(empty($version))
			{
				$status['update_found'] = false;
				$status['update_message'] = l('The '. $type. ' &quot;%s&quot; is already up to date.', $component_info['name']);

				return $status;
			}
		}
		else
		{
			// We shall verify the version you supplied, then.
			$http = api()->load_class('HTTP');

			// Set up the POST data we will be sending.
			$post_data = array('requesttype' => 'verifyversion', 'version' => $version);

			// Want to add some sort of update key or something?
			if($func['strlen'](api()->apply_filters(sha1($component_info['directory']). '_updatekey'), '') > 0)
			{
				$post_data['updatekey'] = api()->apply_filters(sha1($component_info['directory']). '_updatekey', '');
			}

			// For information on the SnowCMS Update Transmission Protocol see
			// http://code.google.com/p/snowcms/wiki/SUTP.
			$request = $http->request($type == 'plugin' ? (strtolower(substr($component_info['guid'], 0, 8)) == 'https://' ? $component_info['guid'] : 'http://'. $component_info['guid']) : $component_info['update_url'], $post_data);

			// Is that version not valid?
			if(empty($request) || trim(strtoupper($request)) == 'DOESNOTEIXST')
			{
				$status['update_found'] = false;
				$status['update_message'] = l('Sorry, but version %s of the '. $type. ' &quot;%s&quot; does not exist.', htmlchars($version), $component_info['name']);

				return $status;
			}
		}

		// We did find the update. Good.
		$status['update_found'] = true;
		$status['update_message'] = l('A new update for the '. $type. ' &quot;%s&quot; is available.', $component_info['name']);

		// Alright, so we have the version we are going to update to, now to
		// actually download the update itself.
		$http = api()->load_class('HTTP');

		// But first generate a file which we will download the update to.
		$tmp_filename = tempnam(dirname(__FILE__), 'update_');

		// Set up the POST data we will be sending.
		$post_data = array('requesttype' => 'download', 'version' => $version);

		// Want to add some sort of update key or something?
		if($func['strlen'](api()->apply_filters(sha1($component_info['directory']). '_updatekey'), '') > 0)
		{
			$post_data['updatekey'] = api()->apply_filters(sha1($component_info['directory']). '_updatekey', '');
		}

		// Alright, now we will download the update. Hopefully.
		if(empty($tmp_filename) || !$http->request($type == 'plugin' ? (strtolower(substr($component_info['guid'], 0, 8)) == 'https://' ? $component_info['guid'] : 'http://'. $component_info['guid']) : $component_info['update_url'], $post_data, 0, $tmp_filename))
		{
			// I guess we couldn't download the component.
			$status['downloaded'] = false;
			$status['download_message'] = l('An error occurred and update version %s of the '. $type. ' &quot;%s&quot; could not be downloaded. Please try again later.', htmlchars($version), $component_info['name']);

			return $status;
		}
		else
		{
			// Sweet! We downloaded it.
			$status['downloaded'] = true;
			$status['download_message'] = l('The update for the '. $type. ' &quot;%s&quot; was successfully downloaded.', $component_info['name']);

			// Good thing we don't have to go through this again!
			// We will set this attribute to true in order to let the install
			// method know it isn't actually installing anything, but updating.
			$this->is_update = true;

			// Oh, don't forget the component information!
			$this->update_info = $component_info;

			// Now call on install, along with the right parameters.
			$result = $this->install($tmp_filename, $type, $options);

			// Merge the result with the status array.
			$status = array_merge($status, $result);

			$extraction = api()->load_class('Extraction');

			// This shouldn't be required, but alas -- it is!
			$extraction->__destruct();

			// Whether or not the update was successful we will delete the
			// downloaded update.
			unlink($tmp_filename);

			// We are no longer updating.
			$this->is_update = false;
			$this->update_info = array();

			// And done.
			return $status;
		}
	}

	/*
		Method: check_status

		Checks the status of the specified file with the SnowCMS Component
		Status Database.

		Parameters:
			string $filename - The name of the file being checked.
			string &$reason - A reference parameter which will contain the reason
												for the status code returned.

		Returns:
			string - Returns a string containing a code identifying the status of
							 the specified file, null if a connection could not be made to
							 the status server or false due to some other issue (like the
							 file does not exist).

		Note:
			The status of a component is checked by sending a request to
			status.snowcms.com along with the SHA-1 hash of the files contents.

			The following are status codes which may be returned:

				approved - Reviewed and approved.

				disapproved - The component is known to the status database, however
											the component was declined the approved status (and a
											reason will be contained within $reason).

				pending - The component is known to the status database but the
									component has yet to be approved.

				unknown - The component is unknown to the status database.

				deprecated - A newer version of the component is available.

				malicious - The component contains malicious code, which is of
										course bad.

				insecure - The component has been identified to have security issues
									 such as a XSS (Cross-Site Scripting), SQL injection, or
									 some other vulnerability.

			The status server will return at least one line which will contain one
			of the status codes listed above, but any following lines will be
			considered a reason for the status code.
	*/
	public function check_status($filename, &$reason = null)
	{
		global $func;

		// Make sure the file exists.
		if(!file_exists($filename))
		{
			return false;
		}

		// The HTTP class will be able to make the request for us.
		$http = api()->load_class('HTTP');

		$response = $http->request(api()->apply_filters('component_status_url', 'http://status.snowcms.com/'), array('sha1' => sha1_file($filename)));

		// Did we get a response?
		if(!empty($response))
		{
			@list($status, $reason) = explode("\r\n", $response, 2);

			// Just to make sure.
			$status = strtolower(trim($status));

			// Make sure we know what they are talking about.
			if(!in_array($status, api()->apply_filters('component_status_codes', array('approved', 'disapproved', 'pending', 'unknown', 'deprecated', 'malicious', 'insecure'))))
			{
				// We will mark it as unknown, then.
				$status = 'unknown';
			}

			// If there was a reason make sure there is nothing funky going on.
			$reason = $func['strlen']($reason) > 0 ? htmlchars($reason) : false;

			// Now return the status code.
			return $status;
		}
		else
		{
			// We couldn't get a response. Maybe it is down?
			return null;
		}
	}

	/*
		Method: get_message

		Interprets the supplied status code into a more friendly message.

		Parameters:
			string $status - The status code.
			string $name - The name of the component.
			string $reason - The reason for the status code.
			string $type - The type of component, either plugin or theme.

		Returns:
			array - Returns an array containing the interpreted message.

		Note:
			The following indices are returned:

				string color - The recommended color of the interpreted message.

				string message - The actual message to be displayed.

				string attr_class - The recommended value for the messages class
														attribute, but this should not be used in
														tandem with color.
	*/
	public function get_message($status, $name, $reason, $type)
	{
		global $func;

		$type = strtolower($type);
		$status = strtolower($status);

		// Check a couple things out first.
		if($func['strlen']($status) == 0 || $func['strlen']($name) == 0 || !in_array($type, array('plugin', 'theme')))
		{
			return false;
		}

		$response = array();

		// Did you get an approved status? That's good.
		if($status == 'approved')
		{
			$response['color'] = 'green';
			$response['message'] = l('The '. $type. ' &quot;%s&quot; has been reviewed and approved by the SnowCMS '. $type. ' database.', $name);
			$response['attr_class'] = 'message-box';
		}
		elseif($status == 'pending')
		{
			// Well, the status database is aware of the component, but it hasn't
			// been reviewed yet.
			$response['color'] = '#1874CD';
			$response['message'] = l('The '. $type. ' &quot;%s&quot; is currently pending review at the SnowCMS '. $type. ' database.<br />Proceed at your own risk.', $name);
			$response['attr_class'] = 'alert-box';
		}
		elseif($status == 'deprecated')
		{
			$response['color'] = '#1874CD';
			$response['message'] = l('The '. $type. ' &quot;%s&quot; has been deprecated by a newer version, which is available at the <a href="http://'. $type. 's.snowcms.com/" target="_blank">SnowCMS '. $type. ' database</a>.<br />It is recommended you download the latest version instead of proceeding.', $name);
			$response['attr_class'] = 'alert-box';
		}
		elseif(in_array($status, array('disapproved', 'unknown', 'malicious', 'insecure')))
		{
			$response['color'] = '#DB2929';

			if($status == 'disapproved')
			{
				// It was reviewed but was denied the approval status.
				$response['message'] = l('The '. $type. ' &quot;%s&quot; has been reviewed and denied approval status by the SnowCMS '. $type. ' database.<br />Reason: %s<br />Proceed at your own risk.', $name, $func['strlen']($reason) > 0 ? $reason : l('None given.'));
			}
			elseif($status == 'unknown')
			{
				// Never heard of it.
				$response['message'] = l('The '. $type. ' &quot;%s&quot; is unknown to the <a href="http://'. $type. 's.snowcms.com/" target="_blank">SnowCMS '. $type. ' database</a>.<br />Proceed at your own risk.', $name);
			}
			elseif($status == 'malicious')
			{
				// Oh noes!
				$response['message'] = l('The '. $type. ' &quot;%s&quot; is malicious and it is <em>highly</em> recommended that you do not continue.<br />Reason: %s<br />Proceed with extreme caution.', $name, $func['strlen']($reason) > 0 ? $reason : l('None given.'));
			}
			elseif($status == 'insecure')
			{
				$response['message'] = l('The '. $type. ' &quot;%s&quot; is known to have security issues and it is not recommended you continue.<br />Reason: %s<br />Proceed at your own risk.', $name, $func['strlen']($reason) > 0 ? $reason: l('None given.'));
			}

			$response['attr_class'] = 'error-message';
		}

		// Maybe some plugin wants to do something?
		api()->run_hooks('component_get_message', array(&$response, $name, $reason, $type));

		// Make sure it worked.
		return !empty($response['color']) && !empty($response['message']) && !empty($response['attr_class']) ? $response : false;
	}
}
?>