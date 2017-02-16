<?php
/**
 *
 * @todo add tests for the different template types
 */
namespace tests\phpunit;

require_once dirname(__DIR__) . '/../ErdikoTestCase.php';
require_once dirname(__DIR__) . '/../factories/MyErdikoUser.php';
require_once dirname(__DIR__) . '/../factories/Mock.php';

use erdiko\authenticate\services\JWTAuthenticator;
use erdiko\authenticate\tests\factories\MyErdikoUser;
use \tests\ErdikoTestCase;


class JWTAuthenticatorTest extends ErdikoTestCase
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
    public function testLoginNoUser()
    {
        $jwtAuth = new JWTAuthenticator(MyErdikoUser::getAnonymous());

        $authParams = array(
            'username'      =>  "fake@mail.com",
            'password'      =>  "password"
        );

        $this->expectException('Exception');
        $result = $jwtAuth->login($authParams);
    }

    /**
     *
     *
     */
    public function testLogin()
    {
        $jwtAuth = new JWTAuthenticator(MyErdikoUser::getAnonymous());

        $authParams = array(
            'secret_key'    =>  "abc123",
            'username'      =>  "jwt@mail.com",
            'password'      =>  "password"
        );

        $result = $jwtAuth->login($authParams, 'mock');

        // make sure the user is returned
		$this->assertNotEmpty($result);
		$this->assertEquals('JWT', $result->user->getDisplayName());
		$this->assertFalse($result->user->isAdmin());
		$this->assertTrue($result->user->hasRole('client'));

        // make sure we get a JWT token returned
		$this->assertNotEmpty($result->token);
    }

    /**
     *
     *
     */
    public function testVerify()
    {
        $jwtAuth = new JWTAuthenticator(MyErdikoUser::getAnonymous());
 
        $authParams = array(
            'secret_key'    =>  "abc123",
            'username'      =>  "jwt@mail.com",
            'password'      =>  "password"
        );
   
        $result = $jwtAuth->verify($authParams,'mock');

		$this->assertTrue($result);
    }

}
