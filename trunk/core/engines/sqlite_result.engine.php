<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//                  Released under the GNU GPL v3 License                 //
//                    www.gnu.org/licenses/gpl-3.0.txt                    //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//       SnowCMS originally pawned by soren121 started in early 2008      //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//                  SnowCMS v2.0 began in November 2009                   //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                       File version: SnowCMS 2.0                        //
////////////////////////////////////////////////////////////////////////////

if(!defined('INSNOW'))
{
	die('Nice try...');
}

class SQLite_Result extends Database_Result
{
	private function sqlite_data_seek($result, $num_row)
	{
		return $num_row == 0 ? sqlite_rewind($result) : sqlite_seek($result, $num_row);
	}

	public function data_seek($row_num = 0)
	{
		// Got something to do?
		$return = null;
		$current = $this->current;
		api()->run_hooks('database_data_seek', array($this->result, $row_num, &$return, &$current));
		$this->current = $current;

		return $return === null ? $this->sqlite_data_seek($this->result, $row_num) : $return;
	}

	public function fetch_array()
	{
		$return = null;
		$current = $this->current;
		api()->run_hooks('database_fetch_array', array($this->result, &$return, &$current));
		$this->current = $current;

		return $return === null ? sqlite_fetch_array($this->result) : $return;
	}

	public function fetch_assoc()
	{
		$return = null;
		$current = $this->current;
		api()->run_hooks('database_fetch_assoc', array($this->result, &$return, &$current));
		$this->current = $current;

		return $return === null ? sqlite_fetch_array($this->result, SQLITE_ASSOC) : $return;
	}

	public function fetch_object()
	{
		$return = null;
		$current = $this->current;
		api()->run_hooks('database_fetch_object', array($this->result, &$return, &$current));
		$this->current = $current;

		return $return === null ? sqlite_fetch_object($this->result) : $return;
	}

	public function fetch_row()
	{
		$return = null;
		$current = $this->current;
		api()->run_hooks('database_fetch_row', array($this->result, &$return, &$current));
		$this->current = $current;

		return $return === null ? sqlite_fetch_array($this->result, SQLITE_NUM) : $return;
	}

	public function field_name($field_offset)
	{
		$return = null;
		api()->run_hooks('database_field_name', array($this->result, $field_offset, &$return));

		return $return === null ? sqlite_field_name($this->result, $field_offset) : $return;
	}

	public function free_result()
	{
		$this->result = null;
		return true;
	}

	public function num_fields()
	{
		$return = null;
		api()->run_hooks('database_num_fields', array($this->result, &$return));

		return $return === null ? sqlite_num_fields($this->result) : $return;
	}

	public function num_rows()
	{
		$return = null;
		api()->run_hooks('database_num_rows', array($this->result, &$return));

		return $return === null ? sqlite_num_rows($this->result) : $return;
	}
}

$db_result_class = 'SQLite_Result';
?>