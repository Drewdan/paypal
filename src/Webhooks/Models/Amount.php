<?php

namespace Drewdan\Paypal\Webhooks\Models;

use Illuminate\Support\Collection;
use Drewdan\Paypal\Common\Contracts\FromArray;

class Amount implements FromArray {

	public function __construct(
		public float $total,
		public string $currency,
		public AmountDetails $details,
	) {
	}

	public static function fromArray(array $data): static {
		return new static(
			total: $data['total'],
			currency: $data['currency'],
			details: AmountDetails::fromArray($data['details']),
		);
	}
}
