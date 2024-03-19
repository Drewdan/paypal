<?php

namespace Drewdan\Paypal\Models;

use Illuminate\Support\Collection;
use Drewdan\Paypal\Contracts\FromArray;

class Links implements FromArray {

	public function __construct(
		public ?Collection $links = null,
	) {
	}

	public static function fromArray(array $data): static {
		$links = collect();

		foreach ($data as $link) {
			$links->push(Link::fromArray($link));
		}

		return new static(
			links: $links,
		);
	}

	public function getByRef(string $ref): ?Link {
		return $this->links->first(fn($link) => $link->rel === $ref);
	}
}
