<?php

namespace Drewdan\Paypal\Orders\Models;

use Illuminate\Support\Facades\App;
use Drewdan\Paypal\Common\Contracts\BuildsPayload;

class Amount implements BuildsPayload {

	public string $currencyCode;
	public float $value;

	public static function make(): static {
		return App::make(static::class);
	}

	public static function fromArray(array $data) {
		return static::make()
			->setCurrencyCode($data['currency_code'])
			->setValue($data['value']);

	}

	public function buildPayload(): array {
		return [
			'currency_code' => $this->currencyCode,
			'value' => $this->value,
		];
	}

	public function setCurrencyCode(string $currencyCode): self {
		$this->currencyCode = $currencyCode;
		return $this;
	}

	public function setValue(float $value): self {
		$this->value = $value;
		return $this;
	}

}
