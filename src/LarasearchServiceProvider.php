<?php

namespace Webcityro\Larasearch;

use Illuminate\Support\ServiceProvider;
use Webcityro\Larasearch\Tests\Repositories\Eloquent\ProductRepository;
use Webcityro\Larasearch\Tests\Repositories\Contracts\ProductRepositoryContract;

class LarasearchServiceProvider extends ServiceProvider {

	public function register(): void {
		//
	}

	public function boot(): void {
		if ($this->app->runningInConsole()) {
			$this->app->bind(ProductRepositoryContract::class, ProductRepository::class);
			$this->registerPublishing();
		}

		$this->registerResources();
	}

	private function registerResources(): void {
		$this->loadMigrationsFrom(__DIR__.'/../tests/database/migrations');
	}

	protected function registerPublishing() {
		$this->publishes([
			__DIR__.'/../config/larasearch.php' => config_path('larasearch.php')
		], 'larasearch-config');
	}
}
