<?php

namespace Drewdan\Paypal\Orders\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Drewdan\Paypal\Common\Contracts\ToArray;
use Drewdan\Paypal\Common\Contracts\FromArray;

class SellerReceivableBreakdown implements FromArray, ToArray {

	public function __construct(
		public ?Amount $grossAmount = null,
		public ?Amount $paypalFee = null,
		public ?Amount $netAmount = null,
	) {
	}

	public static function fromArray(array $data): static {
		return new static(
			grossAmount: Arr::has($data, 'gross_amount')
				? Amount::fromArray($data['gross_amount'])
				: null,
			paypalFee: Arr::has($data, 'paypal_fee')
				? Amount::fromArray($data['paypal_fee'])
				: null,
			netAmount: Arr::has($data, 'net_amount')
				? Amount::fromArray($data['net_amount'])
				: null,
		);
	}

	public function toArray(): array {
		return [
			'gross_amount' => $this->grossAmount?->toArray(),
			'paypal_fee' => $this->paypalFee?->toArray(),
			'net_amount' => $this->netAmount?->toArray(),
		];
	}
}
