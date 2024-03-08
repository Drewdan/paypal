<?php

namespace Drewdan\Paypal\Builders;

use Drewdan\Paypal\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Drewdan\Paypal\Client\PaypalClient;
use Drewdan\Paypal\Models\PurchaseUnit;
use Drewdan\Paypal\Enums\PaymentIntentEnum;
use Drewdan\Paypal\Contracts\BuildsPayload;
use Drewdan\Paypal\Builders\PaymentSource\PaymentSource;

class OrderBuilder implements BuildsPayload {

	private PaypalClient $client;

	public Collection $purchaseUnits;

	public PaymentIntentEnum $intent;

	public PaymentSource $paymentSource;

	public static function make(): static {
		return App::make(static::class);
	}

	public function __construct(

	) {
		$this->purchaseUnits = collect();
		$this->client = PaypalClient::make(true);
	}


	public function setPurchaseUnits(array|Collection $purchaseUnits): static {
		$this->purchaseUnits = $purchaseUnits instanceof Collection
			? $purchaseUnits
			: collect($purchaseUnits);

		return $this;
	}

	public function addPurchaseUnit(PurchaseUnit $purchaseUnit): static {
		$this->purchaseUnits->push($purchaseUnit);

		return $this;
	}

	public function setIntent(PaymentIntentEnum $intent): static {
		$this->intent = $intent;

		return $this;
	}

	public function setPaymentSource(PaymentSource $paymentSource): static {
		$this->paymentSource = $paymentSource;

		return $this;
	}

	public function buildPayload(): array {
		return [
			'purchase_units' => $this->purchaseUnits->map(fn($unit) => $unit->buildPayload())->toArray(),
			'intent' => $this->intent->value,
			'payment_source' => $this->paymentSource->buildPaymentSource(),
		];
	}

	public function create(): Order {
		$response = $this->client->post('checkout/orders', $this->buildPayload());

		return Order::fromResponse($response);
	}

}
