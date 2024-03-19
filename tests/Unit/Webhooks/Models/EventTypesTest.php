<?php

namespace Drewdan\Paypal\Tests\Unit\Webhooks\Models;

use Drewdan\Paypal\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Drewdan\Paypal\Webhooks\Models\EventTypes;

class EventTypesTest extends TestCase {

	public function testCanListEventSubscriptionForWebhook(): void {
		Http::fake([
			'https://api-m.paypal.com/v1/notifications/webhooks/0EH40505U7160970P/event-types' => Http::response($this->getApiResponse('list_event_subscriptions_for_webhook')),
		]);

		$eventTypes = EventTypes::listForWebhookById('0EH40505U7160970P');

		$this->assertCount(3, $eventTypes->eventTypes);

	}
}
