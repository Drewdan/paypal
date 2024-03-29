<?php

namespace Drewdan\Paypal\Common\Models;

use Illuminate\Support\Collection;
use Drewdan\Paypal\Common\Contracts\ToArray;
use Drewdan\Paypal\Common\Contracts\FromArray;

class Links implements FromArray, ToArray {

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

	public function toArray(): array {
		return  $this->links->map(fn($link) => $link->toArray())->toArray();
	}
}
