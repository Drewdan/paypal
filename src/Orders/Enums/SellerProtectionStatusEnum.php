<?php

namespace Drewdan\Paypal\Orders\Enums;

enum SellerProtectionStatusEnum: string {

	case ELIGIBLE = 'ELIGIBLE';
	case PARTIALLY_ELIGIBLE = 'PARTIALLY_ELIGIBLE';
	case NOT_ELIGIBLE = 'NOT_ELIGIBLE';

}
