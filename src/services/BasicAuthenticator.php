<?php
/**
 * BasicAuthenticator
 *
 * @package     erdiko/authenticate/services
 * @copyright   Copyright (c) 2016, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 */
namespace erdiko\authenticate\services;

use erdiko\authenticate\AuthenticatorInterface;
use erdiko\authenticate\MD5PasswordEncoder;
use erdiko\authenticate\UserStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;

class BasicAuthenticator implements AuthenticatorInterface
{
	use \erdiko\authenticate\traits\ConfigLoaderTrait;
	use \erdiko\authenticate\traits\BuilderTrait;

	private $config;
	private $container;
	private $selectedStorage;

	protected $erdikoUser;


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

	public function persistUser(UserStorageInterface $user)
	{
		try {
			$store = $this->container["STORAGES"][$this->selectedStorage];
			$store->persist($user);

			// @todo: call authenticate manager
			$this->generateTokenStorage($user);
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
	}

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

	public function login($credentials = array(), $type = 'jwt_auth')
	{
		$storage = $this->container["STORAGES"][$this->selectedStorage];
		// checks if it's already logged in
		$user = $storage->attemptLoad($this->erdikoUser);
		if($user instanceof UserStorageInterface){
			$this->logout();
		}
		$response = false;
		try {
			$auth = $this->container["AUTHENTICATIONS"][$type];
			$user = $auth->login($credentials);
			if(!empty($user) && (false !== $user)) {
				$this->persistUser( $user );
				$response = true;
			}
		} catch (\Exception $e) {
			\error_log($e->getMessage());
		}
		return $response;
	}

	public function logout()
	{
		try{
			$store = $this->container["STORAGES"][$this->selectedStorage];
			$store->destroy();
			unset($_SESSION['tokenstorage']);
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
	}

	public function getAvailableAuthentications()
	{
		$result = array();
		$types = $this->config["authentication"]["available_types"];
		foreach ($types as $type){
			if($type["enabled"]){
				array_push($result, $type["enabled"]);
			}
		}
		return $result;
	}

	public function generateTokenStorage(UserStorageInterface $user)
	{
		$entityUser = $user->getEntity();

		$userToken = new UsernamePasswordToken($entityUser->getEmail(),$entityUser->getPassword(),'main',array($entityUser->getRole()));
		$_SESSION['tokenstorage'] = $userToken;
	}
}