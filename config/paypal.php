<?php

use Drewdan\Paypal\Webhooks\Enums\WebhookEventEnum;
use Drewdan\Paypal\Webhooks\Handlers\DefaultWebhookHandler;

return [
	'client_id' => env('PAYPAL_CLIENT_ID', ''),
	'secret' => env('PAYPAL_SECRET', ''),
	'environment' => env('PAYPAL_MODE', 'sandbox'),

	'experience_context' => [
		'payment_method_preference' => env('PAYPAL_EC_PAYMENT_METHOD_PREFERENCE', 'IMMEDIATE_PAYMENT_REQUIRED'),
		'brand_name' => env('PAYPAL_EC_BRAND_NAME', config('app.name')),
		'locale' => env('PAYPAL_EC_LOCALE', 'en-US'),
		'landing_page' => env('PAYPAL_EC_LANDING_PAGE'),
		'shipping_preference' => env('PAYPAL_EC_SHIPPING_PREFERENCE'),
		'user_action' => env('PAYPAL_EC_USER_ACTION', 'PAY_NOW'),
		'return_url' => env('PAYPAL_EC_RETURN_URL'),
		'cancel_url' => env('PAYPAL_EC_CANCEL_URL'),
	],

	'webhook' => [
		'handlers' => [
			WebhookEventEnum::PAYMENT_AUTHORIZATION_CREATED->value => DefaultWebhookHandler::class,
		],
	],
];
