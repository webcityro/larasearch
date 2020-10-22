<?php

namespace Webcityro\Larasearch\Search\Payloads;

class SearchOnlyPayload extends Payload {

	public $search;

	public function __construct($search = null) {
		$this->search = $search;
	}

	public function toArray(): array {
		return [
			'search' => is_null($this->search) ? '' : $this->search
		];
	}

	public function hasFilter(): bool {
		return (bool)$this->search;
	}
}
