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
  Class: Upload

  Whenever any file is being uploaded by a user, this is the class to handle
  it. The Upload class is another one of those numerous available tools to
  make your development go a lot quicker.
*/
class Upload
{
  // Variable: loaded
  // Contains all the loaded upload information.
  private $loaded;

  /*
    Constructor: __construct
  */
  public function __construct()
  {
    $this->loaded = array();
  }

  /*
    Method: add

    Adds the specified upload to the uploads table.

    Parameters:
      string $area_name - The name of the area where the file should be uploaded
                          to. This area name is saved in the database, but it is
                          also the name of the folder of where the file where go.
      int $area_id - The areas numeric identifier. For example, say you are uploading
                     an avatar (area_name = profile) of a member who's id is 1, you
                     would supply 1.
      string $file - This is the array in the $_FILES array which contains the uploaded
                     file information, such as the location, original name, etc.
      array $options - Any extra options to save along with the upload.

    Returns:
      int - Returns the uploads identifier which is specific to the specified area name
            and id (Meaning their can be multiple upload_id's that are 1, but in different
            areas). However, if it failed, false will be returned.

    Note:
      The following options are supported:
        upload_time - The timestamp at which the upload was originally uploaded (Defaults to time_utc()).

        member_id - The id of the member of whom the upload was added by, defaults
                    to the current user.

        member_name - The name of the member of whom the upload was added by, defaults
                      to the current user.

        member_email - The email of the member of whom the upload was added by, defaults
                       to the current user.

        member_ip - The IP address of the member of whom the upload was added by, defaults
                    to the current user.

        filename - The name of the file, and only the name, no path!

        downloads - The number of downloads. Defaults to 0.

        upload_type - The type of upload.

        mime_type - The MIME type of the file.
  */
  public function add($area_name, $area_id, $file, $options = array())
  {
    $handled = null;
    api()->run_hooks('upload_add', array(&$handled, $area_name, $area_id, $file, $options));

    if($handled !== null)
    {
      return $handled;
    }

    if(empty($area_name) || empty($file['tmp_name']))
    {
      return false;
    }

    // Before we do too much, make sure the file is an uploaded file.
    if(is_uploaded_file($file['tmp_name']))
    {
      // Set some of our options if you didn't.
      if(!isset($options['upload_time']))
      {
        $options['upload_time'] = time_utc();
      }

      if(!isset($options['member_id']))
      {
        $options['member_id'] = member()->id();
      }

      if(!isset($options['member_name']))
      {
        $options['member_name'] = member()->name();
      }

      if(!isset($options['member_email']))
      {
        $options['member_email'] = member()->email();
      }

      if(!isset($options['member_ip']))
      {
        $options['member_ip'] = member()->ip();
      }

      if(empty($options['filename']))
      {
        $options['filename'] = $file['name'];
      }

      $options['filename'] = basename($options['filename']);

      // The file extension?
      if(strpos($options['filename'], '.') !== false)
      {
        $tmp = explode('.', $options['filename']);
        $options['file_ext'] = strtolower(array_pop($tmp));
      }
      else
      {
        $options['file_ext'] = '';
      }

      // Specified the number of downloads?
      if(!isset($options['downloads']) || (string)$options['downloads'] != (string)(int)$options['downloads'] || (int)$options['downloads'] < 0)
      {
        $options['downloads'] = 0;
      }

      if(empty($options['mime_type']))
      {
        // Should we use the File Info extension?
        if(function_exists('finfo_file'))
        {
          $ff = finfo_open(FILEINFO_MIME, settings()->get('finfo_magic_file', 'string', substr(PHP_OS, 0, 3) == 'WIN' ? 'C:\Program Files\PHP\magic' : '/usr/share/misc/file/magic.mgc'));
          $options['mime_type'] = finfo_file($ff, $file['tmp_name']);
          finfo_close($ff);
        }
        else
        {
          // Use the older, alternative.
          $options['mime_type'] = mime_content_type($file['tmp_name']);

          // The compat function built into SnowCMS determines the type via the files name,
          // for the time being at least, so attempt it again. Maybe.
          if($options['mime_type'] == 'application/octet-stream')
          {
            $options['mime_type'] = mime_content_type($options['filename']);

            // Failed? Give it back the old type.
            if(empty($options['mime_type']))
            {
              $options['mime_type'] = 'appliction/octet-stream';
            }
          }
        }
      }

      $members = api()->load_class('Members');

      // Generate the file location (Well, the files name in the upload directory).
      $options['filelocation'] = sha1(mt_rand(1, 1000). $options['filename']. microtime(true). $members->rand_str(10));

      // Make sure the file doesn't exist.
      while(file_exists(uploaddir. '/'. $options['filelocation']))
      {
        $options['filelocation'] = sha1(mt_rand(1, 1000). $options['filename']. microtime(true). $members->rand_str(10));
      }

      // Get the file size and the checksum of the file.
      $options['filesize'] = filesize($file['tmp_name']);
      $options['checksum'] = sha1_file($file['tmp_name']);

      $allow_file = true;
      api()->run_hooks('upload_add_allow', array(&$allow_file, $options));
      api()->run_hooks('upload_add_options', array(&$options));

      // Now move the file to the right location.
      if(!empty($allow_file) && move_uploaded_file($file['tmp_name'], uploaddir. '/'. $options['filelocation']))
      {
        // Save the information into the database.
        $result = db()->insert('insert', '{db->prefix}uploads',
          array(
            'area_name' => 'string-255', 'area_id' => 'int', 'upload_time' => 'int',
            'member_id' => 'int', 'member_name' => 'string-255', 'member_email' => 'string-255',
            'member_ip' => 'string-150', 'filename' => 'string-255', 'file_ext' => 'string-100',
            'filelocation' => 'string-255', 'filesize' => 'int', 'downloads' => 'int',
            'upload_type' => 'string-100', 'mime_type' => 'string-255', 'checksum' => 'string-40',
          ),
          array(
            $area_name, $area_id, $options['upload_time'],
            $options['member_id'], $options['member_name'], $options['member_email'],
            $options['member_ip'], $options['filename'], $options['file_ext'],
            $options['filelocation'], $options['filesize'], $options['downloads'],
            isset($options['upload_type']) ? $options['upload_type'] : '', $options['mime_type'], $options['checksum'],
          ),
          array(), 'upload_insert_query');

        // Was it a success?
        if($result->success())
        {
          // Yup. Return the ID of the upload.
          return $result->insert_id();
        }
        else
        {
          // Nope, it was a failure. Delete the file.
          unlink($options['filelocation']);

          return false;
        }
      }
      else
      {
        return false;
      }
    }
    else
    {
      // Not an uploaded file. Fishy.
      return false;
    }
  }

