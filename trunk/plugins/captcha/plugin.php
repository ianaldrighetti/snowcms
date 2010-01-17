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
  Dependency Name: plugins.snowcms.com/snowcms/captcha
  Plugin Name: CAPTCHA
  Author: SnowCMS
  Version: 1.0
  Dependencies:
  Update URL: plugins.snowcms.com/snowcms/captcha
  Description:
    CAPTCHA is a plugin which allows you to integrate CAPTCHA
    images into features of SnowCMS.
*/

# Register the CAPTCHA image action.
$api->add_action('captcha', 'captcha_display', dirname(__FILE__). '/captcha.php');
?>