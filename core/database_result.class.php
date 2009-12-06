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
  Class: Database_Result

  This is another abstract class for SnowCMS's databasing system. This
  is a class which is returned by the <Database::query> and <Database::insert>
  methods.
*/
abstract class Database_Result
{
  # Variable: result
  # Contains the resource returned from the execution of the query.
  protected $result = null;

  # Variable: affected_rows
  # The number of rows affected from the query that returned this object.
  protected $affected_rows = 0;

  # Variable: insert_id
  # The number of the latest ID generated by an automatically incrementing column.
  protected $insert_id = 0;

  # Variable: errno
  # The error number which occurred during the processing of the query.
  protected $errno = 0;

  # Variable: error
  # The error string from the error which occurred during the processing of the query.
  protected $error = null;

  # Variable: query_num
  # The number of this query. 0 means it was the first query executed, 1 means second,
  # 2 means 3, etc. etc.
  protected $query_num = 0;

  /*
    Constructor: __construct

    The constructor of Database_Result which populates the attributes.

    Parameters:
      resource $result - The result returned from the databases query function.
      int $affected_rows - The number of rows affected by the query (INSERT, IGNORE, REPLACE, UPDATE)
      int $insert_id - The last ID generated by an automatically incremented column.
      int $errno - The error number generated by the query, if any.
      string $error - The error string generated by the query, if any.
      int $query_num - The queries number.
  */
  public function __construct($result, $affected_rows, $insert_id, $errno, $error, $query_num)
  {
    $this->result = !empty($result) ? $result : null;
    $this->affected_rows = (int)$affected_rows;
    $this->insert_id = max((int)$insert_id, 0);
    $this->errno = max((int)$errno, 0);
    $this->error = !empty($error) ? $error : null;
    $this->query_num = max((int)$query_num, 0);
  }

  /*
    Method: affected_rows

    Parameters:
      none

    Returns:
      int - Returns the number of affected rows by the query.
  */
  public function affected_rows()
  {
    return $this->affected_rows;
  }

  /*
    Method: insert_id

    Parameters:
      none

    Returns:
      int - Returns the last ID of an automatically incremented column.
  */
  public function insert_id()
  {
    return $this->insert_id;
  }

  /*
    Method: errno

    Parameters:
      none

    Returns:
      int - Returns the error number from the last query, if any.
  */
  public function errno()
  {
    return $this->errno;
  }

  /*
    Method: error

    Parameters:
      none

    Returns:
      string - Returns a human readable error if any error occurred
               during the exection of the query.
  */
  public function error()
  {
    return $this->error;
  }

  /*
    Method: query_num

    Parameters:
      none

    Returns:
      int - The number of this query. 0 means it was the first query executed,
            1 means second,  2 means third, etc. etc.
  */
  public function query_num()
  {
    return $this->query_num;
  }

  /*
    Method: error

    Parameters:
      none

    Returns:
      bool - Returns TRUE if the query was successfully executed, FALSE if not.
  */
  public function success()
  {
    return !empty($this->result);
  }

  /*
    Method: data_seek

    Moves the internal pointer to the specified row number.

    Parameters:
      int $row_num - The row number you want to move the pointer to.

    Returns:
      bool - Returns TRUE on success, FALSE on failure.

  */
  abstract public function data_seek($row_num = 0);

  /*
    Method: fetch_array

    Fetches the results array (Both numeric and associative indexes) and increments
    the internal pointer.

    Parameters:
      none

    Returns:
      array

  */
  abstract public function fetch_array();

  /*
    Method: fetch_assoc

    Fetches the results array (associative indexes only) and increments the internal pointer.

    Parameters:
      none

    Returns:
      array
  */
  abstract public function fetch_assoc();

  /*
    Method: fetch_object

    A lot like fetch_assoc, however, an object (stdClass) is returned instead of an array.

    Parameters:
      none

    Returns:
      object
  */
  abstract public function fetch_object();

  /*
    Method: fetch_row

    Fetches the results array (numeric indexes only) and increments the internal pointer.

    Parameters:
      none

    Returns:
      array
  */
  abstract public function fetch_row();

  /*
    Method: field_name

    Fetches the name of the field corresponding to the field offset.

    Parameters:
      int $field_offset - The index of the field to fetch the name of.

    Returns:
      string - Returns the name of the field, FALSE on failure.
  */
  abstract public function field_name($field_offset);

  /*
    Method: free_result

    Frees all memory associated with the result received from a query execution.

    Parameters:
      none

    Returns:
      bool - Returns TRUE on success, FALSE on failure.

    Note: According to most people, you should ONLY free a result IF and ONLY IF
          the query you executed returns huge result sets, otherwise you can end up
          using more memory in the process of freeing the result.
  */
  abstract public function free_result();

  /*
    Method: num_fields

    Returns the number of fields (columns) in a query (SELECT).

    Parameters:
      none

    Returns:
      int - Returns the number of fields from a query.
  */
  abstract public function num_fields();

  /*
    Method: num_rows

    Returns the number of rows from a result set.

    Parameters:
      none

    Returns:
      int - Returns the number of rows from a result set.
  */
  abstract public function num_rows();
}
?>