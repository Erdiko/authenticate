<?php
/**
 *
 * @todo add tests for the different template types
 */
namespace tests\phpunit;

use erdiko\authenticate\services\MyErdikoUser;

require_once dirname(__DIR__) . '/../ErdikoTestCase.php';


class MyErdikoUserTest extends \tests\ErdikoTestCase
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
    }

    /**
     *
     *
     */
    public function testMarshall()
    {
        $this->markTestSkipped('needs to be completed');
    }

    /**
     *
     *
     */
    public function testUnmarshall() 
    {
        $this->markTestSkipped('needs to be completed');
    }

}
