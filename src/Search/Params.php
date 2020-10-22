<?php

namespace Webcityro\Larasearch\Search;

use Webcityro\Larasearch\Search\Payloads\Payload;
use Illuminate\Contracts\Support\Arrayable;

class Params implements Arrayable {

	public $search;
	public $perPage;
	public $page;
	public $orderBy;

	public function __construct(Payload $search, int $perPage, int $page, string $orderBy) {
		$this->search = $search;
		$this->perPage = $perPage;
		$this->page = $page;
		$this->orderBy = $orderBy;
	}

	public function toArray(): array {
		return array_merge([
			'per_page' => $this->perPage,
			'page' => $this->page,
			'order_by' => $this->orderBy,
		], $this->search->toArray());
	}
}
