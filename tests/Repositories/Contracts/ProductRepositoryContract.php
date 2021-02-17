<?php

namespace Webcityro\Larasearch\Tests\Repositories\Contracts;

use Webcityro\Larasearch\Search\Queries\Search;
use Webcityro\Larasearch\Http\Requests\SearchFormRequest;

interface ProductRepositoryContract {

	public function search(SearchFormRequest $request): Search;
}
