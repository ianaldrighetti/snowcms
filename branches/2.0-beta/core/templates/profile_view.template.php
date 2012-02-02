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

			echo '
			<h1>', l('%s\'s profile', api()->context['member_info']['name']), (member()->can('edit_other_profiles') || $_GET['id'] == member()->id() ? ' <span style="font-size: 50%;">'. l('<a href="%s" title="Edit this account">Edit</a>', baseurl. '/index.php?action=profile&amp;id='. $_GET['id']. '&amp;edit'). '</span>' : ''), '</h1>
			<div class="profile_view_data">';

			if(is_array(api()->context['display_data']) && count(api()->context['display_data']) > 0)
			{
				foreach(api()->context['display_data'] as $data)
				{
					if(!empty($data['is_hr']))
					{
						echo '
				<p>&nbsp;</p>
				<hr class="profile_view" size="1" />
				<p>&nbsp;</p>';

						continue;
					}
					elseif(empty($data['label']) || empty($data['value']) || empty($data['show']))
					{
						continue;
					}

					echo '
				<div class="left">
					<label', (!empty($data['title']) ? ' title="'. $data['title']. '"' : ''), '>', $data['label'], '</label>
				</div>
				<div class="right">
					', $data['value'], '
				</div>
				<div class="break">
				</div>';
				}
			}

			echo '
			</div>';
?>