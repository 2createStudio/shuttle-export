<?php
namespace ShuttleExport\Dumper;
use ShuttleExport\Shell;

class Factory {
	public static function make_dumper($db_options) {
		$factory = new self();
		return $factory->make($db_options);
	}

	function __construct() {
		$this->shell = new Shell();
	}

	function make($db_options) {
		if ($this->shell->is_enabled() &&
			$this->shell->has_command('mysqldump') &&
			$this->shell->has_command('gzip')
			) {
			$class_name = 'ShuttleExport\\Dumper\\Native';
		} else {
			$class_name = 'ShuttleExport\\Dumper\\Shell';
		}
		return new $class_name($db_options);
	}
}
