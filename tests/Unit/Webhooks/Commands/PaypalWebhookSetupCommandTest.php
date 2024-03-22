<?php

namespace Drewdan\Paypal\Tests\Unit\Webhooks\Commands;

use Drewdan\Paypal\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Drewdan\Paypal\Webhooks\Enums\WebhookEventEnum;
use Drewdan\Paypal\Webhooks\Handlers\DefaultWebhookHandler;

class PaypalWebhookSetupCommandTest extends TestCase {

	public function testCanCreateWebhooksForConfiguredEvents(): void {
		Http::fake([
			'https://api-m.paypal.com/v1/notifications/webhooks' => Http::sequence()
				->push([])
				->push($this->getApiResponse('create_webhook')),
		]);

		Config::set('paypal.webhook.handlers', [
			WebhookEventEnum::PAYMENT_AUTHORIZATION_CREATED->value => DefaultWebhookHandler::class,
		]);

		$this->artisan('paypal:webhook-setup')
			->expectsOutput('Webhooks created successfully.')
			->assertExitCode(0);
	}

	public function testPromptsToDeleteExistingWebhooks(): void {
		Http::fake([
			'https://api-m.paypal.com/v1/notifications/webhooks' => Http::sequence()
				->push($this->getApiResponse('list_webhooks'))
				->push($this->getApiResponse('create_webhook')),
			'https://api-m.paypal.com/v1/notifications/webhooks/40Y916089Y8324740' => Http::response([]),
			'https://api-m.paypal.com/v1/notifications/webhooks/0EH40505U7160970P' => Http::response([]),
		]);

		Config::set('paypal.webhook.handlers', [
			WebhookEventEnum::PAYMENT_AUTHORIZATION_CREATED->value => DefaultWebhookHandler::class,
		]);

		$this->artisan('paypal:webhook-setup')
			->expectsConfirmation('Webhooks already setup, do you want to reset them? This will delete any webhook you have in Paypal', 'yes')
			->expectsOutput('Webhooks created successfully.')
			->assertExitCode(0);

		Http::assertSentCount(4);
	}
}
