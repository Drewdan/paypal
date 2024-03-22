<?php

namespace Drewdan\Paypal\Webhooks\Commands;

use Illuminate\Console\Command;
use Drewdan\Paypal\Webhooks\Models\Webhook;
use Drewdan\Paypal\Webhooks\Enums\WebhookEventEnum;

class PaypalWebhookSetupCommand extends Command {

	protected $signature = 'paypal:webhook-setup';

	protected $description = 'This command will setup the paypal webhooks.';

	public function handle(): void {
		$config = config('paypal.webhook.handlers');

		if (empty($config)) {
			$this->error('No webhooks configured in the config file.');
			return;
		}

		$configKeys = array_keys($config);

		// convert strings to enums

		$enums = collect($configKeys)->map(fn (string $key) => WebhookEventEnum::from($key));


		// Now we need to check if any webhooks are already setup
		$webhooks = Webhook::all();

		if ($webhooks->isNotEmpty()) {
			$confirmed = $this->confirm('Webhooks already setup, do you want to reset them? This will delete any webhook you have in Paypal');
		} else {
			$confirmed = true;
		}

		if ($webhooks->isNotEmpty() && $confirmed) {
			$webhooks->each(fn (Webhook $webhook) => $webhook->delete());
		}


		if (!$confirmed) {
			return;
		}

		Webhook::builder()
			->setUrl(route('paypal.webhook'))
			->setEvents($enums)
			->create();

		$this->info('Webhooks created successfully.');
	}
}
