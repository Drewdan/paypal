<?php

namespace Drewdan\Paypal\Tests\Unit\Services;

use Drewdan\Paypal\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Drewdan\Paypal\Services\PaypalService;
use Drewdan\Paypal\Exceptions\InvalidClientException;
use Drewdan\Paypal\Exceptions\InvalidRequestException;

class PaypalServiceTest extends TestCase {

	public static function exceptionProvider(): array {
		return [
			'Invalid Request Exception' => ['invalid-request', 401, InvalidRequestException::class],
			'Invalid Client Exception' => ['invalid-client', 401, InvalidClientException::class],
		];
	}

	/**
	 * @test
	 * @dataProvider exceptionProvider
	 */
	public function testCanCatchInvalidRequestExceptionWhenThrown($responseType, $responseCode, $exceptionType) {
		Http::fake([
			'https://api-m.paypal.com/v2/checkout/orders' => Http::response(
				$this->getApiResponse($responseType),
				$responseCode
			),
		]);

		$this->expectException($exceptionType);

		(new PaypalService)->client->post('checkout/orders');
	}
}
