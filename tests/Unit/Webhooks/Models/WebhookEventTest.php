<?php

namespace Drewdan\Paypal\Tests\Unit\Webhooks\Models;


use Drewdan\Paypal\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Date;
use Drewdan\Paypal\Webhooks\Models\WebhookEvent;
use Drewdan\Paypal\Webhooks\Enums\WebhookEventEnum;

class WebhookEventTest extends TestCase {

	public function testCanSimulateEvent() {
		Http::fake([
			'https://api-m.paypal.com/v1/notifications/simulate-event' => Http::response($this->getApiResponse('webhooks/checkout_order_approved')),
		]);

		$webhookEvent = WebhookEvent::simulate(
			'https://example.com',
			WebhookEventEnum::CHECKOUT_ORDER_APPROVED
		);

		$this->assertInstanceOf(WebhookEvent::class, $webhookEvent);
	}

	public function testCanResendEvent() {
		Http::fake([
			'https://api-m.paypal.com/v1/notifications/webhooks-events/8PT597110X687430LKGECATA/resend' => Http::response($this->getApiResponse('webhooks/checkout_order_approved')),
		]);

		$webhookEvent = WebhookEvent::resend('8PT597110X687430LKGECATA');

		$this->assertInstanceOf(WebhookEvent::class, $webhookEvent);
	}

	public function testCanRetrieveEvent() {
		Http::fake([
			'https://api-m.paypal.com/v1/notifications/webhooks-events/8PT597110X687430LKGECATA' => Http::response($this->getApiResponse('webhooks/checkout_order_approved')),
		]);

		$webhookEvent = WebhookEvent::retrieve('8PT597110X687430LKGECATA');

		$this->assertInstanceOf(WebhookEvent::class, $webhookEvent);

		$this->assertEquals('WH-COC11055RA711503B-4YM959094A144403T', $webhookEvent->id);
	}

	public function testCanQueryEventList() {
		Http::fake([
			'https://api-m.paypal.com/v1/notifications/webhooks-events?page_size=10&start_time=2021-01-01T00%3A00%3A00Z&end_time=2021-01-31T23%3A59%3A59Z&transaction_id=8PT597110X687430LKGECATA&event_type=PAYMENT.AUTHORIZATION.CREATED' => Http::response($this->getApiResponse('list_event_notifications')),
		]);

		$events = WebhookEvent::query()
			->pageSize(10)
			->startTime(Date::parse('2021-01-01T00:00:00Z'))
			->endTime(Date::parse('2021-01-31T23:59:59Z'))
			->transactionId('8PT597110X687430LKGECATA')
			->eventType(WebhookEventEnum::PAYMENT_AUTHORIZATION_CREATED)
			->get();

		$this->assertCount(2, $events->getEvents());

		$this->assertTrue($events->hasNextPage());
		$this->assertTrue($events->hasPreviousPage());

	}
}
