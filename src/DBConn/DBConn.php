<?php
namespace ShuttleExport\DBConn;

class DBConn {
	public $host;
	public $username;
	public $password;
	public $name;

	public $charset;

	protected $connection;

	function __construct($options) {
		$this->host = $options['host'];
		if (empty($this->host)) {
			$this->host = '127.0.0.1';
		}

		$this->username = $options['username'];
		$this->password = $options['password'];
		$this->name = $options['db_name'];

		if (!empty($options['charset'])) {
			$this->charset = $options['charset'];
		}
	}

	static function create($options) {
		if (class_exists('mysqli')) {
			$class_name = "ShuttleExport\\DBConn\\Mysqli";
		} else {
			$class_name = "ShuttleExport\\DDBConn\\Mysql";
		}

		$conn = new $class_name($options);

		return $conn;

	}
	
	/**
	 * Setup the charset for the connection. Try 3 different charsets:
     *
	 *  - First, if the user has provided a charset in the configuration, use that
	 *  - Try with utf8mb4, since this is what WordPress uses by default
	 *  - Last, fallback to utf8
	 * 
	 */
	function setup_charset() {
		if ($this->charset) {
			$result = $this->set_charset($this->charset);
			if ($result) {
				return true;
			}
		}

		$result = $this->set_charset("utf8mb4");
		if ($result) {
			$this->charset = "utf8mb4";
			return true;
		}

		$this->charset = "utf8";
		$this->set_charset("utf8");
	}
}
