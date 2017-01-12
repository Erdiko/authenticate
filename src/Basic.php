<?php
/**
 * Basic
 *
 * @package     erdiko/authenticate
 * @copyright   Copyright (c) 2016, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 */
namespace erdiko\authenticate;

class Basic implements AuthenticatorInterface
{
	use \erdiko\authenticate\traits\ConfigLoaderTrait;
	use \erdiko\authenticate\traits\BuilderTrait;

	private $config;
	private $container;
	private $selectedStorage;

	protected $erdikoUser;


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

	public function persistUser(UserInterface $user)
	{
		try {
			$store = $this->container["STORAGES"][$this->selectedStorage];
			$store->persist($user);
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
	}

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

	public function login($credentials = array(), $type = 'mock')
	{
		$storage = $this->container["STORAGES"][$this->selectedStorage];
		// checks if it's already logged in
		$user = $storage->attemptLoad($this->erdikoUser);
		if($user instanceof UserInterface){
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
}