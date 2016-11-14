<?php
/**
 * aErdikoUser
 *
 * @category    Erdiko
 * @package     Authenticate
 * @copyright   Copyright (c) 2016, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 */


namespace erdiko\authenticate;


interface iErdikoUser {
	public static function getAnonymous();
	public function marshall();
	public static function unmarshall($encoded);
}