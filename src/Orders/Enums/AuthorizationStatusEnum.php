<?php

namespace Drewdan\Paypal\Orders\Enums;

enum AuthorizationStatusEnum: string {

	case CREATED = 'CREATED';
	case CAPTURED = 'CAPTURED';
	case DENIED = 'DENIED';
	case PARTIALLY_CAPTURED = 'PARTIALLY_CAPTURED';
	case VOIDED = 'VOIDED';
	case PENDING = 'PENDING';

}
