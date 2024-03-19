<?php

namespace Drewdan\Paypal\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class InvalidRequestException extends Exception {

	public function report() {
		Log::error('An invalid request was made to Paypal. Check the Paypal Credentials');
	}
}
