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

	public function getTotalValue(): ?float {
		return isset($this->purchaseUnits) ?
			collect($this->purchaseUnits)->map(function ($unit) {
				return $unit->amount->value;
			})
				->sum()
			: collect($this->paymentSource)->flatten(1)->map(function ($entry) {
				return ['value' => $entry->amount->value];
			})->sum('value');
	}
}
