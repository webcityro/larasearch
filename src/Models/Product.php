<?php

namespace Webcityro\Larasearch\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

	protected $guarded = [];
	protected $casts = [
		'price' => 'float'
	];
}
