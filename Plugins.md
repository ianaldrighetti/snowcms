## What is a plugin? ##
In SnowCMS, the system can be extended through the creation of plugins. The systems behaviors can be modified through the use of the [API](http://snowcms.googlecode.com/svn/docs/files/core/api-class-php.html), which allows you to attach functions to hooks and filters. These hooks usually pass parameters to your attached functions, a lot of times, they are reference parameters, so data can be changed, or some piece of code can be executed for whatever reason. With a filter, there will always be one parameter, which is a value which can be modified by the function returning the modified value.

A quick example of adding a hook goes like this:
```
$api->add_hook('post_login', 'my_custom_login');

function my_custom_login(&$member)
{
  // Here you can modify the supplied array, $member, which
  // allows you to change their member id, name, etc. either
  // all, some, or none. This hook (post_login) would be likely
  // used if you were bridging another system to SnowCMS.
}
```

## Plugin package ##
Making a plugin is fairly simple, the plugin package can be put into a zip, tarball (not recommended) or gzipped tarball. Whichever you prefer... The package must contain at least two files, plugin.php and plugin.xml. Please note that when you put the files into a package, that you must have the files in the base directory! What I mean by that is do NOT go outside the folder, and then Right Click > 7-zip > Add to archive "whatever.zip", as when whatever.zip is extracted, the files will not be in the base directory of where it is extracted to, but to the location it was extracted to and then inside the directory whatever.

### plugin.php ###
The most important part of the plugin is the plugin.php file. This is the core part of a plugin, which is included, if the plugin is enabled, right after the API class is instantiated. Anything can be done inside the plugin.php, such as including other files for the plugin, adding hooks and so on.

### plugin.xml ###
This is an XML file which contains information about the plugin, like so:
```
<?xml version="1.0"?>
<plugin-info xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://themes.snowcms.com/xml plugin-info.xsd">
  <author>
    <name>Plugin authors name (required)</name>
    <website>Plugin authors website (optional)</website>
    <email>Plugin authors email (optional)</email>
  </author>
  <dependency-name>Dependency name of the plugin (required)</dependency-name>
  <name>Name of the plugin (required)</name>
  <description>Description of the plugin (optional, though recommended!)</description>
  <version>Version of the plugin (required)</version>
</plugin-info>
```

If the plugin.xml file in the package does not contain all the required values, then the plugin will be seen as invalid, and will not install.

## Dependency name ##
Originally, SnowCMS was going to allow plugins to specify dependencies (other plugins that the plugin needed in order to work), but it became to much to handle. However, each plugin still has a dependency name. This dependency name is simply a URL to where information, and the plugin itself, can be obtained.

An example would be plugins.snowcms.com/snowcms/captcha (no http:// included!). There is a recommended format, but of course, you don't have to abide by it. As seen, there is a sub domain, plugins.snowcms.com where plugins reside at the [SnowCMS](http://www.snowcms.com/) site, the first directory is the author of the plugin (in this case, SnowCMS, the developer team), the second directory is the plugins name (in this case, captcha).

Now, when that URL is accessed, the browser should be prompted to download the plugin package. However, there are some special requests that can be made as well.

### Check for updates ###
When the system is checking for updates, a POST request to the dependency name is made, with checkupdate (set to 1) and version (set to the current version of the plugin installed on the users site) as values. So instead of outputting the plugin file itself, it is expected to return the latest version of the plugin available for the specified version of the plugin.

Now, for example, say the version on the site is 1.0, but the plugin has versions 1.0, 1.0.1 and 1.1 available. When the request for update information is made, the server will receive version value of 1.0. If the plugin package 1.1 can update the plugin 1.0 all the way to 1.1 without needing to update to 1.0.1 first, then return 1.1, but if 1.1 cannot update the plugin version 1.0 to 1.1, then return 1.0.1. So it is your choice. But during the update process on the users server, a variable $current\_plugin\_version will be set, before install.php in the plugin is ran, that way if anything special needs to be done for the previous version.