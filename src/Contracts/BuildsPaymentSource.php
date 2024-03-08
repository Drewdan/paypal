<?php

namespace Drewdan\Paypal\Contracts;

interface BuildsPaymentSource {

	public function buildPaymentSource(): array;

}
