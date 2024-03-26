<?php

namespace Drewdan\Paypal\Webhooks\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Drewdan\Paypal\Webhooks\Models\WebhookEvent;

class PaypalWebhookController extends Controller {

	public function __invoke(Request $request): Response {
		$webhookEvent = WebhookEvent::fromResponse($request->toArray());

		$handlers = config('paypal.webhook.handlers');

		if (Arr::has($handlers, $webhookEvent->getEventType()->value)) {
			$handler = $handlers[$webhookEvent->getEventType()->value];
			if ($handler instanceof \Closure) {
				$handler($webhookEvent);
			} else {
				App::make($handler)->handle($webhookEvent);
			}
		}

		return response()->noContent();
	}

}
