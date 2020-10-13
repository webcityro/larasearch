<?php

namespace Webcityro\Larasearch\Tests\Unit;

use Webcityro\Larasearch\Models\Product;
use Webcityro\Larasearch\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InitialTest extends TestCase {

	use RefreshDatabase;

	/** @test */
	public function a_product_can_be_created_with_the_factory() {
		$product = factory(Product::class, 1)->create();
		$this->assertCount(1, Product::all());
	}
}
