<?php

namespace Webcityro\Larasearch\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Output\StreamOutput;
use Webcityro\Larasearch\Console\Commands\Traits\NamespaceResolver;

class MakeRepositoryCommand extends GeneratorCommand {

	use NamespaceResolver;

	protected $name = 'larasearch:make:repository';
	protected $signature  = 'larasearch:make:repository {name : Tne name of the repository} {query : Tne search query class} {--N|no-contract : don\'t generate the contract.} {--c|contract : Provide the contract for this repository.}';
	protected $description = 'Generate a larasearch specific repository.';
	protected $type = 'Repository';

	protected function getStub() {
		return __DIR__.'/stubs/repository/Eloquent/'.($this->option('no-contract') ? 'Repository.php.stub' : 'RepositoryWithContract.php.stub');
	}

	protected function getDefaultNamespace($rootNamespace) {
		return $rootNamespace.'\Repositories\Eloquent';
	}

	public function handle() {
		if (!$this->option('no-contract') && !$this->option('contract')) {
			Artisan::call('larasearch:make:repository-contract', [
				'name' => $this->contractInput()
			]);
		}
		$path = $this->getPath($this->qualifyClass($this->contractInput()));

		parent::handle();
		$this->applyQuery();
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

	protected function applyQuery() {
		$path = $this->getPath($this->qualifyClass($this->getNameInput()));
		$content = str_replace('{{ querySearch }}', $this->getNamespaceString($this->argument('query')), file_get_contents($path));

		if (!$this->option('no-contract')) {
			$content = str_replace('{{ contractNamespace }}', $this->contractNamespace(), $content);
			$content = str_replace('{{ contract }}', $this->contractName(), $content);
		}
		file_put_contents($path, $content);
	}
}
