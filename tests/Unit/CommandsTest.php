<?php

namespace Webcityro\Larasearch\Tests\Unit;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Webcityro\Larasearch\Tests\TestCase;

class CommandsTest extends TestCase {

	/** @test */
	public function a_search_only_request_can_be_created() {
		$productClass = app_path('Http/Requests/ProductRequest.php');

		if (File::exists($productClass)) {
			unlink($productClass);
		}

		$this->assertFileDoesNotExist($productClass);
		$this->artisan('larasearch:make:request', [
			'name' => 'ProductRequest'
		])
			->expectsOutput('Request created successfully.');
		$this->assertFileExists($productClass);

		$expectedContents = <<<CLASS
<?php

namespace App\Http\Requests;

use Webcityro\Larasearch\Http\Requests\SearchRequest;
use Webcityro\Larasearch\Http\Requests\SearchFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest implements SearchFormRequest {

	use SearchRequest;

	public function authorize(): bool {
		return true;
	}

	protected function orderByFields(): array {
		return ['name'];
	}

	protected function defaultOrderByField(): string {
		return 'name';
	}
}

CLASS;

		$this->assertStringEqualsFile($productClass, $expectedContents);
	}

	/** @test */
	public function a_multi_fields_search_request_can_be_created() {
		$productClass = app_path('Http/Requests/ProductRequest.php');

		if (File::exists($productClass)) {
			unlink($productClass);
		}

		$this->assertFileDoesNotExist($productClass);
		$this->artisan('larasearch:make:request', [
			'name' => 'ProductRequest',
			'--multi' => true
		])
			->expectsOutput('Request created successfully.');
		$this->assertFileExists($productClass);

		$expectedContents = <<<CLASS
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webcityro\Larasearch\Search\Payloads\Payload;
use Webcityro\Larasearch\Http\Requests\SearchRequest;
use Webcityro\Larasearch\Http\Requests\SearchFormRequest;
use Webcityro\Larasearch\Search\Payloads\MultiFieldsPayload;

class ProductRequest extends FormRequest implements SearchFormRequest {

	use SearchRequest;

	public function authorize(): bool {
		return true;
	}

	public function searchFields(): array {
		return ['name'];
	}

	protected function orderByFields(): array {
		return ['name'];
	}

	protected function defaultOrderByField(): string {
		return 'name';
	}

	protected function payload(): Payload {
		return new MultiFieldsPayload(\$this->search ?? []);
	}

}

CLASS;

		$this->assertStringEqualsFile($productClass, $expectedContents);
	}

	/** @test */
	public function a_query_can_be_created() {
		$productClass = app_path('Search/Queries/ProductQuery.php');

		if (File::exists($productClass)) {
			unlink($productClass);
		}

		$this->assertFileDoesNotExist($productClass);
		$this->artisan('larasearch:make:query', [
			'name' => 'ProductQuery',
			'--model' => 'Models/Product'
		])
			->expectsOutput('Query created successfully.');

		$this->assertFileExists($productClass);

		$expectedContents = <<<CLASS
<?php

namespace App\Search\Queries;

use Illuminate\Database\Eloquent\Builder;
use Webcityro\Larasearch\Search\Queries\EloquentSearch;
use Webcityro\Larasearch\Search\Queries\Search;

class ProductQuery extends Search {

	use EloquentSearch;

	protected function query(): Builder {
	 	return \App\Models\Product::query();
	}

	protected function filter(Builder \$query, string \$field, string \$value): Builder {
		// The \$field variable contains the field name (column) if your using a Multi fields search or the string "search" if your using a single field search.

		// if your using multi fields search.
		// return \$query->where(\$field, 'LIKE', '%'.\$value.'%');

		// if your using a single field search
		// return \$query->where('name', 'LIKE', '%'.\$value.'%');
	}
}

CLASS;

		$this->assertStringEqualsFile($productClass, $expectedContents);
	}

