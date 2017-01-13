<?php
/**
 * JWTAuthenticator
 *
 * Authenticator class that creates and validates JWT via the JWTAuth Service
 *
 * @note this class should extend Basic
 *
 * @note this should use the JWT class for encode/decode
 *
 * @package     erdiko/authenticate/services
 * @copyright   Copyright (c) 2017, Arroyo Labs, http://www.arroyolabs.com
 * @author      Andy Armstrong, andy@arroyolabs.com
 */

namespace erdiko\authenticate\services;

use erdiko\authenticate\AuthenticatorInterface;
use erdiko\authenticate\UserStorageInterface;

class JWTAuthenticator implements AuthenticatorInterface
{
	use \erdiko\authenticate\traits\ConfigLoaderTrait;
	use \erdiko\authenticate\traits\BuilderTrait;

	private $config;
	private $container;
	private $selectedStorage;

	protected $erdikoUser;

    /**
     * __construct
     *
     */
	public function __construct(UserStorageInterface $user)
	{
		$this->erdikoUser = $user;
		$this->container = new \Pimple\Container();
		$this->config = $this->loadFromJson();
		// Storage
		$this->selectedStorage = $this->config["storage"]["selected"];
		$storage = $this->config["storage"]["storage_types"];
		$this->buildStorages($storage);
		// Authentications
		$authentication = $this->config["authentication"]["available_types"];
		$this->buildAuthenticator($authentication);
	}

    /**
     * persistUser
     */
	public function persistUser(UserStorageInterface $user) { }

    /**
     * current_user
     *
     * Returns the user (currently logged in) from the storage container
     *
     */
	public function currentUser()
	{
		try {
			$store = $this->container["STORAGES"][$this->selectedStorage];
			$user  = $store->attemptLoad($this->erdikoUser);
			if(empty($user)) $user = $this->erdikoUser->getAnonymous();

		} catch (\Exception $e) {
			$user = $this->erdikoUser->getAnonymous();
		}
		return $user;
	}

    /**
     * logout
     *
     * Kill the session
     *
     */
	public function logout()
	{
		try{
			$store = $this->container["STORAGES"][$this->selectedStorage];
			$store->destroy();
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
	}

    /**
     * login
     *
     * Attempt to log the user in via service model
     *
     */
	public function login($credentials = array(), $type = 'mock')
    {
        $storage = $this->container["STORAGES"][$this->selectedStorage];

		// checks if it's already logged in
		$user = $storage->attemptLoad($this->erdikoUser);
		if($user instanceof iErdikoUser){
			$this->logout();
        }

		$result = false;
		try {
			$auth = $this->container["AUTHENTICATIONS"][$type];
            $result = $auth->login($credentials);
            if(isset($result->user))
            	$user = $result->user;
            else
            	throw new \Exception("user failed to load");

			if(!empty($user) && (false !== $user)) {
				$this->persistUser( $user );
				$response = true;
			}
		} catch (\Exception $e) {
			\error_log($e->getMessage());
		}
		return $result;
	}

    /**
     * decodeJWT
     *
     * Decode the JWT via the service model
     *
     */
    public function decodeJWT($credentials, $type = 'mock')
    {
		$result = false;
		try {
			$auth = $this->container["AUTHENTICATIONS"][$type];
            $result = $auth->decodeJWT($credentials);
		} catch (\Exception $e) {
			\error_log($e->getMessage());
		}
		return $result;
    }

}
