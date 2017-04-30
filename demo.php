<?php 
include (__DIR__ . '/vendor/autoload.php');

use ShuttleExport\Exporter;
use ShuttleExport\Exception as ShuttleException;

try {
	Exporter::export(array(
		'db_host'        => '',
		'db_user'        => 'root',
		'db_password'    => 'kuku',
		'db_name'        => 'bourgaswp',
		'db_port'        => 3306,
		'prefix'         => 'wp_',
		'only_tables'    => ['wp_posts', 'wp_postmeta'],
		'exclude_tables' => ['wp_posts', 'wp_postmeta'],
		'charset'        => 'utf8mb4',
		'export_file'    => __DIR__ . '/dumps/' . date('Y_m_d_H_i_s') . '.sql.gz',
	));

	#$wp_dumper = Shuttle_Dumper::create(array(
		#'host' => '',
		#'username' => 'root',
		#'password' => '',
		#'db_name' => 'wordpress',
	#));
#
	#// Dump only the tables with wp_ prefix
	#$wp_dumper->dump('wordpress.sql', 'wp_');
	#
	#$countries_dumper = Shuttle_Dumper::create(array(
		#'host' => '',
		#'username' => 'root',
		#'password' => '',
		#'db_name' => 'world',
		#'include_tables' => array('country', 'city'), // only include those tables
	#));
	#$countries_dumper->dump('world.sql.gz');
#
	#$world_dumper = Shuttle_Dumper::create(array(
		#'host' => '',
		#'username' => 'root',
		#'password' => '',
		#'db_name' => 'world',
		#'exclude_tables' => array('city'), 
	#));
	#$world_dumper->dump('world-no-cities.sql.gz');
} catch(ShuttleException $e) {
	echo "Couldn't dump database: " . $e->getMessage();
} 
