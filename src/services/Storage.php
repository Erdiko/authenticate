<?php
/**
 * Storage
 *
 * @category    Erdiko
 * @package     Authenticate/Services
 * @copyright   Copyright (c) 2016, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 */

namespace erdiko\authenticate\Services;

use erdiko\authenticate\iErdikoUser;

interface Storage
{
  public function persist(iErdikoUser $user);
  public function attemptLoad(iErdikoUser $userModel);
  public function destroy();
}