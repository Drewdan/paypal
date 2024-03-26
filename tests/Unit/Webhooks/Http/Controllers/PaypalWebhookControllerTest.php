<?php

namespace Drewdan\Paypal\Tests\Unit\Webhooks\Http\Controllers;


use Drewdan\Paypal\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Drewdan\Paypal\Webhooks\Models\WebhookEvent;
use Drewdan\Paypal\Webhooks\Enums\WebhookEventEnum;
use Drewdan\Paypal\Webhooks\Handlers\DefaultWebhookHandler;

class PaypalWebhookControllerTest extends TestCase {
	public function testItCanFindHandlerAndRunsIt() {
		$payload = $this->getApiResponse('webhooks/checkout_order_approved');

		$this->mock(DefaultWebhookHandler::class)
			->shouldReceive('handle')
			->once();

		Config::set('paypal.webhook.handlers', [
			WebhookEventEnum::CHECKOUT_ORDER_APPROVED->value => DefaultWebhookHandler::class,
		]);

		$this->postJson(route('paypal.webhook'), $payload)->assertNoContent();
	}

	public function testItCanExecuteClosure() {
		$payload = $this->getApiResponse('webhooks/checkout_order_approved');

		$handler = function ($event) {
			$this->assertInstanceOf(WebhookEvent::class, $event);
		};

		Config::set('paypal.webhook.handlers', [
			WebhookEventEnum::CHECKOUT_ORDER_APPROVED->value => $handler,
		]);

		$this->postJson(route('paypal.webhook'), $payload)->assertNoContent();
	}

	public static function webhookProvider() {
		return [
			'checkout order approved' => ['checkout_order_approved', WebhookEventEnum::CHECKOUT_ORDER_APPROVED],
			'checkout order completed' => ['checkout_order_completed', WebhookEventEnum::CHECKOUT_ORDER_COMPLETED],
			'checkout order saved' => ['checkout_order_saved', WebhookEventEnum::CHECKOUT_ORDER_SAVED],
			'checkout order voided' => ['checkout_order_voided', WebhookEventEnum::CHECKOUT_ORDER_VOIDED],
		];
	}

	/**
	 * @dataProvider webhookProvider
	 *
	 */
	public function testItCanParseWebhookFormatForGivenWebhook(string $payloadRef, WebhookEventEnum $enum) {
		$payload = $this->getApiResponse('webhooks/' . $payloadRef);

		$handler = function ($event) {
			$this->assertInstanceOf(WebhookEvent::class, $event);
		};

		Config::set('paypal.webhook.handlers', [
			$enum->value => $handler,
		]);

		$this->postJson(route('paypal.webhook'), $payload)
			->assertSessionHasNoErrors()
			->assertNoContent();
	}
}
