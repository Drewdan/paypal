<?php

namespace Drewdan\Paypal\Enums;

enum PaymentMethodPreferenceEnum: string {

	case UNRESTRICTED = 'UNRESTRICTED';

	case IMMEDIATE_PAYMENT_REQUIRED = 'IMMEDIATE_PAYMENT_REQUIRED';

}
