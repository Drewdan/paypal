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
			'https://api-m.paypal.com/v1/notifications/simulate-event' => Http::response($this->getApiResponse('simulate_webhook')),
		]);

		$webhookEvent = WebhookEvent::simulate(
			'https://example.com',
			WebhookEventEnum::PAYMENT_AUTHORIZATION_CREATED
		);

		$this->assertInstanceOf(WebhookEvent::class, $webhookEvent);

		$this->assertEquals('8PT597110X687430LKGECATA', $webhookEvent->id);
		$this->assertEquals(Date::parse('2013-06-25T21:41:28Z'), $webhookEvent->create_time);
		$this->assertEquals('authorization', $webhookEvent->resource_type);
		$this->assertEquals('1.0', $webhookEvent->event_version);
		$this->assertEquals(WebhookEventEnum::PAYMENT_AUTHORIZATION_CREATED, $webhookEvent->event_type);
		$this->assertEquals('A payment authorization was created', $webhookEvent->summary);
		$this->assertEquals('1.0', $webhookEvent->resource_version);

		$resource = $webhookEvent->resource;

		$this->assertEquals('2DC87612EK520411B', $resource->id);
		$this->assertEquals(Date::parse('2013-06-25T21:39:15Z'), $resource->create_time);
		$this->assertEquals(Date::parse('2013-06-25T21:39:17Z'), $resource->update_time);
		$this->assertEquals('authorized', $resource->state);
		$this->assertEquals('PAY-36246664YD343335CKHFA4AY', $resource->parent_payment);
		$this->assertEquals(Date::parse('2013-07-24T21:39:15Z'), $resource->valid_until);

		$amount = $resource->amount;

		$this->assertEquals('USD', $amount->currency);
		$this->assertEquals('7.47', $amount->total);

		$details = $amount->details;

		$this->assertEquals('7.47', $details->subtotal);
	}

	public function testCanResendEvent() {
		Http::fake([
			'https://api-m.paypal.com/v1/notifications/webhooks-events/8PT597110X687430LKGECATA/resend' => Http::response($this->getApiResponse('simulate_webhook')),
		]);

		$webhookEvent = WebhookEvent::resend('8PT597110X687430LKGECATA');

		$this->assertInstanceOf(WebhookEvent::class, $webhookEvent);

		$this->assertEquals('8PT597110X687430LKGECATA', $webhookEvent->id);
		$this->assertEquals(Date::parse('2013-06-25T21:41:28Z'), $webhookEvent->create_time);
		$this->assertEquals('authorization', $webhookEvent->resource_type);
		$this->assertEquals('1.0', $webhookEvent->event_version);
		$this->assertEquals(WebhookEventEnum::PAYMENT_AUTHORIZATION_CREATED, $webhookEvent->event_type);
		$this->assertEquals('A payment authorization was created', $webhookEvent->summary);
		$this->assertEquals('1.0', $webhookEvent->resource_version);

		$resource = $webhookEvent->resource;

		$this->assertEquals('2DC87612EK520411B', $resource->id);
		$this->assertEquals(Date::parse('2013-06-25T21:39:15Z'), $resource->create_time);
		$this->assertEquals(Date::parse('2013-06-25T21:39:17Z'), $resource->update_time);
		$this->assertEquals('authorized', $resource->state);
		$this->assertEquals('PAY-36246664YD343335CKHFA4AY', $resource->parent_payment);
		$this->assertEquals(Date::parse('2013-07-24T21:39:15Z'), $resource->valid_until);

		$amount = $resource->amount;

		$this->assertEquals('USD', $amount->currency);
		$this->assertEquals('7.47', $amount->total);

		$details = $amount->details;

		$this->assertEquals('7.47', $details->subtotal);
	}

	public function testCanRetrieveEvent() {
		Http::fake([
			'https://api-m.paypal.com/v1/notifications/webhooks-events/8PT597110X687430LKGECATA' => Http::response($this->getApiResponse('simulate_webhook')),
		]);

		$webhookEvent = WebhookEvent::retrieve('8PT597110X687430LKGECATA');

		$this->assertInstanceOf(WebhookEvent::class, $webhookEvent);

		$this->assertEquals('8PT597110X687430LKGECATA', $webhookEvent->id);
		$this->assertEquals(Date::parse('2013-06-25T21:41:28Z'), $webhookEvent->create_time);
		$this->assertEquals('authorization', $webhookEvent->resource_type);
		$this->assertEquals('1.0', $webhookEvent->event_version);
		$this->assertEquals(WebhookEventEnum::PAYMENT_AUTHORIZATION_CREATED, $webhookEvent->event_type);
		$this->assertEquals('A payment authorization was created', $webhookEvent->summary);
		$this->assertEquals('1.0', $webhookEvent->resource_version);

		$resource = $webhookEvent->resource;

		$this->assertEquals('2DC87612EK520411B', $resource->id);
		$this->assertEquals(Date::parse('2013-06-25T21:39:15Z'), $resource->create_time);
		$this->assertEquals(Date::parse('2013-06-25T21:39:17Z'), $resource->update_time);
		$this->assertEquals('authorized', $resource->state);
		$this->assertEquals('PAY-36246664YD343335CKHFA4AY', $resource->parent_payment);
		$this->assertEquals(Date::parse('2013-07-24T21:39:15Z'), $resource->valid_until);

		$amount = $resource->amount;

		$this->assertEquals('USD', $amount->currency);
		$this->assertEquals('7.47', $amount->total);

		$details = $amount->details;

		$this->assertEquals('7.47', $details->subtotal);
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
