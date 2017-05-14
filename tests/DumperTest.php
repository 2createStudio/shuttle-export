<?php 
use PHPUnit\Framework\TestCase;
use Mockery as m;
use ShuttleExport\Dumper\Factory as DumperFactory;
use ShuttleExport\Dumper\Php as PhpDumper;
use ShuttleExport\Dumper\MysqldumpShellCommand as MysqldumpDumper;
use ShuttleExport\DBConn\Mysqli as MysqliDbConn;

use ShuttleExport\Exception as ShuttleException;

class DumperTest extends TestCase {
	function tearDown() {
		 m::close();
	}

	function get_factory_for_shell_props($shell_props) {
		$factory = new DumperFactory();

		$shell = m::mock('\ShuttleExport\Shell');
		$shell->shouldReceive($shell_props);

		$factory->shell = $shell;
		return $factory;
	}
	public function get_dumpers_for_shell() {
		return [
			[
				'shell_props' => [
					'is_enabled' => true,
					'has_command' => true,
				],
				'expect' => MysqldumpDumper::class,
				'message' => "It doesn't use shell dumper if it's available"
			],

			[
				'shell_props' => [
					'is_enabled' => false,
				],
				'expect' => PhpDumper::class,
				'message' => "It doesn't fallback to native dumper when shell is not avialble"
			],

			[
				'shell_props' => [
					'is_enabled' => true,
					'has_command' => false
				],
				'expect' => PhpDumper::class,
				'message' => "It doesn't fallback to native dumper when required shell commands are unavialble"
			],
		];
		
    }

	/**
	 * @test
	 * @dataProvider get_dumpers_for_shell
	 */
	function assert_dumper_for_shell($shell_props, $expected_dumper, $message) {
		$factory = $this->get_factory_for_shell_props($shell_props);
		$dumper = $factory->make([
			'db_name' => 'test',
			'export_file' => '/dev/null',
		]);
		$this->assertInstanceOf($expected_dumper, $dumper, $message);
	}

	/**
	 * @test
	 * @expectedException \ShuttleExport\Exception
	 * @expectedExceptionMessage Missing required option
	 */
	function it_requires_db_name_and_export_file_location() {
		$factory = new DumperFactory();
		$dumper = $factory->make([]);
	}

	/**
	 * @test
	 * @expectedException \ShuttleExport\Exception
	 */
	function it_throws_exception_when_unnecessary_options_are_provided() {
		$factory = new DumperFactory();
		$dumper = $factory->make([
			'db_name' => 'test',
			'export_file' => '/dev/null',
			'something' => 'unneeded',
		]);
	}

	/**
	 * @test
	 */
	function it_fetches_tables_with_prefix() {
		$factory = new DumperFactory();
		$dumper = $factory->make([
			'db_name' => 'test',
			'export_file' => '/dev/null',
		]);

		$db = m::mock(MysqliDbConn::class)->makePartial();
		$db->prefix = '_some_prefix';
		$db
			->shouldReceive('fetch_numeric')
			->with(\Mockery::pattern('~\\\\_some\\\\_prefix%~'))
			->andReturn([ ['_some_prefix_table_1'], ['_some_prefix_table_2']]);
		$dumper->db = $db;

		// See http://stackoverflow.com/a/28189403/514458 for 
		// info on $canonicalize = true
		$this->assertEquals(
			['_some_prefix_table_1', '_some_prefix_table_2'],
			$dumper->get_tables(),
			"\$canonicalize = true"
		);
	}

	
	/**
	 * @test
	 */
	function it_excludes_tables() {
		$factory = new DumperFactory();
		$dumper = $factory->make([
			'db_name' => 'test',
			'export_file' => '/dev/null',
			'exclude_tables' => ['table2']
		]);

		$db = m::mock(MysqliDbConn::class)->makePartial();
		$db
			->shouldReceive([
				'fetch_numeric' => [ ['table1'], ['table2'], ['table3'] ]
			]);
		$dumper->db = $db;

		$this->assertEquals(
			['table1', 'table3'],
			$dumper->get_tables(),
			"\$canonicalize = true"
		);
	}

	

}
