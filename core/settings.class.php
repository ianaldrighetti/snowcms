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

class Settings
{
  private $settings;
  private $update_settings;

  public function __construct()
  {
    global $api;

    $this->settings = array();
    $this->update_settings = array();

    $this->reload();

    $api->add_hook('snow_exit', array($this, 'save'), 10, 0);
  }

  public function reload()
  {
    global $db;

    # Load up the settings :)
    $result = $db->query('
      SELECT
        variable, value
      FROM {db->prefix}settings');

    while($row = $result->fetch_assoc())
      $this->settings[$row['variable']] = $row['value'];

    if(count($this->update_settings) > 0)
      foreach($this->update_settings as $variable => $value)
        $this->settings[$variable] = $value;
  }

  public function get($variable)
  {
    return isset($this->settings[$variable]) ?  $this->settings[$variable] : null;
  }

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

function init_settings()
{
  global $settings;

  $settings = new Settings();
}
?>