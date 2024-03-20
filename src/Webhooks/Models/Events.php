<?php

namespace Drewdan\Paypal\Webhooks\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Drewdan\Paypal\Common\Models\Links;
use Drewdan\Paypal\Client\PaypalClient;
use Illuminate\Http\Client\PendingRequest;
use Drewdan\Paypal\Common\Contracts\FromResponse;

class Events implements FromResponse {

	private PendingRequest $client;

	public function __construct(
		public Collection $events,
		public int $count,
		public ?Links $links = null,
	) {
		$this->client = PaypalClient::make(
			responseAsArray: true,
			useV1: true
		)->getClient()->baseUrl('');
	}

	public static function fromResponse(array $response): static {
		return new static(
			events: collect($response['events'] ?? [])->map(fn ($event) => WebhookEvent::fromResponse($event)),
			count: $response['count'] ?? 0,
			links: Arr::has($response, 'links')
				? Links::fromArray($response['links'])
				: null,
		);
	}

	public function getEvents(): Collection {
		return $this->events;
	}

	public function hasNextPage(): bool {
		return $this->links->getByRef('next') !== null;
	}

	public function hasPreviousPage(): bool {
		return $this->links->getByRef('previous') !== null;
	}

	public function nextPage(): null|static {
		$link = $this->links->getByRef('next');

		if ($link) {
			$response = $this->client->get($link->href);
			return static::fromResponse($response);
		}

		return null;
	}

	public function previousPage(): null|static {
		$link = $this->links->getByRef('previous');

		if ($link) {
			$response = $this->client->get($link->href);
			return static::fromResponse($response);
		}

		return null;

	}

}
