<?php

namespace Webcityro\Larasearch\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeRepositoryContractCommand extends GeneratorCommand {

	protected $name = 'larasearch:make:repository-contract';
	protected $signature  = 'larasearch:make:repository-contract {name : Tne name of the repository contract}';
	protected $description = 'Generate a larasearch specific repository contract. This will be automatically generated once you generate a repository.';
	protected $type = 'Repository contract';

	protected function getStub() {
		return __DIR__.'/stubs/repository/RepositoryContract.php.stub';
	}

	protected function getDefaultNamespace($rootNamespace) {
		return $rootNamespace.'\Repositories\Contracts';
	}

	public function handle() {
		parent::handle();

		$this->info('Add the next line to your service provider\'s boot method.');
		$this->info('$this->app->bind(\\'.$this->qualifyClass($this->getNameInput()).'::class, \\'.$this->rootNamespace().'Repositories\Eloquent\\'.str_replace('Contract', '', $this->getNameInput()).'::class);');
	}
}
