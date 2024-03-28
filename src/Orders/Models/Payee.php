<?php

namespace Drewdan\Paypal\Orders\Models;

use Drewdan\Paypal\Common\Contracts\ToArray;
use Drewdan\Paypal\Common\Contracts\FromArray;

class Payee implements FromArray, ToArray {

	public function __construct(
		public ?string $email_address = null,
		public ?string $merchant_id = null,
	) {
	}

	public static function fromArray(array $data): static {
		return new static(
			email_address: $data['email_address'] ?? null,
			merchant_id: $data['merchant_id'] ?? null,
		);
	}

	public function toArray(): array {
		return array_filter([
			'email_address' => $this->email_address,
			'merchant_id' => $this->merchant_id,
		]);
	}
}
