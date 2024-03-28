<?php

namespace Drewdan\Paypal\Orders\Models;

use Illuminate\Support\Collection;
use Drewdan\Paypal\Common\Contracts\ToArray;
use Drewdan\Paypal\Common\Contracts\FromArray;

class PurchaseUnits implements FromArray, ToArray {

	public function __construct(
		public ?Collection $purchaseUnits = null,
	) {
	}

	public static function fromArray(array $data): static {
		$items = collect($data)->map(fn ($item) => PurchaseUnit::fromArray($item));

		return new static(purchaseUnits: $items);
	}

	public function toArray(): array {
		return $this->purchaseUnits->map(fn (PurchaseUnit $purchaseUnit) => $purchaseUnit->toArray())->toArray();
	}
}
