<?php

namespace Drewdan\Paypal\Client;

use JsonMapper;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

class PaypalClient {

	const VERSION = '/V2/';

	const SANDBOX_URL = 'https://api-m.sandbox.paypal.com';

	const LIVE_URL = 'https://api-m.paypal.com';

	/** @var \Illuminate\Http\Client\PendingRequest */
	public PendingRequest $client;

	/** @var \JsonMapper  */
	public JsonMapper $mapper;

	public function __construct() {
		$this->client = Http::withBasicAuth(config('paypal.client_id'), config('paypal.secret'))
			->asJson()
			->baseUrl(
				config('paypal.environment') === 'LIVE' ? self::LIVE_URL : self::SANDBOX_URL . self::VERSION
			);
		$this->mapper = new JsonMapper();
	}
}
