<?php

namespace Drewdan\Paypal\Orders\Enums;

enum OrderStatusEnum: string {

	case CREATED = 'CREATED';
	case SAVED = 'SAVED';
	case APPROVED = 'APPROVED';
	case VOIDED = 'VOIDED';
	case COMPLETED = 'COMPLETED';
	case PAYER_ACTION_REQUIRED = 'PAYER_ACTION_REQUIRED';

}
