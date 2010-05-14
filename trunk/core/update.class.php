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
    Method: download

    Downloads the specified update package, and if supplied, the integrity of
    the downloaded package will be checked as well.

    Parameters:
      string $download_url - The URL at which the update package will be downloaded from.
      string $save_to - The complete path (including the files name as well) of where the
                        update package will be saved (downloading the package will fail if
                        the supplied path's directory doesn't exist, or if PHP does not have
                        write access to the specified path).
      string $checksum_url - The URL at which the checksum should be downloaded from. If the
                             length of the string is 32 characters, it will be assumed as MD5,
                             if it is 40 characters, SHA-1 is assumed. If the number of characters
                             is neither, then checking the files integrity will fail.

    Returns:
      array - Returns an array containing a downloaded (true if the file was downloaded successfully)
              and valid (true if the checksum downloaded matched that of the downloaded package, this
              index will be set to null).
  */
  public function download($download_url, $save_to, $checksum_url = null)
  {
    global $api;

    # No download or save to URL?
    if(empty($download_url) || empty($save_to))
    {
      # Well we can't download it!
      return false;
    }

    # Attempt to open the save to path...
    $fp = fopen($save_to, 'w');

    # Did it work?
    if(empty($fp))
    {
      # No it did not, so the download failed.
      return array(
                'downloaded' => false,
                'valid' => !empty($checksum_url) ? false : null,
              );
    }

    # Close it... We just needed to check.
    fclose($fp);

    # We need the HTTP class.
    $http = $api->load_class('HTTP');

    # Now download the update package.
    $downloaded = $http->request($download_url, array(), 0, $save_to);

    # Did it download?
    if(empty($downloaded))
    {
      return array(
                'downloaded' => false,
                'valid' => !empty($checksum_url) ? false : null,
              );
    }

    # Do we need to download a checksum?
    if(empty($checksum_url))
    {
      # Nope.
      return array(
                'downloaded' => true,
                'valid' => null,
              );
    }

    # Now time to download the checksum.
    $checksum = $http->request($checksum_url);
    $valid = strlen($checksum) == 40 ? sha1_file($save_to) == $checksum : (strlen($checksum) == 32 ? md5_file($save_to) == $checksum : false);

    # Is it not valid? Then delete it!
    if(empty($valid))
    {
      @unlink($save_to);
    }

    # We are done, well, almost :P
    return array(
              'downloaded' => true,
              'valid' => $valid,
            );
  }

  /*
    Method: extract

    Extracts the specified package to the specified directory.

    Parameters:
      string $filename - The file containing the update package.
      string $path - The path of where the update package should be
                     extracted to. This needs to be a temporary location
                     as another method handles the actual copying of
                     the files to their new destination. Of course,
                     this directory must be writable.
      string $type - The type of the specified package, such as tar (not
                     tar.gz, as if the Tar class detects the tar as being
                     gzipped, it will be extracted from the gzip automatically).
                     If no type is supplied, the type will be determined by
                     the files extension.

    Returns:
      bool - Returns true if the specified update package was successfully extracted
             to the specified path.
  */
  public function extract($filename, $path, $type = null)
  {
    global $api;

    # No supplied type? Try to auto-detect.
    if(empty($type) && strpos($filename, '.') !== false)
    {
      $tmp = explode('.', $filename);

      if(strtolower(array_pop($tmp)) == 'gz' && strtolower(array_pop($tmp)) == 'tar')
      {
        # Change the type to tar :-)
        $type = 'tar';
      }
    }

    # Does the file not exist? Or the path (not writable either)? Or does is the type not supported?
    if(!file_exists($filename) || !is_file($filename) || !file_exists($path) || !is_dir($path) || !is_writable($path) || $type != 'tar')
    {
      return false;
    }

    if($type == 'tar')
    {
      # We need the Tar class.
      $tar = $api->load_class('Tar');

      # Now open the tarball, or at least try.
      if(!$tar->open($filename))
      {
        # Sorry, we couldn't open it for some reason.
        return false;
      }
      else
      {
        # Is it gzipped? If it is, remove it from its gzipped state.
        if($tar->is_gzipped())
        {
          if(!$tar->ungzip())
          {
            # Well, that sucked.
            return false;
          }
        }

        # Now extract the tarball to the path specified.
        if(!$tar->extract($path))
        {
          # That didn't work :/
          return false;
        }
      }
    }

    # All done!
    return true;
  }

  /*
    Method: get_listing

    With the supplied update directory, this will return an array containing
    all the files which are now in the update directory.

    Parameters:
      string $path - The base path of where the update package is located in.

    Returns:
      array - Returns an array containing all the files which are in the specified
              path, including directories as well. However, if the specified path
              does not exist, false will be returned.
  */
  public function get_listing($path, $implode = true)
  {
    # Get the stuff in the specified path.
    $files = scandir($path);
    $listing = array();

    # Was anything there (does it exist..?)
    if(count($files) > 0)
    {
      foreach($files as $file)
      {
        # Ignore the . and .. directories.
        if($file == '.' || $file == '..')
        {
          continue;
        }

        # Is it a directory? As this index will contain the files and folders
        # within an array.
        if(is_dir($path. '/'. $file))
        {
          # Woo for recursion!
          $listing[$file. '/'] = $this->get_listing($path. '/'. $file, false);
        }
        # Otherwise it will just be set to the files name ;-)
        else
        {
          $listing[$file] = $file;
        }
      }
    }
    else
    {
      # Doesn't exist, sorry.
      return false;
    }

    # This only happens on the very first call of the method.
    if(!empty($implode))
    {
      $tmp = array();

      if(count($listing))
      {
        foreach($listing as $file => $f)
        {
          $tmp[] = $file;

          if(is_array($f))
          {
            $append = $this->get_listing_implode($f);

            if(count($append))
            {
              foreach($append as $a)
              {
                $tmp[] = $file. '/'. $a;
              }
            }
          }
        }

        $listing = $tmp;
      }
    }

    return $listing;
  }

  /*
    Method: get_listing_implode

    Converts the supplied array into a full path instead of relative
    paths, the reason get_listing sets a directory to an array containing
    its children is so that the directory can be initially created if need
    be. Don't understand? That's fine ;-) You don't need to.

    Parameters:
      array $array

    Returns:
      array
  */
  private function get_listing_implode($array)
  {
    $tmp = array();

    if(count($array))
    {
      foreach($array as $a => $d)
      {
        $tmp[] = $a;

        if(is_array($d))
        {
          $append = $this->get_listing_implode($d);

          if(count($append))
            foreach($append as $g)
              $tmp[] = $a. '/'. $g;
        }
      }
    }

    return $tmp;
  }

  /*
    Method: copy

    Copies the specified update file to its new destination.

    Parameters:
      string $path - The base path of where the update files are located.
      string $new_path - The new base path of where the file should be copied.
      string $filename - The name of the file to be copied from {$path}/{$filename}
                         to {$new_path}/{$filename}. If no file name is supplied,
                         then the directory listing will be obtainined internally
                         and all files will be copied at once.

    Returns:
      mixed - Returns true if the file was copied successfully, false if not. However,
              if no file name was supplied, an array containing all the files in path
              will be returned (the index) and their value will be either true or false,
              true if the file was copied, false if not.
  */
  public function copy($path, $new_path, $filename = null)
  {
    # Make sure everything exists and what not.
    if(!file_exists($path) || !is_dir($path) || !is_readable($path) || !file_exists($new_path) || !is_dir($new_path) || !is_writable($new_path) || (!empty($filename) && !file_exists($path. '/'. $filename)))
    {
      return false;
    }

    # One more check, make sure the specified file you want to move is actually in $path.
    $path = realpath($path);
    $tmp = realpath($path. '/'. $filename);

    if(substr($tmp, 0, strlen($path)) != $path)
    {
      # It is not within the specified path, sorry! No trying to do something naughty ;-)
      return false;
    }

    if(empty($filename))
    {
      # Get the directory ourself...
      $listing = array_flip($this->get_listing($path));

      foreach($listing as $file => $d)
      {
        # Now copy them!
        $listing[$file] = $this->copy($path, $new_path, $file);
      }

      return $listing;
    }
    else
    {
      # Time to copy! Woo!
    }
  }
}
?>