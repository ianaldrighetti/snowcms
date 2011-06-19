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

if(!defined('IN_SNOW'))
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
  */
  public function __construct()
  {
    $this->settings = array();
    $this->update_settings = array();

    $this->reload();

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
    // Load up the settings :)
    $result = db()->query('
      SELECT
        variable, value
      FROM {db->prefix}settings');

    $this->settings = array();
    while($row = $result->fetch_assoc())
    {
      $this->settings[$row['variable']] = $row['value'];
    }

    if(count($this->update_settings) > 0)
    {
      foreach($this->update_settings as $variable => $value)
      {
        $this->settings[$variable] = $value;
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
                     returned as.
      mixed $default - If the requested setting variable is not set
                       then this value will be returned, as is.

    Returns:
      mixed - Returns the value of the setting, NULL if the setting
               was not found.

    Note:
      The data types supported vary depending upon plugins. Plugins can
      add more data type by hooking into validation_construct, which
      more information is available in <Validation::add_type>.
  */
  public function get($variable, $type = null, $default = null)
  {
    $validation = api()->load_class('Validation');

    if(!empty($variable) && isset($this->settings[$variable]))
    {
      $value = $this->settings[$variable];
      $valid = $validation->data($value, !empty($type) ? $type : 'string');
    }

    return !empty($valid) ? $value : $default;
  }

  /*
    Method: set

    Adds/updates a settings value.

    Parameters:
      string $variable - The variable to add/update.
      string $value - The new value to set/update $variable to.
      string $type - The data type of the supplied value.

    Returns:
      bool - Returns true on success, false on failure. When it
             fails, that means the value was not of the specified type.

    Note:
      The data types supported vary depending upon plugins. Plugins can
      add more data type by hooking into validation_construct, which
      more information is available in <Validation::add_type>.
  */
  public function set($variable, $value, $type = null)
  {
    // Incrementing/decrementing?
    if(($value === '++' || $value === '--') && (!isset($this->settings[$variable]) || is_numeric($this->settings[$variable])))
    {
      // Change the current value, or make it, if it doesn't exist already.
      $this->update_settings[$variable] = !isset($this->settings[$variable]) ? ($value == '++' ? 1 : -1) : ($value == '++' ? $this->settings[$variable] + 1 : $this->settings[$variable] - 1);
      $this->settings[$variable] = $this->update_settings[$variable];

      // We are done...
      return true;
    }

    $validation = api()->load_class('Validation');

    // Make sure the data is valid.
    $valid = $validation->data($value, !empty($type) ? $type : 'string');

    if(empty($valid))
    {
      return false;
    }

    $this->update_settings[$variable] = is_bool($value) ? (!empty($value) ? 1 : 0) : $value;
    $this->settings[$variable] = $this->update_settings[$variable];

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
      of this object, a callback is registered on a shutdown hook to automatically
      save the settings.

  */
  public function save()
  {
    // Anything that needs actual updating though?
    if(count($this->update_settings) > 0)
    {
      $new_settings = array();
      foreach($this->update_settings as $variable => $value)
      {
        $new_settings[] = array($variable, $value);
      }

      // Now update (or add!) those settings XD
      db()->insert('replace', '{db->prefix}settings',
        array(
          'variable' => 'string-255', 'value' => 'string',
        ),
        $new_settings,
        array('variable'), 'save_settings');

      // No need to update these settings again ;)
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

		api()->run_hooks('post_init_settings');
	}

	return $GLOBALS['settings'];
}
?>