<?php

namespace Webcityro\Larasearch\Search\Queries;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait EloquentSearch
{

	public function total(): int
	{
		return $this->queryWithoutLimit()->count('id');
	}

	protected function queryWithFilter(): Builder
	{
		$query = $this->query();

		if (!$this->params->search->hasFilter()) {
			return $query;
		}

		if (!$this->hasMultiFields()) {
			return $this->filter($query, 'search', $this->params->search->search);
		}

		if (!empty($this->params->search->fields)) {
			foreach ($this->params->search->fields as $field => $value) {
				if (!empty($value)) {
					$query = $this->filter($query, $field, $value);
				}
			}
		}

		return $query;
	}

	protected function queryWithoutLimit(): Builder
	{
		return $this->queryWithFilter()->orderBy($this->orderBy->field, $this->orderBy->direction);
	}

	abstract protected function query(): Builder|QueryBuilder;

	abstract protected function filter(Builder $query, string $field, $value): Builder;

	public function records(): Collection
	{
		return $this->limit($this->queryWithoutLimit())->get();
	}

	protected function limit(Builder $query): Builder
	{
		return $query->take($this->params->perPage)->skip(($this->params->page - 1) * $this->params->perPage);
	}
}
