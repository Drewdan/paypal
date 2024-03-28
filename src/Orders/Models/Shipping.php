<?php

namespace Drewdan\Paypal\Orders\Models;

use Illuminate\Support\Arr;
use Drewdan\Paypal\Common\Contracts\ToArray;
use Drewdan\Paypal\Common\Contracts\FromArray;

class Shipping implements FromArray, ToArray {

	public function __construct(
		public ?string $method = null,
		public ?array $trackers = null,
		public ?Address $address = null,
	) {
	}

	public function toArray(): array {
		return array_filter([
			'method' => $this->method,
			'trackers' => array_map(fn($tracker) => $tracker->toArray(), $this->trackers),
			'address' => $this->address?->toArray(),
		]);
	}

	public static function fromArray(array $data): static {
		$trackers = [];

		foreach ($data['trackers'] ?? [] as $tracker) {
			$trackers[] = Tracker::fromArray($tracker);
		}

		return new static(
			method: $data['method'] ?? null,
			trackers: $trackers,
			address: Arr::has($data, 'address')
				? Address::fromArray($data['address'])
				: null,
		);
	}
}
