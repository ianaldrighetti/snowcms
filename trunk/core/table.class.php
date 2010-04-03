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
    if(isset($options['primary']))
      $this->tables[$tbl_name]['primary'] = $options['primary'];

    # Some options, only if there is a primary key defined!
    if(!empty($this->tables[$tbl_name]['primary']) && isset($options['options']) && is_array($options['options']))
      $this->tables[$tbl_name]['options'] = $options['options'];
    elseif(isset($options['options']))
      return false;

    # How about the callback? Changing that?
    if(!empty($this->tables[$tbl_name]['primary']) && isset($options['callback']) && is_callable($options['callback']))
      $this->tables[$tbl_name]['callback'] = $options['callback'];
    elseif(isset($options['callback']))
      return false;

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
      string $tbl_name - The name of the table. If left null, all tables will
                         be returned.

    Returns:
      mixed - Returns false if the specified table does not exist, but
              an array containing the tables information if it exists.
  */
  public function return_table($tbl_name = null)
  {
    if(!empty($tbl_name) && !$this->table_exists($tbl_name))
      return false;

    return empty($name) ? $this->tables : $this->tables[$tbl_name];
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
        position - The position at which to place the column (0 -> [NUM COLS] - 1).
                   If you were, for example, to add a column at position 0
                   then another at position 0, the last column added would
                   be first, and the first added would be second.
  */
  public function add_column($tbl_name, $column, $options)
  {
    # Does this column already exist? Silly!
    if(!$this->table_exists($tbl_name) || $this->column_exists($tbl_name, $column))
      return false;

    # Did you specify a position?
    if(isset($options['position']))
    {
      $position = (string)$options['position'] == (string)(int)$options['position'] ? (int)$options['position'] : null;
      unset($options['position']);
    }

    # We will validate the column.
    $options = $this->validate_column($column, $options);

    # Hm, didn't work. Good luck with that!
    if(empty($options))
      return false;

    # Add it! Maybe...
    if(!isset($position) || $position === null)
      $this->tables[$tbl_name]['columns'][$column] = $options;
    else
      # Insert it..!
      $this->tables[$tbl_name]['columns'] = array_insert($this->tables[$tbl_name]['columns'], $options, $position, $column);

    return true;
  }

  /*
    Method: edit_column

    Edits the specified column.

    Parameters:
      string $tbl_name - The name of the table the column is in.
      string $column - The name of the column to edit.
      array $options - An array containing new options.

    Returns:
      bool - Returns true if the column was updated successfully, false if not.
  */
  public function edit_column($tbl_name, $column, $options)
  {
    if(!$this->column_exists($tbl_name, $column))
      return false;

    # Did you specify a position?
    if(isset($options['position']))
    {
      $position = (string)$options['position'] == (string)(int)$options['position'] ? (int)$options['position'] : null;
      unset($options['position']);
    }

    # We will validate the column. To apply the changes, simply merge the old options.
    $options = $this->validate_column(array_merge($this->tables[$tbl_name]['columns'][$column], $options));

    # Hm, didn't work. Good luck with that!
    if(empty($options))
      return false;

    # Add it! Maybe...
    if(!isset($position) || $position === null)
      $this->tables[$tbl_name]['columns'][$column] = $options;
    else
    {
      # Delete the old one.
      unset($this->tables[$tbl_name]['columns'][$column]);

      # Insert it..! Again.
      $this->tables[$tbl_name]['columns'] = array_insert($this->tables[$tbl_name]['columns'], $options, $position, $column);
    }

    return true;
  }

  /*
    Method: validate_column

    Validates all the columns information.

    Parameters:
      array $options - The column options to be validated.

    Returns:
      array - Returns the validated column options, false on failure.
  */
  private function validate_column($options)
  {

  }

  /*
    Method: column_exists

    Checks whether or not the specified column exists.

    Parameters:
      string $tbl_name - The name of the table the column is in.
      string $column - The name of the column to check the existence of.

    Returns:
      bool - Returns true if the column exists, false if not.
  */
  public function column_exists($tbl_name, $column)
  {
    return isset($this->tables[$tbl_name]['columns'][$column]);
  }

  /*
    Method: remove

    Removes the specified column from the table.

    Parameters:
      string $tbl_name - The name of the table to remove the column from.
      string $column - The name of the column to remove.

    Returns:
      bool - Returns true if the column was moved, false if not.
  */
  public function remove($tbl_name, $column)
  {
    if(!$this->column_exists($tbl_name, $column))
      return false;

    unset($this->tables[$tbl_name]['columns'][$column]);
    return true;
  }

  /*
    Method: return_column

    Returns the columns information.

    Parameters:
      string $tbl_name - The name of the table column is in.
      string $column - The name of the column to get the information of.
                       Leave this null in order to have all columns returned.

    Returns:
      array - Returns the array containing the information, false if the table
              doesn't exist.
  */
  public function return_column($tbl_name, $column = null)
  {
    if(!empty($column) && !$this->column_exists($tbl_name, $column))
      return false;

    return empty($column) ? $this->tables[$tbl_name]['columns'] : $this->tables[$tbl_name]['columns'][$column];
  }
}
?>