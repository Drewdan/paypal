<?php

namespace Drewdan\Paypal\Orders\Contracts;

interface BuildsPaymentSource {

	public function buildPaymentSource(): array;

}
