<?php
/**
 * JWTAuth
 *
 *
 * @category    app
 * @package     User
 * @copyright   Copyright (c) 2016, Arroyo Labs, http://www.arroyolabs.com
 * @author      Andy Armstrong, andy@arroyolabs.com
 */


namespace erdiko\authenticate\services;

use erdiko\authenticate\services\iAuth;
use erdiko\users\models\User;

use \Firebase\JWT\JWT;

class JWTAuth implements iAuth
{

	public function login( $credentials ) {
		$user = new User();
		$username = (array_key_exists('username', $credentials)) ? $credentials['username'] : '';
        $password = (array_key_exists('password',$credentials)) ? $credentials['password'] : '';

        $user = $user->authenticate($username, $password);

        // make sure we have a secret key to create the JWT 
        if(!array_key_exists("secret_key", $credentials) || empty($credentials["secret_key"])) {
            throw new \Exception("Secret Key is required to create a JWT");
        }

        // get hash alg from params, default to HS256
        $hashAlg = "HS256";
        if(array_key_exists("hash_alg", $credentials) && !empty($credentials["hash_alg"])) {
            $hashAlg = $credentials["hash_alg"];
        }

        // get the logged in user role
        $roleModel = new \erdiko\users\models\Role();
        $role = $roleModel->findById($user->getRole());

        if(empty($role)) {
            throw new \Exception("Role was not found for user " . $user->getUserId());
        }

        // collect token data
        $token = array(
            "id"    => $user->getUserId(),
            "email" => $user->getEntity()->getEmail(),
            "name"  => $user->getEntity()->getName(), 
            "role"  => array(
                "id"    => $role->getId(),
                "name"  => $role->getName()
            )
        );

        $result = (object)array(
            "user"  => $user,
            "token" => JWT::encode($token, $credentials["secret_key"], $hashAlg)
        );

		return $result;
	}
}
