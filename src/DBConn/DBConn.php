<?php
namespace ShuttleExport\DBConn;
use ShuttleExport\Exception;

class DBConn {
	public $host;
	public $username;
	public $password;
	public $name;
	public $port;

	public $charset;

	protected $connection;

	function __construct($options) {
		$this->host = $options['db_host'];
		$this->username = $options['db_user'];
		$this->password = $options['db_password'];
		$this->name = $options['db_name'];
		$this->port = $options['db_port'];
		$this->charset = $options['charset'];
		$this->prefix = $options['prefix'];
	}

	static function create($options) {
		if (class_exists('\mysqli')) {
			$class_name = "ShuttleExport\\DBConn\\Mysqli";
		} else if (function_exists('mysql_connect')) {
			$class_name = "ShuttleExport\\DDBConn\\Mysql";
		} else {
			throw new Exception("The PHP installation doesn't have neither mysqli nor mysql extensions. ");
		}

		return new $class_name($options);
	}
}