	/** @test */
	public function a_repository_contract_can_be_created() {
		$productRepositoryContract = app_path('Repositories/Contracts/ProductRepositoryContract.php');

		if (File::exists($productRepositoryContract)) {
			unlink($productRepositoryContract);
		}

		$this->assertFileDoesNotExist($productRepositoryContract);
		$this->artisan('larasearch:make:repository-contract', [
			'name' => 'ProductRepositoryContract'
		])
			->expectsOutput('Repository contract created successfully.');
		$this->assertFileExists($productRepositoryContract);

		$expectedContents = <<<CLASS
<?php

namespace App\Repositories\Contracts;

use Webcityro\Larasearch\Search\Queries\Search;
use Webcityro\Larasearch\Http\Requests\SearchFormRequest;

interface ProductRepositoryContract {

	public function search(SearchFormRequest \$request): Search;
}

CLASS;

		$this->assertStringEqualsFile($productRepositoryContract, $expectedContents);
	}

	/** @test */
	public function a_repository_can_be_created_without_a_contract() {
		$productRepositoryContract = app_path('Repositories/Contracts/ProductRepositoryContract.php');
		$productRepositoryClass = app_path('Repositories/Eloquent/ProductRepository.php');

		if (File::exists($productRepositoryContract)) {
			unlink($productRepositoryContract);
		}

		if (File::exists($productRepositoryClass)) {
			unlink($productRepositoryClass);
		}

		$this->assertFileDoesNotExist($productRepositoryClass);
		$this->assertFileDoesNotExist($productRepositoryContract);
		$this->artisan('larasearch:make:repository', [
			'name' => 'ProductRepository',
			'query' => 'Search/Queries/ProductQuery',
			'--no-contract' => true
		])
			->expectsOutput('Repository created successfully.');
		$this->assertFileExists($productRepositoryClass);
		$this->assertFileDoesNotExist($productRepositoryContract);

		$expectedContents = <<<CLASS
<?php

namespace App\Repositories\Eloquent;

use Webcityro\Larasearch\Search\Queries\Search;
use Webcityro\Larasearch\Http\Requests\SearchFormRequest;

class ProductRepository {

	public function search(SearchFormRequest \$request): Search {
		return (new \App\Search\Queries\ProductQuery(\$request->requestParams(), \$request->requestOrder(), \$request->searchFields()));
	}
}

CLASS;

		$this->assertStringEqualsFile($productRepositoryClass, $expectedContents);
	}

	/** @test */
	public function a_repository_can_be_created_with_a_contract() {
		$this->withoutExceptionHandling();
		$productRepositoryContract = app_path('Repositories/Contracts/ProductRepositoryContract.php');
		$productRepositoryClass = app_path('Repositories/Eloquent/ProductRepository.php');

		if (File::exists($productRepositoryContract)) {
			unlink($productRepositoryContract);
		}

		if (File::exists($productRepositoryClass)) {
			unlink($productRepositoryClass);
		}

		$this->assertFileDoesNotExist($productRepositoryClass);
		$this->assertFileDoesNotExist($productRepositoryContract);

		$this->artisan('larasearch:make:repository', [
			'name' => 'ProductRepository',
			'query' => 'Search/Queries/ProductQuery',
		])
			->expectsOutput("Repository created successfully.")
			->expectsOutput('Repository contract created successfully.')
			->expectsOutput("Add the next line to your service provider's boot method.")
			->expectsOutput('$this->app->bind(\\App\\Repositories\\Contracts\\ProductRepositoryContract::class, \\App\\Repositories\\Eloquent\\ProductRepository::class);');

		$this->assertFileExists($productRepositoryClass);
		$this->assertFileExists($productRepositoryContract);

		$expectedRepositoryContents = <<<CLASS
<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ProductRepositoryContract;
use Webcityro\Larasearch\Search\Queries\Search;
use Webcityro\Larasearch\Http\Requests\SearchFormRequest;

class ProductRepository implements ProductRepositoryContract {

	public function search(SearchFormRequest \$request): Search {
		return (new \App\Search\Queries\ProductQuery(\$request->requestParams(), \$request->requestOrder(), \$request->searchFields()));
	}
}

CLASS;

		$expectedRepositoryContractContents = <<<CLASS
<?php

namespace App\Repositories\Contracts;

use Webcityro\Larasearch\Search\Queries\Search;
use Webcityro\Larasearch\Http\Requests\SearchFormRequest;

interface ProductRepositoryContract {

	public function search(SearchFormRequest \$request): Search;
}

CLASS;

		$this->assertStringEqualsFile($productRepositoryClass, $expectedRepositoryContents);
		$this->assertStringEqualsFile($productRepositoryContract, $expectedRepositoryContractContents);
	}

