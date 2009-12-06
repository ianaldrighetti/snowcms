<?php
#########################################################################
#                             SnowCMS v2.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#                  Released under the GNU GPL v3 License                #
#                     www.gnu.org/licenses/gpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#                SnowCMS v2.0 began in November 2009                    #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 2.0                         #
#########################################################################

if(!defined('IN_SNOW'))
  die;

/*
  Class: Settings

  This class handles the loading and savings of settings for the system.
*/
class Settings
{
  # Variable: settings
  # Contains the most up-to-date settings and values.
  private $settings;

  # Variable: update_settings
  # Contains the setting variables and values which are to be updated
  # before the Settings object is destructed.
  private $update_settings;

  /*
    Constructor: __construct
  */
  public function __construct()
  {
    global $api;

    $this->settings = array();
    $this->update_settings = array();

    $this->reload();

    $api->add_hook('snow_exit', array($this, 'save'), 10, 0);
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
    global $db;

    # Load up the settings :)
    $result = $db->query('
      SELECT
        variable, value
      FROM {db->prefix}settings');

    $this->settings = array();
    while($row = $result->fetch_assoc())
      $this->settings[$row['variable']] = $row['value'];

    if(count($this->update_settings) > 0)
      foreach($this->update_settings as $variable => $value)
        $this->settings[$variable] = $value;
  }

  /*
    Method: get

    Gets the value of the specified variable from the settings
    attribute.

    Parameters:
      string $variable - The name of the setting

    Returns:
      string - Returns the value of the setting, NULL if the setting
               was not found.
  */
  public function get($variable)
  {
    return !empty($variable) && is_string($variable) && isset($this->settings[$variable]) ?  $this->settings[$variable] : null;
  }

  /*
    Method: set

    Adds/updates a settings value.

    Parameters:
      string $variable - The variable to add/update.
      string $value - The new value to set/update $variable to.

    Returns:
      void - Nothing is returned by this method.

    Note: You can also pass ++ or -- as a string in the value parameter
          to increment, or decrement the variables value.
  */
  public function set($variable, $value)
  {
    if(empty($variable) || !is_string($variable))
      return;

    $update_settings[$variable] = $value == '++' || $value == '--' ? ($value == '++' ? $settings[$variable] + 1 : $settings[$variable] - 1) : $value;
    $settings[$variable] = $update_settings[$variable];
  }

  /*
    Method: save

    Saves the settings to the database, if any settings were updated.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this method.

    Note: You do NOT need to call on this method yourself! In the construction
          of this object, a callback is registered on a shutdown hook to automatically
          save the settings.

  */
  public function save()
  {
    global $db;

    # Anything that needs actual updating though?
    if(count($this->update_settings) > 0)
    {
      $new_settings = array();
      foreach($this->update_settings as $variable => $value)
        $new_settings[] = array($variable, $value);

      # Now update (or add!) those settings XD
      $db->insert('replace', '{db->prefix}settings',
        array(
          'variable' => 'string-255', 'value' => 'string',
        ),
        $new_settings,
        array('variable'), 'save_settings');

      # No need to update these settings again ;)
      $this->update_settings = array();
    }
  }
}


/*
  Function: init_settings
  Sets the global $settings to a new settings object
*/
function init_settings()
{
  global $api, $settings;

  $settings = $api->load_class('Settings');
}
?>