  /*
    Method: remove

    Removes the specified uploads from the uploads table.

    Parameters:
      string $area_name - The area where the upload is in.
      int $area_id - The id of the area upload is in.
      mixed $upload_id - Either a single upload id, or an
                         array of upload ids to remove.

    Returns:
      int - Returns the number of uploads actually deleted.
  */
  public function remove($area_name, $area_id, $upload_id)
  {
    $handled = null;
    api()->run_hooks('upload_remove', array(&$handled, $area_name, $area_id, $upload_id));

    if($handled !== null)
    {
      return $handled;
    }

    if(empty($area_name))
    {
      return false;
    }

    // An array of stuff?
    if(!is_array($upload_id))
    {
      // Nope, so make it one.
      $upload_id = array($upload_id);
    }

    // We need to get the files information, first.
    // That way we can delete the files.
    $result = db()->query('
      SELECT
        upload_id, filelocation
      FROM {db->prefix}uploads
      WHERE area_name = {string:area_name} AND area_id = {int:area_id} upload_id IN({array_int:upload_id})',
      array(
        'area_name' => $area_name,
        'area_id' => $area_id,
        'upload_id' => $upload_id,
      ), 'upload_remove_select_query');

    // Anything at all?
    if($result->num_rows() > 0)
    {
      // Maybe all the id's you supplied didn't exist.
      $upload_id = array();
      while($row = $result->fetch_assoc())
      {
        $upload_id[] = $row['upload_id'];

        // Remove the file.
        unlink(uploaddir. '/'. $row['filelocation']);
      }

      // Now delete them from the database.
      db()->query('
        DELETE FROM {db->prefix}uploads
        WHERE area_name = {string:area_name} AND area_id = {int:area_id} AND upload_id IN({array_int:upload_id})',
        array(
          'area_name' => $area_name,
          'area_id' => $area_id,
          'upload_id' => $upload_id,
        ), 'upload_remove_delete_query');

      // All done ;)
      return count($upload_id);
    }
    else
      // Nothing deleted!
      return 0;
  }

