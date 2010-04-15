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
  Class: Upload

  Whenever any file is being uploaded by a user, this is the class to handle
  it. The Upload class is another one of those numerous available tools to
  make your development go a lot quicker.
*/
class Upload
{
  /*
    Constructor: __construct
  */
  public function __construct()
  {

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

        upload_type - The type of upload.

        mime_type - The MIME type of the file.
  */
  public function add($area_name, $area_id, $file, $options = array())
  {
    global $api, $db, $member, $settings, $upload_dir;

    $handled = null;
    $api->run_hooks('upload_add', array(&$handled, $area_name, $area_id, $file, $options));

    if($handled !== null)
      return $handled;

    if(empty($area_name) || empty($file['tmp_name']))
      return false;

    # Before we do too much, make sure the file is an uploaded file.
    if(is_uploaded_file($file['tmp_name']))
    {
      # Set some of our options if you didn't.
      if(!isset($options['upload_time']))
        $options['upload_time'] = time_utc();

      if(!isset($options['member_id']))
        $options['member_id'] = $member->id();

      if(!isset($options['member_name']))
        $options['member_name'] = $member->name();

      if(!isset($options['member_email']))
        $options['member_email'] = $member->email();

      if(!isset($options['member_ip']))
        $options['member_ip'] = $member->ip();

      if(empty($options['filename']))
        $options['filename'] = $file['name'];

      $options['filename'] = basename($options['filename']);

      # The file extension?
      if(strpos($options['filename'], '.') !== false)
      {
        $tmp = explode('.', $options['filename']);
        $options['file_ext'] = $tmp[count($tmp) - 1];
      }
      else
        $options['file_ext'] = '';


      if(empty($options['mime_type']))
      {
        # Should we use the File Info extension?
        if(function_exists('finfo_file'))
        {
          $ff = finfo_open(FILEINFO_MIME, $settings->get('finfo_magic_file', 'string', null));
          $options['mime_type'] = finfo_file($ff, $file['tmp_name']);
          finfo_close($ff);
        }
        else
           # Use the older, alternative.
           $options['mime_type'] = mime_content_type($file['tmp_name']);
      }

      $members = $api->load_class('Members');

      # Generate the file location (Well, the files name in the upload directory).
      $options['filelocation'] = sha1(mt_rand(1, 1000). $options['filename']. microtime(true). $members->rand_str(10));

      # Make sure the file doesn't exist.
      while(file_exists($upload_dir. '/'. $options['filelocation']))
        $options['filelocation'] = sha1(mt_rand(1, 1000). $options['filename']. microtime(true). $members->rand_str(10));

      # Get the file size and the checksum of the file.
      $options['filesize'] = filesize($file['tmp_name']);
      $options['checksum'] = sha1_file($file['tmp_name']);

      # Now move the file to the right location.
      if(move_uploaded_file($file['tmp_name'], $options['filelocation']))
      {
        # Save the information into the database.
        $result = $db->insert('insert', '{db->prefix}uploads',
          array(
            'area_name' => 'string-255', 'area_id' => 'int', 'upload_time' => 'int',
            'member_id' => 'int', 'member_name' => 'string-255', 'member_email' => 'string-255',
            'member_ip' => 'string-150', 'filename' => 'string-255', 'file_ext' => 'string-100',
            'filelocation' => 'string-255', 'filesize' => 'int', 'upload_type' => 'string-100',
            'mime_type' => 'string-255', 'checksum' => 'string-40',
          ),
          array(
            $area_name, $area_id, $options['upload_time'],
            $options['member_id'], $options['member_name'], $options['member_email'],
            $options['member_ip'], $options['filename'], $options['file_ext'],
            $options['filelocation'], $options['filesize'], $options['upload_type'],
            $options['mime_type'], $options['checksum'],
          ),
          array(), 'upload_insert_query');

        # Was it a success?
        if($result->success())
        {
          # Yup. Return the ID of the upload.
          return $result->insert_id();
        }
        else
        {
          # Nope, it was a failure. Delete the file.
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
      # Not an uploaded file. Fishy.
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
    global $api, $db, $upload_dir;

    $handled = null;
    $api->run_hooks('upload_remove', array(&$handled, $area_name, $area_id, $upload_id));

    if($handled !== null)
      return $handled;

    if(empty($area_name))
      return false;

    # An array of stuff?
    if(!is_array($upload_id))
      # Nope, so make it one.
      $upload_id = array($upload_id);

    # We need to get the files information, first.
    # That way we can delete the files.
    $result = $db->query('
      SELECT
        upload_id, filelocation
      FROM {db->prefix}uploads
      WHERE area_name = {string:area_name} AND area_id = {int:area_id} upload_id IN({array_int:upload_id})',
      array(
        'area_name' => $area_name,
        'area_id' => $area_id,
        'upload_id' => $upload_id,
      ), 'upload_remove_select_query');

    # Anything at all?
    if($result->num_rows() > 0)
    {
      # Maybe all the id's you supplied didn't exist.
      $upload_id = array();
      while($row = $result->fetch_assoc())
      {
        $upload_id[] = $row['upload_id'];

        # Remove the file.
        unlink($upload_dir. '/'. $row['filelocation']);
      }

      # Now delete them from the database.
      $db->query('
        DELETE FROM {db->prefix}uploads
        WHERE area_name = {string:area_name} AND area_id = {int:area_id} AND upload_id IN({array_int:upload_id})',
        array(
          'area_name' => $area_name,
          'area_id' => $area_id,
          'upload_id' => $upload_id,
        ), 'upload_remove_delete_query');

      # All done ;)
      return count($upload_id);
    }
    else
      # Nothing deleted!
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

        upload_type - The type of upload.

        mime_type - The MIME type of the file.
  */
  public function edit($area_name, $area_id, $upload_id, $options)
  {
    global $api, $db, $member;

    $handled = null;
    $api->run_hooks('upload_edit', array(&$handled, $area_name, $area_id, $upload_id, $options));

    if($handled !== null)
      return $handled;

    if(empty($area_name))
      return false;

    # Get the current information.
    $result = $db->query('
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
      return false;

    # !!! TODO
  }
}
?>