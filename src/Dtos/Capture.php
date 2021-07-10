<?php

namespace Drewdan\Paypal\Dtos;

class Capture extends BaseDto {

	public string $id;

	public array $amount;

	public string $status;

	public string $note_to_payer;

	public array $seller_payable_breakdown;

	public string $invoice_id;

	public string $created_time;

	public string $update_time;

	public array $links;

	public function setAmount($amount) {
		$this->amount = (array) $amount;
	}

	public function sellerPayableBreakdown($breakdown) {
		$this->seller_payable_breakdown = (array) $breakdown;
	}
}
