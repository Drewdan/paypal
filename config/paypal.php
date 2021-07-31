<?php

return [
	'client_id' => env('paypal_client_id'),
	'secret' => env('paypal_secret'),
	'environment' => env('PAYPAL_MODE', 'sandbox'),
];
