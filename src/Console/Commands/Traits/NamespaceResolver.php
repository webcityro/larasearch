<?php
namespace Webcityro\Larasearch\Console\Commands\Traits;

use Illuminate\Support\Str;

trait NamespaceResolver {

	protected function getNamespaceString(string $name) {
		$name = ltrim($name, '\\/');
        $name = str_replace('/', '\\', $name);
        $rootNamespace = $this->laravel->getNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return '\\'.$name;
		}

		return $this->getNamespaceString(trim($rootNamespace, '\\').'\\'.$name);
	}
}
