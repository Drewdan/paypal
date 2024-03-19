<?php

namespace Drewdan\Paypal\Models;

use Drewdan\Paypal\Contracts\FromArray;

class Link implements FromArray {

	public function __construct(
		public string $href,
		public string $rel,
		public string $method,
	) {
	}

	public static function fromArray(array $data): static {
		return new static(
			href: $data['href'],
			rel: $data['rel'],
			method: $data['method'],
		);
	}

}
