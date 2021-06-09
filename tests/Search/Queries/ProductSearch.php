<?php

namespace Webcityro\Larasearch\Tests\Search\Queries;

use Illuminate\Database\Eloquent\Builder;
use Webcityro\Larasearch\Tests\Models\Product;
use Webcityro\Larasearch\Search\Queries\Search;
use Webcityro\Larasearch\Search\Queries\EloquentSearch;

class ProductSearch extends Search
{

	use EloquentSearch;

	protected function query(): Builder
	{
		return Product::query();
	}

	protected function filter(Builder $query, string $field, $value): Builder
	{
		return $query->where(($field === 'search' ? 'name' : $field), 'LIKE', '%' . $value . '%');
	}
}
