<?php
namespace ShuttleExport\Dumper;
use ShuttleExport\Dump_File\Dump_File;
use Symfony\Component\Process\Process;
use ShuttleExport\Exception;

class MysqldumpShellCommand extends Dumper {
	function dump() {
		$command = 'mysqldump -h ' . escapeshellarg($this->db->host) .
			' --port=' . escapeshellarg($this->db->port) . 
			' -u ' . escapeshellarg($this->db->username) . 
			' --password=' . escapeshellarg($this->db->password) . 
			' --set-charset=' . escapeshellarg($this->db->charset) . 
			' ' . escapeshellarg($this->db->name);

		$include_all_tables = empty($this->db->prefix) &&
			empty($this->include_tables) &&
			empty($this->exclude_tables);

		if (!$include_all_tables) {
			$tables = $this->get_tables($this->db->prefix);
			$command .= ' ' . implode(' ', array_map('escapeshellarg', $tables));
		}

		if (Dump_File::is_gzip($this->export_file)) {
			$command .= ' | gzip';
		}

		$command .= ' > ' . escapeshellarg($this->export_file);
		$process = new Process($command);

		$process->run();
		if (!$process->isSuccessful()) {
			throw new Exception($process->getErrorOutput());
		}

		return true;
	}

}
