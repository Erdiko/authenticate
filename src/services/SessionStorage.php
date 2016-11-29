<?php
/**
 * SessionStorage
 *
 * @category    Erdiko
 * @package     Authenticate/Services
 * @copyright   Copyright (c) 2016, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 */

namespace erdiko\authenticate\Services;

use erdiko\authenticate\iErdikoUser;

Class SessionStorage implements Storage {

	public function persist(iErdikoUser $user)
	{
		$this->startSession();
		$_SESSION["current_user"] = $user->marshall();
	}

	public function attemptLoad(iErdikoUser $userModel)
	{
		$user = null;
		$this->startSession();
		if(array_key_exists("current_user", $_SESSION)){
			$_user = $_SESSION["current_user"];
			if(!empty($_user)){
				$user = $userModel::unmarshall($_user);
			}
		}
		return $user;
	}

	public function destroy()
	{
		$this->startSession();
		if(array_key_exists("current_user", $_SESSION)){
			unset($_SESSION["current_user"]);
		}
		session_destroy();
	}

	private function startSession()
	{
		if(!file_exists(ERDIKO_VAR . "/session")) {
			mkdir(ERDIKO_VAR . "/session");
		}
		ini_set('session.save_path',ERDIKO_VAR . "/session");
		if (version_compare(phpversion(), '5.4.0', '<')) {
			if(session_id() == '') {
				session_start();
			}
		}
		else
		{
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
		}
	}
}