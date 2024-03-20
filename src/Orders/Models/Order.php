<?php

namespace Drewdan\Paypal\Orders\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Drewdan\Paypal\Client\PaypalClient;
use Drewdan\Paypal\Common\Models\Links;
use Drewdan\Paypal\Orders\Builders\OrderBuilder;
use Drewdan\Paypal\Orders\Enums\OrderStatusEnum;
use Drewdan\Paypal\Common\Contracts\FromResponse;
use Drewdan\Paypal\Orders\Contracts\BuildsPaymentSource;
use Drewdan\Paypal\Orders\Builders\PaymentSource\PaymentSource;

class Order implements FromResponse {

	private PaypalClient $client;

	public function __construct(
		public ?string $create_time = null,
		public ?string $update_time = null,
		public ?string $id = null,
		public ?string $processing_instruction = null,
		public ?Collection $purchase_units = null,
		public ?Links $links = null,
		public ?BuildsPaymentSource $payment_source = null,
		public ?string $intent = null,
		public ?array $payer = null,
		public ?OrderStatusEnum $status = null,
	) {
		$this->client = PaypalClient::make(true);
	}

	public static function fromResponse(array $response): static {
		return new Order(
			create_time: $response['create_time'] ?? null,
			update_time: $response['update_time'] ?? null,
			id: $response['id'] ?? null,
			processing_instruction: $response['processing_instruction'] ?? null,
			purchase_units: Arr::has($response, 'purchase_units')
				? PurchaseUnit::fromArray($response['purchase_units'])
				: null,
			links: Arr::has($response, 'links')
				? Links::fromArray($response['links'])
				: null,
			payment_source: Arr::has($response, 'payment_source')
				? PaymentSource::fromArray($response['payment_source'])
				: null,
			intent: $response['intent'] ?? null,
			payer: $response['payer'] ?? null,
			status: $response['status'] ? OrderStatusEnum::from($response['status']) : null,
		);
	}

	public static function builder(): OrderBuilder {
		return OrderBuilder::make();
	}

	public static function retrieve(string $orderId): Order {
		$response = PaypalClient::make(true)->get('checkout/orders/' . $orderId);

		return Order::fromResponse($response);
	}

	public function capture(): Order {
		$response = $this->client->post('checkout/orders/' . $this->id . '/capture');

		return Order::fromResponse($response);
	}

	public function authorize(): Order {
		$response = $this->client->post('checkout/orders/' . $this->id . '/authorize');

		return Order::fromResponse($response);
	}

	public function getPaymentRedirectUrl(): string {
		return $this->links->getByRef('payer-action')->href;
	}

	public function listCaptures(): Collection {
		return $this->purchase_units
			->map(fn (PurchaseUnit $purchaseUnit) => $purchaseUnit->payments)
			->flatMap(fn (Payment $payment) => $payment->captures)
			->each(fn (Capture $capture) => $capture->orderId = $this->id);
	}


}
