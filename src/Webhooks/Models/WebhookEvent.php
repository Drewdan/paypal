<?php

namespace Drewdan\Paypal\Webhooks\Models;

use Carbon\CarbonImmutable;
use Drewdan\Paypal\Common\Models\Links;
use Drewdan\Paypal\Client\PaypalClient;
use Drewdan\Paypal\Common\Contracts\FromResponse;
use Drewdan\Paypal\Webhooks\Enums\WebhookEventEnum;
use Drewdan\Paypal\Webhooks\Builders\WebhookEventQueryBuilder;

class WebhookEvent implements FromResponse {

	public function __construct(
		public string $id,
		public CarbonImmutable $create_time,
		public string $resource_type,
		public string $event_version,
		public WebhookEventEnum $event_type,
		public string $summary,
		public string $resource_version,
		public Resource $resource,
		public Links $links,
	) {
	}

	public function getEventType(): WebhookEventEnum {
		return $this->event_type;
	}

	public static function fromResponse(array $response): static {
		return new static(
			id: $response['id'],
			create_time: CarbonImmutable::parse($response['create_time']),
			resource_type: $response['resource_type'],
			event_version: $response['event_version'],
			event_type: WebhookEventEnum::from($response['event_type']),
			summary: $response['summary'],
			resource_version: $response['resource_version'],
			resource: Resource::fromArray($response['resource']),
			links: Links::fromArray($response['links']),
		);
	}

	public static function retrieve(string $id): static {
		$response = PaypalClient::make(responseAsArray: true, useV1: true)->get("notifications/webhooks-events/{$id}");

		return static::fromResponse($response);
	}

	public static function resend(string $id): static {
		$response = PaypalClient::make(responseAsArray: true, useV1: true)->post("notifications/webhooks-events/{$id}/resend");

		return static::fromResponse($response);
	}

	public static function simulate(string $url, WebhookEventEnum $eventType, string $resourceVersion = '2.0'): static {
		$response = PaypalClient::make(responseAsArray: true, useV1: true)->post('notifications/simulate-event', [
			'url' => $url,
			'event_type' => $eventType->value,
			'resource_version' => $resourceVersion,
		]);

		return static::fromResponse($response);
	}

	public static function query(): WebhookEventQueryBuilder {
		return WebhookEventQueryBuilder::make();
	}
}
