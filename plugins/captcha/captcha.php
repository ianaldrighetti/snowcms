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

# Title: Display CAPTCHA

/*
  Function: captcha_display

  Renders a CAPTCHA image, for those oh-so-fun CAPTCHA tests.

  Parameters:
    none

  Returns:
    void - Nothing is returned by this function.

  Note:
    This function is overloadable.
*/
function captcha_display()
{
  global $api, $settings;

  $api->run_hooks('captcha_display');

  # Now, let's make sure that the server supports this, it uses GD...
  if(!function_exists('imagecreate'))
  {
    die(l('Your server configuration does not support the <a href="%s">GD extension</a>.', 'http://www.php.net/gd'));
	}
  # We need an identifier for this CAPTCHA image.
  elseif(empty($_GET['id']))
  {
    die(l('No CAPTCHA identifier was supplied, could not complete your request.'));
	}

  # Do you have GD2? Then we can use imagecreatetruecolor.
  $gd_info = gd_info();
  $gd_version = substr($gd_info['GD Version'], strpos($gd_info['GD Version'], '(') + 1, strpos($gd_info['GD Version'], ' ', strpos($gd_info['GD Version'], '(')) - (strpos($gd_info['GD Version'], '(') + 1));
  if(version_compare($gd_version, '2') >= 0)
	{
    $image = imagecreatetruecolor($settings->get('captcha_width', 'int'), $settings->get('captcha_height', 'int'));
	}
  else
  {
    $image = imagecreate($settings->get('captcha_width', 'int'), $settings->get('captcha_height', 'int'));
	}

  # Make our background color.
  $background = imagecolorallocate($image, $api->apply_filters('captcha_bg_red', 255), $api->apply_filters('captcha_bg_green', 255), $api->apply_filters('captcha_bg_blue', 255));

  # Apply the color.
  imagefill($image, 0, 0, $background);

  # Let's make some noise!
  # In order to do so, let's make some random colors, shall we?
  $colors = array();
  for($i = 0; $i < 32; $i++)
  {
    $colors[] = imagecolorallocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
	}

  # Some background noise.
  $num_ellipses = mt_rand(10, 15);
  for($i = 0; $i < $num_ellipses; $i++)
  {
    imageellipse($image, mt_rand(0, $settings->get('captcha_width', 'int')), mt_rand(0, $settings->get('captcha_height', 'int')), mt_rand(1, 50), mt_rand(1, 25), $colors[array_rand($colors)]);
	}

  # Now for the text, 5-6 is usually good.
  $num_chars = $settings->get('captcha_num_chars', 'int');

  # The characters which we will use to put into the CAPTCHA.
  $chars = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789');

  $rand_str = '';
  for($i = 0; $i < $num_chars; $i++)
  {
    $rand_str .= $chars[mt_rand(0, strlen($chars) - 1)];
  }

  # Save it to their session, otherwise, this does no good!!!
  if(!isset($_SESSION['captcha_text']) || !is_array($_SESSION['captcha_text']))
  {
    $_SESSION['captcha_text'] = array();
  }

  $_SESSION['captcha_text'][$_GET['id']] = $rand_str;

  # TrueType font support? If so you are AWESOME.
  if(function_exists('imagettftext'))
  {
    $fonts = scandir(dirname(__FILE__). '/ttf/');
    foreach($fonts as $key => $font)
    {
      if(substr($font, strlen($font) - 3, strlen($font)) != 'ttf')
      {
        unset($fonts[$key]);
      }
    }

    for($i = 0, $x = 10; $i < $num_chars; $i++, $x += 30)
    {
      $size = ceil($settings->get('captcha_height', 'int') / 2) + mt_rand(-2, 2);
      $angle = mt_rand(-30, 30);
      $y = ceil($settings->get('captcha_height', 'int') / 2) + mt_rand(0, floor($settings->get('captcha_height', 'int') / 2) - 10);
      $fontfile = dirname(__FILE__). '/ttf/'. $fonts[array_rand($fonts)];

      imagettftext($image, $size, $angle, $x - 2, $y - 2, $colors[array_rand($colors)], $fontfile, $rand_str[$i]);
      imagettftext($image, $size, $angle, $x, $y, $colors[array_rand($colors)], $fontfile, $rand_str[$i]);
    }
  }
  else
  {
    # Dang... No TrueType support? It won't be as good, but it is possible.
    for($i = 0, $x = 10; $i < $num_chars; $i ++, $x += 30)
    {
      # Get the character image.
      $char_image = imagecreatefrompng(dirname(__FILE__). '/chars/'. strtoupper($rand_str[$i]). '.png');

      # Rotate the image.
      $char_image = imagerotate($char_image, mt_rand(-30, 30), imagecolortransparent($char_image, imagecolorallocate($char_image, 255, 255, 255)));

      # Copy it over.
      imagecopy($image, $char_image, $x, mt_rand(0, floor($settings->get('captcha_height', 'int') / 2) - 10), 0, 0, imagesx($char_image), imagesy($char_image));
      imagedestroy($char_image);
    }
  }

  # Let's create a grid
  $vertical = $settings->get('captcha_width', 'int') / 20;
  for($i = 0; $i < $vertical; $i++)
  {
    imageline($image, (20 * $i) + mt_rand(-5, 5), 0, (20 * $i) + mt_rand(-5, 5), $settings->get('captcha_height', 'int'), $colors[array_rand($colors)]);
	}

  $horizontal = $settings->get('captcha_height', 'int') / 10;
  for($i = 0; $i < $horizontal; $i++)
  {
    imageline($image, 0, (10 * $i) + mt_rand(-5, 5), $settings->get('captcha_width', 'int'), (10 * $i) + mt_rand(-5, 5), $colors[array_rand($colors)]);
	}

  # Remove any previous headers so we can send out new ones!
  if(ob_get_length() > 0)
  {
    ob_clean();
   }

  header('Pragma: no-cache');
  header('Content-Type: image/png');

  # Display the image.
  imagepng($image);

  # Destroy! Destory! >:D
  imagedestroy($image);
  exit;
}
?>