	/** @test */
	public function all_the_necessary_classes_can_be_created_with_one_command() {
		$productRequestClass = app_path('Http/Requests/ProductRequest.php');
		$productQueryClass = app_path('Search/Queries/ProductQuery.php');
		$productRepositoryContract = app_path('Repositories/Contracts/ProductRepositoryContract.php');
		$productRepositoryClass = app_path('Repositories/Eloquent/ProductRepository.php');

		if (File::exists($productRequestClass)) {
			unlink($productRequestClass);
		}

		if (File::exists($productQueryClass)) {
			unlink($productQueryClass);
		}

		if (File::exists($productRepositoryContract)) {
			unlink($productRepositoryContract);
		}

		if (File::exists($productRepositoryClass)) {
			unlink($productRepositoryClass);
		}

		$this->assertFileDoesNotExist($productRequestClass);
		$this->assertFileDoesNotExist($productQueryClass);
		$this->assertFileDoesNotExist($productRepositoryClass);
		$this->assertFileDoesNotExist($productRepositoryContract);
		$this->artisan('larasearch:make:all Product Models/Product')
			->expectsOutput("Request created successfully.")
			->expectsOutput("Query created successfully.")
			->expectsOutput("Repository created successfully.")
			->expectsOutput('Repository contract created successfully.')
			->expectsOutput("Add the next line to your service provider's boot method.")
			->expectsOutput('$this->app->bind(\\App\\Repositories\\Contracts\\ProductRepositoryContract::class, \\App\\Repositories\\Eloquent\\ProductRepository::class);');
		$this->assertFileExists($productRequestClass);
		$this->assertFileExists($productQueryClass);
		$this->assertFileExists($productRepositoryClass);
		$this->assertFileExists($productRepositoryContract);

		$expectedRequestContents = <<<CLASS
<?php

namespace App\Http\Requests;

use Webcityro\Larasearch\Http\Requests\SearchRequest;
use Webcityro\Larasearch\Http\Requests\SearchFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest implements SearchFormRequest {

	use SearchRequest;

	public function authorize(): bool {
		return true;
	}

	protected function orderByFields(): array {
		return ['name'];
	}

	protected function defaultOrderByField(): string {
		return 'name';
	}
}

CLASS;

		$expectedQueryContents = <<<CLASS
<?php

namespace App\Search\Queries;

use Illuminate\Database\Eloquent\Builder;
use Webcityro\Larasearch\Search\Queries\EloquentSearch;
use Webcityro\Larasearch\Search\Queries\Search;

class ProductQuery extends Search {

	use EloquentSearch;

	protected function query(): Builder {
	 	return \App\Models\Product::query();
	}

	protected function filter(Builder \$query, string \$field, string \$value): Builder {
		// The \$field variable contains the field name (column) if your using a Multi fields search or the string "search" if your using a single field search.

		// if your using multi fields search.
		// return \$query->where(\$field, 'LIKE', '%'.\$value.'%');

		// if your using a single field search
		// return \$query->where('name', 'LIKE', '%'.\$value.'%');
	}
}

CLASS;

		$expectedRepositoryContents = <<<CLASS
<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ProductRepositoryContract;
use Webcityro\Larasearch\Search\Queries\Search;
use Webcityro\Larasearch\Http\Requests\SearchFormRequest;

class ProductRepository implements ProductRepositoryContract {

	public function search(SearchFormRequest \$request): Search {
		return (new \App\Search\Queries\ProductQuery(\$request->requestParams(), \$request->requestOrder(), \$request->searchFields()));
	}
}

CLASS;

		$expectedRepositoryContractContents = <<<CLASS
<?php

namespace App\Repositories\Contracts;

use Webcityro\Larasearch\Search\Queries\Search;
use Webcityro\Larasearch\Http\Requests\SearchFormRequest;

interface ProductRepositoryContract {

	public function search(SearchFormRequest \$request): Search;
}

CLASS;

		$this->assertStringEqualsFile($productRequestClass, $expectedRequestContents);
		$this->assertStringEqualsFile($productQueryClass, $expectedQueryContents);
		$this->assertStringEqualsFile($productRepositoryContract, $expectedRepositoryContractContents);
		$this->assertStringEqualsFile($productRepositoryClass, $expectedRepositoryContents);
	}
}
