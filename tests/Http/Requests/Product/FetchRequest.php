<?php

namespace Webcityro\Larasearch\Tests\Http\Requests\Product;

use Webcityro\Larasearch\Http\Requests\SearchRequest;
use Webcityro\Larasearch\Http\Requests\SearchFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class FetchRequest extends FormRequest implements SearchFormRequest {

	use SearchRequest;

	public function authorize(): bool {
		return true;
	}

	protected function orderByFields(): array {
		return ['name', 'price'];
	}

	protected function defaultOrderByField(): string {
		return 'name';
	}
}
