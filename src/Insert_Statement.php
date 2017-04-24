<?php
namespace ShuttleExport;

/**
 * MySQL insert statement builder. 
 */
class Insert_Statement {
	private $rows = array();
	private $length = 0;
	private $table;

	function __construct($table) {
		$this->table = $table;
	}

	function reset() {
		$this->rows = array();
		$this->length = 0;
	}

	function add_row($row) {
		$row = '(' . implode(",", $row) . ')';
		$this->rows[] = $row;
		$this->length += strlen($row);
	}

	function get_sql() {
		if (empty($this->rows)) {
			return false;
		}

		return 'INSERT INTO `' . $this->table . '` VALUES ' . 
			implode(",\n", $this->rows) . '; ';
	}

	function get_length() {
		return $this->length;
	}
}

