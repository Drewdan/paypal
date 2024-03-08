<?php

namespace Drewdan\Paypal\Models;

use Illuminate\Support\Facades\App;
use Drewdan\Paypal\Enums\UserActionEnum;
use Drewdan\Paypal\Enums\LandingPageEnum;
use Drewdan\Paypal\Contracts\BuildsPayload;
use Drewdan\Paypal\Enums\ShippingPreferenceEnum;
use Drewdan\Paypal\Enums\PaymentMethodPreferenceEnum;

class ExperienceContext implements BuildsPayload {

	public function __construct(
		public ?string $brandName = null,
		public ?ShippingPreferenceEnum $shippingPreference = null,
		public ?LandingPageEnum $landingPage = null,
		public ?UserActionEnum $userAction = null,
		public ?PaymentMethodPreferenceEnum $paymentMethod = null,
		public ?string $locale = null,
		public ?string $returnUrl = null,
		public ?string $cancelUrl = null,
	) {

	}

	public static function fromArray(array $data): static {
		return new ExperienceContext(
			brandName: $data['brand_name'],
			shippingPreference: ShippingPreferenceEnum::from($data['shipping_preference']),
			landingPage: LandingPageEnum::from($data['landing_page']),
			userAction: UserActionEnum::from($data['user_action']),
			paymentMethod: PaymentMethodPreferenceEnum::from($data['payment_method_preference']),
			locale: $data['locale'],
			returnUrl: $data['return_url'],
			cancelUrl: $data['cancel_url'],
		);
	}

	public function setBrandName(string $brandName): static {
		$this->brandName = $brandName;
		return $this;
	}

	public function setShippingPreference(ShippingPreferenceEnum $shippingPreference): static {
		$this->shippingPreference = $shippingPreference;
		return $this;
	}

	public function setLandingPage(LandingPageEnum $landingPage): static {
		$this->landingPage = $landingPage;
		return $this;
	}

	public function setUserAction(UserActionEnum $userAction): static {
		$this->userAction = $userAction;
		return $this;
	}

	public function setPaymentMethodPreference(PaymentMethodPreferenceEnum $paymentMethod): static {
		$this->paymentMethod = $paymentMethod;
		return $this;
	}

	public function setLocale(string $locale): static {
		// TODO: Validate the local is acceptable
		$this->locale = $locale;
		return $this;
	}

	public function setReturnUrl(string $returnUrl): static {
		// TODO: add a check to ensure it's a secure URL
		$this->returnUrl = $returnUrl;
		return $this;
	}

	public function setCancelUrl(string $cancelUrl): static {
		// TODO: add a check to ensure it's a secure URL
		$this->cancelUrl = $cancelUrl;
		return $this;
	}

	public static function make(): static {
		return App::make(static::class);
	}

	public function buildPayload(): array {
		return [
			'payment_method_preference' => $this->paymentMethod?->value,
			'brand_name' => $this->brandName,
			'locale' => $this->locale,
			'landing_page' => $this->landingPage?->value,
			'shipping_preference' => $this->shippingPreference?->value,
			'user_action' => $this->userAction?->value,
			'return_url' => $this->returnUrl,
			'cancel_url' => $this->cancelUrl,
		];
	}
}