  /*
    Method: edit

    Edits the uploads information.

    Parameters:
      string $area_name - The area the upload is in.
      int $area_id - The area id the upload is within.
      int $upload_id - The id of the upload being edited.
      array $options - An array containing updated information.

    Returns:
      bool - Returns true if the upload was edited successfully, false if not.

    Note:
      The following options are supported:
        area_name - The name of the area the file is in.

        area_id - The area id of the upload.

        upload_time - The timestamp at which the upload was originally uploaded.

        member_id - The id of the member of whom the upload was added by.

        member_name - The name of the member of whom the upload was added by.

        member_email - The email of the member of whom the upload was added by.

        member_ip - The IP address of the member of whom the upload was added by.

        modified_time - The timestamp at which the upload was edited (Defaults to time_utc()).

        modified_id - See member_id (Defaults to current member).

        modified_name - See member_name (Defaults to current member).

        modified_email - See member_email. (Defaults to current member).

        modified_ip - See member_ip (Defaults to current member).

        filename - The name of the file, and only the name, no path!

        downloads - The number of downloads.

        upload_type - The type of upload.

        mime_type - The MIME type of the file.
  */
  public function edit($area_name, $area_id, $upload_id, $options)
  {
    $handled = null;
    api()->run_hooks('upload_edit', array(&$handled, $area_name, $area_id, $upload_id, $options));

    if($handled !== null)
    {
      return $handled;
    }

    if(empty($area_name))
    {
      return false;
    }

    // Get the current information.
    $result = db()->query('
      SELECT
        *
      FROM {db->prefix}uploads
      WHERE area_name = {string:area_name} AND area_id = {int:area_id} AND upload_id = {int:upload_id}
      LIMIT 1',
      array(
        'area_name' => $area_name,
        'area_id' => $area_id,
        'upload_id' => $upload_id,
      ), 'upload_edit_select_query');

    if($result->num_rows() == 0)
    {
      return false;
    }

    // These are the columns which can be edited.
    $columns = array(
                 'area_name' => 'string-255',
                 'area_id' => 'int',
                 'upload_time' => 'int',
                 'member_id' => 'int',
                 'member_name' => 'string-255',
                 'member_email' => 'string-255',
                 'member_ip' => 'string-150',
                 'modified_time' => 'int',
                 'modified_id' => 'int',
                 'modified_name' => 'string-255',
                 'modified_email' => 'string-255',
                 'modified_ip' => 'string-150',
                 'filename' => 'string-255',
                 'file_ext' => 'string-100',
                 'downloads' => 'int',
                 'upload_type' => 'string-100',
                 'mime_type' => 'string-255',
               );

    // Set a couple default values, if not yet set.
    if(!isset($options['modified_time']))
    {
      $options['modified_time'] = time_utc();
    }

    if(!isset($options['modified_id']))
    {
      $options['modified_id'] = member()->id();
    }

    if(!isset($options['modified_name']))
    {
      $options['modified_name'] = member()->name();
    }

    if(!isset($options['modified_email']))
    {
      $options['modified_email'] = member()->email();
    }

    if(!isset($options['modified_ip']))
    {
      $options['modified_ip'] = member()->ip();
    }

    // Any changes, perhaps?
    api()->run_hooks('upload_edit_values', array(&$columns, &$options));

    $values = array();
    $db_vars = array();
    foreach($options as $key => $value)
    {
      if(isset($columns[$key]))
      {
        $values[] = $key. ' = {'. $columns[$key]. ':value_'. count($values). '}';
        $db_vars['value_'. (count($values) - 1)] = $value;
      }
    }

    if(count($values) > 0)
    {
      // Set a couple other database variables.
      $db_vars['area_name'] = $area_name;
      $db_vars['area_id'] = $area_id;
      $db_vars['upload_id'] = $upload_id;

      // Now actually update the upload information.
      $result = db()->query('
                  UPDATE {db->prefix}uploads
                  SET '. implode(', ', $values). '
                  WHERE area_name = {string:area_name} AND area_id = {int:area_id} AND upload_id = {int:upload_id}
                  LIMIT 1',
                  $db_vars, 'upload_edit_query');

      return $result->success();
    }
    else
    {
      return true;
    }
  }

