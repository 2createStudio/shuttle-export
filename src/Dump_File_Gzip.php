<?php
namespace ShuttleExport;

/**
 * Gzip implementation. Uses gz* functions. 
 */
class Dump_File_Gzip extends Dump_File {
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

