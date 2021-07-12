<?php

namespace Drewdan\Paypal\Tests\Unit\Services\Payments;

use Drewdan\Paypal\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Drewdan\Paypal\Services\Payments\Captures;

class CapturesTest extends TestCase {

	/** @test */
	public function testTheCaptureIsReturned() {
		Http::fake([
			'*' => Http::response($this->getApiResponse('capture')),
		]);

		$client = new Captures;

		$capture = $client->show('foo');

		$this->assertEquals('COMPLETED', $capture->status);
	}

	/** @test */
	public function testThePaymentIsRefunded() {
		$data = $this->getApiResponse('capture_refund');

		Http::fake([
			'*' => Http::response($data),
		]);

		$client = new Captures;

		$capture = $client->refund('foo', 5.99, 'GBP', 'some reason', '123');

		$this->assertEquals('COMPLETED', $capture->status);

	}

}
