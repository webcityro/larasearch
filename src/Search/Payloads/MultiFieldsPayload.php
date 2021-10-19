<?php

namespace Webcityro\Larasearch\Search\Payloads;

class MultiFieldsPayload extends Payload
{

	public $fields;

	public function __construct(array $fields = [])
	{
		$this->fields = $fields;
	}

	public function toArray(): array
	{
		return [
			'search' => $this->fields
		];
	}

	public function hasFilter(): bool
	{
		$found = false;

		foreach ($this->fields as $value) {
			if ($value !== '') {
				$found = true;
				break;
			}
		}
		return (bool)$found;
	}
}
