<?php

namespace Drewdan\Paypal\Orders\Models;

use Drewdan\Paypal\Common\Contracts\ToArray;
use Drewdan\Paypal\Common\Contracts\FromArray;

class Payment implements FromArray, ToArray {

	public function __construct(
		public ?array $authorizations = null,
		public ?array $captures = null,
	) {
	}

	public function toArray(): array {
		return array_filter([
			'authorizations' => array_map(fn($authorization) => $authorization->toArray(), $this->authorizations),
			'captures' => array_map(fn($capture) => $capture->toArray(), $this->captures),
		]);
	}

	public static function fromArray(array $data): static {
		$authorizations = [];
		$captures = [];

		foreach ($data['authorizations'] ?? [] as $authorization) {
			$authorizations[] = Authorization::fromArray($authorization);
		}

		foreach ($data['captures'] ?? [] as $capture) {
			$captures[] = Capture::fromArray($capture);
		}

		return new static(
			authorizations: $authorizations,
			captures: $captures,
		);
	}

}
