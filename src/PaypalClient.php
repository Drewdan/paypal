<?php

namespace Drewdan\Paypal;

use JsonMapper;
use Illuminate\Support\Facades\Http;
use Drewdan\Paypal\Dtos\PaypalOrder;

class PaypalClient {

	const SANDBOX_URL = 'https://api-m.sandbox.paypal.com';

	const LIVE_URL = 'https://api-m.paypal.com';

	/** @var \Illuminate\Http\Client\PendingRequest */
	private $client;

	private $mapper;

	public function __construct() {
		$this->client = Http::withBasicAuth(config('paypal.client_id'), config('paypal.secret'))
			->asJson()
			->baseUrl(config('paypal.environment') === 'LIVE' ? self::LIVE_URL : self::SANDBOX_URL);
		$this->mapper = new JsonMapper();
	}

	public function createOrder(
		array $purchaseUnits,
		string $intent = 'CAPTURE',
		array $applicationContext = []
	): PaypalOrder {
		$response = $this->client->post('/v2/checkout/orders', array_filter([
			'intent' => $intent,
			'purchase_units' => $purchaseUnits,
			'application_context' => $applicationContext,
		]));

		return $this->mapper->map(json_decode($response->body()), new PaypalOrder());
	}

	public function showOrder(string $orderId): PaypalOrder {
		$response = $this->client->get('/v2/checkout/orders/' . $orderId);

		return $this->mapper->map(json_decode($response->body()), new PaypalOrder());
	}

	public function captureOrder(PaypalOrder $paypalOrder, string $paymentMethod = ''): PaypalOrder {
		$response = $this->client->post('/v2/checkout/orders/' . $paypalOrder->id . '/capture', [
			'payment_method' => $paymentMethod
		]);

		return $this->mapper->map(json_decode($response->body()), new PaypalOrder());
	}


}
