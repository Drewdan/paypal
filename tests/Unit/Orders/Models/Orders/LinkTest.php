<?php

namespace Drewdan\Paypal\Tests\Unit\Orders\Models\Orders;

use Drewdan\Paypal\Tests\TestCase;
use Drewdan\Paypal\Common\Models\Link;

class LinkTest extends TestCase {

	public function testCanCreateLink() {
		$link = new Link(
			href: 'https://example.com',
			rel: 'self',
			method: 'GET',
		);

		$this->assertEquals('https://example.com', $link->href);
		$this->assertEquals('self', $link->rel);
		$this->assertEquals('GET', $link->method);
	}

	public function testCanCreateLinkFromArray() {
		$link = Link::fromArray([
			'href' => 'https://example.com',
			'rel' => 'self',
			'method' => 'GET',
		]);

		$this->assertEquals('https://example.com', $link->href);
		$this->assertEquals('self', $link->rel);
		$this->assertEquals('GET', $link->method);
	}
}
