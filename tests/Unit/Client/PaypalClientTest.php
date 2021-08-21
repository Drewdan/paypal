<?php

namespace Drewdan\Paypal\Tests\Unit\Client;

use Drewdan\Paypal\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Drewdan\Paypal\Client\PaypalClient;
use Illuminate\Http\Client\PendingRequest;
use Drewdan\Paypal\Exceptions\MissingCredentialsException;

class PaypalClientTest extends TestCase {

	/** @test */
	public function testExceptionThrownWhenNoCredentialsSet() {
		Config::set('paypal.client_id', '');
		Config::set('paypal.secret', '');

		$this->expectException(MissingCredentialsException::class);

		(new PaypalClient);
	}

	/** @test */
	public function testClientInstantiatedCorrectlyWhenCredentialsSet() {
		$client = new PaypalClient;

		$this->assertInstanceOf(PendingRequest::class, $client->client);
	}

	/** @test */
	public function testClientCanGenerateCorrectBaseUrlForSandbox() {
		Config::set('paypal.environment', 'SANDBOX');
		$expectedUrl = 'https://api-m.sandbox.paypal.com/v2/';
		$client = new PaypalClient;

		$this->assertEquals($expectedUrl, $client->generateBaseUrl());
	}

	/** @test */
	public function testClientCanGenerateCorrectBaseUrlForLive() {
		Config::set('paypal.environment', 'LIVE');
		$expectedUrl = 'https://api-m.paypal.com/v2/';
		$client = new PaypalClient;

		$this->assertEquals($expectedUrl, $client->generateBaseUrl());
	}
}
