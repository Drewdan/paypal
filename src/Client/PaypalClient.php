<?php

namespace Drewdan\Paypal\Client;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\PendingRequest;
use Drewdan\Paypal\Exceptions\InvalidClientException;
use Drewdan\Paypal\Exceptions\InvalidRequestException;
use Drewdan\Paypal\Exceptions\MissingCredentialsException;

class PaypalClient {

	private string $version = '/v2/';

	const SANDBOX_URL = 'https://api-m.sandbox.paypal.com';

	const LIVE_URL = 'https://api-m.paypal.com';

	/** @var \Illuminate\Http\Client\PendingRequest */
	public PendingRequest $client;

	/**
	 * @throws \Drewdan\Paypal\Exceptions\MissingCredentialsException
	 */
	public function __construct(public bool $responseAsArray = false, public bool $useV1 = false) {
		if (
			Str::of(config('paypal.client_id'))->trim()->isEmpty() ||
			Str::of(config('paypal.secret'))->trim()->isEmpty()) {
			throw new MissingCredentialsException('You have not set your Paypal Credentials');
		}

		if ($this->useV1) {
			$this->version = '/v1/';
		}

		$this->client = Http::withBasicAuth(config('paypal.client_id'), config('paypal.secret'))
			->asJson()
			->baseUrl($this->generateBaseUrl());
	}

	public static function make(bool $responseAsArray = false, bool $useV1 = false): static {
		return App::make(
			PaypalClient::class,
			[
				'responseAsArray' => $responseAsArray,
				'useV1' => $useV1,
			]
		);
	}

	public function getClient(): PendingRequest {
		return $this->client;
	}

	public function withQuery(array $query): static {
		$this->client->withQuery($query);
		return $this;
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
		$responseType = $this->responseAsArray ? 'json' : 'object';
		$response = $this->makeRequest($name, $arguments);

		$rawResponse = $response;

		$response = $response->$responseType();

		if (isset($response->error)) {
			$errorMethod = Str::camel($response->error);
			if (method_exists($this, $errorMethod)) {
				$this->$errorMethod($response->error_description);
			}

			//this catches any exception that we do not have custom handlers for
			throw new \Exception($response->error_description);
		}


		if (!$rawResponse->successful()) {
			Log::error('Paypal Request Failed', [
				'type' => $name,
				'arguments' => $arguments,
				'response' => $rawResponse->body(),
			]);

			throw new \Exception($rawResponse->object()?->message ?? 'An error occurred');
		}

		return $response;
	}

	/**
	 * Generates a base URL for the requests
	 *
	 * @return string
	 */
	public function generateBaseUrl(): string {
		return (config('paypal.environment') === 'LIVE'
				? self::LIVE_URL
				: self::SANDBOX_URL) . $this->version;
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
