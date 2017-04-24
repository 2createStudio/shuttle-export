<?php
namespace ShuttleExport\Dump_File;

/**
 * Gzip implementation. Uses gz* functions. 
 */
class Gzip extends Dump_File {
	function open() {
		return gzopen($this->file_location, 'wb9');
	}
	function write($string) {
		return gzwrite($this->fh, $string);
	}
	function end() {
		return gzclose($this->fh);
	}
}

