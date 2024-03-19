<?php

namespace Drewdan\Paypal\Webhooks\Builders;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\App;
use Drewdan\Paypal\Client\PaypalClient;
use Drewdan\Paypal\Webhooks\Models\Events;
use Drewdan\Paypal\Webhooks\Enums\WebhookEventEnum;

class WebhookEventQueryBuilder {

	private PaypalClient $client;

	public int $page_size = 10;
	public CarbonImmutable|Carbon|null $start_time = null;
	public CarbonImmutable|Carbon|null $end_time = null;
	public ?string $transaction_id = null;
	public ?WebhookEventEnum $event_type = null;

	public function __construct() {
		$this->client = PaypalClient::make(
			responseAsArray: true,
			useV1: true
		);
	}

	public static function make(): static {
		return App::make(static::class);
	}

	public function pageSize(int $page_size): static {
		$this->page_size = $page_size;
		return $this;
	}

	public function startTime(CarbonImmutable|Carbon $start_time): static {
		$this->start_time = $start_time;
		return $this;
	}

	public function endTime(CarbonImmutable|Carbon $end_time): static {
		$this->end_time = $end_time;
		return $this;
	}

	public function transactionId(string $transaction_id): static {
		$this->transaction_id = $transaction_id;
		return $this;
	}

	public function eventType(WebhookEventEnum $event_type): static {
		$this->event_type = $event_type;
		return $this;
	}

	public function get(): Events {
		$response = $this->client->get(
			'notifications/webhooks-events',
			array_filter(
				[
					'page_size' => $this->page_size,
					'start_time' => $this->start_time?->toIso8601ZuluString(),
					'end_time' => $this->end_time?->toIso8601ZuluString(),
					'transaction_id' => $this->transaction_id,
					'event_type' => $this->event_type?->value,
				]
			)
		);

		return Events::fromResponse($response);
	}

}
