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

if(!function_exists('mime_content_type'))
{
  /*
    Function: mime_content_type

    Attempts to detect the content type of the specified file.

    Parameters:
      string $filename - The name of the file.

    Returns:
      string - Returns the MIME content type of the file.

    Note:
      This function attempts to detect the type of the file according to
      a text file containing extensions and content types, so it won't
      be as accurate as it could be.

      However, if the system detects that the Fileinfo extension for PHP
      is installed, then that will be used instead of the crappy method.
  */
  function mime_content_type($filename)
  {
    global $settings;

    # Is the Fileinfo extension installed in your PHP setup? Even better!
    if(function_exists('finfo_file'))
    {
      $ff = finfo_open(FILEINFO_MIME, $settings->get('finfo_magic_file', 'string', substr(PHP_OS, 0, 3) == 'WIN' ? 'C:\Program Files\PHP\magic' : '/usr/share/misc/file/magic.mgc'));
      $mime_type = finfo_file($ff, $location);
      finfo_close($ff);

      # Alright, got it!
      return $mime_type;
    }
    # Get the extension of the file. Maybe.
    elseif(strpos($filename, '.') !== false)
    {
      # The extension is SHA-1'd ;)
      $tmp = explode('.', $filename);
      $extension = sha1(strtolower(array_pop($tmp)));

      $fp = fopen(dirname(__FILE__). '/mime.db', 'rb');

      fseek($fp, 0, SEEK_END);

      $filesize = ftell($fp);
      fseek($fp, 0);

      # How many possibilities are there?
      $total = $filesize / (double)295;

      # Now let's get to searching!
      $min = 0;
      $max = $total - 1;
      $searches = 0;
      do
      {
        $mid = ceil(($min + $max) / 2);

        fseek($fp, $mid * 295);
        $current = fread($fp, 40);

        # Did we find it?
        if($current == $extension)
        {
          # Yup, we did!
          break;
        }
        # But don't give up!
        elseif($extension > $current)
        {
          $min = $mid + 1;
        }
        else
        {
          $max = $mid - 1;
        }

        $searches++;
      }
      while($current != $extension && $min <= $max);

      # Was it found?
      if($current == $extension)
      {
        # Yup, so read the mime type and return it!
        list(, $mime_type) = unpack('a255', fread($fp, 255));
        return $mime_type;
      }
    }

    # Just return a generic content type.
    return 'application/octet-stream';
  }
}
?>