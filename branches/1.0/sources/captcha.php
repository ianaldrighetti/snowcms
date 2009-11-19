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
# captcha.php has CAPTCHA related things of course.
#
# void captcha_display();
#   - Generates a random CAPTCHA code, puts it in the session data
#     and outputs it as an image. Don't send any output before or
#     after calling this function.
#
# bool captcha_check(string $code);
#   - Returns whether the unhashed $code is the correct CAPTCHA code.
#

function captcha_display()
{
  global $base_dir, $theme_dir, $user, $settings, $theme;
  
  # Get the CAPTCHA strength.
  # You may be wondering "If people can set it in GET data, what's the point?"
  # Because if it is set in the GET data, the CAPTCHA won't function as
  # normal; you'll see. ;)
  $stength = isset($_GET['strength']) ? (int)$_GET['strength'] : $settings['captcha_strength'];
  
  # Check if this server can handle CAPTCHAs and if they're turned on
  if(!function_exists('imagecreate') || $stength == 0)
    # Stop captcha_display()
    return;
  
  # Set some settings
  $chars = !empty($_GET['chars']) ? min(max((int)$_GET['chars'], 3), 8) : $settings['captcha_chars'];
  $difficulty = max($stength - 1, 1);
  $width = !empty($_GET['width']) ? (int)$_GET['width'] : 250;
  $height = !empty($_GET['height']) ? (int)$_GET['height'] : 120;
  $fontsize = !empty($_GET['fontsize']) ? (int)$_GET['fontsize'] : 32;
  $font = is_readable($theme_dir . '/' . $settings['theme'] . '/captcha.ttf')
          ? $theme_dir . '/' . $settings['theme'] . '/captcha.ttf'
          : $theme_dir . '/default/captcha.ttf';
  $text = is_readable($theme_dir . '/' . $settings['theme'] . '/captcha.txt')
          ? $theme_dir . '/' . $settings['theme'] . '/captcha.txt'
          : $theme_dir . '/default/captcha.txt';

  # Get the CAPTCHA code.
  $code = '';
  for($i = 0; $i < $chars; $i++)
  {
    # Set the seed using mt_rand(), so that str_shuffle() gets
    # some of the power of mt_rand() instead of just rand().
    srand(mt_rand());
    
    # Get a random letter, and use mt_rand() again, for good measure.
    $code .= mb_substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), mt_rand(0, 25), 1);
  }
  
  # Only save the code in the session, if no GET variables are modifying the CAPTCHA.
  if(empty($_GET['strength']) && empty($_GET['chars']) && empty($_GET['width']) && empty($_GET['height']) && empty($_GET['fontsize']))
  {
    # Save the CAPTCHA code in the session.
    $_SESSION['captcha'] = sha1(mb_strtolower($code));
  }
  
  # Check our GD version.
  # Why not just use function_exists('imagecreatruecolor')? Because
  # imagecreatetruecolor() actually exists in GD 1.x, it just can't be used.
  $gd_info = gd_info();
  $gd_version = $gd_info['GD Version'];
  
  # Create a new image resource, in true colour! :D
  if(version_compare($gd_version, '2') >= 0)
    $image = imagecreatetruecolor($width, $height);
  # GD 1.x? No true colour. :'(
  else
    $image = imagecreate($width, $height);
  
  # Define the background colour, white.
  $background = imagecolorallocate($image, 255, 255, 255);
  
  # Fill the background with the background, that's what it's for after all.
  imagefill($image, 0, 0, $background);
  
  # Free the backgrond colour's memory.
  imagecolordeallocate($image, $background);
  
  # Check if server is compatible with TrueType fonts and is set to use them.
  if(function_exists('imagefttext') && $stength != 1)
  {
    # Define the noise (dots) colour, light black or dark grey, depends on how you look at it.
    $noise = imagecolorallocate($image, 40, 40, 40);
    
    # Add noise (dots)
    for($i = 0; $i < 1500 + $difficulty * 500; $i++)
    {
      # Select random positions
      $x = mt_rand() % $width;
      $y = mt_rand() % $height;
      
      # Draw a dot
      if(mt_rand(0, 1))
        imagerectangle($image, $x - 1, $y, $x + 1, $y + 1, $noise);
      else
        imagerectangle($image, $x, $y - 1, $x + 1, $y + 1, $noise);
    }
    
    # Free the noise (dots) colour's memory.
    imagecolordeallocate($image, $noise);
    
    # Draw the text one character at a time
    for($i = 0; $i < mb_strlen($code); $i++)
    {
      # Get the bounding box the character
      $bbox = imagettfbbox($fontsize, 0, $font, $code[$i]);

      # Get the rotation
      $rotate = mt_rand(-20, 20);

      # Get the horizontal position
      $x = 10 + ($i + 0.5) * ($width - 20) / $chars - ($bbox[2] - $bbox[0]) / 2 + mt_rand(-4, 4);

      # Get the vertical position
      $y = ($height + $fontsize) / 2 + mt_rand(-4, 4);
      
      # Create a random colour for the character.
      switch(mt_rand(0, 2))
      {
        case 0: $colour = imagecolorallocate($image, mt_rand(96, 255), mt_rand(96, 255), 0); break;
        case 1: $colour = imagecolorallocate($image, mt_rand(96, 255), 0, mt_rand(96, 255)); break;
        case 2: $colour = imagecolorallocate($image, 0, mt_rand(96, 255), mt_rand(96, 255)); break;
      }
      
      # Get a random font size.
      $fontsize_temp = mt_rand($fontsize - 2, $fontsize + 2) + 2;

      # Draw the character.
      imagettftext($image, $fontsize_temp, $rotate, $x - 1, $y - 1, $colour, $font, $code[$i]);
      
      # Draw the character again to make it thicker.
      imagettftext($image, $fontsize_temp, $rotate, $x + 1, $y + 1, $colour, $font, $code[$i]);
      
      # Free the random colour's memory.
      imagecolordeallocate($image, $colour);
    }
  }
  # Use system fonts because TrueType is disabled on server
  else
  {
    # Draw five random lines for every difficulty level above 1
    for($i = 0; $i < 5; $i++)
    {
      # Create a random colour
      switch(mt_rand(0, 2))
      {
        case 0: $colour = imagecolorallocate($image, mt_rand(96, 255), mt_rand(96, 255), 0); break;
        case 1: $colour = imagecolorallocate($image, mt_rand(96, 255), 0, mt_rand(96, 255)); break;
        case 2: $colour = imagecolorallocate($image, 0, mt_rand(96, 255), mt_rand(96, 255)); break;
      }
      
      # Draw the line
      imageline($image, mt_rand(10, 50), mt_rand(10, $height - 10), $width - mt_rand(10, 50), mt_rand(10, $height - 10), $colour);
    }
    
    # Draw the code one letter at a time
    for($i = 0; $i < mb_strlen($code); $i++)
    {
      # Get the horizontal position
      $x = 10 + ($i + 0.5) * ($width - 20) / $chars - 2 + mt_rand(-5, 5);

      # Get the vertical position
      $y = ($height - 15) / 2 + mt_rand(-8, 8);
      
      # Get the textual letters
      $letters = preg_split('/(\r\n\r\n|\n\n|\r\r)/', file_get_contents($text));
      
      # Split the letters on to lines
      foreach($letters as &$letter)
        $letter = preg_split('/(\r\n|\n|\r)/', $letter);
      
      # Create a random colour
      # If the difficulty is 3+, we'll use darker colours, to merge more into the noise.
      if($strength >= 3)
      {
        switch(mt_rand(0, 2))
        {
          case 0: $colour = imagecolorallocate($image, mt_rand(48, 228), mt_rand(48, 192), 0); break;
          case 1: $colour = imagecolorallocate($image, mt_rand(48, 228), 0, mt_rand(48, 192)); break;
          case 2: $colour = imagecolorallocate($image, 0, mt_rand(48, 228), mt_rand(48, 192)); break;
        }
      }
      #  Difficulty is less than 4? Well, let's brighten them up then.
      else
      {
        switch(mt_rand(0, 2))
        {
          case 0: $colour = imagecolorallocate($image, mt_rand(96, 255), mt_rand(96, 255), 0); break;
          case 1: $colour = imagecolorallocate($image, mt_rand(96, 255), 0, mt_rand(96, 255)); break;
          case 2: $colour = imagecolorallocate($image, 0, mt_rand(96, 255), mt_rand(96, 255)); break;
        }
      }
      
      # Find the letter out of the array
      foreach($letters[mb_strpos('ABCDEFGHIJKLMNOPQRSTUVWXYZ', $code[$i])] as $number => $line)
      {
        # Go through each character at a time
        for($char = 0; $char < mb_strlen($line); $char++)
        {
          # Draw the character if it isn't a space
          if($line[$char] != ' ')
            imagestring($image, 5, $x - 20 + mt_rand(-1, 1) + $char * 7, $y + $number * 7 - 25 + mt_rand(-1, 1), '*', $colour);
        }
      }
    }
  }
  
  # Blur for difficulty levels 2+
  if($difficulty >= 2)
  {
    # Make a copy of the image.
    if(version_compare($gd_version, '2') >= 0)
      $copy = imagecreatetruecolor($width, $height);
    else
      $copy = imagecreate($width, $height);
    
    # Now to actually copy the original image into the copy.
    imagecopy($copy, $image, 0, 0, 0, 0, $width, $height);
    
    # Merge the two, but move them slightly away from each other's centers, so that it blurs.
    imagecopymerge($image, $copy, 0, $difficulty - 1, 0, 0, $width, $height, 70);
    
    # We don't need the copy anymore, so free it.
    imagedestroy($copy);
  }
  
  # Set the image file type.
  header('Content-Type: image/png');

  # Output the image.
  imagepng($image);
  
  # Free the image resource's memory.
  imagedestroy($image);
}

function captcha_check($code)
{
  # Encode the code given and return whether it was correct
  if(isset($_SESSION['captcha']))
    return sha1(mb_strtolower($code)) == $_SESSION['captcha'];
  else
    return false;
}
?>