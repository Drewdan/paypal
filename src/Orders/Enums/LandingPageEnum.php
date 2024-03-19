<?php

namespace Drewdan\Paypal\Orders\Enums;

enum LandingPageEnum: string {

	case LOGIN = 'LOGIN';
	case GUEST_CHECKOUT = 'GUEST_CHECKOUT';

	case NO_PREFERENCE = 'NO_PREFERENCE';

}
