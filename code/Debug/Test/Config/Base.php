<?php

/**
 * Class Sheep_Debug_Test_Config_Base
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Test_Config_Base extends EcomDev_PHPUnit_Test_Case_Config
{

    public function testModelAliases()
    {
        $this->assertModelAlias('sheep_debug/resourceInfo', 'Sheep_Debug_Model_ResourceInfo');
    }

    public function testHelperAlias()
    {
        $this->assertHelperAlias('sheep_debug', 'Sheep_Debug_Helper_Data');
    }

    public function testSetup()
    {
        $this->assertSetupResourceDefined('Sheep_Debug', 'sheep_debug_setup');
    }
    
    public function testObservers()
    {
        // TODO: implement observer
    }
}
