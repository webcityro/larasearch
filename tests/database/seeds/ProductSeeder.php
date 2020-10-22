<?php

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder {

	public function run() {
		factory(Webcityro\Larasearch\Tests\Models\Product::class, 20)->create();
	}
}
