<?php
/**
 * Mock
 *
 * @package     erdiko/authenticate/services
 * @copyright   Copyright (c) 2017, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 */

namespace erdiko\authenticate\tests\factories;

use \erdiko\authenticate\AuthenticationInterface;
use tests\factories\MockErdikoUser;
use tests\factories\UserEntity;

class Mock implements AuthenticationInterface
{
	public function login($credentials)
	{
		/**
		 * this is a mock login, I will fake users for testing purpose
		 */
		$_user = new MockErdikoUser();
		$user = new UserEntity();
		switch ($credentials['username']) {
			case "foo@mail.com":
				$user->setEmail($credentials['username']);
				$user->setName('Foo');
				$user->setId(1);
				$user->setRole("client");
				break;
			case "bar@mail.com":
				$user->setEmail($credentials['username']);
				$user->setName('Bar');
				$user->setId(2);
				$user->setRole("admin");
                break;
			case "jwt@mail.com":
				$user->setEmail($credentials['username']);
				$user->setName('JWT');
				$user->setId(2);
				$user->setRole("client");
				$_user->setEntity($user);
                $result = (object)array(
                    "user"  => $_user,
                    "token" => "abc1234"
                );

                return $result;

				break;
		}
		$_user->setEntity($user);
		return $_user;
    }

    public function verify($credentials = null) { 
        return true;
    }
}

