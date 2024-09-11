<?php

namespace Webcityro\Larasearch\Http\Requests;

use Webcityro\Larasearch\Rules\Search;
use Webcityro\Larasearch\Search\Params;
use Webcityro\Larasearch\Search\OrderBy;
use Illuminate\Validation\Rule;
use Webcityro\Larasearch\Search\Payloads\Payload;
use Webcityro\Larasearch\Search\Payloads\SearchOnlyPayload;

trait SearchRequest
{

	public function rules(): array
	{
		return array_merge([
			'page' => 'required|integer',
			'order_by' => 'required|string',
			'per_page' => [
				'required',
				Rule::in(array_merge([-1], config('larasearch.per_page')))
			],
			'order_field' => [
				Rule::in($this->orderByFields())
			],
			'order_direction' => [
				Rule::in(['asc', 'desc'])
			]
		], $this->searchParams());
	}

	protected function searchParams(): array
	{
		return [
			'search' => empty($this->searchFields()) ? [
				'present',
				'nullable',
				'string'
			] : [
				'present',
				'array',
				new Search($this->searchFields())
			],
		];
	}

	public function searchFields(): array
	{
		return [];
	}

	abstract protected function orderByFields(): array;

	abstract protected function defaultOrderByField(): string;

	protected function defaultOrderByDirection(): string
	{
		return 'asc';
	}

	protected function prepareForValidation(): void
	{
		$this->order_by = $this->order_by ??
			$this->defaultOrderByField() . ':' . $this->defaultOrderByDirection();

		[$order, $direction] = explode(':', $this->order_by);

		$this->offsetSet('order_field', $order);
		$this->offsetSet('order_direction', $direction);

		$this->per_page = (int)($this->per_page ?? config('system.default_per_page'));
		$this->page = (int)($this->page ?? 1);

		if (!empty($this->search) && !empty($this->searchFields()) && !is_array($this->search)) {
			$this->offsetSet('search', json_decode($this->search, true));
		}
	}

	public function requestParams(): Params
	{
		return new Params($this->payload(), $this->per_page, $this->page, $this->order_by);
	}

	protected function payload(): Payload
	{
		return new SearchOnlyPayload($this->search ?? null);
	}

	public function requestOrder(): OrderBy
	{
		return new OrderBy($this->order_field, $this->order_direction);
	}
}
