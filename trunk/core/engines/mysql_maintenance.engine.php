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

        $result = $db->query('
          INSERT INTO `{raw:backup_table}`
          SELECT
            *
          FROM `{raw:table}`',
          array(
            'backup_table' => $backup_table,
            'table' => $table,
          ));

        return $result->success();
      }
    }

    return false;
  }

  public function optimize_table($table)
  {
    global $db;

    # Simple enough.
    $result = $db->query('
      OPTIMIZE TABLE `{raw:table}`',
      array(
        'table' => $table,
      ));

    return $result->success();
  }

  public function table_sql($table, $drop_if_exists = false, $fp = null)
  {
    global $db;

    # MySQL has a specific query to do this.
    $result = $db->query('
      SHOW CREATE TABLE `{raw:table}`',
      array(
        'table' => $table,
      ));

    # Didn't work? Sad.
    if(!$result->success())
      return false;

    list(, $table_sql) = $result->fetch_row();

    # Prepend DROP TABLE IF EXISTS?
    if(!empty($drop_if_exists))
    {
      $table_sql = "DROP TABLE IF EXISTS `{$table}`;\r\n{$table_sql}";
    }

    # Returning it or writing it to a file?
    if(empty($fp))
    {
      return $table_sql;
    }
    else
    {
      return fwrite($fp, $table_sql) > 0;
    }
  }

  public function insert_sql($table, $extended_inserts = false, $rows_per_insert = 10, $start = false, $rows = false, $fp = null)
  {
    global $db;

    # Get the data out.
    $result = $db->query('
                SELECT
                  *
                FROM `{raw:table}`'. ($start !== false && $rows !== false ? '
                LIMIT {int:start}, {int:rows}' : ''),
                array(
                  'table' => $table,
                  'start' => $start,
                  'rows' => $rows,
                ));

    # Anything?
    if($result->num_rows())
    {
      # We need to get the column names.
      $columns = array_keys($result->fetch_assoc());

      # But we still need that data! :P
      $result->data_seek(0);

      $rows = array();
      while($row = $result->fetch_assoc())
      {
        # Just add all the data.
        # After we sanitize it ;)
        foreach($row as $key => $value)
        {
          $row[$key] = $db->escape($value);
        }

        $rows[] = '(\''. implode('\', \'', array_values($row)). '\')';
      }

      # Rows per insert <= 1?
      if($rows_per_insert <= 1)
      {
        # Then that isn't extended inserts! You silly ;)
        $extended_inserts = false;
      }

      # Okay, now time to build the quer(y|ies) ;-)
      if(!empty($extended_inserts))
      {
        # With extended inserts, woo!
        $rows = array_chunk($rows, $rows_per_insert);
        $db_query = '';
        foreach($rows as $row)
        {
          $db_query .= 'INSERT INTO `'. $table. '` (`'. implode('`, `', $columns). '`) VALUES'. implode(', ', $row). ';'. "\r\n";
        }
      }
      else
      {
        $db_query = '';
        foreach($rows as $row)
        {
          $db_query .= 'INSERT INTO `'. $table. '` (`'. implode('`, `', $columns). '`) VALUES'. $row. ';'. "\r\n";
        }
      }

      # Writing it to a file?
      if(empty($fp))
      {
        return $db_query;
      }
      else
      {
        return fwrite($fp, $db_query) > 0;
      }
    }
    else
    {
      # Just an empty string.
      return '';
    }
  }
}

$db_maintenance_class = 'MySQL_Maintenance';
?>