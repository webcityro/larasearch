<?php

namespace Webcityro\Larasearch\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class Search implements Rule {

	protected $fields = [];

	public function __construct(array $fields) {
		$this->fields = $fields;
	}

	public function passes($attribute, $value) {
		$passed = true;

		if (is_array($value) && !empty($value)) {
			foreach ($value as $field => $fieldValue) {
				if (!in_array($field, $this->fields)) {
					$passed = false;
					break;
				}
			}
		}

		return $passed;
	}

	public function message() {
		return 'Invalid search field.';
	}
}
