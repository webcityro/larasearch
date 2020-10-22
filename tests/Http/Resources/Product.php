<?php

namespace Webcityro\Larasearch\Tests\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Product extends JsonResource {

	public function toArray($request) {
		return [
			'id' => $this->id,
			'name' => $this->name,
			'price' => $this->price,
			'edit_url' => 'product/'.$this->id.'/edit',
			'destroy_url' => 'product/destroy/'.$this->id,
		];
	}
}
