<?php

namespace ComposePress\Settings;


class RegistryTest extends \PHPUnit_Framework_TestCase {
	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	public function test_dotify() {
		$this->assertEquals( [ 'a.b.c' => 'd' ], Registry::dotify( [ 'a' => [ 'b' => [ 'c' => 'd' ] ] ] ) );
	}

	public function test_undotify() {
		$this->assertEquals( [ 'a' => [ 'b' => [ 'c' => 'd' ] ] ], Registry::undotify( [ 'a.b.c' => 'd' ] ) );
	}
}
