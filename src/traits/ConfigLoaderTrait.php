<?php
  /**
   * ConfigLoader
   *
   * @package     erdiko/authenticate/traits
   * @copyright   Copyright (c) 2016, Arroyo Labs, http://www.arroyolabs.com
   * @author      Leo Daidone, leo@arroyolabs.com
   * @author      John Arroyo, john@arroyolabs.com
   */

namespace erdiko\authenticate\traits;

trait ConfigLoaderTrait
{

  // attempt to load config form authorize.json, specially settings for DI.
  // Optional, it might include guards and rules.
  public function loadFromJson($context=null)
  {
    if($context==null)
      $context = getenv('ERDIKO_CONTEXT');

	  return \erdiko\core\Helper::getConfig("authenticate",$context);
  }
}