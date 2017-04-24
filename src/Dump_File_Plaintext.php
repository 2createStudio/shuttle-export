<?php
namespace ShuttleExport;

/**
 * Plain text implementation. Uses standard file functions in PHP. 
 */
class Dump_File_Plaintext extends Dump_File {
	function open() {
		return fopen($this->file_location, 'w');
	}
	function write($string) {
		return fwrite($this->fh, $string);
	}
	function end() {
		return fclose($this->fh);
	}
}

