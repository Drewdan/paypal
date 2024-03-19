<?php

namespace Drewdan\Paypal\Builders\PaymentSource;

use Illuminate\Support\Facades\App;

class PaymentSource {

	public static function make(): static {
		return App::make(static::class);
	}

	public static function fromArray(array $data): static {
		$classMap = [
			'paypal' => Paypal::class,
			'token' => Token::class,
		];

		// get the array key

		$key = array_key_first($data);

		// get the class name from the class map

		$className = $classMap[$key] ?? null;

		if (!$className) {
			throw new \Exception('Invalid payment source type');
		}

		// create a new instance of the class
		return $className::fromArray($data[$key]);
	}

}
