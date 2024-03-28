<?php

namespace Drewdan\Paypal\Orders\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Drewdan\Paypal\Common\Contracts\ToArray;
use Drewdan\Paypal\Common\Contracts\FromArray;
use Drewdan\Paypal\Common\Contracts\BuildsPayload;

class PurchaseUnit implements BuildsPayload, FromArray, ToArray {

	public function __construct(
		public ?string $referenceId = null,
		public ?string $description = null,
		public ?string $customId = null,
		public ?string $invoiceId = null,
		public ?string $softDescriptor = null,
		public ?array $items = null,
		public ?Payment $payments = null,
		public ?Amount $amount = null,
		public ?Shipping $shipping = null,
		public ?Payee $payee = null,
	) {
	}

	public function toArray(): array {
		return array_filter([
			'reference_id' => $this->referenceId,
			'description' => $this->description,
			'custom_id' => $this->customId,
			'invoice_id' => $this->invoiceId,
			'soft_descriptor' => $this->softDescriptor,
			'items' => $this->items,
			'payments' => $this->payments?->toArray(),
			'amount' => $this->amount?->toArray(),
			'shipping' => $this->shipping?->toArray(),
			'payee' => $this->payee?->toArray(),
		]);
	}

	public static function fromArray(array $data): static {
		return new PurchaseUnit(
			referenceId: $data['reference_id'] ?? null,
			description: $data['description'] ?? null,
			customId: $data['custom_id'] ?? null,
			invoiceId: $data['invoice_id'] ?? null,
			softDescriptor: $data['soft_descriptor'] ?? null,
			items: $data['items'] ?? null,
			payments: Arr::has($data, 'payments')
				? Payment::fromArray($data['payments'])
				: null,
			amount: Arr::has($data, 'amount')
				? Amount::fromArray($data['amount'])
				: null,
			shipping: Arr::has($data, 'shipping')
				? Shipping::fromArray($data['shipping'])
				: null,
			payee: Arr::has($data, 'payee')
				? Payee::fromArray($data['payee'])
				: null,
		);
	}

	public static function make(): static {
		return App::make(static::class);
	}

	public function setReferenceId(string $referenceId): self {
		$this->referenceId = $referenceId;
		return $this;
	}

	public function setDescription(string $description): self {
		$this->description = $description;
		return $this;
	}

	public function setAmount(float $value, string $currency): self {
		$this->amount = Amount::make()
			->setValue($value)
			->setCurrencyCode($currency);
		return $this;

	}


	public function buildPayload(): array {
		return array_filter([
			'reference_id' => $this->referenceId,
			'amount' => $this->amount->buildPayload(),
		]);
	}
}
