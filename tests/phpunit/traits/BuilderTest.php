<?php
/**
 *
 * @todo add tests for the different template types
 */
namespace tests\phpunit\traits;

use erdiko\authenticate\traits\BuilderTrait;

require_once dirname(dirname(__DIR__)) . '/ErdikoTestCase.php';

class BuilderTest extends \tests\ErdikoTestCase
{
	use BuilderTrait;

	public $container = null;

	public $storages_json = "{\"storage\": {
\"selected\": \"session\",
\"storage_types\": [{
    \"name\": \"session\",
    \"namespace\": \"erdiko_authenticate_Services\",
    \"classname\": \"SessionStorage\",
    \"enabled\": true
  }, {
    \"name\": \"database\",
    \"namespace\": \"erdiko_authenticate_Services\",
    \"classname\": \"DatabaseStorage\",
    \"enabled\": false
  }, {
    \"name\": \"filesystem\",
    \"namespace\": \"erdiko_authenticate_Services\",
    \"classname\": \"FilesystemStorage\",
    \"enabled\": false
  }, {
    \"name\": \"memcache\",
    \"namespace\": \"erdiko_authenticate_Services\",
    \"classname\": \"MemcacheStorage\",
    \"enabled\": false
  }]
}}";

	public $auth_json = "{\"authentication\": {
\"available_types\": [{
      \"name\": \"mock\",
      \"namespace\": \"erdiko_authenticate_Services\",
      \"classname\": \"Mock\",
      \"enabled\": true
    }, {
      \"name\": \"oauth\",
      \"namespace\": \"erdiko_authenticate_Services\",
      \"classname\": \"OAuth\",
      \"enabled\": false
    }, {
      \"name\": \"database\",
      \"namespace\": \"erdiko_authenticate_Services\",
      \"classname\": \"Database\",
      \"enabled\": false
    }]
}}";

	public function setUp()
	{
		$this->container = new \Pimple\Container();
	}

	public function tearDown()
	{
		unset($this->container);
	}

	public function testBuildStorages()
	{
		$storages = json_decode($this->storages_json,true);
		$this->buildStorages($storages["storage"]["storage_types"]);

		$this->assertInstanceOf(\Pimple\Container::class,$this->container["STORAGES"]);
		$this->assertNotEmpty($this->container["STORAGES"]["session"]);
	}

	public function testBuildAuthenticator()
	{
		$auth = json_decode($this->auth_json,true);
		$this->buildAuthenticator($auth["authentication"]["available_types"]);

		$this->assertInstanceOf(\Pimple\Container::class,$this->container["AUTHENTICATIONS"]);
		$this->assertNotEmpty($this->container["AUTHENTICATIONS"]["mock"]);
	}
}
