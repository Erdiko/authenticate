<?php
/**
 *
 * @todo add tests for the different template types
 */
namespace tests\phpunit;

use erdiko\authenticate\BasicAuth;
use erdiko\authenticate\Services\MyErdikoUser;

require_once dirname(__DIR__) . '/ErdikoTestCase.php';


class BasicAuthTest extends \tests\ErdikoTestCase
{
	public static function setUpBeforeClass()
	{
		$_SESSION = array();
		ini_set("session.use_cookies",0);
		ini_set("session.use_only_cookies",0);
	}

	public function testCreate()
	{
		$basic = new BasicAuth(MyErdikoUser::getAnonymous());
		$this->assertInstanceOf(BasicAuth::class, $basic);
	}

	/**
	 * @depends testCreate
	 */
	public function testGetAvailableAuthentications()
	{
		$basic = new BasicAuth(MyErdikoUser::getAnonymous());
		$result = $basic->getAvailableAuthentications();
		$this->assertInternalType('array', $result);
		$this->assertGreaterThan(0, count($result));
	}

	/**
	 * @depends testCreate
	 */
	public function testPersistUser()
	{
		$basic = new BasicAuth(MyErdikoUser::getAnonymous());
		$basic->persistUser(MyErdikoUser::getAnonymous());

		$this->assertNotEmpty($_SESSION['current_user']);
	}

	/**
	 * @depends testPersistUser
	 */
	public function testCurrent_user()
	{
		$basic = new BasicAuth(MyErdikoUser::getAnonymous());
		$basic->persistUser(MyErdikoUser::getAnonymous());
		$current = $basic->current_user();

		$this->assertEquals($current, MyErdikoUser::unmarshall($_SESSION['current_user']));
	}

	/**
	 * @depends testCreate
	 */
	public function testLogin()
	{
		$basic = new BasicAuth(MyErdikoUser::getAnonymous());
		$basic->login(array('username'=>'foo@mail.com'),'mock');

		$current = $basic->current_user();

		$this->assertNotEmpty($current);
		$this->assertEquals('Foo', $current->getDisplayName());
		$this->assertFalse($current->isAdmin());
		$this->assertTrue($current->hasRole('client'));
	}

	/**
	 * @depends testCreate
	 */
	public function testLogout()
	{
		$basic = new BasicAuth(MyErdikoUser::getAnonymous());
		$basic->logout();

		$this->assertFalse(array_key_exists('current_user',$_SESSION));
		$current = $basic->current_user();
		$this->assertEquals('anonymous', $current->getUsername());
	}
}
