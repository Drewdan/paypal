<?php

namespace Drewdan\Paypal\Orders\Models;

use Drewdan\Paypal\Common\Contracts\ToArray;
use Drewdan\Paypal\Common\Contracts\FromArray;
use Drewdan\Paypal\Orders\Enums\SellerProtectionStatusEnum;

class SellerProtection implements FromArray, ToArray {

	public function __construct(
		public SellerProtectionStatusEnum $status,
		public array $disputeCategories,
	) {
	}

	public function toArray(): array {
		return [
			'status' => $this->status->value,
			'dispute_categories' => $this->disputeCategories,
		];
	}

	public static function fromArray(array $data): static {
		return new static(
			status: SellerProtectionStatusEnum::from($data['status']),
			disputeCategories: $data['dispute_categories'] ?? [],
		);
	}

}
