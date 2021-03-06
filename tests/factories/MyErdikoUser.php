<?php
/**
 * MyErdikoUser
 *
 * @package     erdiko/authenticate/services
 * @copyright   Copyright (c) 2017, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 */

namespace erdiko\authenticate\tests\factories;

use erdiko\authenticate\UserStorageInterface;

class MyErdikoUser implements UserStorageInterface
{
	private $user_id;
	private $roles = array('general');
	private $username = "general";
	private $display_name = "General";
	private $profile;


	public function __construct()
	{
	}

	/*public function marshall() {
		// TODO: Implement marshall() method.
	}*/

	public function marshall($type="json")
	{
		$tmp = $this;
		// unset sensitive fields
		unset($tmp->password);
		unset($tmp->created_at);
		unset($tmp->updated_at);
		$out = array();
		foreach ($tmp as $field=>$value){
			$out[$field] = $value;
		}
		$response = null;
		switch ($type) {
			case "object":
				$response = (object)$out;
				break;
			case "array":
				$response = (array)$out;
				break;
			case "json":
			default:
				$response = json_encode($out);
		}
		return $response;
	}

    public static function unmarshall($encoded) 
    {
		// TODO: Implement unmarshall() method.
		$decode = json_decode( $encoded, true );
		if(empty($decode)){
			$entity = self::getGeneral();
		} else {
			$entity = new MyErdikoUser();
			foreach ($decode as $key=>$value) {
				if($key!=='profile') {
					$key    = str_replace( ' ', '', ucwords( str_replace( '_', ' ', $key ) ) );
					$method = "set{$key}";
					$entity->$method( $value );
				}
			}
		}
		return $entity;
	}

	public function setEntity($entity)
	{
		if (!($entity instanceof  entity)) {
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

	public static function getGeneral()
	{
		$_user = new MyErdikoUser();
		$_user->setUserId(0);
		$_user->setRoles(array('general'),true);
		$_user->setEmail('anonymouys@mail.com');
		return $_user;
	}

	public function isAdmin()
	{
		return $this->hasRole('admin');
	}

	/**
	 * @param null $role
	 *
	 * @return bool
	 */
	public function hasRole($role=null)
	{
		return (is_null($role))
			? false
			: in_array(strtolower($role),$this->roles);
	}

	public function isGeneral()
	{
		return ($this->username == "general");
    }

	/**
	 * @return mixed
	 */
	public function getUserId()
	{
		return $this->user_id;
	}

	/**
	 * @param mixed $user_id
	 */
	public function setUserId($user_id)
	{
		$this->user_id = $user_id;
	}

	/**
	 * @return array
	 */
	public function getRoles()
	{
		return $this->roles;
	}

	/**
	 * @param $rol
	 */
	public function appendRole($rol)
	{
		array_push($this->roles, $rol);
	}

	/**
	 *
	 */
	protected function clearRoles()
	{
		$this->roles = array();
	}

	/**
	 * @param array $roles
	 * @param bool  $clear
	 */
	public function setRoles($roles=array(), $clear=true)
	{
		if($clear) $this->clearRoles();
		if(is_array($roles) && (count($roles))>0) {
			foreach ($roles as $rol) {
				$this->appendRole($rol);
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function getProfile()
	{
		return $this->profile;
	}

	/**
	 * @param mixed $profile
	 */
	public function setProfile(Profile $profile)
	{
		$this->profile = $profile;
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * @return string
	 */
	public function getDisplayName()
	{
		return $this->display_name;
	}

	/**
	 * @param string $display_name
	 */
	public function setDisplayName($display_name)
	{
		$this->display_name = $display_name;
	}
}

class Profile
{
	private $firstName;
	private $lastName;
	private $email;
	private $phones=[];
	private $address;

	/**
	 * @return mixed
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}

	/**
	 * @param mixed $firstName
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
	}

	/**
	 * @return mixed
	 */
	public function getLastName()
	{
		return $this->lastName;
	}

	/**
	 * @param mixed $lastName
	 */
	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
	}

	/**
	 * @return mixed
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param mixed $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @return array
	 */
	public function getPhones()
	{
		return $this->phones;
	}

	/**
	 * @param array $phones
	 */
	public function setPhones($phones)
	{
		$this->phones = $phones;
	}

	/**
	 * @return mixed
	 */
	public function getAddress()
	{
		return $this->address;
	}

	/**
	 * @param mixed $address
	 */
	public function setAddress($address)
	{
		$this->address = $address;
	}


}
