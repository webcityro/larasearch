<?php

namespace Webcityro\Larasearch;

use Illuminate\Support\ServiceProvider;
use Webcityro\Larasearch\Tests\Repositories\Eloquent\ProductRepository;
use Webcityro\Larasearch\Tests\Repositories\Contracts\ProductRepositoryContract;

class LarasearchServiceProvider extends ServiceProvider {

	public function register(): void {
		$this->commands([
			\Webcityro\Larasearch\Console\Commands\MakeRequestCommand::class,
			\Webcityro\Larasearch\Console\Commands\MakeRepositoryCommand::class,
			\Webcityro\Larasearch\Console\Commands\MakeRepositoryContractCommand::class,
			\Webcityro\Larasearch\Console\Commands\MakeQueryCommand::class,
			\Webcityro\Larasearch\Console\Commands\MakeAllCommand::class,
		]);
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
