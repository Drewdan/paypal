<?php

namespace Drewdan\Paypal\Webhooks\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Drewdan\Paypal\Common\Models\Links;
use Drewdan\Paypal\Client\PaypalClient;
use Drewdan\Paypal\Common\Contracts\FromResponse;
use Drewdan\Paypal\Webhooks\Builders\WebhookBuilder;

class Webhook implements FromResponse {

	private PaypalClient $client;

	public function __construct(
		public ?string $id = null,
		public ?string $url = null,
		public ?EventTypes $event_types = null,
		public ?Links $links = null,
		public bool $isDeleted = false,
	) {
		$this->client = PaypalClient::make(
			responseAsArray: true,
			useV1: true
		);
	}

	public static function builder(): WebhookBuilder {
		return App::make(WebhookBuilder::class);
	}

	public static function all(): Collection {
		$response = PaypalClient::make(responseAsArray: true, useV1: true)->get('notifications/webhooks');
		$webhooks = collect();

		if (!Arr::has($response, 'webhooks')) {
			return $webhooks;
		}

		foreach($response['webhooks'] as $webhook) {
			$webhooks->push(static::fromResponse($webhook));
		}

		return $webhooks;
	}

	public static function retrieve(string $id): static {
		$response = PaypalClient::make(responseAsArray: true, useV1: true)->get("notifications/webhooks/{$id}");

		return static::fromResponse($response);
	}

	public static function fromResponse(array $response): static {
		return new Webhook(
			id: $response['id'] ?? null,
			url: $response['url'] ?? null,
			event_types: Arr::has($response, 'event_types')
				? EventTypes::fromArray($response['event_types'])
				: null,
			links: $response['links']
				? Links::fromArray($response['links'])
				: null,
		);
	}

	public function listEvents(): Collection {
		return $this->event_types->eventTypes;
	}

	public function delete(): void {
		$this->client->delete("notifications/webhooks/{$this->id}");
		$this->isDeleted = true;
	}
}
