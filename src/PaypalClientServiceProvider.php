<?php

namespace Drewdan\Paypal;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Drewdan\Paypal\Webhooks\Commands\PaypalWebhookSetupCommand;

class PaypalClientServiceProvider extends ServiceProvider {

	public function boot(): void {
		$this->publishes([
			__DIR__ . '/../config/paypal.php' => config_path('paypal.php'),
		], 'drewdan-paypal-config');

		Route::group($this->routeConfiguration(), function () {
			$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
		});

		if ($this->app->runningInConsole()) {
			$this->commands([
				PaypalWebhookSetupCommand::class,
			]);
		}

	}

	public function register(): void {
	}

	protected function routeConfiguration(): array {
		return [
			'prefix' => config('paypal.prefix'),
			'middleware' => config('paypal.middleware'),
		];
	}

}
