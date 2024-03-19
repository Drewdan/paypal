<?php

namespace Drewdan\Paypal\Orders\Models;

use Illuminate\Support\Arr;
use Drewdan\Paypal\Client\PaypalClient;
use Drewdan\Paypal\Common\Contracts\FromArray;

class Capture implements FromArray {

	public function __construct(
		public ?string $id = null,
		public ?string $status = null,
		public ?Amount $amount = null,
		public ?string $create_time = null,
		public ?string $update_time = null,
		public ?bool $final_capture = null,
		public ?SellerProtection $seller_protection = null,
		public ?array $links = null,
		public ?string $orderId = null,
	) {
	}

	public static function fromArray(array $data): static {
		return new static(
			id: $data['id'] ?? null,
			status: $data['status'] ?? null,
			amount: Arr::has($data, 'amount')
				? Amount::fromArray($data['amount'])
				: null,
			create_time: $data['create_time'] ?? null,
			update_time: $data['update_time'] ?? null,
			final_capture: $data['final_capture'] ?? null,
			seller_protection: Arr::has($data, 'seller_protection')
				? SellerProtection::fromArray($data['seller_protection'])
				: null,
			links: $data['links'] ?? null,
		);
	}

	public function addTrackingInformation(string $carrier, string $trackingNumber, bool $notifyPayer = false): Order {
		// TODO: Implement Items
		$response = PaypalClient::make(true)->post('checkout/orders/' . $this->orderId . '/track', [
			'capture_id' => $this->id,
			'carrier' => $carrier,
			'tracking_number' => $trackingNumber,
			'notify_payer' => $notifyPayer,
		]);

		return Order::fromResponse($response);
	}
}
