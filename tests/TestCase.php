<?php

namespace Drewdan\Paypal\Tests;

use Dotenv\Dotenv;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Spatie\LaravelRay\RayServiceProvider;
use Drewdan\Paypal\PaypalClientServiceProvider;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;

class TestCase extends \Orchestra\Testbench\TestCase {

	public function setUp(): void {
		parent::setUp();

		Http::preventStrayRequests();
	}

	protected function getPackageProviders($app) {
		$app->useEnvironmentPath(__DIR__.'/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        parent::getEnvironmentSetUp($app);

		return [
			PaypalClientServiceProvider::class,
			RayServiceProvider::class,
		];
	}

	protected function getEnvironmentSetUp($app) {
		Config::set('paypal.client_id', 'client_id');
		Config::set('paypal.secret', 'secret');
		Config::set('paypal.environment', 'LIVE');


	}

	public function getApiResponse(string $name, bool $associative = true) {
		return json_decode(file_get_contents(__DIR__ . '/stubs/' . $name . '.json'), $associative);
	}

}
