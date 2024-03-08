<?php

namespace Drewdan\Paypal\Enums;

enum LandingPageEnum: string {

	case LOGIN = 'LOGIN';
	case GUEST_CHECKOUT = 'GUEST_CHECKOUT';

	case NO_PREFERENCE = 'NO_PREFERENCE';

}
