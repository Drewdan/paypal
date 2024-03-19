<?php

namespace Drewdan\Paypal\Services\Orders;

use Drewdan\Paypal\Services\PaypalService;
use Drewdan\Paypal\Dtos\Order as PaypalOrder;

class Order extends PaypalService {

	/**
	 * Create an order, this order will generate a payment link for the user, once used, the payment will need to be captured
	 *
	 * @param array $purchaseUnits
	 * @param string $intent
	 * @param array $applicationContext
	 * @return \Drewdan\Paypal\Dtos\Order
	 * @throws \JsonMapper_Exception
	 * @deprecated Use OrderBuilder instead
	 */
	public function create(
		array $purchaseUnits,
		string $intent = 'CAPTURE',
		array $applicationContext = []
	): PaypalOrder {
		$response = $this->client->post('checkout/orders', array_filter([
			'intent' => $intent,
			'purchase_units' => $purchaseUnits,
			'application_context' => $applicationContext,
		]));

		return $this->mapper->map($response, new PaypalOrder);
	}

	/**
	 * Shows an order, including its current status and any associated payment links
	 *
	 * @param string $orderId
	 * @return \Drewdan\Paypal\Dtos\Order
	 * @throws \JsonMapper_Exception
	 */
	public function show(string $orderId): PaypalOrder {
		$response = $this->client->get('checkout/orders/' . $orderId);

		return $this->mapper->map($response, new PaypalOrder);
	}

	/**
	 * Capture an order, which finalises and takes payment from the customer
	 *
	 * @param \Drewdan\Paypal\Dtos\Order $paypalOrder
	 * @param string $paymentMethod
	 * @return \Drewdan\Paypal\Dtos\Order
	 * @throws \JsonMapper_Exception
	 */
	public function capture(PaypalOrder $paypalOrder, string $paymentMethod = ''): PaypalOrder {
		$response = $this->client->post('checkout/orders/' . $paypalOrder->id . '/capture', [
			'payment_method' => $paymentMethod
		]);

		return $this->mapper->map($response, new PaypalOrder);
	}

}
