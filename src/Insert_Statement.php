<?php
namespace ShuttleExport;

/**
 * MySQL insert statement builder. 
 */
class Insert_Statement {
	/**
	 * Maximum length of single insert statement. This "magic" number
	 * has been taken from phpMiniAdmin: 
	 * https://github.com/osalabs/phpminiadmin/blob/2b394346961c6545080a07151f97e858ac432c1a/phpminiadmin.php#L861
	 * Not sure how it was chosen, but it works. 
	 */
	const LENGTH_THRESHOLD = 838860;

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
