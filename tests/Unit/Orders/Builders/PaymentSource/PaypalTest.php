<?php

namespace Drewdan\Paypal\Tests\Unit\Orders\Builders\PaymentSource;


use Drewdan\Paypal\Tests\TestCase;
use Drewdan\Paypal\Orders\Enums\UserActionEnum;
use Drewdan\Paypal\Orders\Enums\LandingPageEnum;
use Drewdan\Paypal\Orders\Models\ExperienceContext;
use Drewdan\Paypal\Orders\Enums\ShippingPreferenceEnum;
use Drewdan\Paypal\Orders\Builders\PaymentSource\Paypal;
use Drewdan\Paypal\Orders\Enums\PaymentMethodPreferenceEnum;

class PaypalTest extends TestCase {

	public function testCanSetExperienceContext() {
		$paypal = new Paypal();
		$experienceContext = ExperienceContext::make()
			->setPaymentMethodPreference(PaymentMethodPreferenceEnum::IMMEDIATE_PAYMENT_REQUIRED)
			->setBrandName('Test Brand')
			->setLocale('en-GB')
			->setLandingPage(LandingPageEnum::LOGIN)
			->setShippingPreference(ShippingPreferenceEnum::SET_PROVIDED_ADDRESS)
			->setUserAction(UserActionEnum::PAY_NOW)
			->setReturnUrl('https://example.com/return')
			->setCancelUrl('https://example.com/cancel');

		$paypal->setExperienceContext($experienceContext);

		$this->assertEquals($experienceContext, $paypal->experienceContext);
	}

	public function testCanSetVaultId() {
		$paypal = new Paypal();
		$paypal->setVaultId('vaultId');

		$this->assertEquals('vaultId', $paypal->vaultId);
	}

	public function testSettingAnInvalidBirthDateThrowsAnException() {
		$this->expectException(\InvalidArgumentException::class);
		$paypal = new Paypal();
		$paypal->setBirthDate('invalid');
	}

	public function testCanSetValidBirthDate() {
		$paypal = new Paypal();
		$paypal->setBirthDate('1990-01-01');

		$this->assertEquals('1990-01-01', $paypal->birthDate);
	}
}
