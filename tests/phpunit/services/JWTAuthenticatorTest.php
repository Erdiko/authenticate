<?php
/**
 *
 * @todo add tests for the different template types
 */
namespace tests\phpunit;

use erdiko\authenticate\services\JWTAuthenticator;
use erdiko\authenticate\services\MyErdikoUser;

require_once dirname(__DIR__) . '/../ErdikoTestCase.php';

class JWTAuthenticatorTest extends \tests\ErdikoTestCase
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
        //error_reporting(E_ALL);
        //ini_set('display_errors', 1);
    }

    /**
     *
     *
     */
    public function testConstructor()
    {
		$jwtAuth = new JWTAuthenticator(MyErdikoUser::getAnonymous());
		$this->assertInstanceOf(JWTAuthenticator::class, $jwtAuth);
    }

    /**
     *
     *
     */
    public function testLogin()
    {
        $jwtAuth = new JWTAuthenticator(MyErdikoUser::getAnonymous());
    }

    /**
     *
     *
     */
    public function testDecodeJWT()
    {
        $jwtAuth = new JWTAuthenticator(MyErdikoUser::getAnonymous());
    }

}
