<?php
/**
 *
 * @todo add tests for the different template types
 */
namespace tests\phpunit\services;

require_once dirname(__DIR__) . '/../ErdikoTestCase.php';
require_once dirname(__DIR__) . '/../factories/MyErdikoUser.php';
require_once dirname(__DIR__) . '/../factories/Mock.php';

use erdiko\authenticate\tests\factories\MyErdikoUser;
use erdiko\authenticate\services\SessionStorage;
use \tests\ErdikoTestCase;



class SessionStorageTest extends ErdikoTestCase
{
	public $session = null;
	public $user;

	public static function setUpBeforeClass()
	{
		$_SESSION = array();
		ini_set("session.use_cookies",0);
		ini_set("session.use_only_cookies",0);
	}

	public function setUp()
	{
		$this->session = new SessionStorage();
		$this->user = new MyErdikoUser();
	}

	public function tearDown()
	{
		$_SESSION = array();
		unset($this->session);
	}

	public function testSessionPersistAnnonymous()
	{
		$this->session->persist(MyErdikoUser::getAnonymous());
		$current_user = MyErdikoUser::unmarshall($_SESSION["current_user"]);

		$this->assertNotEmpty($current_user);
		$this->assertTrue($current_user->isAnonymous());
	}

	public function testSessionPersist()
	{
		$this->user->setUsername("test@arroyolabs.com");
		$this->user->setRoles(array("client"));
		$this->session->persist($this->user);

		$current_user = MyErdikoUser::unmarshall($_SESSION["current_user"]);

		$this->assertNotEmpty($current_user);
		$this->assertTrue($current_user->hasRole('client'));
	}

	public function testAttemptLoad()
	{
		$this->user->setUsername("test@arroyolabs.com");
		$this->user->setRoles(array("admin"));
		$this->session->persist($this->user);

		$current = $this->session->attemptLoad($this->user);
		$this->assertNotEmpty($current);
		$this->assertTrue($current->isAdmin());
	}

	public function testDestroy()
	{
		$this->session->persist(MyErdikoUser::getAnonymous());
		$this->session->destroy();
		$this->assertFalse(isset($_SESSION["current_user"]));
	}
}
