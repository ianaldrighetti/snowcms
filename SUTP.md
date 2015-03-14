# What is the SnowCMS Update Transmission Protocol? #

The SnowCMS Update Transmission Protocol (or SUTP for short) is the format in which SnowCMS makes requests to update servers for such things as version lists, version validating, checking for the latest update, and downloading the update itself.

When a plugin or theme (known as components) is created, they must supply an XML file containing information about that component. In a plugins XML file a _Globally Unique IDentifier_, or GUID, is specified. This GUID is pretty much just a URL which can be queried to receive information about the plugin's updates and the like. Themes also have something similar but it is called the update URL, and not required unlike the plugin's GUID, but the idea is the same.

While SnowCMS will have a central repository for plugins and themes which will automatically support the SUTP, the system can communicate with other update servers for information. To do that the plugins GUID should be a unique URL on your website which will respond to the queries for updates made by SnowCMS. In order to implement your own update server you will need to know what type of requests to expect from SnowCMS, so here it is.

updatecheck -- check for updates, verifyversion -- verifies specified version, download -- downloads the update, listversions -- lists available versions

## Type of Requests ##

Currently the SUTP outlines four types of requests, which will be specified via POST data with a key of requesttype. The following, in bold, are possible values of requesttype.

**updatecheck**

A request type of updatecheck means that someone is requesting for the latest version of the component. This type of request will usually also come with another POST key of version which will contain the version of the component that is checking for updates (we will get to requests made without a version in a bit).

The update server is to take that version and then find out which version the requesting system should update to -- if any.

But what if the requesting system supplies a version of 1.0, and there are multiple updates available (such as 1.0.1, 1.1 and 2.0)? That all depends on how you want to provide updates for your components. If v2.0 of your component can properly handle updating the component from 1.0 to 2.0 then by all means tell the requesting system to update to 2.0. However if the component must update from 1.0.1 to 1.1 and finally 1.1 to 2.0, then tell the requesting system to update to 1.0.1. So as said -- it all depends.

To tell the system which update the system should download, simply output that version to the page -- but nothing more!

So what if there are no updates available? There are a couple responses which are completely valid: HTTP 404 response code, a blank page, or just a response of UPTODATE.

**verifyversion**

A request type of verifyversion means that the version value supplied in the POST data is to be validated.

If the version supplied exists then the response should be a message body of EXISTS -- which means that the version of the component can be downloaded via a download request type.

However, if the version is not valid the response needs to be a message body of DOESNOTEXIST, a blank page, or an HTTP 404 response code.

**download**

A request type of download is self-explanatory, and will also be accompanied by a version value in the POST data.

This type of request expects the update server output the contents of the specified component version -- in a packaged form (zip, tar or tar.gz).

If the version specified does not exist then an HTTP 404 response code is expected.

**listversions**

The last request type supported is listversions, which is a request for the update server to generate a list of all versions of the component available.

The list is to contain one version per line, and each version can be followed by a string which identifies why the version was deprecated (a comma separating the two). Currently the only identifier supported is insecure, which means the version contains an identified vulnerability which could be exploited.

## Other Information ##

There is one other value which could be transmitted within the POST data, that being an updatekey.

This update key could be used for any purpose, and must be supplied via applying a filter to sha1(component directory) + '_updatekey'. Possible uses could be components which are not free and require a license, and the license key could be transmitted to the update server and could then be validated before the update server makes any response._

Though I have never made any sort of program/script that requires a license, so I don't know how useful this is. If anyone has any other suggestions, please let us know on the [developer forums](http://dev.snowcms.com).