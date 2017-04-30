<?php 
use PHPUnit\Framework\TestCase;
use Mockery as m;
use ShuttleExport\Dumper\Factory as DumperFactory;
use ShuttleExport\Dumper\Php as PhpDumper;
use ShuttleExport\Dumper\MysqldumpShellCommand as MysqldumpDumper;

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
	function it_passes_table_list_to_mysqldump_when_specific_tables_are_provided() {
		$mysqldump_dumper = new MysqldumpDumper([
			'db_name' => 'test',
			'export_file' => '/dev/null',
			'only_tables' => ['table1', 'table2'],
		]);
		$mocked_process = m::mock('Symfony\Component\Process\Process');
		$mocked_process->shouldReceive([
			'run' => true,
			'isSuccessful' => true,
		]);

		$mocked_process->shouldReceive('setCommand')->withArgs(function ($mysqldump_cmd) {
			return preg_match('/"table1"/', $mysqldump_cmd) &&
				preg_match('/"table2"/', $mysqldump_cmd);
		});
		$mysqldump_dumper->process = $mocked_process;
		$mysqldump_dumper->dump();
		$this->assertTrue(true);
	}

	

	

}
