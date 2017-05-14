<?php
namespace ShuttleExport\Dumper;
use ShuttleExport\Dump_File\Dump_File;
use Symfony\Component\Process\Process;
use ShuttleExport\Exception;

class MysqldumpShellCommand extends Dumper {

	public $process;

	function init() {
		// Save a reference to the Process object so it can be mocked in tests
		$this->process = new Process('');
		parent::init();
	}
	function dump() {
		$command = 'mysqldump -h ' . escapeshellarg($this->db->host) .
			' --port=' . escapeshellarg($this->db->port) . 
			' -u ' . escapeshellarg($this->db->username) . 
			' --password=' . escapeshellarg($this->db->password) . 
			' --set-charset=' . escapeshellarg($this->db->charset) . 
			' ' . escapeshellarg($this->db->name);

		$include_all_tables = empty($this->db->prefix) &&
			empty($this->only_tables) &&
			empty($this->exclude_tables);

		if (!$include_all_tables) {
			$tables = $this->get_tables($this->db->prefix);
			$command .= ' ' . implode(' ', array_map('escapeshellarg', $tables));
		}

		if (Dump_File::is_gzip($this->export_file)) {
			$command .= ' | gzip';
		}

		$command .= ' > ' . escapeshellarg($this->export_file);
		$this->process->setCommandLine($command);

		// Translate the exception to \ShuttleExport\Exception
		try {
			$this->process->run();
		} catch(\RuntimeException $e) {
			throw new Exception($e->getMessage());
		}

		if (!$this->process->isSuccessful()) {
			throw new Exception($this->process->getErrorOutput());
		}

		return true;
	}

}
