<?php

namespace Webcityro\Larasearch\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeRequestCommand extends GeneratorCommand {

	protected $name = 'larasearch:make:request';
	protected $signature  = 'larasearch:make:request {name : Tne name of the request} {--m|multi : Create a request that accepts multiple search fields}';
	protected $description = 'Generate a larasearch specific request.';
	protected $type = 'Request';

	protected function getStub() {
		return __DIR__.'/stubs/request/'.($this->option('multi') ? 'MultiFieldsRequest.php.stub' : 'SearchOnlyRequest.php.stub');
	}

	protected function getDefaultNamespace($rootNamespace) {
		return $rootNamespace.'\Http\Requests';
	}

	public function handle() {
		parent::handle();
	}
}
