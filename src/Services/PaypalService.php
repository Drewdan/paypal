<?php

namespace Drewdan\Paypal\Services;

use JsonMapper;
use Drewdan\Paypal\Client\PaypalClient;

class PaypalService {

	/** @var \Drewdan\Paypal\Client\PaypalClient */
	public PaypalClient $client;

	/** @var \JsonMapper  */
	public JsonMapper $mapper;

	public function __construct() {
		$this->client = new PaypalClient;
		$this->mapper = new JsonMapper;
	}
}
