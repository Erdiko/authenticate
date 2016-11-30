<?php
/**
 *
 * @todo add tests for the different template types
 */
namespace tests\phpunit\traits;

use erdiko\authenticate\Traits\ConfigLoader;

require_once dirname(dirname(__DIR__)) . '/ErdikoTestCase.php';

/**
 * @FIXME: this tests needs to run on installed erdiko project with `auth.json` placed in config/application/
 */

/**
 * Class ConfigLoaderTest
 * @package erdiko\tests\authenticate\Traits
 */

class ConfigLoaderTest extends \tests\ErdikoTestCase
{
    use ConfigLoader;

	/**
	 * @expectedException
	 */
    public function testloadFromJsonFail()
    {
    	putenv("ERDIKO_CONTEXT=notexists");
	    try {
		    $result = $this->loadFromJson();
	    } catch (\Exception $e) {}
    }

    public function testloadFromJson()
    {
    	putenv("ERDIKO_CONTEXT=default"); // Load the test config (instead of default)

        $result = $this->loadFromJson();
		$this->assertInternalType('array', $result);
	    $this->assertNotEmpty($result);
	    $this->assertTrue(array_key_exists('authentication', $result));
	    $this->assertTrue(array_key_exists('storage', $result));
    }
}
