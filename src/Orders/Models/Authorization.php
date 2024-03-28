<?php

namespace Drewdan\Paypal\Orders\Models;

use Drewdan\Paypal\Common\Contracts\ToArray;
use Drewdan\Paypal\Common\Contracts\FromArray;
use Drewdan\Paypal\Orders\Enums\AuthorizationStatusEnum;

class Authorization implements FromArray, ToArray {

	public function __construct(
		public string $id,
		public AuthorizationStatusEnum $status,
		public Amount $amount,
		public SellerProtection $sellerProtection,
		public string $expirationTime,
		public array $links,
	) {
	}

	public function toArray(): array {
		return [
			'id' => $this->id,
			'status' => $this->status->value,
			'amount' => $this->amount->toArray(),
			'seller_protection' => $this->sellerProtection->toArray(),
			'expiration_time' => $this->expirationTime,
			'links' => $this->links,
		];

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
