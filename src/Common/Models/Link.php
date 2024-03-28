<?php

namespace Drewdan\Paypal\Common\Models;

use Drewdan\Paypal\Common\Contracts\ToArray;
use Drewdan\Paypal\Common\Contracts\FromArray;

class Link implements FromArray, ToArray {

	public function __construct(
		public string $href,
		public ?string $rel,
		public string $method,
	) {
	}

	public static function fromArray(array $data): static {
		return new static(
			href: $data['href'],
			rel: $data['rel'] ?? null,
			method: $data['method'],
		);
	}

	public function toArray(): array {
		return array_filter([
			'href' => $this->href,
			'rel' => $this->rel,
			'method' => $this->method,
		]);
	}

}
