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
		$this->callCommand('larasearch:make:request', 'Request', [
			'--multi' => $this->option('multi')
		]);
		$this->callCommand('larasearch:make:query', 'Query', [
			'--model' => $this->argument('model')
		]);
		$this->callCommand('larasearch:make:repository-contract', 'RepositoryContract');
		$this->callCommand('larasearch:make:repository', 'Repository', [
			'query' => $this->getNamespaceString('Search\\Queries\\'.$this->argument('name').'Query'),
			'--contract' => $this->argument('name').'RepositoryContract'
		]);
	}

	public function callCommand($command, $name = '', $args = []) {
		Artisan::call($command, array_merge([
			'name' => $this->argument('name').$name
		], $args));

		$this->info(Artisan::output());
	}
}
