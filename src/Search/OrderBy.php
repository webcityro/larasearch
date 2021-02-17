<?php

namespace Webcityro\Larasearch\Search;

class OrderBy {

	public $field;
	public $direction;

	public function __construct(string $field, string $direction) {
		$this->field = $field;
		$this->direction = $direction;
	}
}
