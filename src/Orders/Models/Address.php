<?php

namespace Drewdan\Paypal\Orders\Models;

use Drewdan\Paypal\Common\Contracts\ToArray;
use Drewdan\Paypal\Common\Contracts\FromArray;

class Address implements FromArray, ToArray {

	public function __construct(
		public string $address_line_1,
		public string $address_line_2,
		public string $admin_area_2,
		public string $admin_area_1,
		public string $postal_code,
		public string $country_code,
	) {
	}

	public function toArray(): array {
		return [
			'address_line_1' => $this->address_line_1,
			'address_line_2' => $this->address_line_2,
			'admin_area_2' => $this->admin_area_2,
			'admin_area_1' => $this->admin_area_1,
			'postal_code' => $this->postal_code,
			'country_code' => $this->country_code,
		];
	}

	public static function fromArray(array $data): static {
		return new static(
			address_line_1: $data['address_line_1'],
			address_line_2: $data['address_line_2'],
			admin_area_2: $data['admin_area_2'],
			admin_area_1: $data['admin_area_1'],
			postal_code: $data['postal_code'],
			country_code: $data['country_code'],
		);
	}

}
