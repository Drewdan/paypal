<?php

namespace Drewdan\Paypal\Models;

use Drewdan\Paypal\Contracts\FromArray;

class Tracker implements FromArray {

	public function __construct(
		public ?string $id = null,
		public ?array $links = null,
		public ?string $create_time = null,
		public ?string $update_time = null,
	) {
	}

	public static function fromArray(array $data): static {
		return new static(
			id: $data['id'] ?? null,
			links: $data['links'] ?? null,
			create_time: $data['create_time'] ?? null,
			update_time: $data['update_time'] ?? null,
		);
	}
}
