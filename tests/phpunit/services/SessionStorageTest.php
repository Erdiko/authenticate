<?php
/**
 *
 * @todo add tests for the different template types
 */
namespace tests\phpunit\services;

require_once dirname(__DIR__) . '/../ErdikoTestCase.php';
require_once dirname(__DIR__) . '/../factories/MockErdikoUser.php';
require_once dirname(__DIR__) . '/../factories/Mock.php';

use erdiko\authenticate\services\SessionStorage;
use \tests\ErdikoTestCase;
use tests\factories\MockErdikoUser;

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
        if ( !isset( $_SESSION ) ) $_SESSION = array(  );
		$this->session = new SessionStorage();
		$this->user = new MockErdikoUser();
	}

	public function tearDown()
	{
		$_SESSION = array();
		unset($this->session);
	}

	public function testSessionPersistAnnonymous()
	{
		$this->session->persist(MockErdikoUser::getGeneral());
		$current_user = MockErdikoUser::unmarshall($_SESSION["current_user"]);

		$this->assertNotEmpty($current_user);
		$this->assertEquals('general', $current_user->getRole());
	}

	public function testSessionPersist()
	{
		$entity = $this->user->getEntity();
		$entity->setEmail("test@arroyolabs.com");
		$entity->setRole("client");
		$this->user->setEntity($entity);
		$this->session->persist($this->user);

		$current_user = MockErdikoUser::unmarshall($_SESSION["current_user"]);

		$this->assertNotEmpty($current_user);
		$this->assertEquals('client', $current_user->getRole());
	}

	public function testAttemptLoadNoSession()
	{
		$entity = $this->user->getEntity();
		$entity->setEmail("test@arroyolabs.com");
		$entity->setRole("admin");
		$this->user->setEntity($entity);

		$current = $this->session->attemptLoad($this->user);
		$this->assertNull($current);
	}

	public function testAttemptLoad()
	{
		$entity = $this->user->getEntity();
		$entity->setEmail("test@arroyolabs.com");
		$entity->setRole("admin");
		$this->user->setEntity($entity);
		$this->session->persist($this->user);
		$current = $this->session->attemptLoad($this->user);
		$this->assertNotEmpty($current);
		$this->assertEquals('admin', $current->getRole());
	}

	public function testDestroy()
	{
		$this->session->persist(MockErdikoUser::getGeneral());
		$this->session->destroy();
		$this->assertFalse(isset($_SESSION["current_user"]));
	}
}
