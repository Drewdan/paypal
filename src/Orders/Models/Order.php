<?php

namespace Drewdan\Paypal\Orders\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Drewdan\Paypal\Client\PaypalClient;
use Drewdan\Paypal\Common\Models\Links;
use Drewdan\Paypal\Common\Models\Resource;
use Drewdan\Paypal\Common\Contracts\ToArray;
use Drewdan\Paypal\Orders\Builders\OrderBuilder;
use Drewdan\Paypal\Orders\Enums\OrderStatusEnum;
use Drewdan\Paypal\Common\Contracts\FromResponse;
use Drewdan\Paypal\Orders\Contracts\BuildsPaymentSource;
use Drewdan\Paypal\Orders\Builders\PaymentSource\PaymentSource;

class Order extends Resource implements FromResponse, ToArray {

	private PaypalClient $client;

	public function __construct(
		public ?string $create_time = null,
		public ?string $update_time = null,
		public ?string $id = null,
		public ?string $processing_instruction = null,
		public ?PurchaseUnits $purchase_units = null,
		public ?Links $links = null,
		public ?BuildsPaymentSource $payment_source = null,
		public ?string $intent = null,
		public ?array $payer = null,
		public ?OrderStatusEnum $status = null,
		public ?Amount $gross_amount = null,
	) {
		$this->client = PaypalClient::make(true);
	}

	public function toArray(): array {
		return array_filter([
			'create_time' => $this->create_time,
			'update_time' => $this->update_time,
			'id' => $this->id,
			'processing_instruction' => $this->processing_instruction,
			'purchase_units' => $this->purchase_units?->toArray(),
			'links' => $this->links?->toArray(),
			'payment_source' => $this->payment_source?->toArray(),
			'intent' => $this->intent,
			'payer' => $this->payer,
			'status' => $this->status?->value,
			'gross_amount' => $this->gross_amount?->toArray(),
		]);
	}

	public static function fromResponse(array $response): static {
		return new Order(
			create_time: $response['create_time'] ?? null,
			update_time: $response['update_time'] ?? null,
			id: $response['id'] ?? null,
			processing_instruction: $response['processing_instruction'] ?? null,
			purchase_units: Arr::has($response, 'purchase_units')
				? PurchaseUnits::fromArray($response['purchase_units'])
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
			gross_amount: Arr::has($response, 'gross_amount')
				? Amount::fromArray($response['gross_amount'])
				: null,
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
		$response = $this->client->post('checkout/orders/' . $this->id . '/capture', null);

		return Order::fromResponse($response);
	}

	public function authorize(): Order {
		$response = $this->client->post('checkout/orders/' . $this->id . '/authorize');

		return Order::fromResponse($response);
	}

	public function getPaymentRedirectUrl(): string {
		return $this->links->getByRef('payer-action')->href;
	}

	public function getPurchaseUnits(): Collection {
		return $this->purchase_units->purchaseUnits;
	}

	public function listCaptures(): Collection {
		return $this->getPurchaseUnits()
			->map(fn (PurchaseUnit $purchaseUnit) => $purchaseUnit->payments)
			->flatMap(fn (Payment $payment) => $payment->captures)
			->each(fn (Capture $capture) => $capture->orderId = $this->id);
	}
}
