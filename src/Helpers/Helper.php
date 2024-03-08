<?php

namespace Drewdan\Paypal\Helpers;

class Helper {

	public static function recursivelyRemoveNullValues(array $array): array {
		$filtered = collect($array)->filter();

		$filtered = $filtered->map(function ($value) {
			if (is_array($value)) {
				return self::recursivelyRemoveNullValues($value);
			}

			return $value;
		});

		return $filtered->filter()->toArray();
	}

}
