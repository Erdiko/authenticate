<?php
/**
 * User Model
 * @todo should refactor and move some of the get methods into a user service class (e.g. getUsers())
 *
 * @package     erdiko/users/models
 * @copyright   Copyright (c) 2017, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 */

namespace tests\factories;

require_once dirname(__FILE__) . '/UserEntity.php';

use erdiko\authenticate\UserStorageInterface;
use \tests\factories\UserEntity;

class MockErdikoUser implements
	UserStorageInterface
{
	protected $_user;

	public function __construct() {
		$this->_user = self::createAnonymous();
	}

	public function setEntity($entity)
	{
		if (!($entity instanceof  UserEntity)) {
			throw new \Exception('Parameter must be an entity User');
		}
		$this->_user = $entity;
	}

	/**
	 *
	 */
	public function getEntity()
	{
		return self::unmarshall($this->marshall());
	}
	
	public static function getAnonymous() {
		$_user = new MockErdikoUser();
		return $_user;
	}

	protected static function createAnonymous()
	{
		$_entity = new UserEntity();
		$_entity->setId(0);
		$_entity->setName('anonymous');
		$_entity->setEmail('anonymous@mail.com');
		$_entity->setRole('anonymous');
		return $_entity;
	}

	public function marshall() {
		return $this->_user->marshall('json');
	}

	public static function unmarshall( $encoded ) {
		$decode = json_decode($encoded, true);
		$entity = new UserEntity();
		foreach ($decode as $key=>$value) {
			$method = "set".str_replace('_', '', ucwords($key, '_'));
			$entity->$method($value);
		}
		return $entity;
	}

	public function authenticate($email, $password)
	{
		return $this;
	}

	public function getDisplayName()
	{
		return $this->_user->getName();
	}

	public function isAdmin()
	{
		return $this->hasRole('admin');
	}

	public function isAnonymous()
	{
		return $this->hasRole();
	}

	public function hasRole($role = "anonymous")
	{
		return $this->_user->getRole() == $role;
	}

	public function getRole()
	{
		return  $this->_user->getRole();
	}

	public function getRoles()
	{
		return  array($this->_user->getRole());
	}

}
