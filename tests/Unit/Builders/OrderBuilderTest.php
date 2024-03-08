<?php

namespace Drewdan\Paypal\Tests\Unit\Builders;

use Drewdan\Paypal\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Drewdan\Paypal\Models\PurchaseUnit;
use Drewdan\Paypal\Enums\UserActionEnum;
use Drewdan\Paypal\Builders\OrderBuilder;
use Drewdan\Paypal\Enums\LandingPageEnum;
use Drewdan\Paypal\Enums\PaymentIntentEnum;
use Drewdan\Paypal\Models\ExperienceContext;
use Drewdan\Paypal\Enums\ShippingPreferenceEnum;
use Drewdan\Paypal\Builders\PaymentSource\Token;
use Drewdan\Paypal\Builders\PaymentSource\Paypal;
use Drewdan\Paypal\Enums\PaymentMethodPreferenceEnum;

class OrderBuilderTest extends TestCase {

	public function testCanGeneratePaypalOrder() {
		Http::fake([
			'*' => Http::response($this->getApiResponse('order_created')),
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


		$paypalOrder = OrderBuilder::make()
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

		$this->assertNotNull($paypalOrder);

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
	}

	public function testCanGeneratePaypalOrderWithDefaultExperienceContext() {
		Http::fake([
			'*' => Http::response($this->getApiResponse('order_created')),
		]);

		Config::set('paypal.experience_context', [
			'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
			'brand_name' => 'Test Brand',
			'locale' => 'en-GB',
			'landing_page' => 'LOGIN',
			'shipping_preference' => 'SET_PROVIDED_ADDRESS',
			'user_action' => 'PAY_NOW',
			'return_url' => 'https://example.com/return',
			'cancel_url' => 'https://example.com/cancel',
		]);


		$paypalOrder = OrderBuilder::make()
			->setIntent(PaymentIntentEnum::CAPTURE)
			->setPaymentSource(
				Paypal::make()
					->withDefaultExperienceContext()
					->setEmailAddress('johnjones@example.co.uk')
			)
			->addPurchaseUnit(
				PurchaseUnit::make()
					->setReferenceId('testReferenceId')
					->setAmount(100.00, 'USD')
			)
			->create();

		$this->assertNotNull($paypalOrder);

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
	}

	public function testCanGenerateTokenOrder() {
		Http::fake([
			'*' => Http::response($this->getApiResponse('order_created')),
		]);

		$paypalOrder = OrderBuilder::make()
			->setIntent(PaymentIntentEnum::CAPTURE)
			->setPaymentSource(
				Token::make()
					->setId('testTokenId')
					->setType('testTokenType')
			)
			->addPurchaseUnit(
				PurchaseUnit::make()
					->setReferenceId('testReferenceId')
					->setAmount(100.00, 'USD')
			)
			->create();

		$this->assertNotNull($paypalOrder);

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
					'token' => [
						'id' => 'testTokenId',
						'type' => 'testTokenType',
					],
				],
			];

			$this->assertEquals($expectedData, $data);

			return $request->url() === 'https://api-m.paypal.com/v2/checkout/orders';
		});
	}
}
