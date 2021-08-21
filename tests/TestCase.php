<?php

namespace Drewdan\Paypal\Tests;

use Illuminate\Support\Facades\Config;
use Drewdan\Paypal\PaypalClientServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase {

	public function setUp(): void {
		parent::setUp();
	}

	protected function getPackageProviders($app) {
		return [
			PaypalClientServiceProvider::class,
		];
	}

	protected function getEnvironmentSetUp($app) {
		Config::set('paypal.client_id', 'client_id');
		Config::set('paypal.secret', 'secret');
		Config::set('paypal.environment', 'LIVE');
	}

	public function getApiResponse(string $name, bool $associative = true) {
		return json_decode(file_get_contents(__DIR__ .'/stubs/' . $name . '.json'), $associative);
	}

}
