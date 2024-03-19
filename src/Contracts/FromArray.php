<?php

namespace Drewdan\Paypal\Contracts;

use Illuminate\Support\Collection;

interface FromArray {

	public static function fromArray(array $data): static | Collection;

}
