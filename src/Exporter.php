<?php
namespace ShuttleExport;
use ShuttleExport\Dumper\Factory;

/**
 * A facade that hides the Dumper Factory and keeps the
 * public interface cleaner. 
 */
class Exporter {
	static function export($settings) {
		$factory = new Factory();
		$dumper = $factory->make($settings);
		return $dumper->dump();
	}
}
