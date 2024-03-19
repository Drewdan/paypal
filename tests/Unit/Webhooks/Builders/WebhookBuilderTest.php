<?php

namespace Drewdan\Paypal\Tests\Unit\Webhooks\Builders;


use Drewdan\Paypal\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Drewdan\Paypal\Webhooks\Enums\WebhookEventEnum;
use Drewdan\Paypal\Webhooks\Builders\WebhookBuilder;

class WebhookBuilderTest extends TestCase {

	public function testCanCreateWebhooks() {
		Http::fake([
			'https://api-m.paypal.com/v1/notifications/webhooks' => Http::response($this->getApiResponse('create_webhook')),
		]);

		$webhook = WebhookBuilder::make()
			->setUrl('https://example.com/example_webhook')
			->setEvents(
				[
					WebhookEventEnum::PAYMENT_AUTHORIZATION_CREATED,
					WebhookEventEnum::PAYMENT_CAPTURE_COMPLETED,
				]
			)
			->create();

		$this->assertEquals('https://example.com/example_webhook', $webhook->url);
		$this->assertCount(2, $webhook->listEvents());
	}
}
