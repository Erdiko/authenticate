<?php
/**
 *
 * @todo add tests for the different template types
 */
namespace tests\phpunit;

require_once dirname(__DIR__) . '/../ErdikoTestCase.php';
require_once dirname(__DIR__) . '/../factories/MyErdikoUser.php';
require_once dirname(__DIR__) . '/../factories/Mock.php';

use erdiko\authenticate\services\BasicAuthenticator;
use erdiko\authenticate\tests\factories\MyErdikoUser;
use \tests\ErdikoTestCase;


class BasicAuthenticatorTest extends ErdikoTestCase
{

	public static function setUpBeforeClass()
    {
		$_SESSION = array();
		ini_set("session.use_cookies",0);
		ini_set("session.use_only_cookies",0);
	}

    protected function setUp()
    {
        global $_SESSION;
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        putenv("ERDIKO_CONTEXT=tests");
    }

    public function tearDown()
    {
        $_SESSION = array();
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
	    try {
            $basic = new BasicAuthenticator(MyErdikoUser::getAnonymous());
            $basic->persistUser(MyErdikoUser::getAnonymous());
            $this->assertNotEmpty($_SESSION['current_user']);
            $current = $basic->currentUser();

            $this->assertEquals($current, MyErdikoUser::unmarshall($_SESSION['current_user']));
        } catch (\Exception $e) {
	        var_dump($e->getMessage());
        }
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
