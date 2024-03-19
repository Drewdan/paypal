<?php

namespace Drewdan\Paypal\Orders\Builders\PaymentSource;

use Illuminate\Support\Arr;
use Drewdan\Paypal\Helpers\Helper;
use Drewdan\Paypal\Orders\Enums\UserActionEnum;
use Drewdan\Paypal\Orders\Enums\LandingPageEnum;
use Drewdan\Paypal\Orders\Models\ExperienceContext;
use Drewdan\Paypal\Orders\Enums\ShippingPreferenceEnum;
use Drewdan\Paypal\Orders\Contracts\BuildsPaymentSource;
use Drewdan\Paypal\Orders\Enums\PaymentMethodPreferenceEnum;

class Paypal extends PaymentSource implements BuildsPaymentSource {

	public function __construct(
		public ?ExperienceContext $experienceContext = null,
		public ?string $vaultId = null,
		public ?string $emailAddress = null,
		public ?string $givenName = null,
		public ?string $surname = null,
		public ?string $phoneNumber = null,
		public ?string $birthDate = null,
	) {
	}

	public function setExperienceContext(ExperienceContext $experienceContext): static {
		$this->experienceContext = $experienceContext;
		return $this;
	}

	public function withDefaultExperienceContext(): static {
		$this->experienceContext = ExperienceContext::make()
			->setPaymentMethodPreference(PaymentMethodPreferenceEnum::from(config('paypal.experience_context.payment_method_preference')))
			->setBrandName(config('paypal.experience_context.brand_name'))
			->setLocale(config('paypal.experience_context.locale'))
			->setLandingPage(LandingPageEnum::from(config('paypal.experience_context.landing_page')))
			->setShippingPreference(ShippingPreferenceEnum::from(config('paypal.experience_context.shipping_preference')))
			->setUserAction(UserActionEnum::from(config('paypal.experience_context.user_action')))
			->setReturnUrl(config('paypal.experience_context.return_url'))
			->setCancelUrl(config('paypal.experience_context.cancel_url'));

		return $this;
	}

	public function setVaultId(?string $vaultId): self {
		$this->vaultId = $vaultId;

		return $this;
	}

	public function setEmailAddress(?string $emailAddress): self {
		$this->emailAddress = $emailAddress;

		return $this;
	}

	public function setGivenName(?string $givenName): self {
		$this->givenName = $givenName;

		return $this;
	}

	public function setSurname(?string $surname): self {
		$this->surname = $surname;

		return $this;
	}

	public function setPhoneNumber(?string $phoneNumber): self {
		$this->phoneNumber = $phoneNumber;

		return $this;
	}

	public function setBirthDate(?string $birthDate): self {
		// check the birthday conforms to this pattern: ^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$

		if (!preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $birthDate)) {
			throw new \InvalidArgumentException('The birth date must be in the format YYYY-MM-DD');
		}

		$this->birthDate = $birthDate;

		return $this;
	}

	public function buildPaymentSource(): array {
		$payload = [
			'paypal' => [
				'experience_context' => $this->experienceContext->buildPayload(),
				'vault_id' => $this->vaultId,
				'email_address' => $this->emailAddress,
				'name' => [
					'given_name' => $this->givenName,
					'surname' => $this->surname,
				],
				'phone' => [
					'phone_number' => $this->phoneNumber,
				],
				'birth_date' => $this->birthDate,
			],
		];

		return Helper::recursivelyRemoveNullValues($payload);
	}

	public static function fromArray(array $data): static {
		return new Paypal(
			experienceContext: Arr::has($data, 'experience_context') ? ExperienceContext::fromArray($data['experience_context']) : null,
			vaultId: $data['vault_id'] ?? null,
			emailAddress: $data['email_address'] ?? null,
			givenName: $data['name']['given_name'] ?? null,
			surname: $data['name']['surname'] ?? null,
			phoneNumber: $data['phone']['phone_number'] ?? null,
			birthDate: $data['birth_date'] ?? null,
		);
	}


}
