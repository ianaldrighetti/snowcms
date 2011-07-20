<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//            Released under the Microsoft Reciprocal License             //
//                 www.opensource.org/licenses/ms-rl.html                 //
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

if(!defined('INSNOW'))
{
	die('Nice try...');
}
?>	<h<?php echo substr($_SERVER['QUERY_STRING'], 0, 12) == 'action=admin' ? 3 : 1; ?>><?php echo api()->context['error_title']; ?></h<?php echo substr($_SERVER['QUERY_STRING'], 0, 12) == 'action=admin' ? 3 : 1; ?>>
	<p><?php echo api()->context['error_message']; ?></p>