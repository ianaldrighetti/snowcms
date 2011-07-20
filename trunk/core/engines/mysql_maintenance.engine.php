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

class MySQL_Maintenance extends Database_Maintenance
{
	public function backup_table($table, $backup_table)
	{
		// Get the create table query.
		$create_table = $this->table_sql($table);

		if(!empty($create_table))
		{
			// Alright, before we can create the table, we must rename
			// the tables name...
			$create_table = str_replace($table, $backup_table, $create_table);

			// Now make it!
			$result = db()->query($create_table);

			if($result->success())
			{
				// The new table was created, now copy the data. Pretty easy with MySQL.
				// INSERT SELECT :-)

				$result = db()->query('
					INSERT INTO `{identifier:backup_table}`
					SELECT
						*
					FROM `{identifier:table}`',
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
		// Simple enough.
		$result = db()->query('
			OPTIMIZE TABLE `{identifier:table}`',
			array(
				'table' => $table,
			));

		return $result->success();
	}

	public function table_sql($table, $drop_if_exists = false, $fp = null)
	{
		// MySQL has a specific query to do this.
		$result = db()->query('
			SHOW CREATE TABLE `{identifier:table}`',
			array(
				'table' => $table,
			));

		// Didn't work? Sad.
		if(!$result->success())
			return false;

		list(, $table_sql) = $result->fetch_row();

		// Prepend DROP TABLE IF EXISTS?
		if(!empty($drop_if_exists))
		{
			$table_sql = "DROP TABLE IF EXISTS `{$table}`;\r\n{$table_sql}";
		}

		// Returning it or writing it to a file?
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
		// Get the data out.
		$result = db()->query('
								SELECT
									*
								FROM `{identifier:table}`'. ($start !== false && $rows !== false ? '
								LIMIT {int:start}, {int:rows}' : ''),
								array(
									'table' => $table,
									'start' => $start,
									'rows' => $rows,
								));

		// Anything?
		if($result->num_rows())
		{
			// We need to get the column names.
			$columns = array_keys($result->fetch_assoc());

			// But we still need that data! :P
			$result->data_seek(0);

			$rows = array();
			while($row = $result->fetch_assoc())
			{
				// Just add all the data.
				// After we sanitize it ;)
				foreach($row as $key => $value)
				{
					$row[$key] = db()->escape($value);
				}

				$rows[] = '(\''. implode('\', \'', array_values($row)). '\')';
			}

			// Rows per insert <= 1?
			if($rows_per_insert <= 1)
			{
				// Then that isn't extended inserts! You silly ;)
				$extended_inserts = false;
			}

			// Okay, now time to build the quer(y|ies) ;-)
			if(!empty($extended_inserts))
			{
				// With extended inserts, woo!
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

			// Writing it to a file?
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
			// Just an empty string.
			return '';
		}
	}

	public function create_table($table, $columns, $indexes = array(), $on_exists = 'fail')
	{
		// So what should we do if the table exists?
		if($on_exists == 'fail' || $on_exists == 'overwrite')
		{
			// So we may need to fail, or delete the existing table.
			// Check to see if it exists.
			$exists = in_array($table, db()->tables());

			if($exists && $on_exists == 'fail')
			{
				// Alright, you wanted it to fail. So we will.
				return false;
			}
			elseif($exists && $on_exists == 'overwrite')
			{
				// Delete the table.
				$result = db()->query('
										DROP TABLE {identifier:table}',
										array(
											'table' => $table,
										), null, 'drop_table');

				// If it couldn't drop the table, we can't continue.
				if(!$result->success())
				{
					return false;
				}
			}
			elseif($exists && $on_exists == 'update')
			{
				// This will be so much fun!!!
				// So get all the columns that currently exist.
				$existing_columns = db()->columns($table);

				foreach($columns as $colname => $column)
				{
					// Does it not exist..?
					if(!in_array($colname, $existing_columns))
					{
						// Add it! :-)
						$this->alter_table($table, $column, 'column', isset($prev_colname) ? $prev_colname : 'at_first');
					}

					// Keep track of the previous column.
					$prev_colname = $colname;
				}

				// Okay, we are done ;-)
				return true;
			}
		}

		// No columns? No table!
		if(empty($columns) || !is_array($columns) || count($columns) == 0)
		{
			return false;
		}

		// Start to build the query, starting with the CREATE TABLE.
		$db_query = "CREATE TABLE `". $table. "`\r\n(";

		// Now it is time to build the column information.
		$prepared_columns = array();
		foreach($columns as $colname => $column)
		{
			$prepard_columns[] = "  `". $colname. "` ". $column['type']. (isset($column['length']) ? "({$column['length']})" : ''). (isset($column['attributes']) ? ' '. $column['attributes'] : ''). (!empty($column['null']) ? ' NULL' : ' NOT NULL'). (isset($column['default']) ? ' DEFAULT \''. db()->escape($column['default']). '\'' : ''). (!empty($column['auto_increment']) ? ' AUTO_INCREMENT');
		}

		// Now add the columns do the CREATE TABLE statement.
		$db_query .= "\r\n". implode(",\r\n", $prepared_columns);

		// Any indexes? There might not be.
		if(!empty($indexes) && is_array($indexes) && count($indexes) > 0)
		{
			$prepared_indexes = array();
			foreach($indexes as $index)
			{
				$prepared_indexes[] = '  '. (isset($index['type']) && in_array(strtolower($index['type']), array('primary', 'unique')) ? strtoupper($index['type']). ' ' : ''). 'KEY (`'. implode('`, `', $index['columns']). '`)';
			}

			// Now add the indexes...
			$db_query .= "\r\n". implode(",\r\n", $prepared_indexes);
		}

		// Now the final stuff...
		// !!! Maybe allow an engine and charset to be specified?
		$db_query = "\r\n) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

		// Now create the table ;-)
		$result = db()->query($db_query, array(), null, 'create_table');
		return $result->success();
	}

	public function alter_table($table, $column, $type = 'column', $location = 'at_end', $on_exists = 'fail')
	{
		// Make sure that table exists ;-)
		if(!in_array($table, db()->tables()))
		{
			// And it doesn't. Nice.
			return false;
		}
		// Should it fail if the column exists..?
		elseif($on_exists == 'fail' && in_array($column, db()->columns($table)))
		{
			return false;
		}

		// Start to build the query.
		$db_query = 'ALTER TABLE {identifier:table} ';
		$db_vars = array(
								 'table' => $table,
								 'column' => $column['name'],
							 );

		// Changing a column..?
		if($type == 'column')
		{
			// Does it exist? If it does, it will be a MODIFY, not an ADD.
			$exists = in_array($column, db()->columns($table));

			// Dropping the specified colun/index are you?
			if(!empty($column['drop']))
			{
				$db_query .= 'DROP COLUMN {identifier:column}';
			}
			// Changing and renaming an existing column?
			elseif(isset($column['name']) && isset($column['new_name']))
			{
				$db_query .= 'CHANGE COLUMN {identifier:column} {identifier:new_name} ';
				$db_vars['new_name'] = $column['new_name'];
			}
			// Modifying an existing column?
			elseif($exists)
			{
				$db_query .= 'MODIFY COLUMN {identifier:column} ';
			}
			// Looks like we are adding a column!
			else
			{
				$db_query .= 'ADD COLUMN {identifier:column}';
			}

			// We will need to add the column declaration after the query...
			// unless it is a drop query.
			if(empty($column['drop']))
			{
				$db_query .= $column['type']. (isset($column['length']) ? "({$column['length']})" : ''). (isset($column['attributes']) ? ' '. $column['attributes'] : ''). (!empty($column['null']) ? ' NULL' : ' NOT NULL'). (isset($column['default']) ? ' DEFAULT \''. db()->escape($column['default']). '\'' : ''). (!empty($column['auto_increment']) ? ' AUTO_INCREMENT');

				// Want the column placed somewhere special..?
				if(!empty($location))
				{
					// In the first place?
					if($location == 'at_first')
					{
						$db_query .= ' FIRST';
					}
					// At the end?
					elseif($location == 'at_end')
					{
						$db_query .= ' AFTER {identifier:location}';
						$columns = db()->columns($table);
						$db_vars['location'] = array_pop($columns);
					}
					// After a column...
					else
					{
						$db_query .= ' AFTER {identifier:location}';
						$db_vars['location'] = $location;
					}
				}
			}
		}
		// An index, of some sort?
		else
		{
			// Dropping the index?
			if(!empty($column['drop']))
			{
				$db_query .= 'DROP '. ($type == 'primary' ? 'PRIMARY KEY' : 'INDEX {identifier:column}');
			}
			else
			{
				// They will all need this:
				$db_vars['columns'] = $column['columns'];

				// A primary key?
				if($type == 'primary')
				{
					$db_query .= 'ADD PRIMARY KEY ({array_identifier:columns})';
				}
				// A unique key, how interesting.
				elseif($type == 'unique')
				{
					$db_query .= 'ADD UNIQUE KEY '. (!empty($column['column']) ? '{identifier:column} ' : ''). '({array_identifier:columns})';
				}
				// A fulltext index? MySQL supports it, otherwise you ought to return false ;-)
				elseif($type == 'fulltext')
				{
					$db_query = 'ADD FULLTEXT KEY '. (!empty($column['column']) ? '{identifier:column} ' : ''). '({array_identifier:columns})';
				}
				// Just a regular ol' key.
				else
				{
					$db_query = 'ADD KEY '. (!empty($column['column']) ? '{identifier:column} ' : ''). '({array_identifier:columns})';
				}
			}
		}

		// Now run the built query.
		$result = db()->query($db_query, $db_vars, null, 'alter_table');

		// And return whether or not it was a success.
		return $result->success();
	}
}

$db_maintenance_class = 'MySQL_Maintenance';
?>