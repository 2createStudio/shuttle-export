<?php
namespace ShuttleExport\Dumper;
use ShuttleExport\Dump_File\Dump_File;

class ShellCommand extends Dumper {
	function dump($export_file_location, $table_prefix='') {
		$command = 'mysqldump -h ' . escapeshellarg($this->db->host) .
			' -u ' . escapeshellarg($this->db->username) . 
			' --password=' . escapeshellarg($this->db->password) . 
			' ' . escapeshellarg($this->db->name);

		$include_all_tables = empty($table_prefix) &&
			empty($this->include_tables) &&
			empty($this->exclude_tables);

		if (!$include_all_tables) {
			$tables = $this->get_tables($table_prefix);
			$command .= ' ' . implode(' ', array_map('escapeshellarg', $tables));
		}

		$error_file = tempnam(sys_get_temp_dir(), 'err');

		$command .= ' 2> ' . escapeshellarg($error_file);

		if (Dump_File::is_gzip($export_file_location)) {
			$command .= ' | gzip';
		}

		$command .= ' > ' . escapeshellarg($export_file_location);

		exec($command, $output, $return_val);

		if ($return_val !== 0) {
			$error_text = file_get_contents($error_file);
			unlink($error_file);
			throw new Exception('Couldn\'t export database: ' . $error_text);
		}

		unlink($error_file);
	}
}
