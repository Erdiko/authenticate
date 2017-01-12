<?php
/**
 * Storage
 *
 * @package     erdiko/authenticate
 * @copyright   Copyright (c) 2016, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 */

namespace erdiko\authenticate;

use erdiko\authenticate\UserInterface;

interface StorageInterface
{
  public function persist(UserInterface $user);
  public function attemptLoad(UserInterface $userModel);
  public function destroy();
}