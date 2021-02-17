<?php

namespace Webcityro\Larasearch\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Webcityro\Larasearch\Console\Commands\Traits\NamespaceResolver;

class MakeQueryCommand extends GeneratorCommand {

	use NamespaceResolver;

	protected $name = 'larasearch:make:query';
	protected $signature  = 'larasearch:make:query {name : Tne name of the query.} {--model=MyModel : The model on weth to preform the search.}';
	protected $description = 'Generate a larasearch specific search query.';
	protected $type = 'Query';

	protected function getStub() {
		return __DIR__.'/stubs/search/queries/SearchQuery.php.stub';
	}

	protected function getDefaultNamespace($rootNamespace) {
		return $rootNamespace.'\Search\Queries';
	}

	public function handle() {
		parent::handle();

		if ($this->option('model') !== 'MyModel') {
			$this->applyModel();
		}
	}

	protected function applyModel() {
		$path = $this->getPath($this->qualifyClass($this->getNameInput()));
		$content = file_get_contents($path);
		file_put_contents($path, str_replace('MyModel', $this->getNamespaceString($this->option('model')), $content));
	}
}
