<?php

namespace Drewdan\Paypal\Client;

use JsonMapper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\PendingRequest;
use Drewdan\Paypal\Exceptions\InvalidClientException;
use Drewdan\Paypal\Exceptions\InvalidRequestException;
use Drewdan\Paypal\Exceptions\MissingCredentialsException;

class PaypalClient {

	const VERSION = '/v2/';

	const SANDBOX_URL = 'https://api-m.sandbox.paypal.com';

	const LIVE_URL = 'https://api-m.paypal.com';

	/** @var \Illuminate\Http\Client\PendingRequest */
	public PendingRequest $client;


	/**
	 * @throws \Drewdan\Paypal\Exceptions\MissingCredentialsException
	 */
	public function __construct() {
		if (
			Str::of(config('paypal.client_id'))->trim()->isEmpty() ||
			Str::of(config('paypal.secret'))->trim()->isEmpty()) {
			throw new MissingCredentialsException('You have not set your Paypal Credentials');
		}
		$this->client = Http::withBasicAuth(config('paypal.client_id'), config('paypal.secret'))
			->asJson()
			->baseUrl(
				config('paypal.environment') === 'LIVE' ? self::LIVE_URL : self::SANDBOX_URL . self::VERSION
			);
	}

	/**
	 * Handles the calls to the Http Client, get, post patch etc
	 *
	 * @param string $name
	 * @param array $arguments
	 * @return object
	 * @throws \Exception
	 */
	public function __call(string $name, array $arguments = []) {
		$response = $this->makeRequest($name, $arguments)->object();

		if (isset($response->error)) {
			$errorMethod = Str::camel($response->error);
			if (method_exists($this, $errorMethod)) {
				$this->$errorMethod($response->error_description);
			}

			//this catches any exception that we do not have custom handlers for
			throw new \Exception($response->error_description);
		}

		return $response;
	}

	/**
	 * Makes a request and returns an instance of a Http Response
	 *
	 * @param string $name
	 * @param array $arguments
	 * @return \Illuminate\Http\Client\Response
	 */
	private function makeRequest(string $name, array $arguments = []): Response {
		return $this->client->asJson()->$name(...$arguments);
	}

	/**
	 * This is the exception that will be thrown if there is an invalid request
	 *
	 * @throws \Drewdan\Paypal\Exceptions\InvalidRequestException
	 */
	protected function invalidRequest(string $message) {
		throw new InvalidRequestException($message);
	}

	/**
	 * This will be thrown if the client credentials are incorrect, malformed or missing
	 *
	 * @throws \Drewdan\Paypal\Exceptions\InvalidClientException
	 */
	protected function invalidClient(string $message) {
		throw new InvalidClientException($message);
	}
}
