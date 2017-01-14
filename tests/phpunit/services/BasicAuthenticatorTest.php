<?php
/**
 *
 * @todo add tests for the different template types
 */
namespace tests\phpunit;

use erdiko\authenticate\services\BasicAuthenticator;
use erdiko\authenticate\services\MyErdikoUser;

require_once dirname(__DIR__) . '/../ErdikoTestCase.php';


class BasicAuthenticatorTest extends \tests\ErdikoTestCase
{
	public static function setUpBeforeClass()
    {
        @session_start();
		$_SESSION = array();
		ini_set("session.use_cookies",0);
		ini_set("session.use_only_cookies",0);
	}

    protected function setUp()
    {
        @session_start();
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

	public function testCreate()
	{
		$basic = new BasicAuthenticator(MyErdikoUser::getAnonymous());
		$this->assertInstanceOf(BasicAuthenticator::class, $basic);
	}

	/**
	 * @depends testCreate
	 */
	public function testGetAvailableAuthentications()
	{
		$basic = new BasicAuthenticator(MyErdikoUser::getAnonymous());
		$result = $basic->getAvailableAuthentications();
		$this->assertInternalType('array', $result);
		$this->assertGreaterThan(0, count($result));
	}

	/**
	 * @depends testCreate
	 */
	public function testPersistUser()
    {
        $basic = new BasicAuthenticator(MyErdikoUser::getAnonymous());
		$basic->persistUser(MyErdikoUser::getAnonymous());
		$this->assertNotEmpty($_SESSION['current_user']);
	}

	/**
	 * @depends testPersistUser
	 */
	public function testCurrentUser()
	{
		$basic = new BasicAuthenticator(MyErdikoUser::getAnonymous());
		$basic->persistUser(MyErdikoUser::getAnonymous());
		$current = $basic->currentUser();

		$this->assertEquals($current, MyErdikoUser::unmarshall($_SESSION['current_user']));
	}

	/**
	 * @depends testCreate
	 */
	public function testLogin()
	{
        $basic = new BasicAuthenticator(MyErdikoUser::getAnonymous());
		$basic->login(array('username'=>'foo@mail.com'), 'mock');

		$current = $basic->currentUser();

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
		$basic = new BasicAuthenticator(MyErdikoUser::getAnonymous());
		$basic->logout();

		$this->assertFalse(array_key_exists('current_user',$_SESSION));
		$current = $basic->currentUser();
		$this->assertEquals('anonymous', $current->getUsername());
	}
}
