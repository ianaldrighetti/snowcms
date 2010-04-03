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
  Class: Table

  With the Table class, you can query the database and display the results
  in an orderly fashion. Using this class you can quickly generate lists
  which can be sorted, managed, and so on.
*/
class Table
{
  # Variable: tables
  private $tables;

  /*
    Constructor: __construct

    Parameters:
      none
  */
  public function __construct()
  {
    $this->tables = array();
  }

  /*
    Method: add

    Adds a new table, with which, columns can be added.

    Parameters:
      string $tbl_name - The name of the table to add.
      array $options - An array containing options for the table.

    Returns:
      bool - Returns true if the table was added, false if the table already
             exists.

    Note:
      The following are supported options:
        db_query - The query which will return the data which will be
                   displayed in the table.
        db_vars - An array containing variables which will be replaced
                  in the query. Just as would be done with the $db object.
        primary - The primary column of the query. Only required if you want
                  to allow users to select rows, this should be a column
                  which uniquely identifies the row in some way. For example,
                  member_id.
        options - This is an associative array which specifies actions which
                  can be done with selected rows (identifier => action label).
                  If this is set, callback is required.
        callback - The callback which will take the selected action (identifier)
                   and an array containing the selected rows identifier (primary).
  */
  public function add($tbl_name, $options = array())
  {
    if($this->table_exists($tbl_name) || empty($db_query) || !is_array($db_vars))
      return false;

    # Make the array, which we will edit ;)
    $this->tables[$tbl_name] = array(
                                 'columns' => array(),
                                 'db_query' => null,
                                 'db_vars' => null,
                                 'primary' => null,
                                 'options' => array(),
                                 'callback' => null,
                               );

    # Now try to edit it.
    if(!$this->edit($tbl_name, $options))
    {
      # It didn't work. Uh oh!
      $this->remove($tbl_name);
      return false;
    }

    # Added!
    return true;
  }

  /*
    Method: edit

    Edits the specified table.

    Parameters:
      string $tbl_name - The table handle to edit.
      array $options - An array containing the new options (See <Table::add>'s note).

    Returns:
      bool - Returns true if the table was edited successfully, false if the
             table does not exist.
  */
  public function edit($tbl_name, $options = array())
  {
    if(!$this->table_exists($tbl_name) || !is_array($options))
      return false;

    # The database query, important.
    if(!empty($options['db_query']))
      $this->tables[$tbl_name]['db_query'] = $options['db_query'];
    elseif(isset($options['db_query']))
      return false;

    # The database variables, important, usually.
    if(isset($options['db_vars']) && is_array($options['db_vars']))
      $this->tables[$tbl_name]['db_vars'] = $options['db_vars'];
    elseif(isset($options['db_vars']))
      return false;

    # The primary column identifier, not necessarily important, but can be!
    if(!empty($options['primary']))
      $this->tables[$tbl_name]['primary'] = $options['primary'];

    # Some options, only if there is a primary key defined!
    if(

    return true;
  }

  /*
    Method: table_exists

    Returns whether or not the table handle exists.

    Parameters:
      string $tbl_name - The name of the tables handle.

    Returns:
      bool - Returns true if the table exists, false if not.
  */
  public function table_exists($tbl_name)
  {
    return is_string($tbl_name) && strlen($tbl_name) > 0 && isset($this->tables[$tbl_name]);
  }

  /*
    Method: remove

    Removes the specified table.

    Parameters:
      string $tbl_name - The name of the tables handle.

    Returns:
      bool - Returns true if the table was removed successfully, false if
             the table doesn't exist.
  */
  public function remove($tbl_name)
  {
    if(!$this->table_exists($tbl_name))
      return false;

    unset($this->tables[$tbl_name]);
    return true;
  }

  /*
    Method: return_table

    Returns the specified tables information.

    Parameters:
      string $tbl_name - The name of the table.

    Returns:
      mixed - Returns false if the specified table does not exist, but
              an array containing the tables information if it exists.
  */
  public function return_table($tbl_name)
  {
    return $this->table_exists($tbl_name) ? $this->tables[$tbl_name] : false;
  }

  /*
    Method: add_column

    Adds a column to the specified table.

    Parameters:
      string $tbl_name - The name of the table.
      string $column - The name of the column to add.
      array $options - An array containing the columns options.

    Returns:
      bool - Returns true if the column was successfully added,
             false if the column already exists or if the supplied
             information was incorrect.

    Note:
      Here are the following supported options:
        column - The columns identifier from in the result set.
                 For example, if this was member_id, and you were
                 querying the members table, the value which would
                 appear would be the member id of that row. Defaults
                 to the false if not supplied, which means there is
                 no column for the column, as it will just take the row
                 information in the defined function. You can
                 also specify a function to handle the output which
                 will appear in the column, if that is done, the
                 function will receive the whole rows array, not just
                 the column specific one.
        label - The label of the column, in the header row.
        title - The mouse over text of the column for the label.
        sortable - Whether or not the column should be allowed to be
                   sorted by. This cannot be enabled for columns which
                   do not come from the database (Ex: column not set),
                   however, you can set it to not be sortable even if
                   technically it can. For whatever reason.
        function - A function which will accept an array containing
                   the current row result set, and return a string
                   which will be displayed in that specific column.
                   Required if column is not specified.
}
?>