<?php

namespace Drewdan\Paypal\Orders\Enums;

enum PurchaseUnitItemCategoryEnum: string {

	case DIGITAL_GOODS = 'DIGITAL_GOODS';
	case PHYSICAL_GOODS = 'PHYSICAL_GOODS';
	case DONATION = 'DONATION';

}
