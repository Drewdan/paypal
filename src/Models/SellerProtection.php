<?php

namespace Drewdan\Paypal\Models;

use Drewdan\Paypal\Contracts\FromArray;
use Drewdan\Paypal\Enums\SellerProtectionStatusEnum;

class SellerProtection implements FromArray {

	public function __construct(
		public SellerProtectionStatusEnum $status,
		public array $disputeCategories,
	) {
	}

	public static function fromArray(array $data): static {
		return new static(
			status: SellerProtectionStatusEnum::from($data['status']),
			disputeCategories: $data['dispute_categories'] ?? [],
		);
	}

}
