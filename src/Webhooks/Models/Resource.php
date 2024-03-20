<?php

namespace Drewdan\Paypal\Webhooks\Models;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Drewdan\Paypal\Common\Models\Links;
use Drewdan\Paypal\Common\Contracts\FromArray;

class Resource implements FromArray {

	public function __construct(
		public string $id,
		public CarbonImmutable $create_time,
		public CarbonImmutable $update_time,
		public string $state,
		public Amount $amount,
		public string $parent_payment,
		public CarbonImmutable $valid_until,
		public Links $links,
	) {
	}

	public static function fromArray(array $data): static|Collection {
		return new static(
			id: $data['id'],
			create_time: CarbonImmutable::parse($data['create_time']),
			update_time: CarbonImmutable::parse($data['update_time']),
			state: $data['state'],
			amount: Amount::fromArray($data['amount']),
			parent_payment: $data['parent_payment'],
			valid_until: CarbonImmutable::parse($data['valid_until']),
			links: Links::fromArray($data['links']),
		);
	}
}
