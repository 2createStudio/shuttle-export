<?php
namespace ShuttleExport\Dumper;

use ShuttleExport\Dump_File\Dump_File;
use ShuttleExport\Insert_Statement;

class Php extends Dumper {
	/**
	 * End of line style used in the dump
	 */
	public $eol = "\r\n";

	public function dump() {
		$this->db->connect();

		$eol = $this->eol;

		$this->dump_file = Dump_File::create($this->export_file);

		$this->dump_file->write("-- Generation time: " . date('r') . $eol);
		$this->dump_file->write("-- Host: " . $this->db->host . $eol);
		$this->dump_file->write("-- DB name: " . $this->db->name . $eol);
		
		$this->dump_file->write("/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;$eol");
		$this->dump_file->write("/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;$eol");
		$this->dump_file->write("/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;$eol");
		$this->dump_file->write("/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;$eol");
		$this->dump_file->write("/*!40103 SET TIME_ZONE='+00:00' */;$eol");
		$this->dump_file->write("/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;$eol");
		$this->dump_file->write("/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;$eol");
		$this->dump_file->write("/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;$eol");
		$this->dump_file->write("/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;$eol$eol");

		$this->dump_file->write("/*!40030 SET NAMES " . $this->db->charset . " */;$eol");

		$tables = $this->get_tables();
		foreach ($tables as $table) {
			$this->dump_table($table);
		}
		
		$this->dump_file->write("$eol$eol");
		$this->dump_file->write("/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;$eol");
		$this->dump_file->write("/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;$eol");
		$this->dump_file->write("/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;$eol");
		$this->dump_file->write("/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;$eol");
		$this->dump_file->write("/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;$eol");
		$this->dump_file->write("/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;$eol");
		$this->dump_file->write("/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;$eol$eol");

		unset($this->dump_file);
	}

	protected function dump_table($table) {
		$eol = $this->eol;

		$this->dump_file->write("DROP TABLE IF EXISTS `$table`;$eol");

		$create_table_sql = $this->get_create_table_sql($table);
		$this->dump_file->write($create_table_sql . $eol . $eol);

		$data = $this->db->query("SELECT * FROM `$table`");

		$insert = new Insert_Statement($table);

		while ($row = $this->db->fetch_row($data)) {
			$row_values = array();
			foreach ($row as $value) {
				$row_values[] = $this->db->escape($value);
			}
			$insert->add_row( $row_values );

			if ($insert->get_length() > Insert_Statement::LENGTH_THRESHOLD) {
				// The insert got too big: write the SQL and create
				// new insert statement
				$this->dump_file->write($insert->get_sql() . $eol);
				$insert->reset();
			}
		}

		$sql = $insert->get_sql();
		if ($sql) {
			$this->dump_file->write($insert->get_sql() . $eol);
		}
		$this->dump_file->write($eol . $eol);
	}
	
	public function get_create_table_sql($table) {
		$create_table_sql = $this->db->fetch('SHOW CREATE TABLE `' . $table . '`');
		return $create_table_sql[0]['Create Table'] . ';';
	}
}
