<?php

namespace Drewdan\Paypal\Webhooks\Models;

use Drewdan\Paypal\Common\Contracts\FromArray;
use Drewdan\Paypal\Webhooks\Enums\WebhookEventEnum;

class EventType implements FromArray {

	public function __construct(
		public WebhookEventEnum $name,
		public string $description,
		public ?string $status,
	) {
	}

	public static function fromArray(array $data): static {
		return new static(
			name: WebhookEventEnum::from($data['name']),
			description: $data['description'],
			status: $data['status'] ?? null,
		);
	}

}
