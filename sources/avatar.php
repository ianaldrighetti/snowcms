<?php
#########################################################################
#                             SnowCMS v1.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#               Released under the GNU Lesser GPL v3 License            #
#                    www.gnu.org/licenses/lgpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#  SnowCMS v1.0 began in November 2008 by Myles, aldo and antimatter15  #
#                       aka the SnowCMS Dev Team                        #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 1.0                         #
#########################################################################

# No Direct access please ^^
if(!defined('InSnow'))
  die;

#
# Handle the uploaded avatars.
#
# void avatar_display();
#   - Display an uploaded avatar, based on GET data.
#

function avatar_display()
{
  global $avatar_dir;
  
  # Get the avatar from the collection's filename
  $collection = isset($_GET['collection']) && preg_match('/^[a-z-_]+\.[a-z-_]+$/is', $_GET['collection']) ? $_GET['collection'] : '';
  
  # Get the uploaded avatar ID
  $upload = isset($_GET['u']) ? $_GET['u'] : 0;
  
  # Get the extension, if it's valid
  if(!empty($_GET['ext']) && in_array($_GET['ext'], array('.bmp', '.gif', '.png', '.jpg')))
    $ext = $_GET['ext'];
  else
    $ext = '';
  
  # Are we displaying an avatar from the collection?
  if(!empty($collection))
  {
    # Check that the avatar is valid
    if(file_exists($avatar_dir. '/collection/'. $collection))
    {
      # Set the avatar's file type
      header('Content-Type: image/png');
      
      # Display the avatar
      echo file_get_contents($avatar_dir. '/collection/'. $collection);
    }
    else
      die('Invalid image.');
  }
  # Then are we displaying an uploaded avatar?
  elseif(!empty($upload))
  {
    # Check that the avatar is valid
    if(file_exists($avatar_dir. '/avatar-'. $upload. $ext))
    {
      # Set the avatar's MIME type
      header('Content-Type: image/'. mb_substr($ext, 1));
      
      # Display the avatar
      echo file_get_contents($avatar_dir. '/avatar-'. $upload. $ext);
    }
    else
      die('Invalid image.');
  }
  else
    die('Invalid image.');
}
?>