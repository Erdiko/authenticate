<?php
/**
 * AuthenticationManagerTest
 *
 * @package     erdiko/authenticate
 * @copyright   Copyright (c) 2017, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 */

namespace erdiko\authenticate\tests\phpunit;

require_once dirname(__DIR__) . '/ErdikoTestCase.php';

use  \tests\ErdikoTestCase;
use erdiko\authenticate\AuthenticationManager;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class AuthenticationManagerTest  extends ErdikoTestCase
{
    public $userProvider;

    public function setUp()
    {
        $this->userProvider = new InMemoryUserProvider(
            array(
                'bar@mail.com' => array(
                    // plain: asdf1234
                    'password' => '$2y$10$zlyw7wJinnKfsySiWF5daOkxDycAghCjDuiNS4ykn1dOprzoehnde',
                    'roles'    => array('ROLE_ADMIN'),
                ),
                'foo@mail.com' => array(
                    // plain: asdf1234
                    'password' => '$2y$10$zlyw7wJinnKfsySiWF5daOkxDycAghCjDuiNS4ykn1dOprzoehnde',
                    'roles'    => array('ROLE_USER'),
                ),
            )
        );
    }


    public function testCreate()
    {
        $auth = new AuthenticationManager($this->userProvider);
        $this->assertNotEmpty($auth);
        $this->assertInstanceOf('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface', $auth);
    }

    /**
     * @expectedException
     * @expectedExceptionMessage Bad credentials
     */
    public function testAuthenticateInvalidToken()
    {
        try {
            $auth = new AuthenticationManager($this->userProvider);
            $token = new UsernamePasswordToken('email', 'password', 'main', array());
            $tokenInvalid = $auth->authenticate($token);
        } catch (\Exception $e) {
            $this->assertEquals('Bad credentials.',$e->getMessage());
        }
    }

    public function testAuthenticate()
    {
        $auth = new AuthenticationManager($this->userProvider);
        $token = new UsernamePasswordToken('bar@mail.com', 'asdf1234', 'main', array());
        $authenticated = $auth->authenticate($token);
        $this->assertNotEmpty($authenticated);
        $this->assertInstanceOf('Symfony\Component\Security\Core\Authentication\Token\TokenInterface',$authenticated);
    }
}