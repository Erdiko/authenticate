<?php
/**
 * Builder
 *
 * @category    Erdiko
 * @package     Authenticate/Traits
 * @copyright   Copyright (c) 2016, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 */

namespace erdiko\authenticate\traits;


trait Builder 
{

	public function buildStorages($storage = array())
	{
		$_container = new \Pimple\Container();
		foreach ($storage as $item){
			if($item["enabled"]==1){
				$class = '\\'
	                       . str_replace('_','\\',$item["namespace"]) . '\\'
	                       . ucfirst($item["classname"]);
				$_container[$item["name"]] = $this->container->factory(function ($c)use($class) {
					return new $class;
				});
			} else {
				continue;
			}
		}
		$this->container["STORAGES"] = $_container;
		unset($_container);
	}

	public function buildAuthenticator($authenticator = array())
	{
		$_container = new \Pimple\Container();
		foreach ($authenticator as $item){
			if($item["enabled"]==1){
				$class = '\\'
                          . str_replace('_','\\',$item["namespace"]) . '\\'
                          . ucfirst($item["classname"]);
				$_container[$item["name"]] = $this->container->factory(function ($c)use($class) {
					return new $class();
				});
			} else {
				continue;
			}
		}
		$this->container["AUTHENTICATIONS"] = $_container;
		unset($_container);
	}
}