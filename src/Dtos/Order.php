<?php

namespace Drewdan\Paypal\Dtos;

use JsonMapper;

class Order extends BaseDto {

	public string $id;

	public string $status;

	public array $payer;

	public array $paymentSource;

	public array $purchaseUnits;

	public array $links;

	public function setPaymentSource($value): void {
		$this->paymentSource = (array) $value;
	}

	public function setPurchaseUnits($value): void {
		$this->paymentSource = (array) $value;
	}

	public function setPayer($value): void {
		$this->payer = (array) $value;
	}

	public function getLinkByRel(string $rel): Link {
		$link = collect($this->links)->filter(function ($link) use ($rel) {
			return $link->rel === $rel;
		})->first();

		return (new JsonMapper)->map($link, new Link);
	}

	public function getTotalValue() {
		return collect($this->purchaseUnits)->map(function ($unit) {
			return $unit->amount->value;
		})
			->sum();
	}
}
