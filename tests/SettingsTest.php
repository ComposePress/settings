<?php

namespace ComposePress\Settings;

use ComposePress\Settings;
use Mockery as m;
use PHPUnit\Framework\TestResult;


/**
 * Class SettingsTest
 *
 * @package ComposePress\Settings
 * @runTestsInSeparateProcesses
 */
class SettingsTest extends \PHPUnit_Framework_TestCase {
	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	public function run( TestResult $result = null ) {
		$this->setPreserveGlobalState( false );

		return parent::run( $result );
	}

	/**
	 *
	 */
	public function test_set_no_page() {
		$mock   = m::mock( 'alias:\ComposePress\Settings\Registry' );
		$plugin = new PluginMock();
		$result = [ 'testpage' => [ 'settinga' => 'test' ] ];
		$mock->shouldReceive( 'set_page' )->withArgs( [
			'testing_plugin',
			'testpage',
			'settinga',
			'test',
		] )->andReturn( $result );
		$mock->shouldReceive( 'undotify' )->andReturn( $result );
		$mock->shouldReceive( 'mass_set' )->andReturn( $result );
		$settings         = new Settings();
		$settings->parent = $plugin;
		$this->assertEquals( $result, $settings->set( 'testpage.settinga', 'test' ) );
	}

	/**
	 *
	 */
	public function test_get_no_page() {
		$mock   = m::mock( 'alias:\ComposePress\Settings\Registry' );
		$plugin = new PluginMock();
		$result = [ 'testpage' => [ 'settinga' => 'test' ] ];
		$mock->shouldReceive( 'get_page' )->withArgs( [
			'testing_plugin',
			'testpage',
			'settinga',
			'test',
		] )->andReturn( $result );
		$mock->shouldReceive( 'undotify' )->andReturn( $result );
		$mock->shouldReceive( 'mass_get' )->andReturn( $result );
		$settings         = new Settings();
		$settings->parent = $plugin;
		$this->assertEquals( $result, $settings->set( 'testpage.settinga', 'test' ) );
	}
}
