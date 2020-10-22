<?php

namespace Webcityro\Larasearch\Search\Queries;

use Webcityro\Larasearch\Search\Meta;
use Webcityro\Larasearch\Search\OrderBy;
use Webcityro\Larasearch\Search\Params;
use Illuminate\Support\Collection;

abstract class Search {

	protected $params;
	protected $orderBy;
	private $searchFields;

	public function __construct(Params $params, OrderBy $orderBy, array $searchFields) {
		$this->params = $params;
		$this->orderBy = $orderBy;
		$this->searchFields = $searchFields;
	}

	public function meta(): Meta {
		$total = $this->total();
		$lastPage = $this->lastPage($total);

		if ($lastPage < $this->params->page) {
			$this->params->page = $lastPage;
		}

		return new Meta(
			$total,
			$lastPage,
			$this->prevPage(),
			$this->nextPage($lastPage)
		);
	}

	public function params(): Params {
		return $this->params;
	}

	public function searchFields(): array {
		return $this->searchFields;
	}

	public function hasMultiFields(): bool {
		return !empty($this->searchFields);
	}

	abstract public function total(): int;

	abstract public function records(): Collection;

	protected function lastPage(int $total): int {
		return ceil($total / $this->params->perPage) ?: 1;
	}

	protected function prevPage(): ?int {
		return $this->params->page <= 1 ? null : $this->params->page - 1;
	}

	protected function nextPage(int $lastPage): ?int {
		return $this->params->page < $lastPage ? $this->params->page + 1 : null;
	}
}
