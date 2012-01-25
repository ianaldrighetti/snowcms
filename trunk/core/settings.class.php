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
	Class: Settings

	This class handles the loading and savings of settings for the system.
*/
class Settings
{
	// Variable: settings
	// Contains the most up-to-date settings and values.
	private $settings;

	// Variable: update_settings
	// Contains the setting variables and values which are to be updated
	// before the Settings object is destructed.
	private $update_settings;

	/*
		Constructor: __construct

		Sets up the Settings class, such as loading all the settings from the
		database, along with registering a callback during the PHP shutdown
		process, during which settings are then saved.

		Parameters:
			none
	*/
	public function __construct()
	{
		$this->settings = array();
		$this->update_settings = array();

		// We will use the reload method to load everything up for the first
		// time... No need to copy-paste!
		$this->reload();

		api()->run_hooks('settings_construct');
		api()->add_hook('snow_exit', array($this, 'save'), 10, 0);
	}
	/*
		Method: reload

		Loads all the current settings from the database into the
		settings attribute. If there were any settings added/updated
		during the running of the system, those additions/updates
		are added to the settings array.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this method.
	*/
	public function reload()
	{
		// Load up the settings, which isn't all that complicated... Thankfully.
		$result = db()->query('
			SELECT
				variable, type, value
			FROM {db->prefix}settings');

		// Since we are reloading we can reset the settings array.
		$this->settings = array();
		while($row = $result->fetch_assoc())
		{
			// Save the settings information, which involves type casting the
			// value to its specified type.
			$this->settings[$row['variable']] = array(
																						'value' => $row['value'],
																						'type' => $row['type'],
																						'ready' => $row['type'] == 'string',
																					);
		}

		// Just because we reloaded the settings from the database doesn't mean
		// the current sessions modifications shouldn't be maintained!
		if(count($this->update_settings) > 0)
		{
			foreach($this->update_settings as $variable => $setting)
			{
				// If the setting is being deleted, then we should be sure it
				// doesn't get set again.
				if(!empty($setting['delete']))
				{
					unset($this->settings[$variable]);
				}
				// The variable may have been changed (++ or --).
				elseif(in_array('change', array_keys($setting)))
				{
					// Just make sure the setting exists...
					if($this->exists($variable))
					{
						$this->settings[$variable]['value'] += (int)$setting['change'];
					}
					else
					{
						$this->settings[$variable] = array(
																					 'value' => (int)$setting['change'],
																					 'type' => 'int',
																					 'ready' => true,
																				 );
					}
				}
				else
				{
					// Just set it!
					$this->settings[$variable] = $setting;
				}
			}
		}
	}

	/*
		Method: get

		Gets the value of the specified variable from the settings
		attribute.

		Parameters:
			string $variable - The name of the setting.
			string $type - The data type to have the setting value
										 returned as. If the type is not specified then its
										 current type will be used.
			mixed $default - If the requested setting variable is not set
											 then this value will be returned, as is.

		Returns:
			mixed - Returns the value of the setting, NULL if the setting
							was not found.

		Note:
			The data types supported vary depending upon plugins. Plugins can
			add more data type by hooking into typecast_construct, with more
			information available at <Typecast::add_type>.
	*/
	public function get($variable, $type = null, $default = null)
	{
		// First let's check if the setting even exists.
		if(!$this->exists($variable))
		{
			// Nope, it does not. So we will use the default value passed.
			return $type === null ? $default : typecast()->to($type, $default);
		}

		// Is this setting not 'ready' yet? We may need to do a type cast!
		if(empty($this->settings[$variable]['ready']))
		{
			$this->settings[$variable]['value'] = typecast()->to($this->settings[$variable]['type'], $this->settings[$variable]['value']);
			$this->settings[$variable]['ready'] = true;
		}

		// Good, the setting exists.
		return $type === null || $type == $this->settings[$variable]['type'] ? $this->settings[$variable]['value'] : typecast()->to($type, $this->settings[$variable]['value']);
	}

	/*
		Method: set

		Adds/updates a settings value.

		Parameters:
			string $variable - The variable to add/update.
			string $value - The new value to set/update $variable to.

		Returns:
			bool - Returns true on success, false on failure. When it
						 fails, that means the value was not of the specified type.

		Note:
			Please note that all values supplied will have their data type
			maintained across every page load.

			A value may be incremented or decremented by specifying a ++ or -- for
			the $value parameter.
	*/
	public function set($variable, $value)
	{
		// Should the value be incremented or decremented?
		if(($value === '++' || $value === '--') && (!isset($this->settings[$variable]['value']) || is_numeric($this->settings[$variable]['value'])))
		{
			// Change the current value, or make it, if it doesn't exist already.
			$this->update_settings[$variable] = !isset($this->settings[$variable]['value']) ? ($value == '++' ? 1 : -1) : ($value == '++' ? $this->settings[$variable]['value'] + 1 : $this->settings[$variable]['value'] - 1);
			$this->settings[$variable] = $this->update_settings[$variable];

			// If this setting doesn't exist yet, then we will need to create it.
			if(!$this->exists($variable))
			{
				// In which case we can be lazy!
				$this->set($variable, $value == '++' ? 1 : -1);
			}
			else
			{
				// Otherwise we will need to do a bit of work. But not much!
				$this->settings[$variable]['value'] += $value == '++' ? 1 : -1;

				// Maybe the update array already is showing that this setting will
				// be incremented/decremented...
				if(in_array($variable, array_keys($this->update_settings)) && in_array('change', array_keys($this->update_settings[$variable])))
				{
					// It is, so we shall add/subtract from that value!
					$this->update_settings[$variable]['change'] += $value == '++' ? 1 : -1;
					$this->update_settings[$variable]['ready'] = true;
				}
				else
				{
					// Nothing indicated that this setting was going to be incremented
					// or decremented, so we should do that now!
					$this->update_settings[$variable] = array(
																								'value' => null,
																								'type' => 'int',
																								'change' => $value == '++' ? 1 : -1,
																								'ready' => true,
																							);
				}
			}

			// Make sure this setting has not been marked for removal.
			unset($this->update_settings[$variable]['delete']);

			// We are done...
			return true;
		}

		// Save the update information.
		$this->update_settings[$variable] = array(
																					'value' => $value,
																					'type' => typecast()->typeof($value),
																					'ready' => true,
																				);

		$this->settings[$variable] = $this->update_settings[$variable];

		return true;
	}

	/*
		Method: exists

		Determines whether the specified setting exists.

		Parameters:
			string $name - The name of the setting.

		Returns:
			bool - Returns true if the specified setting exists and false if not.
	*/
	public function exists($name)
	{
		return in_array($name, array_keys($this->settings));
	}

	/*
		Method: remove

		Deletes the specified setting from the database.

		Parameters:
			string $name - The name of the setting to delete.

		Returns:
			bool - Returns true on success and false on failure (such as the
						 setting specified does not exist).

		Note:
			Please note that if <Settings::set> with the same setting name is
			called that the setting will no longer be marked for removal.
	*/
	public function remove($name)
	{
		// We can't remove something if it doesn't exist!
		if(!$this->exists($name))
		{
			return false;
		}

		// Simply mark it as deleted.
		$this->update_settings[$name] = array(
																			'value' => null,
																			'type' => null,
																			'delete' => true,
																			'ready' => false,
																		);

		// Since we don't delete the setting from the database instantly, we
		// will make it act as though it has been removed.
		unset($this->settings[$name]);

		return true;
	}

	/*
		Method: save

		Saves the settings to the database, if any settings were updated.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this method.

		Note:
			You do NOT need to call on this method yourself! In the construction
			of this object, a callback is registered on a shutdown hook to
			automatically save the settings.
	*/
	public function save()
	{
		// Do we even need to bother with updating the settings table?
		if(count($this->update_settings) > 0)
		{
			// There are a few different ways that a setting can be modified,
			// which is to simply update it with the specified value, to increment
			// or decrement the value, or to completely remove it from the
			// database.
			$update_settings = array();
			$change_settings = array();
			$remove_settings = array();
			foreach($this->update_settings as $variable => $setting)
			{
				// The delete key will be set and be true if the setting needs to
				// be deleted.
				if(!empty($setting['delete']))
				{
					$remove_settings[] = $variable;
				}
				// The change key tells us that the setting is being incremented or
				// decremented.
				elseif(in_array('change', array_keys($setting)))
				{
					// But there is no need to do anything if the change ends up being
					// 0!
					if($setting['change'] != 0)
					{
						$change_settings[$setting['change']][] = $variable;
					}
				}
				else
				{
					// Otherwise we're just updating it.
					$update_settings[] = array($variable, $setting['type'], typecast()->to('string', $setting['value']));
				}
			}

			// Since there are a few different ways settings can be updated, we
			// will need to see what types of changes were made to be able to
			// properly update the settings in the database.
			if(count($update_settings) > 0)
			{
				db()->insert('replace', '{db->prefix}settings',
					array(
						'variable' => 'string-255', 'type' => 'string-30', 'value' => 'string',
					),
					$update_settings,
					array('variable'), 'update_settings');
			}

			if(count($change_settings) > 0)
			{
				// We may have to do a few different queries... Possibly!
				foreach($change_settings as $change => $variables)
				{
					db()->query('
						UPDATE {db->prefix}settings
						SET value = value '. ($change > 0 ? '+' : '-'). ' {int:change}, type = \'int\'
						WHERE variable IN({array_string:variables})',
						array(
							'change' => $change,
							'variables' => $variables,
						), 'change_settings');
				}
			}

			if(count($remove_settings) > 0)
			{
				// So long, suckers!!!
				db()->query('
					DELETE FROM {db->prefix}settings
					WHERE variable IN({array_string:variables})',
					array(
						'variables' => $remove_settings,
					), 'remove_settings');
			}

			// All the updates have been processed and saved to the database, so
			// there is nothing we need to deal with now.
			$this->update_settings = array();
		}
	}
}

/*
	Function: settings

	Returns the current instance of the <Settings> object. If no Settings
	object has yet to be created, one will be created when this function is
	first called.

	Parameters:
		none

	Returns:
		object
*/
function settings()
{
	if(!isset($GLOBALS['settings']))
	{
		// Looks like we need to make an instance of the Settings class.
		$GLOBALS['settings'] = api()->load_class('Settings');
	}

	return $GLOBALS['settings'];
}
?>