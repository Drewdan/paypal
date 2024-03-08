<?php

namespace Drewdan\Paypal\Enums;

enum PaymentIntentEnum: string {

	case CAPTURE = 'CAPTURE';

	case AUTHORIZE = 'AUTHORIZE';

}
