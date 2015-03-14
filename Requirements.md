# Requirements #

Currently SnowCMS is still under active development, but a little information on some requirements couldn't hurt, right?

Here is a basic list of requirements, but they might not be all inclusive.
  * PHP 5 is a must, there is no PHP 4 support, and none is planned, after all, PHP 5 has been out for a very long time, if your host is still running PHP 4, you should be running away, screaming like a crazy person :-P.
  * The [XML Parser](http://www.php.net/manual/en/book.xml.php) extension is required, it is enabled in PHP 5 by default, but hey, you might have a sucky host!
  * MySQL, a version number is unknown, v5 is recommended, but v4 could work. Soon we plan to introduce SQLite v2 support, and hopefully SQLite v3, PostgreSQL and SQL Server.
  * Here are others that aren't required, but recommended:
    * cURL (for downloading remote files, such as plugins, though fsockopen works too, cURL is better :-))
    * mail() support (Not all hosts (free hosts) support the PHP mail function, it isn't required, as long as fsockopen works, SMTP can be used as well)
    * Zlib is _highly_ recommended, as it is used for the extraction of gzipped files, which is usually what plugins will be encased in, along with system updates.
    * JSON function support (From what I have seen, most hosts have this, but if not, don't worry! There are compatibility functions to emulate this, but of course, the ones built-in to PHP are always faster).
    * Safe mode OFF (No one likes safe mode...)
    * GD (Not required, but a good idea)
    * mbstring (multi-byte) support for better utf-8 support

Remember, this list may not incorporate all requirements, but I did as best as I could ;) Since SnowCMS is still in development, an installer has yet to be written, so unless you know what your doing, installing SnowCMS right now won't be a walk in the park.

### What was SnowCMS developed on? ###

Just incase you are wondering, SnowCMS is developed on IIS 7.5 with PHP 5.2.13 and MySQL 5.1.46-community