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
  Class: Update

  The Update class facilitates the means for you guessed it, updating files,
  such as updating the SnowCMS system in its entirety, or a plugin.
*/
class Update
{
  # Variable: filename
  # The location of the update package.
  private $filename;

  /*
    Method: __construct
  */
  public function __construct()
  {
    $this->filename = null;
  }

  /*
    Method: set_filename

    Parameters:
      string $filename - The name of the file which contains the update
                         package.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function set_filename($filename)
  {
    if(!file_exists($filename) || !is_file($filename))
    {
      return false;
    }

    $this->filename = $filename;
    return true;
  }

  /*
    Method: step

    Does the specified step.

    Parameters:
      int $step - The step to do.
      array $options - Any options to pass to the step.

    Returns:
      array - Returns an array containing information which is dependent
              upon each individual step.
  */
  public function step($step, $options = array())
  {
    global $api;

    $step = (int)$step;
    if(empty($this->filename) || $step < 0 || !is_array($options))
    {
      return false;
    }

    # Downloading the update file, are we?
    if($step == 1)
    {
      # We don't need a checksum URL, but we do need a download url.
      if(empty($options['download_url']))
      {
        return false;
      }

      return $this->download_update($options);
    }
  }

  /*
    Method: download_update
  */
  private function download_update($options)
  {
    global $api;

    $http = $api->load_class('HTTP');
    $downloaded = $http->request($options['download_url'], isset($options['post_data']) ? $options['post_data'] : array(), 0, $this->filename);

    if(empty($downloaded))
    {
      return array('downloaded' => false);
    }
    else
    {
      # Check the checksum, if supplied.
    }
  }
}
?>