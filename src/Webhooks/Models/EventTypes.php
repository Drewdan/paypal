<?php

namespace Drewdan\Paypal\Webhooks\Models;

use Illuminate\Support\Collection;
use Drewdan\Paypal\Client\PaypalClient;
use Drewdan\Paypal\Common\Contracts\FromArray;

class EventTypes implements FromArray {

	public function __construct(
		public Collection $eventTypes,
	) {
	}

	public static function fromArray(array $data): static {
		$eventTypes = collect();
		foreach ($data as $eventType) {
			$eventTypes->push(EventType::fromArray($eventType));
		}

		return new static(
			eventTypes: $eventTypes,
		);
	}

	public static function listForWebhookById(string $id) {
		$response = PaypalClient::make(responseAsArray: true, useV1: true)->get("notifications/webhooks/{$id}/event-types");


		return static::fromArray($response['event_types']);
	}
}
