<?php

namespace Drewdan\Paypal\Webhooks\Contracts;

use Drewdan\Paypal\Webhooks\Models\WebhookEvent;

interface HandlesPaypalWebhookEvent {

	public function handle(WebhookEvent $event): void;

}
