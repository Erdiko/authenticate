<?php
/**
 * JWTAuth
 *
 * @note this should not require the Users model (circular reference)
 *
 * @package     erdiko/authenticate/services
 * @copyright   Copyright (c) 2017, Arroyo Labs, http://www.arroyolabs.com
 * @author      Andy Armstrong, andy@arroyolabs.com
 */


namespace erdiko\authenticate\services;

use erdiko\authenticate\AuthenticationInterface;

use \Firebase\JWT\JWT;

class JWTAuthentication implements AuthenticationInterface
{

    protected $user;

    protected $role;

    /**
     *
     *
     */
    public function __construct($user = null, $role = null)
    {
        if(!empty($user)) {
            $this->user = $user;
        } else {
            $this->user = new \erdiko\users\models\User;
        }

        if(!empty($role)) {
            $this->role = $role;
        } else {
            $this->role = new \erdiko\users\models\Role;
        }

    }

    /**
     * login
     *
     */
    public function login($credentials)
    {
		$username = (array_key_exists('username', $credentials)) ? $credentials['username'] : '';
        $password = (array_key_exists('password', $credentials)) ? $credentials['password'] : '';

        $user = $this->user->authenticate($username, $password);

        if(empty($user) || false == $user) {
            throw new \Exception("Invalid username or password");
        }

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
        // @note this relies on the users package so would be a circular reference -john
        $role = $this->role->findById($user->getRole());

        if(empty($role)) {
            throw new \Exception("User is missing a role");
        }

        // collect token data
        $token = array(
            "id"    => $user->getUserId(),
            "email" => $user->getEntity()->getEmail(),
            "name"  => $user->getEntity()->getName(),
            "role"  => array(
                "id"    => $role->getId(),
                "name"  => $role->getName()
            ),
            "created" => time() // store time JWT was created so we can expire them if needed
        );

        $result = (object)array(
            "user"  => $user,   // return the user so we can store in the session

            // create the jwt token
            "token" => JWT::encode($token, $credentials["secret_key"], $hashAlg)
        );

		return $result;
    }

    /**
     * decodeJWT
     * @param array $credentials
     * @return string $token
     */
    public function verify($credentials)
    {
        if(!array_key_exists("jwt", $credentials) || empty($credentials["jwt"])) {
            throw new \Exception("JWT is required");
        }

        // Make sure we have a secret key to decode the JWT
        if(!array_key_exists("secret_key", $credentials) || empty($credentials["secret_key"])) {
            throw new \Exception("Secret Key is required to decode this JWT");
        }

        // Get hash alg from params, default to HS256
        $hashAlgs = array("HS256");
        if(array_key_exists("hash_alg", $credentials) && !empty($credentials["hash_alg"])) {
            $hashAlgs[] = $credentials["hash_alg"];
            $hashAlgs = array_unique($hashAlgs);
        }

        $token = JWT::decode($credentials["jwt"], $credentials["secret_key"], $hashAlgs);

        // User ID is a good checksum to make sure we have a valid JWT
        if(empty($token) || empty($token->id)) {
            throw new \Exception("JWT Token invalid");
        }

        return $token;
    }

}
