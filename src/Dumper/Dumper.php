<?php
namespace ShuttleExport\Dumper;
use ShuttleExport\DBConn\DBConn;
use ShuttleExport\Exception;

abstract class Dumper {
	/**
	 * @var \ShuttleExport\DBConn
	 */	
	public $db;

	/**
	 * @var \ShuttleExport\Dump_File\Dump_File
	 */
	public $dump_file;

	/**
	 * Specificed tables to include
	 */
	public $only_tables;

	/**
	 * Specified tables to exclude
	 */
	public $exclude_tables = array();

	function __construct($db_options) {
		$db_options = $this->validate_options($db_options);

		$this->db = DBConn::create($db_options);

		$this->export_file = $db_options['export_file'];
		$this->only_tables = $db_options['only_tables'];
		$this->exclude_tables = $db_options['exclude_tables'];

		$this->init();
	}

	/**
	 * This function could be implemented in extended classes
	 */
	function init() {
		// pass
	}

	private function validate_options($db_options) {
		$options = [
			'db_host'        => [ 'required' => false, 'default' => '127.0.0.1' ],
			'db_port'        => [ 'required' => false, 'default' => 3306        ],
			'db_user'        => [ 'required' => false, 'default' => 'root'      ],
			'db_password'    => [ 'required' => false, 'default' => ''          ],
			'db_name'        => [ 'required' => true                            ],
			'export_file'    => [ 'required' => true                            ],
			'prefix'         => [ 'required' => false, 'default' => null        ],
			'only_tables'    => [ 'required' => false, 'default' => null        ],
			'exclude_tables' => [ 'required' => false, 'default' => []          ],
			'charset'        => [ 'required' => false, 'default' => 'utf8'      ],
		];

		$errors = [];
		foreach ($options as $option_name => $option_props) {
			$is_required = $option_props['required'];
			$is_present = !empty($db_options[$option_name]);

			// Make sure that required options are present
			if ($is_required && !$is_present) {
				throw new Exception("Missing required option: $option_name");
			}

			// Add default values for non-present options
			if (!$is_present) {
				$db_options[$option_name] = $option_props['default'];
			}
		}

		$unknown_options = array_diff_key($db_options, $options);
		if (!empty($unknown_options)) {
			throw new Exception( "Unknown options: " . implode(', ', $unknown_options));
		}

		$dir = dirname($db_options['export_file']);

		return $db_options;
	}

	/**
	 * Create an export file from the tables with that prefix.
	 * @param string $export_file_location the file to put the dump to.
	 *		Note that whenever the file has .gz extension the dump will be comporessed with gzip
	 * @param string $table_prefix Allow to export only tables with particular prefix
	 * @return void
	 */
	abstract public function dump();

	public function get_tables() {
		if (!empty($this->only_tables)) {
			return $this->only_tables;
		}
		// $tables will only include the tables and not views.
		// TODO - Handle views also, edits to be made in function 'get_create_table_sql' line 336
		$escaped_prefix = $this->db->escape_like($this->db->prefix);
		$tables = $this->db->fetch_numeric('
			SHOW FULL TABLES
			WHERE Table_Type = "BASE TABLE"
			AND Tables_in_' . $this->db->name . ' LIKE "' . $escaped_prefix . '%"
		');

		$tables_list = array();
		foreach ($tables as $table_row) {
			$table_name = $table_row[0];
			if (!in_array($table_name, $this->exclude_tables)) {
				$tables_list[] = $table_name;
			}
		}
		return $tables_list;
	}
}
