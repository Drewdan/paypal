<?php

namespace Drewdan\Paypal\Orders\Enums;

enum PaymentMethodPreferenceEnum: string {

	case UNRESTRICTED = 'UNRESTRICTED';

	case IMMEDIATE_PAYMENT_REQUIRED = 'IMMEDIATE_PAYMENT_REQUIRED';

}
