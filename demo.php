<?php 
include ('dumper.php');

try {
	$world_dumper = Shuttle_Dumper::create(array(
		'host' => '',
		'username' => 'root',
		'password' => '',
		'db_name' => 'world',
	));

	// dump the database to gzipped file
	$world_dumper->dump('world.sql.gz');

	// dump the database to plain text file
	$world_dumper->dump('world.sql');

	$wp_dumper = Shuttle_Dumper::create(array(
		'host' => '',
		'username' => 'root',
		'password' => '',
		'db_name' => 'wordpress',
	));

	// Dump only the tables with wp_ prefix
	$wp_dumper->dump('wordpress.sql', 'wp_');
	
	$countries_dumper = Shuttle_Dumper::create(array(
		'host' => '',
		'username' => 'root',
		'password' => '',
		'db_name' => 'world',
		'include_tables' => array('country', 'city'), // only include those tables
	));
	$countries_dumper->dump('world.sql.gz');

	$world_dumper = Shuttle_Dumper::create(array(
		'host' => '',
		'username' => 'root',
		'password' => '',
		'db_name' => 'world',
		'exclude_tables' => array('city'), 
	));
	$world_dumper->dump('world-no-cities.sql.gz');

} catch(Shuttle_Exception $e) {
	echo "Couldn't dump database: " . $e->getMessage();
}