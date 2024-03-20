<?php

namespace Drewdan\Paypal;

use Illuminate\Support\ServiceProvider;

class PaypalClientServiceProvider extends ServiceProvider {

	public function boot(): void {
		$this->publishes([
			__DIR__ . '/../config/paypal.php' => config_path('paypal.php'),
		], 'drewdan-paypal-config');
	}

	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register(): void {
	}
}
