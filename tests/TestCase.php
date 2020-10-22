<?php

namespace Webcityro\Larasearch\Tests;

use Webcityro\Larasearch\LarasearchServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase {

	protected function setUp(): void {
		parent::setUp();

		$this->withFactories(__DIR__.'/database/factories');
	}


	protected function getPackageProviders($app): array {
		return [
			LarasearchServiceProvider::class
		];
	}

	protected function getEnvironmentSetUp($app): void {
		$app['config']->set('database.default', 'testDB');
		$app['config']->set('database.connections.testDB', [
			'driver' => 'sqlite',
			'database' => ':memory:'
		]);
	}
}