  /*
    Method: load

    Loads the specified uploads for access later.

    Parameters:
      string $area_name - The area the upload is located in.
      int $area_id - The area id of the upload.
      mixed $upload_id - The ID of the upload to load, this can
                         either be one number, or an array of numbers.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function load($area_name, $area_id, $upload_id)
  {
    if(empty($area_name) || empty($upload_id))
    {
      return false;
    }

    // Didn't give us an array? We will make it one!
    if(!is_array($upload_id))
    {
      $upload_id = array($upload_id);
    }

    if(count($upload_id) > 0)
    {
      foreach($upload_id as $key => $id)
      {
        if((int)$id > 0)
        {
          $upload_id[$key] = (int)$id;
        }
        else
        {
          unset($upload_id[$key]);
        }
      }

      // We don't want to load the same thing multiple times.
      $upload_id = array_unique($upload_id);

      if(count($upload_id) == 0)
      {
        return false;
      }
    }

    $handled = null;
    api()->run_hooks('upload_load', array(&$handled, $area_name, $area_id, $upload_id));

    if($handled !== null)
    {
      return $handled;
    }

    // Unset all uploads that we already loaded.
    foreach($upload_id as $key => $id)
    {
      if(isset($this->loaded[$area_name][$area_id][$upload_id]))
      {
        unset($upload_id[$key]);
      }
    }

    $result = db()->query('
                SELECT
                  upload_id, upload_time, member_id, member_name, member_email, member_ip,
                  modified_time, modified_id, modified_name, modified_email, modified_ip,
                  filename, file_ext, filelocation, filesize, downloads, upload_type,
                  mime_type, checksum
                FROM {db->prefix}uploads
                WHERE area_name = {string:area_name} AND area_id = {string:area_id} AND upload_id IN({array_int:upload_id})',
                array(
                  'area_name' => $area_name,
                  'area_id' => $area_id,
                  'upload_id' => $upload_id,
                ), 'upload_load_query');

    if($result->num_rows() > 0)
    {
      if(!isset($this->loaded[$area_name][$area_id]))
      {
        $this->loaded[$area_name][$area_id] = array();
      }

      while($row = $result->fetch_assoc())
      {
        $upload = array(
                    'id' => $row['upload_id'],
                    'time' => $row['upload_time'],
                    'date' => timeformat($row['upload_time']),
                    'member' => array(
                                  'id' => $row['member_id'],
                                  'name' => $row['member_name'],
                                  'email' => $row['member_email'],
                                  'ip' => $row['member_ip'],
                                ),
                    'modified' => array(
                                    'time' => $row['modified_time'],
                                    'date' => timeformat($row['modified_time']),
                                    'member' => array(
                                                  'id' => $row['modified_id'],
                                                  'name' => $row['modified_name'],
                                                  'email' => $row['modified_email'],
                                                  'ip' => $row['modified_ip'],
                                                ),
                                  ),
                    'file' => array(
                                'name' => $row['filename'],
                                'extension' => $row['file_ext'],
                                'location' => realpath(uploaddir. '/'. $row['filelocation']),
                                'size' => $row['filesize'],
                                'downloads' => $row['downloads'],
                                'type' => $row['upload_type'],
                                'mime' => $row['mime_type'],
                                'checksum' => $row['checksum'],
                              ),
                  );

        api()->run_hooks('upload_load_array', array(&$upload, $row));

        $this->loaded[$area_name][$area_id][$row['upload_id']] = $upload;
      }
    }

    return true;
  }

  /*
    Method: get

    Once the uploads have been loaded via <Upload::load>, the upload
    information can be obtained through this method.

    Parameters:
      string $area_name - The area the upload is located.
      int $area_id - The area ID of the upload.
      mixed $upload_id - Either a single or an array of upload
                         ids to return.

    Returns:
      array - If you requested multiple uploads, an array containing nested
              arrays will be returned. Each index of the subarrays is the
              id of the upload. However, if you only requested one upload,
              an array containing the information will be returned only. If
              the requested id doesn't exist, false will be returned.
  */
  public function get($area_name, $area_id, $upload_id)
  {
    if(empty($area_name) || empty($upload_id))
    {
      return false;
    }

    // Didn't give us an array? We will make it one!
    if(!is_array($upload_id))
    {
      $upload_id = array($upload_id);
    }

    if(count($upload_id) > 0)
    {
      foreach($upload_id as $key => $id)
      {
        if((int)$id > 0)
        {
          $upload_id[$key] = (int)$id;
        }
        else
        {
          unset($upload_id[$key]);
        }
      }

      // We don't want to load the same thing multiple times.
      if(count($upload_id) > 1)
      {
        $upload_id = array_unique($upload_id);
      }

      if(count($upload_id) == 0)
      {
        return false;
      }
    }

    $handled = null;
    api()->run_hooks('upload_get', array(&$handled, $area_name, $area_id, $upload_id));

    if($handled !== null)
    {
      return $handled;
    }

    // Sure, we turned the singleton into an array, but now we will check
    // to see if you only wanted one upload ;)
    if(count($upload_id) == 1)
    {
      return isset($this->loaded[$area_name][$area_id][$upload_id[array_rand($upload_id)]]) ? $this->loaded[$area_name][$area_id][$upload_id[array_rand($upload_id)]] : false;
    }
    else
    {
      $uploads = array();

      // Pretty easy ;)
      foreach($upload_id as $id)
      {
        $uploads[$id] = $this->get($id);
      }

      return $uploads;
    }
  }

