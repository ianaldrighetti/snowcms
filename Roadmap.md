# Road Map for SnowCMS v2.0 #

### 2.0 alpha ###

_SnowCMS 2.0 alpha is now completed, and may be downloaded on our [Downloads page](http://code.google.com/p/snowcms/downloads/list)._

This version will have the basic functionality working, such as adding plugins (√), managing them (updating (√), disabling (√), activating (√), deleting (√)), updating the system (√), system settings (√), managing users (√), group permissions (√), theme management (installing (√), updating (√), deleting (√)) and the like.

2.0 alpha will finally have a working installer (√).

What won't be quite working: anything but MySQL.

### 2.0 alpha 2 ###

_Note: while we will not be releasing a 2.0 alpha 2 version, this stage is currently completed._

A lot will change in 2.0 alpha 2, however, little will change that would be noticed by the one using the system, these are all underlying (unless there are bug fixes).

Currently in order to access $api, $member, etc. you must first _global_ them. This can be quite a pain, so these objects will be encapsulated in a function (api(), member(), etc.) which will return these objects, and can be used like you would its variable counterpart (member()->is\_logged() instead of $member->is\_logged()) (√, 6/19/2011).

Not only will the objects be accessed without needing to _global_ them, system variables such as $base\_url, $core\_dir, $plugin\_dir and so forth will be converted to constants, such as baseurl, coredir, plugindir which means no globaling needed (√).

There will be one other major change: the Theme class. Currently the source files have embedded HTML, which means if a plugin really wanted to they could not easily modify the HTML. In order to get passed this both the Theme methods header and footer will be removed, and when the page is finally to be displayed a new method called render will be called, which will be passed a parameter called $template, this is a template file containing the HTML in between the overall header and footer of the page. Plugins can then hook into the render method and reroute the location of the template file to wherever they please (√).

### 2.0 alpha 3 ###

_Note: there will be no 2.0 alpha 3 release, as we will skip right to SnowCMS 2.0 beta. However part of this stage has been completed, that being the new Form and Input classes._

This alpha release will have two main objectives: allow the creation of forms in a much more flexible manner while still having the security functionality of the Form class... and **SQLite**!

The first objective will be accomplished by remaking the Form class, and separating it into two components. Any part of the system using the Form class will operate without any (or very little... we'll see!) modifications, a new class called FormProcessor (name not final) which will process the submission of the Form. The FormProcessor will be passed all the information about the inputs in the form to be processed and sanitize, check, verify, etc. the data of the form, the added bonus is you can code the form yourself.

### 2.0 beta ###

_SnowCMS 2.0 beta is currently a work-in-progress. You can monitor changes through the [commit changes list](http://code.google.com/p/snowcms/source/list)._

In celebration of finally leaving the alpha stages, there will be a brand new (and much better) default administrative control panel layout! Yay. You can check out a [sneak peak of the new layout on the SnowCMS blog](http://www.snowcms.com/sneak-peak-beta-cp-102/).

Resolve [issue #69](https://code.google.com/p/snowcms/issues/detail?id=#69) and [issue #71](https://code.google.com/p/snowcms/issues/detail?id=#71).

### 2.0 beta 2 ###

This may no longer be required, unless a significant amount of bugs are found in 2.0 beta.

### 2.0 RC ###

Just a few bugs left to squash, almost there! But just to be sure...

### 2.0 final ###

Once all known bugs and issues are fixed, we can finally call SnowCMS 2.0 complete... Next stop? SnowCMS 2.1!

_**This road map could change in the future, currently it is highly unlikely for the alpha releases to be changed at all (though some could be added), but expect more than one beta and at least one release candidate, all of which will mainly be bug fixes.**_

# Road Map for SnowCMS v2.1 #

This road map is still in planning, however the goal of the 2.1 release is to improve the database abstraction layer to be much more flexible and allow as many different database types as possible without having to alter the queries using so many string manipulation functions.