<?php

namespace Drewdan\Paypal\Dtos;

class BaseDto {

	/**
	 * Converts the object to an array
	 *
	 * @return array|null
	 */
	public function toArray(): ?array {
		return array_filter((array) $this);
	}
}
