<?php

namespace Drewdan\Paypal\Orders\Enums;

enum PaymentIntentEnum: string {

	case CAPTURE = 'CAPTURE';

	case AUTHORIZE = 'AUTHORIZE';

}
