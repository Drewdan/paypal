<?php

namespace Drewdan\Paypal\Tests\Unit\Orders\Models\Orders;

use Drewdan\Paypal\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Drewdan\Paypal\Orders\Models\Order;
use Drewdan\Paypal\Orders\Models\PurchaseUnit;
use Drewdan\Paypal\Orders\Enums\UserActionEnum;
use Drewdan\Paypal\Orders\Enums\LandingPageEnum;
use Drewdan\Paypal\Orders\Enums\PaymentIntentEnum;
use Drewdan\Paypal\Orders\Models\ExperienceContext;
use Drewdan\Paypal\Orders\Enums\ShippingPreferenceEnum;
use Drewdan\Paypal\Orders\Builders\PaymentSource\Paypal;
use Drewdan\Paypal\Orders\Enums\PaymentMethodPreferenceEnum;

class OrderTest extends TestCase {

	public function testCreatingAnOrder() {
		Http::fake([
			'*' => Http::response($this->getApiResponse('order_created_new')),
		]);

		$experienceContext = ExperienceContext::make()
			->setPaymentMethodPreference(PaymentMethodPreferenceEnum::IMMEDIATE_PAYMENT_REQUIRED)
			->setBrandName('Test Brand')
			->setLocale('en-GB')
			->setLandingPage(LandingPageEnum::LOGIN)
			->setShippingPreference(ShippingPreferenceEnum::SET_PROVIDED_ADDRESS)
			->setUserAction(UserActionEnum::PAY_NOW)
			->setReturnUrl('https://example.com/return')
			->setCancelUrl('https://example.com/cancel');


		$order = Order::builder()
			->setIntent(PaymentIntentEnum::CAPTURE)
			->setPaymentSource(
				Paypal::make()
					->setExperienceContext($experienceContext)
					->setEmailAddress('johnjones@example.co.uk')
			)
			->addPurchaseUnit(
				PurchaseUnit::make()
					->setReferenceId('testReferenceId')
					->setAmount(100.00, 'USD')
			)
			->create();

		$this->assertInstanceOf(Order::class, $order);

		Http::assertSent(function (Request $request) {
			$data = $request->data();

			$expectedData = [
				'intent' => 'CAPTURE',
				'purchase_units' => [
					[
						'reference_id' => 'testReferenceId',
						'amount' => [
							'currency_code' => 'USD',
							'value' => '100.00',
						],
					],
				],
				'payment_source' => [
					'paypal' => [
						'experience_context' => [
							'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
							'brand_name' => 'Test Brand',
							'locale' => 'en-GB',
							'landing_page' => 'LOGIN',
							'shipping_preference' => 'SET_PROVIDED_ADDRESS',
							'user_action' => 'PAY_NOW',
							'return_url' => 'https://example.com/return',
							'cancel_url' => 'https://example.com/cancel',
						],
						'email_address' => 'johnjones@example.co.uk',
					],
				],
			];

			$this->assertEquals($expectedData, $data);

			return $request->url() === 'https://api-m.paypal.com/v2/checkout/orders';
		});

		$this->assertEquals(
			'https://www.paypal.com/checkoutnow?token=5O190127TN364715T',
			$order->getPaymentRedirectUrl()
		);
	}

	public function testRetrievingAnOrder() {
		Http::fake([
			'*' => Http::response($this->getApiResponse('order_created')),
		]);

		$order = Order::retrieve('testOrderId');

		$this->assertInstanceOf(Order::class, $order);
	}

	public function testUpdatingAnOrder() {
		$this->markTestSkipped('Not implementing this yet');
	}

	public function testConfirmingAnOrder() {
		$this->markTestSkipped('Not implementing this yet');

	}

	public function testAuthorizingAnOrder() {
		Http::fake([
			'https://api-m.paypal.com/v2/checkout/orders/5O190127TN364715T' => Http::response($this->getApiResponse('order_created')),
			'https://api-m.paypal.com/v2/checkout/orders/5O190127TN364715T/authorize' => Http::response($this->getApiResponse('authorized')),
		]);

		$order = Order::retrieve('5O190127TN364715T');
		$authorizedOrder = $order->authorize();

		$this->assertInstanceOf(Order::class, $authorizedOrder);

	}

	public function testCapturingAnOrder() {
		Http::fake([
			'https://api-m.paypal.com/v2/checkout/orders/5O190127TN364715T' => Http::response($this->getApiResponse('order_created')),
			'https://api-m.paypal.com/v2/checkout/orders/5O190127TN364715T/capture' => Http::response($this->getApiResponse('capture')),
		]);

		$order = Order::retrieve('5O190127TN364715T');
		$capturedOrder = $order->capture();

		$this->assertInstanceOf(Order::class, $capturedOrder);

	}

	public function testAddingTrackingInformationToAnOrder() {
		Http::fake([
			'https://api-m.paypal.com/v2/checkout/orders/5O190127TN364715T' => Http::response($this->getApiResponse('order_created')),
			'https://api-m.paypal.com/v2/checkout/orders/5O190127TN364715T/capture' => Http::response($this->getApiResponse('capture')),
			'https://api-m.paypal.com/v2/checkout/orders/5O190127TN364715T/track' => Http::response($this->getApiResponse('add_tracking_information_for_an_order')),
		]);

		$order = Order::retrieve('5O190127TN364715T');
		$capturedOrder = $order->capture();

		$captures = $capturedOrder->listCaptures(); // Collection

		$firstCapture = $captures->first();

		$trackedOrder = $firstCapture->addTrackingInformation(
			'UPS',
			'123456789',
		);

		$this->assertInstanceOf(Order::class, $trackedOrder);

	}

	public function testUpdatingOrderCancellingTrackingInformationForAnOrder() {
		$this->markTestSkipped('Not implementing this yet');
	}
}
