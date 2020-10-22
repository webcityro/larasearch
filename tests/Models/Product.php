<?php

namespace Webcityro\Larasearch\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

	protected $guarded = [];
	protected $casts = [
		'price' => 'float'
	];
}
