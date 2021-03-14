<?php

namespace Drewdan\Paypal\Dtos;

class PaypalOrder {

	public $id;

	public $status;

	public $payer;

	public $paymentSource;

	public $purchaseUnits;

	public $links;

	public function getLinkByRel(string $rel): PaypalLink {
		$link = collect($this->links)->filter(function ($link) use ($rel) {
			return $link->rel === $rel;
		})->first();

		return (new \JsonMapper)->map($link, new PaypalLink);
	}

	public function getTotalValue() {
		return collect($this->purchaseUnits)->map(function ($unit) {
			return $unit->amount->value;
		})
			->sum();
	}
}
