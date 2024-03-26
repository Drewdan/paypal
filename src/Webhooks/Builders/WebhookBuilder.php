<?php

namespace Drewdan\Paypal\Webhooks\Builders;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Drewdan\Paypal\Client\PaypalClient;
use Drewdan\Paypal\Webhooks\Models\Webhook;
use Drewdan\Paypal\Common\Contracts\BuildsPayload;
use Drewdan\Paypal\Webhooks\Enums\WebhookEventEnum;

class WebhookBuilder implements BuildsPayload {

	private PaypalClient $client;

	public string $url = '';

	public Collection $events;

	public function __construct() {
		$this->events = collect();
		$this->client = PaypalClient::make(
			responseAsArray: true,
			useV1: true
		);
	}

	public static function make(): static {
		return App::make(static::class);
	}

	public function setUrl(string $url): static {
		$this->url = $url;

		return $this;
	}

	public function setEvents(array|Collection $events): static {
		$this->events = $events instanceof Collection
			? $events
			: collect($events);

		return $this;
	}

	public function addEvent(string $event): static {
		$this->events->push($event);

		return $this;
	}

	public function buildPayload(): array {
		return [
			'url' => $this->url,
			'event_types' => $this->events->map(fn (WebhookEventEnum $event) => ['name' => $event->value]),
		];
	}

	public function create(): Webhook {
		$response = $this->client->client->post('notifications/webhooks', $this->buildPayload());

		return Webhook::fromResponse($response->json());
	}
}
