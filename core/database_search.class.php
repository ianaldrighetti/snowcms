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
  Class: Database_Search

  An abstract class database engines implement in order to allow plugins
  to search a table.
*/
abstract class Database_Search
{
  /*
    Constructor: __construct
  */
  public function __construct()
  {

  }

  /*
    Method: index_table

    Creates an index for the specified table.

    Parameters:
      string $table - The table to index.
      string $area_name - Either the value for the area name, or the column
                          name in the table which identifies the area for a
                          specific row.
      bool $area_name_is_column - True if $area_name is a column in the table,
                                  false if not.
      mixed $area_id - Either the value for the area id (must be an integer)
                       or a column name in the table which identifies the area
                       id for a specific row (must be a string).
      string $id_column - The column in the table which identifies the current
                          row, such as a messages id.
      string $message_column - The column which contains the message to be indexed.
  */
}
?>