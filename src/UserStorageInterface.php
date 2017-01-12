<?php
/**
 * UserStorageInterface
 *
 * @package     erdiko/authenticate
 * @copyright   Copyright (c) 2016, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 * @author      John Arroyo, john@arroyolabs.com
 */

namespace erdiko\authenticate;


interface UserStorageInterface 
{
	public static function getAnonymous();
	public function marshall();
	public static function unmarshall($encoded);
}