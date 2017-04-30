<?php
namespace ShuttleExport\Dumper;
use ShuttleExport\Shell;

class Factory {
	function __construct() {
		$this->shell = new Shell();
	}

	function make($db_options) {
		if ($this->shell->is_enabled() &&
			$this->shell->has_command('mysqldump') &&
			$this->shell->has_command('gzip')
			) {
			$class_name = 'ShuttleExport\\Dumper\\MysqldumpShellCommand';
		} else {
			$class_name = 'ShuttleExport\\Dumper\\Php';
		}
		return new $class_name($db_options);
	}
}
