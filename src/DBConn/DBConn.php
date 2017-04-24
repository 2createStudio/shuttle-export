<?php
namespace ShuttleExport\DBConn;

class DBConn {
	public $host;
	public $username;
	public $password;
	public $name;

	protected $connection;

	function __construct($options) {
		$this->host = $options['host'];
		if (empty($this->host)) {
			$this->host = '127.0.0.1';
		}
		$this->username = $options['username'];
		$this->password = $options['password'];
		$this->name = $options['db_name'];
	}

	static function create($options) {
		if (class_exists('mysqli')) {
			$class_name = "ShuttleExport\\DBConn\\Mysqli";
		} else {
			$class_name = "ShuttleExport\\DDBConn\\Mysql";
		}

		return new $class_name($options);
	}
}
