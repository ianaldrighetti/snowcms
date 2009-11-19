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
# mixed downloads_upload(array $file[, string $folder = null[, bool $base64 = false[, bool $is_avatar = false]]]);
#   array $file - the $_FILES[upload_name] array passed on.
#   string $folder - the relative (from $base_dir) directory in which to upload the file too.
#                    If none is specified, it will go into the downloads directory.
#   bool $base64 - Whether to base64 encode the file. If set to true, the files contents will
#                  be encoded with base64_encode, but once the file is downloaded, it will be
#                  decoded. If set to true and the settings say don't base64 encode, it won't!
#   bool $is_avatar - Whether or not this is a user uploaded avatar (It won't be base64 encoded).
#
# returns bool - If the file was uploaded successfully and isn't an avatar, the ID of the download
#                will be returned. If the file was uploaded successfully and is an avatar, the file name
#                will be returned, but if it failed, FALSE will be issued.
#

function downloads_upload($file, $folder = null, $base64 = true, $is_avatar = false)
{
  global $db;
}
?>