  /*
    Method: download

    This method does all that is necessary to have the user download
    the selected upload.

    Parameters:
      string $area_name - The area where the upload is in.
      int $area_id - The id of the area upload is in.
      int $upload_id - The id of the upload to download.
      bool $return - Whether or not this method should return the files
                     contents instead of prompting the file for download.

    Returns:
      mixed - Nothing is returned by this method if $return is false, otherwise
              the files contents is returned.
  */
  public function download($area_name, $area_id, $upload_id, $return = false)
  {
    $handled = null;
    api()->run_hooks('upload_download', array(&$handled, $area_name, $area_id, $upload_id, $return));

    if($handled !== null)
    {
      return $handled;
    }

    // Has this been loaded?
    $upload = $this->get($area_name, $area_id, $upload_id);

    if(empty($upload))
    {
      return false;
    }

    // Pretty simple if you just want the files contents.
    if(!empty($return))
    {
      return file_exists($upload['file']['location']) ? file_get_contents($upload['file']['location']) : false;
    }
    else
    {
      // Clear the output buffer...
      @ob_clean();
      @ob_end_flush();

      // An ETag will be useful.
      if(file_exists($upload['file']['location']))
      {
        $etag = '"'. sha1($upload['file']['checksum']. filemtime($upload['file']['location'])). '"';
      }

      // Check to make sure the file exists.
      if(!file_exists($upload['file']['location']))
      {
        header('HTTP/1.1 404 '. l('Upload Not Found'));
        header('Content-Type: text/plain');

        die(l('Sorry, but the file you have requested to download was not found on this server.'));
      }
      // Modified since check..?
      elseif(!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']))
      {
        list($modified_since) = explode(';', $_SERVER['HTTP_IF_MODIFIED_SINCE']);

        // So, has it been modified since..?
        if(strtotime($modified_since) >= filemtime($upload['file']['location']))
        {
          header('HTTP/1.1 304 Not Modified');
          exit;
        }
      }
      elseif(!empty($_SERVER['HTTP_IF_NONE_MATCH']) && strpos($_SERVER['HTTP_IF_NONE_MATCH'], $etag))
      {
        header('HTTP/1.1 304 Not Modified');
        exit;
      }

      // That's one more download.
      db()->query('
        UPDATE {db->prefix}uploads
        SET downloads = downloads + 1
        WHERE area_name = {string:area_name} AND area_id = {int:area_id} AND upload_id = {int:upload_id}
        LIMIT 1',
        array(
          'area_name' => $area_name,
          'area_id' => $area_id,
          'upload_id' => $upload_id,
        ), 'upload_download_increment_query');

      api()->run_hooks('upload_download_pre_headers', array($upload));

      header('Content-Type: '. $upload['file']['mime']);
      header('Content-Length: '. $upload['file']['size']);
      header('Last-Modified: '. gmdate('D, d M Y H:i:s', filemtime($upload['file']['location'])));
      header('ETag: '. $etag);
      header('Content-Disposition: '. (isset($_GET['inline']) ? 'inline' : 'attachment'). '; filename="'. $upload['file']['name']. '"');

      // If you want to do something now, do it.
      api()->run_hooks('upload_download_post_headers', array($upload));

      // Open the file for reading!
      $fp = fopen($upload['file']['location'], 'rb');

      @set_time_limit(0);
      while(!feof($fp))
      {
        echo fread($fp, 8192);

        flush();
      }

      fclose($fp);
      exit;
    }
  }
}
?>