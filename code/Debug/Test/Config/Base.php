<?php

/**
 * Class Sheep_Debug_Test_Config_Base
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Test_Config_Base extends EcomDev_PHPUnit_Test_Case_Config
{

    public function testModelAliases()
    {
        $this->assertModelAlias('sheep_debug/resourceInfo', 'Sheep_Debug_Model_ResourceInfo');
        $this->assertModelAlias('core/email', 'Sheep_Debug_Model_Core_Email');
        $this->assertModelAlias('core/email_template', 'Sheep_Debug_Model_Core_Email_Template');
    }

    public function testSetup()
    {
        $this->assertSetupResourceDefined('Sheep_Debug', 'sheep_debug_setup');
    }

    public function testBlocks()
    {
        $this->assertBlockAlias('sheep_debug/abstract', 'Sheep_Debug_Block_Abstract');
    }

    public function testHelperAlias()
    {
        $this->assertHelperAlias('sheep_debug', 'Sheep_Debug_Helper_Data');
    }

    public function testObservers()
    {
        $this->assertEventObserverDefined('global', 'controller_front_init_before', 'sheep_debug/observer', 'onControllerFrontInitBefore');
        $this->assertEventObserverDefined('global', 'controller_action_layout_generate_blocks_after', 'sheep_debug/observer', 'onLayoutGenerate');
        $this->assertEventObserverDefined('global', 'core_block_abstract_to_html_before', 'sheep_debug/observer', 'onBlockToHtml');
        $this->assertEventObserverDefined('global', 'core_block_abstract_to_html_after', 'sheep_debug/observer', 'onBlockToHtmlAfter');
        $this->assertEventObserverDefined('global', 'controller_action_postdispatch', 'sheep_debug/observer', 'onActionPostDispatch');
        $this->assertEventObserverDefined('global', 'core_collection_abstract_load_before', 'sheep_debug/observer', 'onCollectionLoad');
        $this->assertEventObserverDefined('global', 'eav_collection_abstract_load_before', 'sheep_debug/observer', 'onCollectionLoad');
        $this->assertEventObserverDefined('global', 'model_load_after', 'sheep_debug/observer', 'onModelLoad');
        $this->assertEventObserverDefined('global', 'controller_action_predispatch', 'sheep_debug/observer', 'onActionPreDispatch');
        $this->assertEventObserverDefined('global', 'controller_front_send_response_after', 'sheep_debug/observer', 'onControllerFrontSendResponseAfter');
    }

    public function testFrontend()
    {
        $this->assertRouteIn('sheep_debug');
        $this->assertLayoutFileDefined('frontend', 'sheep_debug.xml');
    }

    public function testAdmin()
    {
        $this->assertLayoutFileDefined('adminhtml', 'sheep_debug.xml');
    }

    public function testDefaultConfigs()
    {
        $this->assertDefaultConfigValue('sheep_debug/options/enable', 1);
        $this->assertDefaultConfigValue('sheep_debug/options/persist', 1);
        $this->assertDefaultConfigValue('sheep_debug/options/persist_expiration', 1);
        $this->assertDefaultConfigValue('sheep_debug/options/force_varien_profile', 1);
    }

}
