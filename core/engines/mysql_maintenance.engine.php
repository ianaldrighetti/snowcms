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

class MySQL_Maintenance extends Database_Maintenance
{
  public function backup_table($table, $backup_table)
  {
    global $db;

    # Get the create table query.
    $create_table = $this->table_sql($table);

    if(!empty($create_table))
    {
      # Alright, before we can create the table, we must rename
      # the tables name...
      $create_table = str_replace($table, $backup_table, $create_table);

      # Now make it!
      $result = $db->query($create_table);

      if($result->success())
      {
        # The new table was created, now copy the data. Pretty easy with MySQL.
        # INSERT SELECT :-)

        # !!! TODO
      }
    }

    return false;
  }
}

$db_maintenance_class = 'MySQL_Maintenance';
?>