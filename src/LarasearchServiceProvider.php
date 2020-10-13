<?php

namespace Webcityro\Larasearch;

use Illuminate\Support\ServiceProvider;

class LarasearchServiceProvider extends ServiceProvider {

	public function register(): void {
		//
	}

	public function boot(): void {
		$this->registerResources();
	}

	private function registerResources(): void {
		$this->loadMigrationsFrom(__DIR__.'/../database/migrations');
	}
}
