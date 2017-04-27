<?php
namespace ShuttleExport\Dumper;
use ShuttleExport\DBConn\DBConn;
use ShuttleExport\Dumper\ShellCommand as ShellCommandDumper;
use ShuttleExport\Dumper\Native as NativeDumper;

/**
 * Main facade
 */
abstract class Dumper {
	
	/**
	 * @var \ShuttleExport\DBConn
	 */	
	public $db;

	/**
	 * @var \ShuttleExport\Dump_File\Dump_File
	 */
	public $dump_file;

	/**
	 * End of line style used in the dump
	 */
	public $eol = "\n";

	/**
	 * Specificed tables to include
	 */
	public $include_tables;

	/**
	 * Specified tables to exclude
	 */
	public $exclude_tables = array();

	function __construct($db_options) {
		$this->db = DBConn::create($db_options);

		if (isset($db_options['include_tables'])) {
			$this->include_tables = $db_options['include_tables'];
		}
		if (isset($db_options['exclude_tables'])) {
			$this->exclude_tables = $db_options['exclude_tables'];
		}
	}

	/**
	 * Create an export file from the tables with that prefix.
	 * @param string $export_file_location the file to put the dump to.
	 *		Note that whenever the file has .gz extension the dump will be comporessed with gzip
	 * @param string $table_prefix Allow to export only tables with particular prefix
	 * @return void
	 */
	abstract public function dump($export_file_location, $table_prefix='');

	protected function get_tables($table_prefix) {
		if (!empty($this->include_tables)) {
			return $this->include_tables;
		}
		
		// $tables will only include the tables and not views.
		// TODO - Handle views also, edits to be made in function 'get_create_table_sql' line 336
		$escaped_prefix = $this->db->escape_like($table_prefix);
		$tables = $this->db->fetch_numeric('
			SHOW FULL TABLES
			WHERE Table_Type = "BASE TABLE"
			AND Tables_in_' . $this->db->name . ' LIKE "' . $escaped_prefix . '%"
		');

		$tables_list = array();
		foreach ($tables as $table_row) {
			$table_name = $table_row[0];
			if (!in_array($table_name, $this->exclude_tables)) {
				$tables_list[] = $table_name;
			}
		}
		return $tables_list;
	}
}
