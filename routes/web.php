<?php

use Illuminate\Support\Facades\Route;
use Drewdan\Paypal\Webhooks\Http\Controllers\PaypalWebhookController;

Route::post('paypal/webhook', PaypalWebhookController::class)->name('paypal.webhook');
