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

class MySQL_Result extends Database_Result
{
  public function data_seek($row_num = 0)
  {
    return mysql_data_seek($this->result, $row_num);
  }

  public function fetch_array()
  {
    return mysql_fetch_array($this->result);
  }

  public function fetch_assoc()
  {
    return mysql_fetch_assoc($this->result);
  }

  public function fetch_object()
  {
    return mysql_fetch_object($this->result);
  }

  public function fetch_row()
  {
    return mysql_fetch_row($this->result);
  }

  public function field_name($field_offset)
  {
    return mysql_field_name($this->result, $field_offset);
  }

  public function free_result()
  {
    return mysql_free_result($this->result);
  }

  public function num_fields()
  {
    return mysql_num_fields($this->result);
  }

  public function num_rows()
  {
    return mysql_num_rows($this->result);
  }
}

$db_result_class = 'MySQL_Result';
?>