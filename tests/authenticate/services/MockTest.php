<?php
/**
 *
 * @todo add tests for the different template types
 */
namespace erdiko\authenticate\tests\authenticate\services;

use erdiko\authenticate\Services\Mock;

require_once dirname(dirname(__DIR__)) . '/ErdikoTestCase.php';


class MockTest extends \tests\ErdikoTestCase
{
	public $mock;

	public $validClient = array(
		"username" => "foo@mail.com"
	);

	public $validAdmin = array(
		"username" => "bar@mail.com"
	);

	public function setUp()
	{
		$this->mock = new Mock();
	}

	public function tearDown()
	{
		unset($this->mock);
	}

	public function testLoginClient()
	{
		$client = $this->mock->login($this->validClient);
		$this->assertEquals("Foo", $client->getDisplayName());
		$this->assertTrue($client->hasRole('client'));
		$this->assertFalse($client->isAdmin());
	}

	public function testLoginAdmin() {
		$client = $this->mock->login( $this->validAdmin );
		$this->assertEquals( "Bar", $client->getDisplayName() );
		$this->assertTrue( $client->isAdmin() );
	}
}
