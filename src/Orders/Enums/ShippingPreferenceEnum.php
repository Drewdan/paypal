<?php

namespace Drewdan\Paypal\Orders\Enums;

enum ShippingPreferenceEnum: string {

	case GET_FROM_FILE = 'GET_FROM_FILE';
	case NO_SHIPPING = 'NO_SHIPPING';
	case SET_PROVIDED_ADDRESS = 'SET_PROVIDED_ADDRESS';

}
