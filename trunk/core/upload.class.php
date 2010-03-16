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
    Method: add_upload

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
  */
}
?>