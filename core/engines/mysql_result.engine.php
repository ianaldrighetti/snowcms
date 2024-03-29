<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//            Released under the Microsoft Reciprocal License             //
//                 www.opensource.org/licenses/ms-rl.html                 //
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

class MySQL_Result extends Database_Result
{
	public function data_seek($row_num = 0)
	{
		static $run_hooks = false;

		$return = null;
		if($run_hooks)
		{
			$current = $this->current;
			api()->run_hooks('database_data_seek', array($this->result, $row_num, &$return, &$current));
			$this->current = $current;
			$run_hooks = $return !== null;
		}

		return $return === null ? mysql_data_seek($this->result, $row_num) : $return;
	}

	public function fetch_array()
	{
		static $run_hooks = false;

		$return = null;
		if($run_hooks)
		{
			$current = $this->current;
			api()->run_hooks('database_fetch_array', array($this->result, &$return, &$current));
			$this->current = $current;
			$run_hooks = $return !== null;
		}

		return $return === null ? mysql_fetch_array($this->result) : $return;
	}

	public function fetch_assoc()
	{
		static $run_hooks = true;

		$return = null;
		if($run_hooks)
		{
			$current = $this->current;
			api()->run_hooks('database_fetch_assoc', array($this->result, &$return, &$current));
			$this->current = $current;
			$run_hooks = $return !== null;
		}

		return $return === null ? mysql_fetch_assoc($this->result) : $return;
	}

	public function fetch_object()
	{
		static $run_hooks = true;

		$return = null;
		if($run_hooks)
		{
			$current = $this->current;
			api()->run_hooks('database_fetch_object', array($this->result, &$return, &$current));
			$this->current = $current;
			$run_hooks = $return !== null;
		}

		return $return === null ? mysql_fetch_object($this->result) : $return;
	}

	public function fetch_row()
	{
		static $run_hooks = true;

		$return = null;
		if($run_hooks)
		{
			$current = $this->current;
			api()->run_hooks('database_fetch_row', array($this->result, &$return, &$current));
			$this->current = $current;
			$run_hooks = $return !== null;
		}

		return $return === null ? mysql_fetch_row($this->result) : $return;
	}

	public function field_name($field_offset)
	{
		static $run_hooks = true;

		$return = null;
		if($run_hooks)
		{
			api()->run_hooks('database_field_name', array($this->result, $field_offset, &$return));
			$run_hooks = $return !== null;
		}

		return $return === null ? mysql_field_name($this->result, $field_offset) : $return;
	}

	public function free_result()
	{
		@mysql_free_result($this->result);
		$this->result = null;

		return true;
	}

	public function num_fields()
	{
		static $run_hooks = true;

		$return = null;
		if($run_hooks)
		{
			api()->run_hooks('database_num_fields', array($this->result, &$return));
			$run_hooks = $return !== null;
		}

		return $return === null ? mysql_num_fields($this->result) : $return;
	}

	public function num_rows()
	{
		static $run_hooks = true;

		$return = null;
		if($run_hooks)
		{
			api()->run_hooks('database_num_rows', array($this->result, &$return));
			$run_hooks = $return !== null;
		}

		return $return === null ? mysql_num_rows($this->result) : $return;
	}
}

$db_result_class = 'MySQL_Result';
?>