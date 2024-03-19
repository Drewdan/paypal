<?php

namespace Drewdan\Paypal\Common\Contracts;

interface FromResponse {

	public static function fromResponse(array $response): static;

}
