<?php
/**
 * AuthenticationInterface
 *
 * @package     erdiko/authenticate
 * @copyright   Copyright (c) 2016, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 */

namespace erdiko\authenticate\Services;


interface AuthenticationInterface
{
  public function login($credentials);
  // public function verify($token = null);
}