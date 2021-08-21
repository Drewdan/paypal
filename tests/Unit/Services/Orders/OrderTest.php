<?php

namespace Drewdan\Paypal\Tests\Unit\Services\Orders;

use JsonMapper;
use Drewdan\Paypal\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Drewdan\Paypal\Services\Orders\Order;
use Illuminate\Foundation\Testing\WithFaker;
use Drewdan\Paypal\Dtos\Order as PaypalOrder;

class OrderTest extends TestCase {

	use WithFaker;

	/** @test */
	public function testCreatingCharge() {
		Http::fake([
			'*' => Http::response($this->getApiResponse('order_created')),
		]);

		$purchaseUnits = [
			[
				'amount' => [
					'currency_code' => 'GBP',
					'value' => 12.50,
				],
			],
		];

		$applicationContext = [
			'brand_name' => 'My Online Shop',
			'shipping_preference' => 'NO_SHIPPING',
			'user_action' => 'PAY_NOW',
			'return_url' => 'https://localhost/return',
			'cancel_url' => 'https://localhost/cancel',
		];

		$client = new Order;

		$order = $client->create($purchaseUnits, 'CAPTURE', $applicationContext);

		$this->assertEquals($order->id, $this->getApiResponse('order_created', false)->id);
	}

	/** @test */
	public function testRetrievingOrder() {
		$id = $this->faker->word;

		Http::fake([
			'*' => Http::response($this->getApiResponse('show_order')),
		]);

		$client = new Order;

		$order = $client->show($id);

		$this->assertEquals($this->getApiResponse('show_order')['id'], $order->id);
		$this->assertEquals('PAYER_ACTION_REQUIRED', $order->status);
	}

	public function testCapturingThePayment() {
		$data = $this->getApiResponse('captured_order');

		Http::fake([
			'*' => Http::response($data),
		]);

		$client = new Order;

		$mapper = new JsonMapper;

		$paypalOrder = $mapper->map((object) $data, new PaypalOrder);

		$completedOrder = $client->capture($paypalOrder);

		$this->assertEquals('COMPLETED', $completedOrder->status);

	}
}
