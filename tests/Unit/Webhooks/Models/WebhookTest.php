<?php

namespace Drewdan\Paypal\Tests\Unit\Webhooks\Models;


use Drewdan\Paypal\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Drewdan\Paypal\Webhooks\Models\Webhook;
use Drewdan\Paypal\Webhooks\Enums\WebhookEventEnum;

class WebhookTest extends TestCase {

	public function testCreatingWebhook() {
		Http::fake([
			'https://api-m.paypal.com/v1/notifications/webhooks' => Http::response($this->getApiResponse('create_webhook')),
		]);

		$webhook = Webhook::builder()
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

	public function testListingWebhooks() {
		Http::fake([
			'https://api-m.paypal.com/v1/notifications/webhooks' => Http::response($this->getApiResponse('list_webhooks')),
		]);

		$webhooks = Webhook::all();

		$this->assertCount(2, $webhooks);
		$this->assertInstanceOf(Webhook::class, $webhooks->first());
	}

	public function testRetrievingWebhook() {
		Http::fake([
			'https://api-m.paypal.com/v1/notifications/webhooks/0EH40505U7160970P' => Http::response($this->getApiResponse('show_webhook_details')),
		]);

		$webhook = Webhook::retrieve('0EH40505U7160970P');

		$this->assertEquals('https://example.com/example_webhook', $webhook->url);
		$this->assertCount(3, $webhook->listEvents());

		// get the first event
		$event = $webhook->listEvents()->first();

		$this->assertEquals(WebhookEventEnum::PAYMENT_AUTHORIZATION_CREATED, $event->name);
		$this->assertEquals('A payment authorization was created.', $event->description);
		$this->assertEquals('ENABLED', $event->status);
	}

	public function testDeletingWebhook() {
		Http::fake([
			'https://api-m.paypal.com/v1/notifications/webhooks/0EH40505U7160970P' => Http::response($this->getApiResponse('show_webhook_details')),

		]);

		$webhook = Webhook::retrieve('0EH40505U7160970P');

		$webhook->delete();

		Http::assertSentCount(2);
	}
}
