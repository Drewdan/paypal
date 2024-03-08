<?php

namespace Drewdan\Paypal\Models;

use Drewdan\Paypal\Contracts\FromArray;

class Payment implements FromArray {

	public function __construct(
		public ?array $authorizations = null,
		public ?array $captures = null,
	) {
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
