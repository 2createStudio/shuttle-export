<?php
namespace ShuttleExport\Dumper;
use ShuttleExport\Dump_File\Dump_File;
use Symfony\Component\Process\Process;

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

		if (Dump_File::is_gzip($export_file_location)) {
			$command .= ' | gzip';
		}

		$command .= ' > ' . escapeshellarg($export_file_location);
		$process = new Process($command);

		$process->run();
		if (!$process->isSuccessful()) {
			$err = 'Couldn\'t export database: ' . $process->getErrorOutput();
			throw new Exception($err);
		}
	}

}
