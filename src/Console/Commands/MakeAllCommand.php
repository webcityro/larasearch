<?php

namespace Webcityro\Larasearch\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Webcityro\Larasearch\Console\Commands\Traits\NamespaceResolver;

class MakeAllCommand extends Command {

	use NamespaceResolver;

	protected $signature  = 'larasearch:make:all {name : Tne name of the resource.} {model=MyModel : The model on weth to preform the search.} {--m|multi : Accepts multiple search fields}';
	protected $description = 'Generate all the larasearch specific search classes.';

	public function handle() {
		$this->call('larasearch:make:request', [
			'name' => $this->argument('name').'Request',
			'--multi' => $this->option('multi')
		]);
		$this->call('larasearch:make:query', [
			'name' => $this->argument('name').'Query',
			'--model' => $this->argument('model')
		]);
		$this->call('larasearch:make:repository', [
			'name' => $this->argument('name').'Repository',
			'query' => $this->getNamespaceString('Search\\Queries\\'.$this->argument('name').'Query'),
		]);
		$this->call('make:resource', [
			'name' => $this->argument('name'). 'Resource',
		]);
	}
}
