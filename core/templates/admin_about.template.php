<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

  echo '
  <h1><img src="', theme()->url(), '/style/images/about-small.png" alt="" /> ', l('About SnowCMS'), '</h1>
  <p>', l('SnowCMS is a light, powerful and free content management system, otherwise known as a CMS. It has a powerful plugin system allowing you to have minor changes made to your site, or large features such as a forum, blog, or both! By default SnowCMS only has a member management and plugin system, meaning you can have your site with as few or as many features as you want, and nothing more. SnowCMS is written in the popular language <abbr title="PHP: Hypertext Preprocessor">PHP</abbr> and uses MySQL or SQLite for storage.'), '</p>
  <br />
  <p>', l('SnowCMS is released under the <a href="http://www.gnu.org/licenses/quick-guide-gplv3.html" title="GNU General Public License v3">GPL v3</a> license, meaning you are free to use, modify and redistribute SnowCMS if you so please. While you do have those freedoms, please keep in mind that a lot of work was put into SnowCMS by the <a href="http://www.snowcms.com/">SnowCMS Developer Team</a>, but also no warranty is provided by this software, nor are we or anyone else responsible for anything that may occur while using this system.'), '</p>

  <h3>', l('Developers'), '</h3>
  <p>', l('The following people are currently, or have been previously, major contributors to the <a href="http://www.snowcms.com/" title="SnowCMS">SnowCMS</a> project, we thank them for all their help!'), '</p>
  <ul>
    <li>Ian Aldrighetti (aldo) - ', l('Lead Developer of SnowCMS v0.7, 1.0 and 2.0'), '</li>
  </ul>

  <h3>', l('Credits'), '</h3>
  <p>', l('There are a few places where SnowCMS used the works of others, and this section is dedicated to their credit!'), '</p>
  <ul>
    <li>', l('Admin Control Panel icons from the <a href="http://kde-look.org/content/show.php/Oxygen+Icons?content=74184" title="Oxygen Icon set" target="_blank">Oxygen Icon set</a>.'), '</li>
    <li>', l('Admin Control Panel inspired by the <a href="http://www.jaws-project.com/" title="Jaws Project" target="_blank">Jaws Project</a>.'), '</li>
  </ul>

  <h1 style="margin-top: 20px;"><img src="', theme()->url(), '/about-small.png" alt="" /> ', l('System Information'), '</h1>
  <p><strong>Operating system:</strong> ', admin_get_os_information(), '</p>
  <p><strong>Server software:</strong> ', admin_get_software_information(), '</p>
  <p><strong>PHP version:</strong> ', PHP_VERSION, '</p>
  <p><strong>Database:</strong> ', db()->type, '</p>
  <p><strong>Database version:</strong> ', db()->version(), '</p>';
?>