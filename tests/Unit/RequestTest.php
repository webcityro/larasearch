<?php

namespace Webcityro\Larasearch\Tests\Unit;

use Illuminate\Routing\Redirector;
use Webcityro\Larasearch\Tests\Models\Product;
use Webcityro\Larasearch\Tests\TestCase;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Webcityro\Larasearch\Http\Resources\SearchCollection;
use Webcityro\Larasearch\Tests\Http\Requests\Product\FetchRequest;
use Webcityro\Larasearch\Tests\Repositories\Eloquent\ProductRepository;
use Webcityro\Larasearch\Tests\Http\Resources\Product as ProductResource;
use Webcityro\Larasearch\Tests\Http\Requests\Product\MultiFieldsFetchRequest;

class RequestTest extends TestCase {

	use RefreshDatabase;

	protected function setUp(): void {
		parent::setUp();

		config(['larasearch.per_page' => [1, 2, 3]]);
		config(['larasearch.default_per_page' => [1]]);
	}

	/** @test */
	public function a_product_can_be_created_with_the_factory() {
		$product = factory(Product::class, 1)->create();
		$this->assertCount(1, Product::all());
	}

	/** @test */
	public function validation_fails_with_empty_request() {
		try {
			$request = new FetchRequest([]);
			$request
				->setContainer(app())
				->setRedirector(app(Redirector::class))
				->validateResolved();
		} catch (ValidationException $ex) {}

		$this->assertJsonStringEqualsJsonString(json_encode([
			'search' => [__('validation.present', ['attribute' => 'search'])],
			'order_by' => [__('validation.required', ['attribute' => 'order by'])],
			'per_page' => [__('validation.required', ['attribute' => 'per page'])],
			'page' => [__('validation.required', ['attribute' => 'page'])],
		]), json_encode($ex->errors()));
	}

	/** @test */
	public function validation_fails_with_invalid_values() {
		try {
			$request = new FetchRequest([
				'search' => json_encode([]),
				'per_page' => 4,
				'page' => 'a',
				'order_by' => 'invalid:sort'
			]);
			$request
				->setContainer(app())
				->setRedirector(app(Redirector::class))
				->validateResolved();
		} catch (ValidationException $ex) {}

		$this->assertJsonStringEqualsJsonString(json_encode([
			'order_field' => [__('validation.in', ['attribute' => 'order field'])],
			'order_direction' => [__('validation.in', ['attribute' => 'order direction'])],
			'per_page' => [__('validation.in', ['attribute' => 'per page'])],
			'page' => [__('validation.integer', ['attribute' => 'page'])],
		]), json_encode($ex->errors()));
	}

	/** @test */
	public function returns_records_with_default_filter() {
		$this->withoutExceptionHandling();
		$products = factory(Product::class, 15)->create()->sortBy('name');

		try {
			$request = new FetchRequest([
				'search' => '',
				'per_page' => 1,
				'page' => 1,
				'order_by' => 'name:asc'
			]);
			$request
				->setContainer(app())
				->setRedirector(app(Redirector::class))
				->validateResolved();
		} catch (ValidationException $ex) {}

		$records = $this->formatRecords($products, 0, 1);

		$collection = new SearchCollection(
			(new ProductRepository)->search($request), ProductResource::class
		);

		$this->assertJsonStringEqualsJsonString(json_encode([
			'params' => [
				'search' => '',
				'per_page' => 1,
				'page' => 1,
				'order_by' => 'name:asc',
			],
			'meta' => [
				'total' => 15,
				'prev_page' => null,
				'next_page' => 2,
				'last_page' => 15,
			],
			'records' => $records,
		]), $collection->toJson());
	}

	/** @test */
	public function returns_filtered_records_with_search_only_request() {
		$this->withoutExceptionHandling();
		$products = $this->create17Products();

		try {
			$request = new FetchRequest([
				'search' => '27.5',
				'per_page' => 2,
				'page' => 1,
				'order_by' => 'price:desc'
			]);
			$request
				->setContainer(app())
				->setRedirector(app(Redirector::class))
				->validateResolved();
		} catch (ValidationException $ex) {}

		$collection = new SearchCollection(
			(new ProductRepository)->search($request), ProductResource::class
		);

		$records = $products->whereIn('id', [1, 2, 3, 4, 6])->map(function (Product $product) {
			return array_merge($product->only('id', 'name', 'price'),
				[
					'edit_url' => 'product/'.$product->id.'/edit',
					'destroy_url' => 'product/destroy/'.$product->id
				]
			);
		})->sortByDesc('price')->skip(0)->take(2)->values()->toArray();

		$this->assertJsonStringEqualsJsonString(json_encode([
			'params' => [
				'search' => '27.5',
				'per_page' => 2,
				'page' => 1,
				'order_by' => 'price:desc'
			],
			'meta' => [
				'total' => 5,
				'prev_page' => null,
				'next_page' => 2,
				'last_page' => 3,
			],
			'records' => $records,
		]), $collection->toJson());
	}

