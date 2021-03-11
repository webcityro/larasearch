<?php

namespace Webcityro\Larasearch;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
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
		if ($this->app->runningUnitTests()) {
			$this->app->bind(ProductRepositoryContract::class, ProductRepository::class);
		}

		$this->registerPublishing();

		View::share('perPage', config('larasearch.per_page'));
		View::share('defaultPerPage', config('larasearch.default_per_page'));

		$this->registerResources();
		$this->registerDirectives();
	}

	private function registerResources(): void {
		if ($this->app->runningUnitTests()) {
			$this->loadMigrationsFrom(__DIR__.'/../tests/database/migrations');
		}
	}

	protected function registerPublishing() {
		$this->publishes([
			__DIR__.'/../config/larasearch.php' => config_path('larasearch.php')
		], 'larasearch-config');
	}

	protected function registerDirectives() {
		Blade::directive('larasearchHead', function ($defer = false) {
			return '<script'.($defer ? ' defer' : '').'>
				window.Larasearch = {
					perPage: '.json_encode(config('larasearch.per_page')).',
					defaultPerPage: '.config('larasearch.default_per_page')
				.'};
			</script>';
        });
	}
}
