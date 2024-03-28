<?php

namespace Drewdan\Paypal\Orders\Models;

use Drewdan\Paypal\Common\Contracts\ToArray;
use Drewdan\Paypal\Common\Contracts\FromArray;

class Tracker implements FromArray, ToArray {

	public function __construct(
		public ?string $id = null,
		public ?array $links = null,
		public ?string $create_time = null,
		public ?string $update_time = null,
	) {
	}

	public function toArray(): array {
		return [
			'id' => $this->id,
			'links' => $this->links,
			'create_time' => $this->create_time,
			'update_time' => $this->update_time,
		];
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
