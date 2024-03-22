<?php

namespace Drewdan\Paypal\Webhooks\Handlers;

use Illuminate\Support\Facades\Log;
use Drewdan\Paypal\Webhooks\Models\WebhookEvent;
use Drewdan\Paypal\Webhooks\Contracts\HandlesPaypalWebhookEvent;

class DefaultWebhookHandler implements HandlesPaypalWebhookEvent {

	public function handle(WebhookEvent $event): void {
		Log::info('Handling webhook event', ['event' => $event]);
	}
}