	/** @test */
	public function default_filter_works_with_empty_array_as_tha_search_param() {
		$this->withoutExceptionHandling();
		$products = factory(Product::class, 15)->create()->sortBy('name');

		try {
			$request = new MultiFieldsFetchRequest([
				'search' => json_encode([]),
				'per_page' => 1,
				'page' => 1,
				'order_by' => 'name:asc'
			]);
			$request
				->setContainer(app())
				->setRedirector(app(Redirector::class))
				->validateResolved();
		} catch (ValidationException $ex) {}

		$records = $this->formatRecords($products, 0, 1);

		$collection = new SearchCollection(
			(new ProductRepository)->search($request), ProductResource::class
		);

		$this->assertJsonStringEqualsJsonString(json_encode([
			'params' => [
				'search' => [],
				'per_page' => 1,
				'page' => 1,
				'order_by' => 'name:asc',
			],
			'meta' => [
				'total' => 15,
				'prev_page' => null,
				'next_page' => 2,
				'last_page' => 15,
			],
			'records' => $records,
		]), $collection->toJson());
	}

	/** @test */
	public function overwrites_last_page_if_current_page_exceeds_number_of_available_pages() {
		$this->withoutExceptionHandling();
		$products = factory(Product::class, 15)->create()->sortBy('name');

		try {
			$request = new MultiFieldsFetchRequest([
				'search' => json_encode([]),
				'per_page' => 1,
				'page' => 16,
				'order_by' => 'name:asc'
			]);
			$request
				->setContainer(app())
				->setRedirector(app(Redirector::class))
				->validateResolved();
		} catch (ValidationException $ex) {}

		$records = $this->formatRecords($products, 14, 1);

		$collection = new SearchCollection(
			(new ProductRepository)->search($request), ProductResource::class
		);

		$this->assertJsonStringEqualsJsonString(json_encode([
			'params' => [
				'search' => [],
				'per_page' => 1,
				'page' => 15,
				'order_by' => 'name:asc',
			],
			'meta' => [
				'total' => 15,
				'prev_page' => 14,
				'next_page' => null,
				'last_page' => 15,
			],
			'records' => $records,
		]), $collection->toJson());
	}

	/** @test */
	public function returns_filtered_records_with_multi_fields_search() {
		$this->withoutExceptionHandling();
		$products = $this->create17Products();

		try {
			$request = new MultiFieldsFetchRequest([
				'search' => json_encode([
					'name' => 'Trek',
					'price' => '2700.0'
				]),
				'per_page' => 2,
				'page' => 1,
				'order_by' => 'name:asc'
			]);
			$request
				->setContainer(app())
				->setRedirector(app(Redirector::class))
				->validateResolved();
		} catch (ValidationException $ex) {}

		$collection = new SearchCollection(
			(new ProductRepository)->search($request), ProductResource::class
		);

		$records = $products->whereIn('id', [2, 3])->map(function (Product $product) {
			return array_merge($product->only('id', 'name', 'price'),
				[
					'edit_url' => 'product/'.$product->id.'/edit',
					'destroy_url' => 'product/destroy/'.$product->id
				]
			);
		})->sortByDesc('price')->skip(0)->take(2)->values()->toArray();

		$this->assertJsonStringEqualsJsonString(json_encode([
			'params' => [
				'search' => [
					'name' => 'Trek',
					'price' => '2700.0'
				],
				'per_page' => 2,
				'page' => 1,
				'order_by' => 'name:asc'
			],
			'meta' => [
				'total' => 2,
				'prev_page' => null,
				'next_page' => null,
				'last_page' => 1,
			],
			'records' => $records,
		]), $collection->toJson());
	}

	private function create17Products()	{
		return collect([
			factory(Product::class)->create([
				'id' => 1, 'name' => 'Trek Remedy 7 27.5', 'price' => '2200.00'
			]),
			factory(Product::class)->create([
				'id' => 2, 'name' => 'Trek Remedy 8 27.5', 'price' => '2700.00'
			]),
			factory(Product::class)->create([
				'id' => 3, 'name' => 'Trek Remedy 9.7 27.5', 'price' => '2700.00'
			]),
			factory(Product::class)->create([
				'id' => 4, 'name' => 'Yeti SB165 27.5', 'price' => '5599.00'
			]),
			factory(Product::class)->create([
				'id' => 5, 'name' => 'Yeti SB150 29', 'price' => '5699.00'
			]),
			factory(Product::class)->create([
				'id' => 6, 'name' => 'Kona Process 153 CR/DL 27.5', 'price' => '3500.00'
			]),
			factory(Product::class)->create([
				'id' => 7, 'name' => 'Kona Hei Hei 29', 'price' => '3650.00'
			]),
		]);
	}

	private function formatRecords($products, $skip, $take) {
		return $products->skip($skip)->take($take)->map(function (Product $product) {
			return array_merge($product->only('id', 'name', 'price'),
				[
					'edit_url' => 'product/'.$product->id.'/edit',
					'destroy_url' => 'product/destroy/'.$product->id
				]
			);
		})->values()->toArray();
	}

}
