<?php

namespace Webcityro\Larasearch\Search\Queries;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait EloquentSearch
{
	protected Builder|QueryBuilder $queryWithFilter;

	public function total(): int
	{
		return $this->queryWithoutLimit()->count('id');
	}

	protected function queryWithFilter(): Builder
	{
		if (!empty($this->queryWithFilter)) {
			return $this->queryWithFilter;
		}

		$this->queryWithFilter = $this->query();

		if (!$this->params->search->hasFilter()) {
			return $this->queryWithFilter;
		}

		if (!$this->hasMultiFields()) {
			return $this->filter($this->queryWithFilter, 'search', $this->params->search->search);
		}

		foreach ($this->params->search->fields as $field => $value) {
			if (!$this->params->search->isFieldEmpty($value)) {
				$this->queryWithFilter = $this->filter($this->queryWithFilter, $field, $value);
			}
		}

		return $this->queryWithFilter;
	}

	protected function queryWithoutLimit(): Builder
	{
		return $this->queryWithFilter()->orderBy($this->orderBy->field, $this->orderBy->direction);
	}

	abstract protected function query(): Builder|QueryBuilder;

	abstract protected function filter(Builder|QueryBuilder $query, string $field, $value): Builder;

	public function records(): Collection
	{
		return $this->limit($this->queryWithoutLimit())->get();
	}

	protected function limit(Builder $query): Builder
	{
		return $this->params->perPage == -1 ? $query :  $query->take($this->params->perPage)->skip(($this->params->page - 1) * $this->params->perPage);
	}
}
