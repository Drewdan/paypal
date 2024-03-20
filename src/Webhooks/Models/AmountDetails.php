<?php

namespace Drewdan\Paypal\Webhooks\Models;

use Illuminate\Support\Collection;
use Drewdan\Paypal\Common\Contracts\FromArray;

class AmountDetails implements FromArray {

	public function __construct(
		public float $subtotal,
	) {
	}

	public static function fromArray(array $data): static|Collection {
		return new static(
			subtotal: $data['subtotal'],
		);
	}
}
