<?php 
use PHPUnit\Framework\TestCase;
use Mockery as m;
use ShuttleExport\Dumper\Factory as DumperFactory;
use ShuttleExport\Dumper\Native as NativeDumper;
use ShuttleExport\Dumper\Shell as ShellDumper;

class DumperTest extends TestCase {
	function tearDown() {
		 m::close();
	}

	/**
	 * @test
	 * @dataProvider get_dumpers_for_shell
	 */
	function assert_dumper_for_shell($shell_props, $expected_dumper, $message) {
		$factory = new DumperFactory();

		$shell = m::mock('\ShuttleExport\Shell');
		$shell->shouldReceive($shell_props);

		$factory->shell = $shell;

		$dumper = $factory->make([
			'host' => '',
			'username' => '',
			'password' => '',
			'db_name' => '',
		]);
		$this->assertInstanceOf($expected_dumper, $dumper, $message);
	}

	public function get_dumpers_for_shell() {
		return [
			[
				'shell_props' => [
					'is_enabled' => true,
					'has_command' => true,
				],
				'expect' => ShellDumper::class,
				'message' => "It doesn't use shell dumper if it's available"
			],

			[
				'shell_props' => [
					'is_enabled' => false,
				],
				'expect' => NativeDumper::class,
				'message' => "It doesn't fallback to native dumper when shell is not avialble"
			],

			[
				'shell_props' => [
					'is_enabled' => true,
					'has_command' => false
				],
				'expect' => NativeDumper::class,
				'message' => "It doesn't fallback to native dumper when required shell commands are unavialble"
			],
		];
		
    }

}
