<?php

namespace Drewdan\Paypal\Orders\Models;

use Drewdan\Paypal\Common\Contracts\FromArray;

class Shipping implements FromArray {

	public function __construct(
		public ?array $trackers = null,
	) {
	}

	public static function fromArray(array $data): static {
		$trackers = [];

		foreach ($data['trackers'] ?? [] as $tracker) {
			$trackers[] = Tracker::fromArray($tracker);
		}

		return new static(
			trackers: $trackers,
		);
	}
}
