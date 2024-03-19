<?php

namespace Drewdan\Paypal\Orders\Models;

use Drewdan\Paypal\Common\Contracts\FromArray;
use Drewdan\Paypal\Orders\Enums\AuthorizationStatusEnum;

class Authorization implements FromArray {

	public function __construct(
		public string $id,
		public AuthorizationStatusEnum $status,
		public Amount $amount,
		public SellerProtection $sellerProtection,
		public string $expirationTime,
		public array $links,
	) {
	}

	public static function fromArray(array $data): static {
		return new Authorization(
			id: $data['id'],
			status: AuthorizationStatusEnum::from($data['status']),
			amount: Amount::fromArray($data['amount']),
			sellerProtection: SellerProtection::fromArray($data['seller_protection']),
			expirationTime: $data['expiration_time'],
			links: $data['links'],
		);
	}

}
