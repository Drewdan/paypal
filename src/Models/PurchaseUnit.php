<?php

namespace Drewdan\Paypal\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Drewdan\Paypal\Contracts\FromArray;
use Drewdan\Paypal\Contracts\BuildsPayload;

class PurchaseUnit implements BuildsPayload, FromArray {

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
	) {
	}

	public static function fromArray(array $data): Collection {
		$items = collect();

		foreach ($data as $item) {
			$items->push(
				new PurchaseUnit(
					referenceId: $item['reference_id'] ?? null,
					description: $item['description'] ?? null,
					customId: $item['custom_id'] ?? null,
					invoiceId: $item['invoice_id'] ?? null,
					softDescriptor: $item['soft_descriptor'] ?? null,
					items: $item['items'] ?? null,
					payments: Arr::has($item, 'payments')
						? Payment::fromArray($item['payments'])
						: null,
					shipping: Arr::has($item, 'shipping')
						? Shipping::fromArray($item['shipping'])
						: null,
				)
			);
		}

		return $items;
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
		return [
			'reference_id' => $this->referenceId,
			'amount' => $this->amount->buildPayload(),
		];
	}
}
