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
?>
		</div>
		<div id="sidebar-container">
			<div id="sidebar">
				<?php
					// Get all widgets for the right sidebar.
					$widgets = theme_get_widgets('right-sidebar');

					// Check if there are any. If not, we'll just output a boring
					// ol' menu.
					if(count($widgets) == 0)
					{
						echo '
				<ul id="navigation">';

						theme_menu();

						echo '
				</ul>';
					}
					else
					{
						echo '
				<ul id="right-widgets">';

				// Iterate through all the widgets to display them.
				foreach($widgets as $widget)
				{
					echo '
					', $widget;
				}

						echo '
				</ul>';
					}
				?>
			</div>
		</div>
		<div style="clear:both;"></div>
	</div>
	<p id="footer">
				<?php theme_foot(); ?>
	</p>

</body>
</html>
