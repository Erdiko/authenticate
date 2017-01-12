<?php
/**
 * JWTAuthenticator
 *
 * Authenticator class that creates and validates JWT via the JWTAuth Service
 *
 * @note this should use the JWT class for encode/decode
 *
 * @package     erdiko/authenticate/services
 * @copyright   Copyright (c) 2016, Arroyo Labs, http://www.arroyolabs.com
 * @author      Andy Armstrong, andy@arroyolabs.com
 */

namespace erdiko\authenticate;

use erdiko\authenticate\services\AuthenticatorInterface;
use erdiko\authenticate\UserInterface;

class JWTAuthenticator implements AuthenticatorInterface
{
	use erdiko\authenticate\traits\ConfigLoaderTrait;
	use erdiko\authenticate\traits\BuilderTrait;

	private $config;
	private $container;
	private $selectedStorage;

	protected $erdikoUser;

    /**
     * __construct
     *
     */
	public function __construct(UserInterface $user)
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
	public function persistUser(UserInterface $user) { }

    /**
     * current_user
     *
     * Returns the user (currently logged in) from the storage container
     *
     */
	public function current_user()
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
            $user = $result->user;

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
