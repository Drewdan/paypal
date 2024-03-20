<?php

namespace Drewdan\Paypal\Orders\Builders\PaymentSource;

use Drewdan\Paypal\Orders\Contracts\BuildsPaymentSource;

class Token extends PaymentSource implements BuildsPaymentSource {

	public ?string $id = null;

	public ?string $type = null;

	public function setId(string $id): self {
		$this->id = $id;
		return $this;
	}

	public function setType(string $type): self {
		$this->type = $type;
		return $this;
	}

	public function buildPaymentSource(): array {
		if ($this->id === null) {
			throw new \Exception('Token ID is required');
		}

		if ($this->type === null) {
			throw new \Exception('Token type is required');
		}

		return [
			'token' => [
				'id' => $this->id,
				'type' => $this->type,
			],
		];
	}
}
