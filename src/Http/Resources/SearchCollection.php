<?php

namespace Webcityro\Larasearch\Http\Resources;

use Webcityro\Larasearch\Search\Queries\Search;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SearchCollection extends ResourceCollection {

	private $meta;
	private $params;

	public function __construct(Search $search, string $collects) {
		$this->collects = $collects;
		$this->meta = $search->meta();
		$this->params = $search->params();

		parent::__construct($search->records());
	}

	public function toArray($request): array {
		return [
			'records' => $this->collection,
			'params' => $this->params->toArray(),
			'meta' => $this->meta->toArray(),
		];
	}
}
