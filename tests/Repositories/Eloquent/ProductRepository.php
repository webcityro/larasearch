<?php

namespace Webcityro\Larasearch\Tests\Repositories\Eloquent;

use Webcityro\Larasearch\Search\Queries\Search;
use Webcityro\Larasearch\Tests\Search\Queries\ProductSearch;
use Webcityro\Larasearch\Http\Requests\SearchFormRequest;
use Webcityro\Larasearch\Tests\Repositories\Contracts\ProductRepositoryContract;

class ProductRepository implements ProductRepositoryContract {

	public function search(SearchFormRequest $request): Search {
		return (new ProductSearch($request->requestParams(), $request->requestOrder(), $request->searchFields()));
	}
}
