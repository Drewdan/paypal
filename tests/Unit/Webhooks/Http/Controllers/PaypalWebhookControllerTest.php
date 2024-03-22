<?php

namespace Drewdan\Paypal\Tests\Unit\Webhooks\Http\Controllers;


use Drewdan\Paypal\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Drewdan\Paypal\Webhooks\Models\WebhookEvent;
use Drewdan\Paypal\Webhooks\Enums\WebhookEventEnum;
use Drewdan\Paypal\Webhooks\Handlers\DefaultWebhookHandler;

class PaypalWebhookControllerTest extends TestCase {
	public function testItCanFindHandlerAndRunsIt() {
		$payload = $this->getApiResponse('simulate_webhook');

		$this->mock(DefaultWebhookHandler::class)
			->shouldReceive('handle')
			->once();

		Config::set('paypal.webhook.handlers', [
			WebhookEventEnum::PAYMENT_AUTHORIZATION_CREATED->value => DefaultWebhookHandler::class,
		]);

		$this->postJson(route('paypal.webhook'), $payload)->assertNoContent();
	}

	public function testItCanExecuteClosure() {
		$payload = $this->getApiResponse('simulate_webhook');

		$handler = function ($event) {
			$this->assertInstanceOf(WebhookEvent::class, $event);
		};

		Config::set('paypal.webhook.handlers', [
			WebhookEventEnum::PAYMENT_AUTHORIZATION_CREATED->value => $handler,
		]);

		$this->postJson(route('paypal.webhook'), $payload)->assertNoContent();
	}
}
