<?php

namespace Drewdan\Paypal\Orders\Models;

use Drewdan\Paypal\Common\Contracts\FromArray;
use Drewdan\Paypal\Orders\Enums\SellerProtectionStatusEnum;

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
