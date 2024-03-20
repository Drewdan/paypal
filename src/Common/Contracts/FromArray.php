<?php

namespace Drewdan\Paypal\Common\Contracts;

use Illuminate\Support\Collection;

interface FromArray {

	public static function fromArray(array $data): static | Collection;

}
