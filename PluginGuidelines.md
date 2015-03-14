## Guidelines ##
There are some basic guidelines for creating plugins for the SnowCMS platform. These guidelines are, of course, not enforced if the plugin is not hosted on the [SnowCMS](http://www.snowcms.com/) plugins site, but they are enforced if it is hosted on SnowCMS.

### Database compatibility ###
SnowCMS has an object, $db, which handles querying the database, the programmer does not know what the database type actually is (it could be MySQL, SQLite, PostgreSQL, etc.), and they don't need to know. The SQL engines, as they are called, handle any compatibility issues, such as modifying queries if they need to be. When the developer is making a query to the database (select, update, delete), the query should be formatted the way a MySQL query is, as the MySQL engine makes no modifications around compatibility, but all other engines conform around MySQL queries.

With that, you are not allowed to call mysql\_query (sqlite\_query, mssql\_query, etc) or any of those functions directory. The database object has all those available, and those should be used instead.

### Security ###
As with any platform, we want to ensure the system is as safe as it can possibly be. So you are not allowed to have a way for anyone to upload files into the plugins directory, as this could cause some security issue, such as someone uploading a malicious plugin into that directory, then somehow, the administrator install it. Though this scenario is highly unlikely, it doesn't matter! If anything can go wrong, it will go wrong! But of course, if you can demonstrate that there are security precautions in place, such as only administrators can do it or something such as that, we may be willing to allow it.

Another biggy is to NOT modify the system files in any way! That is what the API is for, to modify the systems behavior without changing the underlying code itself. Now we realized that there might be times where something needs to be done and can't be done due to a hook or filter not being in place, and we can help you by you helping us! Take a look at this [topic](http://dev.snowcms.com/index.php?topic=207.0), as it tells you how to request new hooks/filters.

The main reason why you should not modify system files is because when the system is updated, patches are not applied to the file, the whole file is overwritten with the new code, removing any modifications to the file.

### Tools ###
There are numerous tools available in SnowCMS, such as the [Form](http://snowcms.googlecode.com/svn/docs/files/core/form-class-php.html) class (for creating submittable forms), the [HTTP](http://snowcms.googlecode.com/svn/docs/files/core/http-class-php.html) class (for fetching files on the Internet) and numerous others. These are made to make the development of plugins easier, but also allow more extendability. For instance, the Form class automatically creates a hook which other plugins can hook into and then change the form without you doing anything, as for the HTTP class, other plugins can implement different ways to fetch files on the Internet (which, by default, already supports both fsockopen and cURL transparently).

So be sure to take a look at the [documentation for SnowCMS](http://snowcms.googlecode.com/svn/docs/index.html), as there just might be a tool to make your life easier!

### Conclusion ###
In conclusion, if a plugin submitted to SnowCMS is found to break any of these guidelines, the plugin will be rejected. That doesn't mean that the plugin cannot be installed, it still can be! However, it will not be offered on the SnowCMS plugin site until it is fixed.

Also, SnowCMS includes a feature that when a plugin is being installed, the hash of the plugin package to be installed is sent to the SnowCMS site. There the hash is searched for in the database, and information about that plugin will be returned. That means if your plugin is approved on the SnowCMS site, a message during installation will notify you of that, same goes for if it is disapproved (rejected). So if a plugin is disapproved, the person installing the plugin will have to then click on "Proceed with installation" link in order to continue.

Plugins can also be marked as malicious (purposely containing security issues), insecure (a vulnerability is found), deprecated (a newer version is available), unknown (the hash is unknown to SnowCMS) and pending (the plugin is still currently in review).

But of course, _even if a plugin is not approved, it can still be installed, the process just won't continue automatically_. We don't do this because we want to be evil or controlling (like a company that starts with an A and ends in pple :-P), but we want to help make sure that the people using SnowCMS get the best experience possible.

**NOTE:** If you have a plugin which is only available to those who pay, you can still get your plugin "approved" by SnowCMS. You will have to send the plugin package(s) to SnowCMS (not available right now, but will be soon!!!) so we can review them ourselves, we will simply test them and review the code, and then determine whether or not the plugin follows the "approved" guidelines. After that, we will delete the plugins.

For those of you who might be thinking, why on an open source system would you want to allow closed-source (or at least paid) plugins? Simple. We want our customer base to be as large as possible, and by allowing closed-source/paid-only plugins, we can further extend our user base. But of course, if you happen to be a developer making money off of plugins, we (SnowCMS) wouldn't mind getting a little donation :-P.