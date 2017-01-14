<?php
/**
 * AuthenticationInterface
 *
 * @package     erdiko/authenticate
 * @copyright   Copyright (c) 2016, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 * @author      John Arroyo, john@arroyolabs.com
 */

namespace erdiko\authenticate;


interface AuthenticationInterface
{

    public function login($credentials);

    public function verify($credentials);

}
