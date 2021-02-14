<?php

namespace Webcityro\Larasearch\Search;

use Illuminate\Contracts\Support\Arrayable;

class Meta implements Arrayable {

	public $total;
	public $lastPage;
	public $prevPage;
	public $nextPage;

	public function __construct(
		int $total,
		int $lastPage,
		int $prevPage = null,
		int $nextPage = null
	) {
		$this->total = $total;
		$this->lastPage = $lastPage;
		$this->prevPage = $prevPage;
		$this->nextPage = $nextPage;
	}

	public function toArray(): array {
		return [
			'total' => $this->total,
			'prev_page' => $this->prevPage,
			'next_page' => $this->nextPage,
			'last_page' => $this->lastPage,
		];
	}
}
