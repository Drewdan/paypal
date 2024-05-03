<?php

namespace Drewdan\Paypal\Tests\Unit\Client;

use Drewdan\Paypal\Tests\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
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

	public function testThrowsExceptionWhenNonSuccessfulStatusCodeReturned() {
		Config::set('paypal.client_id', 'foo');
		Config::set('paypal.secret', 'bar');
		Config::set('paypal.environment', 'LIVE');

		$client = App::make(PaypalClient::class);

		$this->expectException(\Exception::class);

		Http::fake(['https://api-m.paypal.com/v2/checkout/orders/5O190127TN364715T' => Http::response($this->getApiResponse('malformed_request_json'), 400)]);

		$client->get('checkout/orders/5O190127TN364715T');


	}
}
