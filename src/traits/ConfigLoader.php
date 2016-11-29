<?php
  /**
   * ConfigLoader
   *
   * @category    Erdiko
   * @package     Authenticate/Traits
   * @copyright   Copyright (c) 2016, Arroyo Labs, http://www.arroyolabs.com
   * @author      Leo Daidone, leo@arroyolabs.com
   * @author      John Arroyo, john@arroyolabs.com
   */

namespace erdiko\authenticate\traits;

trait ConfigLoader
{

  // attempt to load config form authorize.json, specially settings for DI.
  // Optional, it might include guards and rules.
  public function loadFromJson($context=null)
  {
    return \erdiko\core\Helper::getConfig("authorize", $context);
  }
}