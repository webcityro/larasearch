<?php

namespace Webcityro\Larasearch\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\GeneratorCommand;
use Webcityro\Larasearch\Console\Commands\Traits\NamespaceResolver;

class MakeRepositoryCommand extends GeneratorCommand {

	use NamespaceResolver;

	protected $name = 'larasearch:make:repository';
	protected $signature  = 'larasearch:make:repository {name : Tne name of the repository} {query : Tne search query class} {--N|no-contract : don\'t generate the contract.} {--c|contract : Provide the contract for this repository.}';
	protected $description = 'Generate a larasearch specific repository.';
	protected $type = 'Repository';

	protected $path;
	protected $content;

	protected function getStub() {
		return __DIR__.'/stubs/repository/Eloquent/'.($this->option('no-contract') ? 'Repository.php.stub' : 'RepositoryWithContract.php.stub');
	}

	protected function getDefaultNamespace($rootNamespace) {
		return $rootNamespace.'\Repositories\Eloquent';
	}

	public function handle() {
		$this->path = $this->getPath($this->qualifyClass($this->getNameInput()));

		parent::handle();


		$this->loadContent();
		$this->makeContract();
		$this->applyContract();
		$this->applyQuery();
		$this->saveContent();
	}

	private function makeContract() {
		if ($this->option('no-contract') || $this->option('contract')) {
			return;
		}

		$this->call('larasearch:make:repository-contract', [
			'name' => $this->contractInput()
		], $this->output);
	}

	private function contractInput() {
		return $this->option('contract') ?: $this->getNameInput().'Contract';
	}

	private function contractName()	{
		$bum = explode('/', $this->contractInput());
		return end($bum);
	}

	private function contractNamespace() {
		return ltrim($this->getNamespaceString('Repositories/Contracts/'.$this->contractInput()), '\\');
	}

	protected function applyContract() {
		if ($this->option('no-contract')) {
			return;
		}

		$this->content = str_replace('{{ contractNamespace }}', $this->contractNamespace(), $this->content);
		$this->content = str_replace('{{ contract }}', $this->contractName(), $this->content);
	}

	protected function applyQuery() {
		$this->content = str_replace('{{ querySearch }}', $this->getNamespaceString($this->argument('query')), $this->content);
	}

	protected function loadContent() {
		$this->content = file_get_contents($this->path);
	}

	protected function saveContent() {
		file_put_contents($this->path, $this->content);
	}
}
