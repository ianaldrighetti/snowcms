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
  Class: Database_Maintenance

  Database_Maintenance contains tools for backing up tables, backing up
  the data contained within those tables, optimization, and also the
  creation of and altering of tables.
*/
abstract class Database_Maintenance
{
  /*
    Method: backup_table

    Creates a copy of $table into $backup_table.

    Parameters:
      string $table - The original table to backup.
      string $backup_table - The table to copy $table's data over to (The table $backup_table
                             should not be created, as this method will do that)

    Returns:
     bool - TRUE if the table was successfully backed up, FALSE on failure.

  */
  abstract public function backup_table($table, $backup_table);

  /*
    Method: optimize_table

    Optimizes the specified table.

    Parameters:
      string - The table to optimize.

    Returns:
     bool - TRUE on success, FALSE on failure.

    Note:
       If the database system has a command that optimizes the whole database (Ex: SQLite)
          it is recommended you have a static variable to check if this method was already called.

  */
  abstract public function optimize_table($table);

  /*
    Method: table_sql

    Generates the CREATE TABLE command for the specified table, if $file is specified,
    append that data to the specified file and return a bool (TRUE on success, FALSE on failure),
    otherwise return the string containing the CREATE TABLE.

    Parameters:
      string $table - The table to generate the CREATE TABLE command for.
      bool $drop_if_exists - Whether or not to include DROP TABLE IF EXISTS `$table` command before  the
                             the CREATE TABLE. (Be sure the database supports such a command)
      string $fp - The file pointer to write the command to.

    Returns:
     mixed - Could return a string containing the command ($file empty) or a bool containing
             whether or not the command was successfully written to $fp.

  */
  abstract public function table_sql($table, $drop_if_exists = false, $fp = null);

  /*
    Method: insert_sql

    Generates the INSERT INTO commands for the table data contained within the specified table.
    If $file is specified, append that data to the file, otherwise return the string containing the data.

    Parameters:
      string $table - The table to get the data from.
      bool $extended_inserts - Whether or not to use extended inserts in the backup, if the database
                               type doesn't support extended inserts, ignore this.
      int $rows_per_insert - If $extended_inserts is true, this contains the number of rows that should be
                             included in a single INSERT INTO command.
      int $start - Where to start reading the data from (LIMIT $start, $rows). If this is false, don't
                   include a LIMIT clause in your SELECT * query.
      int $rows - The number of rows to retrieve from the SELECT * query (LIMIT $start, $rows). If this is false,
                  don't include a LIMIT clause in your SELECT * query.
      string $fp - The file pointer to write the data commands to.

    Returns:
     mixed - Could return a string containing the data ($file empty) or a bool containing
             whether or not the data was successfully written to $fp.

  */
  abstract public function insert_sql($table, $extended_inserts = false, $rows_per_insert = 10, $start = false, $rows = false, $fp = null);

  /*
    Method: create_table

    Creates a table with the specified table name, columns and indexes.

    Parameters:
      string $table - The name of the table to create.
      array $columns - An array of columns which contains each columns information. Ex:
                       $columns = array('col_name' => array('type' => 'int', 'length' => 10, 'attributes' => 'unsigned', 'null' => false, 'auto_increment' => true), 'another_col' => array(...))
                       Note:
       Make note that any MySQL datatype could be used in type, so if you get a MySQL only supported datatype, use a similar one of equal or greater capabilities.
                             attributes could be UNSIGNED, ZEROFILL or BINARY.
      array $indexes - Any indexes (PRIMARY, UNIQUE or INDEX) you want added to the table. Example:
                       array(
                          array(
                            'columns' => array('id'),
                            'type' => 'primary',
                          ), // Creates a PRIMARY index on the column id
                          array(
                            array(
                              'columns' => array('first_name', 'last_name'),
                              'type' => 'unique',
                            ), // Creates a UNIQUE indexes on BOTH first_name and last_name
                          ),
                       )
      string $on_exists - What to do if the table you want to create already exists.
                          Options:
                            fail - False is returned signifying table creation failed.
                            overwrite - Drops the old table (And it's data!!!) and replaces it with the newer one.
                            update - Adds the columns that weren't found to the table.

  */
  abstract public function create_table($table, $columns, $indexes = array(), $on_exists = 'fail');

  /*
    Method: alter_table

    This method is still a Work In Progress, and could change at any time.

    Parameters:
      string $table - The name of the table to alter.
      array $column - An array containing column/index information.
      string $type - The type of alteration which is being done, which can
                     be either 'column' or 'index'
      string $location - If the column does not exist, where should it be placed?
                         This can either be at_first, ar_end or a column name
                         (if a column name is supplied, the column you are adding will
                         be added after it). However, you can also specify 'fail'
                         which if the column already exists, this method will return FALSE.

    Returns:
      bool - Returns TRUE if the column/index was added/updated successfully, FALSE
             on failure.
  */
  abstract public function alter_table($table, $column, $type = 'column', $location = 'at_end');
}
?>
