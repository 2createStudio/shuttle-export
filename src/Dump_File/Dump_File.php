<?php
namespace ShuttleExport\Dump_File;

use ShuttleExport\Exception;

/**
 * Abstract dump file: provides common interface for writing
 * data to dump files. 
 */
abstract class Dump_File {
	/**
	 * File Handle
	 */
	protected $fh;

	/**
	 * Location of the dump file on the disk
	 */
	protected $file_location;

	abstract function write($string);
	abstract function end();

	static function create($filename) {
		if (self::is_gzip($filename)) {
			return new Gzip($filename);
		}
		return new Plaintext($filename);
	}
	function __construct($file) {
		$this->file_location = $file;
		$this->fh = $this->open();

		if (!$this->fh) {
			throw new Exception("Couldn't create a dump file");
		}
	}

	public static function is_gzip($filename) {
		return (bool) preg_match('~\.gz$~i', $filename);
	}	
}

